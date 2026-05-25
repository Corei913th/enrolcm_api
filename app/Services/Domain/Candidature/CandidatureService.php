<?php

namespace App\Services\Domain\Candidature;

use App\Enums\StatutCandidature;
use App\Enums\StatutPaiement;
use App\Helpers\CodeGeneratorHelper;
use App\Helpers\DateHelper;
use App\Models\Alert;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Centre;
use App\Models\Concours;
use App\Models\Paiement;
use App\Services\Domain\Candidature\Validators\CandidatureValidationService;
use App\Services\Domain\Notification\Generators\AlertGeneratorService;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use App\Traits\HasAdvancedSearch;
use App\Traits\HasOptimizedUpdate;
use App\Traits\HasSmartCache;
use Illuminate\Pagination\LengthAwarePaginator;

class CandidatureService
{
    use HasAdvancedSearch, HasOptimizedUpdate, HasSmartCache;

    public function __construct(
        private readonly ActivityLoggerService $logger,
        private readonly AlertGeneratorService $alertGenerator
    ) {}

    protected function getModelTags(): array
    {
        return ['candidatures', 'lists'];
    }

    /**
     * Créer une candidature
     */
    public function create(array $data): Candidature
    {
        $candidature = Candidature::create([
            'candidat_id' => $data['candidat_id'],
            'concours_id' => $data['concours_id'],
            'session_id' => $data['session_id'],
            'statut_candidature' => $data['statut_candidature'] ?? StatutCandidature::SOUMISE,
            'code_cand_temp' => $data['code_cand_temp'],
        ]);

        $this->logger->logActivity('create', 'candidature', $candidature->id);
        $this->invalidateCacheAfterModification($candidature->id);

        return $candidature;
    }

    /**
     * Générer un code candidat temporaire
     */
    public function generateTempCode(string $utilisateurId): string
    {
        return CodeGeneratorHelper::generateTempCode();
    }

    /**
     * Créer une candidature complète
     */
    public function createCandidature($candidat, string $concoursId, $session, $dateInscription): Candidature
    {
        $candidature = Candidature::create([
            'candidat_id' => $candidat->utilisateur_id,
            'concours_id' => $concoursId,
            'session_id' => $session->id,
            'statut_candidature' => StatutCandidature::BROUILLON,
            'date_inscription' => $dateInscription,
            'code_cand_temp' => $this->generateTempCode($candidat->utilisateur_id),
        ]);

        $concours = Concours::find($concoursId);
        if ($concours && $concours->frais_inscription > 0) {
            Paiement::create([
                'candidature_id' => $candidature->id,
                'montant' => $concours->frais_inscription,
                'statut' => StatutPaiement::PENDING,
                'reference' => 'PAY-' . strtoupper(uniqid()),
            ]);
        }

        $this->logger->logActivity('create_candidature', 'candidature', $candidature->id, [
            'candidat_id' => $candidat->utilisateur_id,
            'concours_id' => $concoursId,
        ]);

        $this->invalidateCacheAfterModification($candidature->id);

        return $candidature;
    }

    /**
     * Récupérer une candidature ou échouer
     */
    public function getCandidatureOrFail(string $candidatureId): Candidature
    {
        return Candidature::findOrFail($candidatureId);
    }

    /**
     * Récupérer les candidatures pour un concours avec filtres (OPTIMISÉ)
     */
    public function getCandidatsForConcours(string $concoursId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $page = request()->input('page', 1);

        return $this->rememberList(
            array_merge($filters, ['concours_id' => $concoursId]),
            $page,
            $perPage,
            function () use ($concoursId, $filters, $perPage) {
                $query = Candidature::query()
                    ->select([
                        'candidatures.id',
                        'candidatures.candidat_id',
                        'candidatures.concours_id',
                        'candidatures.session_id',
                        'candidatures.centre_examen_id',
                        'candidatures.statut_candidature',
                        'candidatures.code_cand_temp',
                        'candidatures.code_cand_def',
                        'candidatures.date_inscription',
                        'candidatures.created_at',
                    ])
                    ->where('candidatures.concours_id', $concoursId);

                $query->with([
                    'candidat',
                    'candidat.utilisateur:id,email,user_name,telephone',
                    'session:id,libelle_session',
                ]);

                $simpleFilters = [];
                if (isset($filters['statut_candidature'])) {
                    $simpleFilters['candidatures.statut_candidature'] = $filters['statut_candidature'];
                }
                if (isset($filters['session_id'])) {
                    $simpleFilters['candidatures.session_id'] = $filters['session_id'];
                }
                $this->applyFilters($query, $simpleFilters);

                // Recherche avancée optimisée
                if (isset($filters['search'])) {
                    $this->applySearch(
                        $query,
                        $filters['search'],
                        [
                            'candidatures.code_cand_temp' => 'partial',
                            'candidatures.code_cand_def' => 'partial',
                        ],
                        [
                            'candidat.nom_cand' => 'words',
                            'candidat.prenom_cand' => 'words',
                            'candidat.utilisateur.email' => 'partial',
                            'candidat.utilisateur.user_name' => 'partial',
                        ]
                    );
                }

                // Tri optimisé
                $sortBy = $filters['sort_by'] ?? 'candidatures.created_at';
                $sortOrder = $filters['sort_order'] ?? 'desc';
                $this->applySort(
                    $query,
                    $sortBy,
                    $sortOrder,
                    'candidatures.created_at',
                    [
                        'candidatures.created_at',
                        'candidatures.statut_candidature',
                        'candidatures.code_cand_def',
                    ]
                );

                return $query->paginate($perPage);
            },
            'candidatures_concours'
        );
    }

    /**
     * Mettre à jour le statut d'une candidature
     * param string $candidatureId
     * param StatutCandidature $statut
     */
    public function updateStatut(string $candidatureId, StatutCandidature $statut): Candidature
    {
        $candidature = $this->getCandidatureOrFail($candidatureId);

        $wasUpdated = $this->updateIfDirty($candidature, [
            'statut_candidature' => $statut,
        ]);

        if ($wasUpdated) {
            $this->logger->logActivity('update_statut', 'candidature', $candidatureId, [
                'old_statut' => $candidature->getOriginal('statut_candidature'),
                'new_statut' => $statut->value,
            ]);

            $this->invalidateCacheAfterModification($candidatureId);
        }

        return $candidature->fresh();
    }

    /**
     * Rejeter une candidature
     */
    public function rejeter(string $candidatureId, string $motif): Candidature
    {
        $candidature = $this->getCandidatureOrFail($candidatureId);

        $wasUpdated = $this->updateIfDirty($candidature, [
            'statut_candidature' => StatutCandidature::REJETEE,
            'motif_rejet' => $motif,
        ]);

        if ($wasUpdated) {
            $this->logger->logActivity('rejeter', 'candidature', $candidatureId, [
                'motif' => $motif,
                'old_statut' => $candidature->getOriginal('statut_candidature'),
            ]);

            $this->invalidateCacheAfterModification($candidatureId);
        }

        return $candidature->fresh();
    }

    /**
     * Obtenir les statistiques des candidatures pour un concours
     */
    public function getStatsForConcours(string $concoursId): array
    {
        return $this->rememberStatic("candidatures_stats_{$concoursId}", function () use ($concoursId) {
            $total = Candidature::where('concours_id', $concoursId)->count();

            $byStatut = Candidature::where('concours_id', $concoursId)
                ->selectRaw('statut_candidature, COUNT(*) as count')
                ->groupBy('statut_candidature')
                ->pluck('count', 'statut_candidature')
                ->toArray();

            return [
                'total' => $total,
                'by_statut' => $byStatut,
                'taux_completion' => $total > 0 ? round(($byStatut[StatutCandidature::VALIDE->value] ?? 0) / $total * 100, 2) : 0,
            ];
        });
    }

    /**
     * Récupérer les candidatures d'un candidat avec filtres
     */
    public function getCandidaturesByCandidat(string $candidatId, array $filters = [], int $per_page = 20)
    {
        $query = Candidature::query()
            ->where('candidatures.candidat_id', $candidatId)
            ->with([
                'concours:id,libelle_concours,date_limite_depot,date_examen,ecole_id,est_actif',
                'concours.ecole:id,libelle_ecole,logo_path',
                'session:id,libelle_session',
                'paiement:id,candidature_id,statut,montant,reference',
                'convocation:id,candidature_id,numero_convocation,est_telechargee',
                'resultatFinal:id,candidature_id,est_admis,decision,moyenne_generale,rang',
            ]);

        // Filtres
        $simpleFilters = [];
        $showArchivesOnly = false;

        if (isset($filters['statut'])) {
            if ($filters['statut'] === 'ARCHIVE') {
                $showArchivesOnly = true;
            } else {
                $simpleFilters['candidatures.statut_candidature'] = $filters['statut'];
            }
        }
        if (isset($filters['concours_id'])) {
            $simpleFilters['candidatures.concours_id'] = $filters['concours_id'];
        }
        $this->applyFilters($query, $simpleFilters);

        return $query->orderBy('candidatures.created_at', 'desc')->get()
            ->filter(function ($candidature) use ($filters, $showArchivesOnly) {
                if ($showArchivesOnly) {
                    return $candidature->concours && ! $candidature->concours->est_actif;
                }

                if (isset($filters['include_archives']) && filter_var($filters['include_archives'], FILTER_VALIDATE_BOOLEAN)) {
                    return true;
                }

                return $candidature->concours && $candidature->concours->est_actif;
            })
            ->values();
    }

    /**
     * Statistiques du tableau de bord candidat
     */
    public function getDashboardStats(string $candidatId): array
    {
        return $this->rememberStatic("candidat_dashboard_{$candidatId}", function () use ($candidatId) {
            $candidat = Candidat::where('utilisateur_id', $candidatId)->first();
            if ($candidat) {
                $this->alertGenerator->generateCandidateAlerts($candidat);
            }

            $candidatures = Candidature::where('candidat_id', $candidatId)
                ->with([
                    'concours:id,libelle_concours,date_limite_depot,date_examen,ecole_id,est_actif',
                    'concours.ecole:id,libelle_ecole,logo_path',
                ])
                ->get();

            $stats = [
                'total_candidatures' => $candidatures->count(),
                'en_cours' => $candidatures->whereIn('statut_candidature', [
                    StatutCandidature::SOUMISE,
                    StatutCandidature::DOCUMENTS_VERIFIES,
                    StatutCandidature::PAIEMENT_VERIFIE,
                ])->count(),
                'validees' => $candidatures->where('statut_candidature', StatutCandidature::VALIDE)->count(),
                'rejetees' => $candidatures->where('statut_candidature', StatutCandidature::REJETEE)->count(),
            ];

            // Récupérer les IDs de candidatures ACTIVES seulement
            $activeCandidatureIds = $candidatures
                ->filter(fn ($c) => $c->concours && $c->concours->est_actif)
                ->pluck('id')
                ->toArray();

            $alertes = Alert::whereIn('candidature_id', $activeCandidatureIds)
                ->where('is_dismissed', false)
              // ->where('severity', 'critical') // REMOVED: On veut toutes les alertes (warning inclus)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $echeances = $candidatures
                ->filter(fn ($c) => $c->concours)
                ->flatMap(function ($candidature) {
                    $items = [];

                    if ($candidature->concours->date_limite_depot) {
                        $items[] = [
                            'candidature_id' => $candidature->id,
                            'concours' => $candidature->concours->libelle_concours,
                            'ecole' => $candidature->concours->ecole->libelle_ecole ?? null,
                            'logo_path' => $candidature->concours->ecole->logo_path ?? null,
                            'date_limite' => $candidature->concours->date_limite_depot->toDateTimeString(),
                            'type' => 'depot',
                        ];
                    }

                    if ($candidature->concours->date_examen) {
                        $items[] = [
                            'candidature_id' => $candidature->id,
                            'concours' => $candidature->concours->libelle_concours,
                            'ecole' => $candidature->concours->ecole->libelle_ecole ?? null,
                            'logo_path' => $candidature->concours->ecole->logo_path ?? null,
                            'date_limite' => $candidature->concours->date_examen->toDateTimeString(),
                            'type' => 'examen',
                        ];
                    }

                    return $items;
                })
                ->filter(fn ($item) => DateHelper::isFuture($item['date_limite']))
                ->sortBy('date_limite')
                ->take(10)
                ->values();

            $recentes = $candidatures
                ->filter(function ($candidature) {
                    return $candidature->concours && $candidature->concours->est_actif;
                })
                ->sortByDesc('created_at')
                ->take(3)
                ->map(function ($candidature) {
                    return [
                        'id' => $candidature->id,
                        'concours' => $candidature->concours->libelle_concours ?? null,
                        'ecole' => $candidature->concours->ecole->libelle_ecole ?? null,
                        'logo_path' => $candidature->concours->ecole->logo_path ?? null,
                        'statut' => $candidature->statut_candidature,
                        'date_candidature' => $candidature->date_candidature,
                    ];
                })
                ->values();

            return [
                'stats' => $stats,
                'alertes' => $alertes,
                'echeances' => $echeances,
                'candidatures_recentes' => $recentes,
            ];
        }, 300); // Cache 5 minutes
    }

    /**
     * Récupérer les centres disponibles pour un concours
     */
    public function getCentresDisponibles(string $concoursId): array
    {
        $centres = Centre::query()
            ->select(['id', 'libelle_centre', 'type_centre', 'ville_centre', 'region', 'capacite'])
            ->whereHas('concours', function ($query) use ($concoursId) {
                $query->where('concours.id', $concoursId)
                    ->where('concours_centre.est_actif', true);
            })
            ->where('est_actif', true)
            ->orderBy('ville_centre')
            ->orderBy('libelle_centre')
            ->get();

        return [
            'centres_examen' => $centres->where('type_centre', 'EXAMEN')->values(),
            'centres_depot' => $centres->where('type_centre', 'DEPOT')->values(),
            'centres_mixtes' => $centres->where('type_centre', 'MIXTE')->values(),
        ];
    }

    /**
     * Compléter une candidature avec les centres
     */
    public function completerCandidature(string $candidatureId, array $data): Candidature
    {
        $candidature = $this->getCandidatureOrFail($candidatureId);

        if (! $candidature->peutEtreModifiee()) {
            throw new \DomainException('Cette candidature ne peut plus être modifiée');
        }

        if (isset($data['centre_examen_id'])) {
            $this->verifyCenterBelongToConcours($data['centre_examen_id'], $candidature->concours_id, ['EXAMEN', 'MIXTE']);
        }

        if (isset($data['centre_depot_id'])) {
            $this->verifyCenterBelongToConcours($data['centre_depot_id'], $candidature->concours_id, ['DEPOT', 'MIXTE']);
        }

        $wasUpdated = $this->updateIfDirty($candidature, [
            'centre_examen_id' => $data['centre_examen_id'] ?? $candidature->centre_examen_id,
            'centre_depot_id' => $data['centre_depot_id'] ?? $candidature->centre_depot_id,
            'date_depot_physique' => $data['date_depot_physique'] ?? $candidature->date_depot_physique,
        ]);

        if ($wasUpdated) {
            $this->logger->logActivity('complete_candidature', 'candidature', $candidatureId, $data);
            $this->invalidateCacheAfterModification($candidatureId);

            // Check if candidature is ready for auto-validation
            $candidature->refresh();
            try {
                $validationService = app(CandidatureValidationService::class);
                $validationService->checkAndValidateIfReady($candidature);
            } catch (\Exception $e) {
                $this->logger->logActivity('candidature_auto_validation_failed', 'candidature', $candidatureId, [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $candidature->fresh(['centreExamen', 'centreDepot']);
    }

    /**
     * Vérifier qu'un centre appartient au concours et est du bon type
     */
    private function verifyCenterBelongToConcours(string $centreId, string $concoursId, array $typesAcceptes): void
    {
        $centre = Centre::find($centreId);

        if (! $centre) {
            throw new \DomainException('Centre introuvable');
        }

        if (! $centre->est_actif) {
            throw new \DomainException('Ce centre n\'est pas actif');
        }

        if (! in_array($centre->type_centre, $typesAcceptes)) {
            throw new \DomainException("Ce centre n'est pas du bon type");
        }

        $appartient = $centre->concours()
            ->where('concours.id', $concoursId)
            ->where('concours_centre.est_actif', true)
            ->exists();

        if (! $appartient) {
            throw new \DomainException('Ce centre n\'est pas disponible pour ce concours');
        }
    }
}
