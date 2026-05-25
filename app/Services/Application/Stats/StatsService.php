<?php

namespace App\Services\Application\Stats;

use App\Enums\StatutCandidature;
use App\Enums\StatutPaiement;
use App\Enums\StatutVerificationDocument;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Centre;
use App\Models\Concours;
use App\Models\Departement;
use App\Models\Document;
use App\Models\Ecole;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;

class StatsService
{
    /**
     * Statistiques globales pour le dashboard
     */
    public function getDashboardStats(): array
    {
        return [
            'candidats' => $this->getCandidatsStats(),
            'candidatures' => $this->getCandidaturesStats(),
            'paiements' => $this->getPaiementsStats(),
            'documents' => $this->getDocumentsStats(),
            'concours' => $this->getConcoursStatsGlobal(),
        ];
    }

    /**
     * Statistiques des candidats
     */
    private function getCandidatsStats(): array
    {
        $total = Candidat::count();
        $actifs = Candidat::where('est_actif', true)->count();

        return [
            'total' => $total,
            'actifs' => $actifs,
            'nouveaux_aujourdhui' => Candidat::whereDate('created_at', today())->count(),
            'nouveaux_cette_semaine' => Candidat::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),
        ];
    }

    /**
     * Statistiques des candidatures
     */
    private function getCandidaturesStats(): array
    {
        $total = Candidature::count();
        $actives = Candidature::actives()->count();
        $invalides = Candidature::whereIn('statut_candidature', [
            StatutCandidature::REJETEE->value,
            StatutCandidature::ANNULEE->value,
        ])->count();
        $validees = Candidature::where('statut_candidature', StatutCandidature::VALIDE->value)->count();

        return [
            'total' => $total,
            'actives' => $actives,
            'invalides' => $invalides,
            'validees' => $validees,
            'en_attente_validation' => $actives - $validees,
        ];
    }

    /**
     * Statistiques des paiements
     */
    private function getPaiementsStats(): array
    {
        $total = Paiement::count();
        $verified = Paiement::where('statut', StatutPaiement::VERIFIED)->count();
        $pending = Paiement::where('statut', StatutPaiement::PENDING)->count();
        $rejected = Paiement::where('statut', StatutPaiement::REJECTED)->count();
        $montantTotal = Paiement::where('statut', StatutPaiement::VERIFIED)->sum('montant');

        return [
            'total' => $total,
            'verified' => $verified,
            'pending' => $pending,
            'rejected' => $rejected,
            'montant_total' => (float) $montantTotal,
        ];
    }

    /**
     * Statistiques des documents
     */
    private function getDocumentsStats(): array
    {
        return [
            'en_attente' => Document::where('statut_verification', StatutVerificationDocument::EN_ATTENTE)->count(),
            'valides' => Document::where('statut_verification', StatutVerificationDocument::VALIDE)->count(),
            'rejetes' => Document::where('statut_verification', StatutVerificationDocument::REJETE)->count(),
        ];
    }

    /**
     * Statistiques des concours (pour dashboard)
     */
    private function getConcoursStatsGlobal(): array
    {
        $actifs = Concours::where('est_actif', true)->count();
        $ouverts = Concours::where('est_actif', true)
            ->where('date_limite_depot', '>=', now())
            ->count();

        return [
            'actifs' => $actifs,
            'ouverts' => $ouverts,
            'fermes' => $actifs - $ouverts,
        ];
    }

    /**
     * Statistiques détaillées pour un concours
     */
    public function getConcoursStats(string $concoursId): array
    {
        $concours = Concours::with(['filieres', 'configurationPaiement'])->findOrFail($concoursId);

        $candidatures = Candidature::where('concours_id', $concoursId);
        $total = $candidatures->count();
        $actives = (clone $candidatures)->whereIn('statut_candidature', [
            StatutCandidature::SOUMISE->value,
            StatutCandidature::DOCUMENTS_VERIFIES->value,
            StatutCandidature::PAIEMENT_VERIFIE->value,
            StatutCandidature::VALIDE->value,
        ])->count();
        $invalides = (clone $candidatures)->whereIn('statut_candidature', [
            StatutCandidature::REJETEE->value,
            StatutCandidature::ANNULEE->value,
        ])->count();
        $validees = (clone $candidatures)->where('statut_candidature', StatutCandidature::VALIDE->value)->count();

        return [
            'concours' => [
                'id' => $concours->id,
                'libelle_concours' => $concours->libelle_concours,
            ],
            'global' => [
                'total_candidatures' => $total,
                'actives' => $actives,
                'invalides' => $invalides,
                'validees' => $validees,
                'places_disponibles' => $concours->nbre_max_places,
                'taux_remplissage' => $concours->nbre_max_places > 0
                  ? round(($total / $concours->nbre_max_places) * 100, 2)
                  : 0,
            ],
            'par_filiere' => $this->getStatsByFiliere($concoursId),
            'par_region' => $this->getStatsByRegion($concoursId),
            'par_genre' => $this->getStatsByGenre($concoursId),
            'pyramide_ages' => $this->getPyramideAges($concoursId),
            'par_serie_bac' => $this->getStatsBySerieBac($concoursId),
            'par_mention' => $this->getStatsByMention($concoursId),
            'par_etablissement_origine' => $this->getStatsByEtablissement($concoursId),
            'timeline' => $this->getTimelineStats($concoursId),
        ];
    }

    /**
     * Stats par filière
     */
    private function getStatsByFiliere(string $concoursId): array
    {
        $concours = Concours::with('filieres')->findOrFail($concoursId);

        return $concours->filieres->map(function ($filiere) use ($concoursId) {
            $candidatures = Candidature::where('concours_id', $concoursId)
                ->whereHas('candidat', function ($q) use ($filiere) {
                    $q->where('filiere_id', $filiere->id);
                })
                ->count();

            $nombrePlaces = $filiere->pivot->nombre_places ?? 0;

            return [
                'filiere_id' => $filiere->id,
                'nom_filiere' => $filiere->nom_filiere,
                'nombre_places' => $nombrePlaces,
                'candidatures' => $candidatures,
                'taux_remplissage' => $nombrePlaces > 0
                  ? round(($candidatures / $nombrePlaces) * 100, 2)
                  : 0,
            ];
        })->toArray();
    }

    /**
     * Stats par région
     */
    private function getStatsByRegion(string $concoursId): array
    {
        $stats = Candidature::where('concours_id', $concoursId)
            ->join('candidats', 'candidatures.candidat_id', '=', 'candidats.utilisateur_id')
            ->select('candidats.region', DB::raw('COUNT(*) as nombre'))
            ->whereNotNull('candidats.region')
            ->groupBy('candidats.region')
            ->get();

        $total = $stats->sum('nombre');

        return $stats->mapWithKeys(function ($stat) use ($total) {
            return [
                $stat->region => [
                    'nombre' => $stat->nombre,
                    'pourcentage' => $total > 0 ? round(($stat->nombre / $total) * 100, 2) : 0,
                ],
            ];
        })->toArray();
    }

    /**
     * Stats par genre
     */
    private function getStatsByGenre(string $concoursId): array
    {
        $stats = Candidature::where('concours_id', $concoursId)
            ->join('candidats', 'candidatures.candidat_id', '=', 'candidats.utilisateur_id')
            ->select('candidats.sexe_cand', DB::raw('COUNT(*) as nombre'))
            ->whereNotNull('candidats.sexe_cand')
            ->groupBy('candidats.sexe_cand')
            ->get();

        $total = $stats->sum('nombre');
        $masculin = $stats->where('sexe_cand', 'M')->first()->nombre ?? 0;
        $feminin = $stats->where('sexe_cand', 'F')->first()->nombre ?? 0;

        return [
            'masculin' => $masculin,
            'feminin' => $feminin,
            'pourcentage_masculin' => $total > 0 ? round(($masculin / $total) * 100, 2) : 0,
            'pourcentage_feminin' => $total > 0 ? round(($feminin / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Pyramide des âges
     */
    private function getPyramideAges(string $concoursId): array
    {
        $candidats = Candidature::where('concours_id', $concoursId)
            ->join('candidats', 'candidatures.candidat_id', '=', 'candidats.utilisateur_id')
            ->select('candidats.age_cand', 'candidats.sexe_cand')
            ->whereNotNull('candidats.age_cand')
            ->get();

        $tranches = [
            '16-18' => ['masculin' => 0, 'feminin' => 0],
            '19-21' => ['masculin' => 0, 'feminin' => 0],
            '22-24' => ['masculin' => 0, 'feminin' => 0],
            '25+' => ['masculin' => 0, 'feminin' => 0],
        ];

        foreach ($candidats as $candidat) {
            $age = $candidat->age_cand;
            $sexe = strtoupper((string) ($candidat->sexe_cand ?? ''));
            $genre = match ($sexe) {
                'M' => 'masculin',
                'F' => 'feminin',
                default => null,
            };

            if ($genre === null) {
                continue;
            }

            if ($age >= 16 && $age <= 18) {
                $tranches['16-18'][$genre]++;
            } elseif ($age >= 19 && $age <= 21) {
                $tranches['19-21'][$genre]++;
            } elseif ($age >= 22 && $age <= 24) {
                $tranches['22-24'][$genre]++;
            } elseif ($age >= 25) {
                $tranches['25+'][$genre]++;
            }
        }

        $result = [];
        foreach ($tranches as $tranche => $data) {
            $result[] = [
                'tranche' => $tranche,
                'masculin' => $data['masculin'],
                'feminin' => $data['feminin'],
                'total' => $data['masculin'] + $data['feminin'],
            ];
        }

        $ages = $candidats->pluck('age_cand')->filter();

        return [
            'tranches' => $result,
            'age_moyen' => $ages->count() > 0 ? round($ages->avg(), 1) : 0,
            'age_median' => $ages->count() > 0 ? $ages->median() : 0,
            'age_min' => $ages->count() > 0 ? $ages->min() : 0,
            'age_max' => $ages->count() > 0 ? $ages->max() : 0,
        ];
    }

    /**
     * Stats par série bac
     */
    private function getStatsBySerieBac(string $concoursId): array
    {
        return Candidature::where('concours_id', $concoursId)
            ->join('candidats', 'candidatures.candidat_id', '=', 'candidats.utilisateur_id')
            ->select('candidats.serie_bac', DB::raw('COUNT(*) as nombre'))
            ->whereNotNull('candidats.serie_bac')
            ->groupBy('candidats.serie_bac')
            ->pluck('nombre', 'serie_bac')
            ->toArray();
    }

    /**
     * Stats par mention
     */
    private function getStatsByMention(string $concoursId): array
    {
        return Candidature::where('concours_id', $concoursId)
            ->join('candidats', 'candidatures.candidat_id', '=', 'candidats.utilisateur_id')
            ->select('candidats.mention', DB::raw('COUNT(*) as nombre'))
            ->whereNotNull('candidats.mention')
            ->groupBy('candidats.mention')
            ->pluck('nombre', 'mention')
            ->toArray();
    }

    /**
     * Stats par établissement d'origine
     */
    private function getStatsByEtablissement(string $concoursId): array
    {
        return Candidature::where('concours_id', $concoursId)
            ->join('candidats', 'candidatures.candidat_id', '=', 'candidats.utilisateur_id')
            ->select('candidats.etablissement_origine', DB::raw('COUNT(*) as nombre'))
            ->whereNotNull('candidats.etablissement_origine')
            ->groupBy('candidats.etablissement_origine')
            ->orderByDesc('nombre')
            ->limit(10)
            ->get()
            ->map(fn ($item) => [
                'etablissement' => $item->etablissement_origine,
                'nombre' => $item->nombre,
            ])
            ->toArray();
    }

    /**
     * Timeline des inscriptions
     */
    private function getTimelineStats(string $concoursId): array
    {
        $inscriptions = Candidature::where('concours_id', $concoursId)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as nombre'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($item) => [
                'date' => $item->date,
                'nombre' => $item->nombre,
            ])
            ->toArray();

        return [
            'inscriptions_par_jour' => $inscriptions,
        ];
    }

    /**
     * Stats par centre d'examen
     */
    public function getStatsByCentre(?string $concoursId = null, ?string $sessionId = null): array
    {
        $query = Centre::with(['candidatures' => function ($q) use ($concoursId, $sessionId) {
            if ($concoursId) {
                $q->where('concours_id', $concoursId);
            }
            if ($sessionId) {
                $q->where('session_id', $sessionId);
            }
        }]);

        return $query->get()->map(function ($centre) {
            $candidatures = $centre->candidatures;
            $total = $candidatures->count();

            return [
                'centre_id' => $centre->id,
                'nom_centre' => $centre->libelle_centre,
                'ville' => $centre->ville_centre,
                'region' => $centre->region,
                'capacite' => $centre->capacite,
                'candidatures_assignees' => $total,
                'taux_occupation' => $centre->capacite > 0
                  ? round(($total / $centre->capacite) * 100, 2)
                  : 0,
            ];
        })->toArray();
    }

    /**
     * Stats par région
     */
    public function getStatsByRegionGlobal(?string $concoursId = null, ?string $sessionId = null): array
    {
        $query = Candidature::join('candidats', 'candidatures.candidat_id', '=', 'candidats.utilisateur_id')
            ->whereNotNull('candidats.region');

        if ($concoursId) {
            $query->where('candidatures.concours_id', $concoursId);
        }
        if ($sessionId) {
            $query->where('candidatures.session_id', $sessionId);
        }

        $stats = $query->select('candidats.region', DB::raw('COUNT(*) as nombre'))
            ->groupBy('candidats.region')
            ->get();

        $total = $stats->sum('nombre');

        return $stats->map(function ($stat) use ($total) {
            return [
                'region' => $stat->region,
                'candidatures' => $stat->nombre,
                'pourcentage' => $total > 0 ? round(($stat->nombre / $total) * 100, 2) : 0,
            ];
        })->toArray();
    }

    /**
     * Widgets pour le dashboard
     */
    public function getWidgets(): array
    {
        $stats = $this->getDashboardStats();

        return [
            'kpis' => [
                [
                    'label' => 'Total Candidatures',
                    'value' => $stats['candidatures']['total'],
                    'evolution' => '+11.1%',
                    'trend' => 'up',
                ],
                [
                    'label' => 'Paiements en Attente',
                    'value' => $stats['paiements']['pending'],
                    'evolution' => '-15%',
                    'trend' => 'down',
                ],
                [
                    'label' => 'Documents à Valider',
                    'value' => $stats['documents']['en_attente'],
                    'evolution' => '+8%',
                    'trend' => 'up',
                ],
            ],
        ];
    }

    /**
     * Statistiques globales (écoles, départements, filières, niveaux)
     * GET /api/admin/stats/global
     */
    public function getGlobalStats(): array
    {
        return [
            'ecoles' => [
                'total' => Ecole::count(),
                'actives' => Ecole::where('est_actif', true)->count(),
                'inactives' => Ecole::where('est_actif', false)->count(),
            ],
            'departements' => [
                'total' => Departement::count(),
                'actifs' => Departement::where('est_actif', true)->count(),
                'inactifs' => Departement::where('est_actif', false)->count(),
            ],
            'filieres' => [
                'total' => Filiere::count(),
                'actives' => Filiere::where('est_actif', true)->count(),
                'inactives' => Filiere::where('est_actif', false)->count(),
            ],
            'niveaux' => [
                'total' => Niveau::count(),
                'actifs' => Niveau::where('est_actif', true)->count(),
                'inactifs' => Niveau::where('est_actif', false)->count(),
            ],
        ];
    }

    /**
     * Statistiques des écoles
     * GET /api/admin/stats/ecoles
     */
    public function getEcolesStats(): array
    {
        return [
            'total' => Ecole::count(),
            'actives' => Ecole::where('est_actif', true)->count(),
            'inactives' => Ecole::where('est_actif', false)->count(),
        ];
    }

    /**
     * Statistiques des départements
     * GET /api/admin/stats/departements
     */
    public function getDepartementsStats(): array
    {
        return [
            'total' => Departement::count(),
            'actifs' => Departement::where('est_actif', true)->count(),
            'inactifs' => Departement::where('est_actif', false)->count(),
        ];
    }

    /**
     * Statistiques des filières
     * GET /api/admin/stats/filieres
     */
    public function getFilieresStats(): array
    {
        return [
            'total' => Filiere::count(),
            'actives' => Filiere::where('est_actif', true)->count(),
            'inactives' => Filiere::where('est_actif', false)->count(),
        ];
    }

    /**
     * Statistiques des niveaux
     * GET /api/admin/stats/niveaux
     */
    public function getNiveauxStats(): array
    {
        return [
            'total' => Niveau::count(),
            'actifs' => Niveau::where('est_actif', true)->count(),
            'inactifs' => Niveau::where('est_actif', false)->count(),
        ];
    }

    /**
     * Statistiques des centres
     * GET /api/admin/stats/centres
     */
    public function getCentresStats(): array
    {
        $centres = Centre::with('region')->get();
        $regionsUniques = $centres->pluck('region_id')->unique()->filter()->count();

        return [
            'total' => $centres->count(),
            'actifs' => $centres->where('est_actif', true)->count(),
            'inactifs' => $centres->where('est_actif', false)->count(),
            'regions_count' => $regionsUniques,
        ];
    }

    /**
     * Statistiques des concours (globales)
     * GET /api/admin/stats/concours
     */
    public function getConcoursStatsGlobalDetailed(): array
    {
        $total = Concours::count();
        $actifs = Concours::where('est_actif', true)->count();
        $inactifs = Concours::where('est_actif', false)->count();

        return [
            'total' => $total,
            'actifs' => $actifs,
            'inactifs' => $inactifs,
        ];
    }

    /**
     * Stats par école
     */
    public function getStatsByEcole(?string $sessionId = null): array
    {
        $query = Concours::with(['filieres', 'configurationPaiement']);

        if ($sessionId) {
            $query->where('session_id', $sessionId);
        }

        return $query->get()->map(function ($concours) {
            $candidatures = Candidature::where('concours_id', $concours->id);
            $total = $candidatures->count();

            $parFiliere = $concours->filieres->map(function ($filiere) use ($concours) {
                $candidaturesFiliere = Candidature::where('concours_id', $concours->id)
                    ->whereHas('candidat', function ($q) use ($filiere) {
                        $q->where('filiere_id', $filiere->id);
                    })
                    ->count();

                $nombrePlaces = $filiere->pivot->nombre_places ?? 0;

                return [
                    'filiere' => $filiere->nom_filiere,
                    'candidatures' => $candidaturesFiliere,
                    'places' => $nombrePlaces,
                    'taux' => $nombrePlaces > 0 ? round(($candidaturesFiliere / $nombrePlaces) * 100, 2) : 0,
                ];
            });

            $parRegion = Candidature::where('concours_id', $concours->id)
                ->join('candidats', 'candidatures.candidat_id', '=', 'candidats.utilisateur_id')
                ->select('candidats.region', DB::raw('COUNT(*) as nombre'))
                ->whereNotNull('candidats.region')
                ->groupBy('candidats.region')
                ->pluck('nombre', 'region');

            $ages = Candidature::where('concours_id', $concours->id)
                ->join('candidats', 'candidatures.candidat_id', '=', 'candidats.utilisateur_id')
                ->whereNotNull('candidats.age_cand')
                ->pluck('candidats.age_cand');

            $genres = Candidature::where('concours_id', $concours->id)
                ->join('candidats', 'candidatures.candidat_id', '=', 'candidats.utilisateur_id')
                ->select('candidats.sexe_cand', DB::raw('COUNT(*) as nombre'))
                ->whereNotNull('candidats.sexe_cand')
                ->groupBy('candidats.sexe_cand')
                ->get();

            $masculin = $genres->where('sexe_cand', 'MASCULIN')->first()->nombre ?? 0;
            $feminin = $genres->where('sexe_cand', 'FEMININ')->first()->nombre ?? 0;
            $totalGenre = $masculin + $feminin;

            return [
                'ecole_id' => $concours->id,
                'nom_ecole' => $concours->libelle_concours,
                'region' => $concours->region ?? null,
                'total_candidatures' => $total,
                'places_disponibles' => $concours->nbre_max_places,
                'taux_remplissage' => $concours->nbre_max_places > 0
                  ? round(($total / $concours->nbre_max_places) * 100, 2)
                  : 0,
                'par_filiere' => $parFiliere->toArray(),
                'par_region_origine' => $parRegion->toArray(),
                'moyenne_age' => $ages->count() > 0 ? round($ages->avg(), 1) : 0,
                'ratio_genre' => [
                    'masculin' => $totalGenre > 0 ? round(($masculin / $totalGenre) * 100, 2) : 0,
                    'feminin' => $totalGenre > 0 ? round(($feminin / $totalGenre) * 100, 2) : 0,
                ],
            ];
        })->toArray();
    }

    /**
     * Stats détaillées des paiements
     */
    public function getStatsPaiements(?string $dateDebut = null, ?string $dateFin = null, ?string $concoursId = null): array
    {
        $query = Paiement::query();

        if ($dateDebut) {
            $query->whereDate('created_at', '>=', $dateDebut);
        }
        if ($dateFin) {
            $query->whereDate('created_at', '<=', $dateFin);
        }
        if ($concoursId) {
            $query->where('concours_id', $concoursId);
        }

        $paiements = $query->get();
        $total = $paiements->count();
        $verified = $paiements->where('statut', StatutPaiement::VERIFIED)->count();
        $pending = $paiements->where('statut', StatutPaiement::PENDING)->count();
        $rejected = $paiements->where('statut', StatutPaiement::REJECTED)->count();
        $montantTotal = $paiements->where('statut', StatutPaiement::VERIFIED)->sum('montant');

        // Par concours
        $parConcours = Paiement::with('concoursPaiement.concours')
            ->when($dateDebut, fn ($q) => $q->whereDate('created_at', '>=', $dateDebut))
            ->when($dateFin, fn ($q) => $q->whereDate('created_at', '<=', $dateFin))
            ->when($concoursId, fn ($q) => $q->where('concours_id', $concoursId))
            ->get()
            ->groupBy('concours_id')
            ->map(function ($group) {
                $verified = $group->where('statut', StatutPaiement::VERIFIED)->count();
                $pending = $group->where('statut', StatutPaiement::PENDING)->count();
                $rejected = $group->where('statut', StatutPaiement::REJECTED)->count();
                $montant = $group->where('statut', StatutPaiement::VERIFIED)->sum('montant');

                return [
                    'concours' => $group->first()->concoursPaiement->concours->libelle_concours ?? 'N/A',
                    'nombre' => $group->count(),
                    'montant' => (float) $montant,
                    'verified' => $verified,
                    'pending' => $pending,
                    'rejected' => $rejected,
                ];
            })
            ->values()
            ->toArray();

        // Par jour
        $parJour = Paiement::query()
            ->when($dateDebut, fn ($q) => $q->whereDate('created_at', '>=', $dateDebut))
            ->when($dateFin, fn ($q) => $q->whereDate('created_at', '<=', $dateFin))
            ->when($concoursId, fn ($q) => $q->where('concours_id', $concoursId))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as nombre'), DB::raw('SUM(montant) as montant'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($item) => [
                'date' => $item->date,
                'nombre' => $item->nombre,
                'montant' => (float) $item->montant,
            ])
            ->toArray();

        // Par banque (depuis OCR)
        $parBanque = $paiements->whereNotNull('banque_ocr')
            ->groupBy('banque_ocr')
            ->map(fn ($group) => $group->count())
            ->toArray();

        // Temps moyen de validation
        $paiementsVerified = $paiements->where('statut', StatutPaiement::VERIFIED)
            ->whereNotNull('date_validation');

        $tempsTotal = 0;
        $count = 0;
        foreach ($paiementsVerified as $paiement) {
            if ($paiement->date_validation && $paiement->created_at) {
                $tempsTotal += $paiement->created_at->diffInHours($paiement->date_validation);
                $count++;
            }
        }
        $tempsMoyenValidation = $count > 0 ? round($tempsTotal / $count, 1) : 0;

        // Stats OCR
        $paiementsAvecOcr = $paiements->whereNotNull('ocr_confidence');
        $tauxAutoValidation = $total > 0 ? round(($paiementsAvecOcr->count() / $total) * 100, 2) : 0;
        $confianceMoyenne = $paiementsAvecOcr->count() > 0
          ? round($paiementsAvecOcr->avg('ocr_confidence'), 2)
          : 0;

        return [
            'global' => [
                'total_paiements' => $total,
                'montant_total' => (float) $montantTotal,
                'verified' => $verified,
                'pending' => $pending,
                'rejected' => $rejected,
                'taux_validation' => $total > 0 ? round(($verified / $total) * 100, 2) : 0,
            ],
            'par_concours' => $parConcours,
            'par_jour' => $parJour,
            'par_banque' => $parBanque,
            'temps_moyen_validation' => $tempsMoyenValidation . ' heures',
            'ocr_stats' => [
                'taux_auto_validation' => $tauxAutoValidation,
                'confiance_moyenne' => $confianceMoyenne,
            ],
        ];
    }

    /**
     * Stats détaillées des documents
     */
    public function getStatsDocuments(?string $concoursId = null): array
    {
        $query = Document::query();

        if ($concoursId) {
            $query->whereHas('candidature', function ($q) use ($concoursId) {
                $q->where('concours_id', $concoursId);
            });
        }

        $documents = $query->get();
        $total = $documents->count();
        $enAttente = $documents->where('statut_verification', StatutVerificationDocument::EN_ATTENTE)->count();
        $valides = $documents->where('statut_verification', StatutVerificationDocument::VALIDE)->count();
        $rejetes = $documents->where('statut_verification', StatutVerificationDocument::REJETE)->count();

        // Par type
        $parType = $documents->groupBy('type_document')->map(function ($group, $type) {
            $soumis = $group->count();
            $valides = $group->where('statut_verification', StatutVerificationDocument::VALIDE)->count();
            $rejetes = $group->where('statut_verification', StatutVerificationDocument::REJETE)->count();
            $enAttente = $group->where('statut_verification', StatutVerificationDocument::EN_ATTENTE)->count();

            return [
                'type' => $type,
                'soumis' => $soumis,
                'valides' => $valides,
                'rejetes' => $rejetes,
                'en_attente' => $enAttente,
                'taux_validation' => $soumis > 0 ? round(($valides / $soumis) * 100, 2) : 0,
            ];
        })->values()->toArray();

        // Temps moyen de validation
        $documentsValides = $documents->where('statut_verification', StatutVerificationDocument::VALIDE)
            ->whereNotNull('date_verification');

        $tempsTotal = 0;
        $count = 0;
        foreach ($documentsValides as $document) {
            if ($document->date_verification && $document->created_at) {
                $tempsTotal += $document->created_at->diffInDays($document->date_verification);
                $count++;
            }
        }
        $tempsMoyenValidation = $count > 0 ? round($tempsTotal / $count, 1) : 0;

        // Motifs de rejet
        $motifsRejet = $documents->where('statut_verification', StatutVerificationDocument::REJETE)
            ->whereNotNull('motif_rejet')
            ->groupBy('motif_rejet')
            ->map(fn ($group) => [
                'motif' => $group->first()->motif_rejet,
                'nombre' => $group->count(),
            ])
            ->values()
            ->toArray();

        return [
            'global' => [
                'total_soumis' => $total,
                'en_attente' => $enAttente,
                'valides' => $valides,
                'rejetes' => $rejetes,
                'taux_validation' => $total > 0 ? round(($valides / $total) * 100, 2) : 0,
            ],
            'par_type' => $parType,
            'temps_moyen_validation' => $tempsMoyenValidation . ' jours',
            'motifs_rejet' => $motifsRejet,
        ];
    }

    /**
     * Stats temporelles (timeline)
     */
    public function getStatsTimeline(?string $concoursId = null, ?string $dateDebut = null, ?string $dateFin = null, string $granularite = 'jour'): array
    {
        $queryInscriptions = Candidature::query();
        $queryPaiements = Paiement::query();

        if ($concoursId) {
            $queryInscriptions->where('concours_id', $concoursId);
            $queryPaiements->where('concours_id', $concoursId);
        }
        if ($dateDebut) {
            $queryInscriptions->whereDate('created_at', '>=', $dateDebut);
            $queryPaiements->whereDate('created_at', '>=', $dateDebut);
        }
        if ($dateFin) {
            $queryInscriptions->whereDate('created_at', '<=', $dateFin);
            $queryPaiements->whereDate('created_at', '<=', $dateFin);
        }

        // Inscriptions par période
        $inscriptions = $queryInscriptions
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as nombre'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $cumul = 0;
        $inscriptionsData = $inscriptions->map(function ($item) use (&$cumul) {
            $cumul += $item->nombre;

            return [
                'periode' => $item->date,
                'nombre' => $item->nombre,
                'cumul' => $cumul,
            ];
        })->toArray();

        // Paiements par période
        $paiementsData = $queryPaiements
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as nombre'), DB::raw('SUM(montant) as montant'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($item) => [
                'periode' => $item->date,
                'nombre' => $item->nombre,
                'montant' => (float) $item->montant,
            ])
            ->toArray();

        // Projections
        $totalInscriptions = $cumul;
        $joursEcoules = $inscriptions->count();
        $rythmeActuel = $joursEcoules > 0 ? round($totalInscriptions / $joursEcoules, 1) : 0;

        return [
            'inscriptions' => $inscriptionsData,
            'paiements' => $paiementsData,
            'projections' => [
                'inscriptions_actuelles' => $totalInscriptions,
                'jours_ecoules' => $joursEcoules,
                'rythme_actuel' => $rythmeActuel . ' inscriptions/jour',
            ],
        ];
    }

    /**
     * Stats comparatives entre sessions
     */
    public function getStatsComparatives(string $sessionActuelleId, string $sessionPrecedenteId): array
    {
        // Session actuelle
        $candidaturesActuelles = Candidature::whereHas('concours', function ($q) use ($sessionActuelleId) {
            $q->where('session_id', $sessionActuelleId);
        })->count();

        $concoursActuels = Concours::where('session_id', $sessionActuelleId)->get();
        $placesActuelles = $concoursActuels->sum('nbre_max_places');
        $tauxRemplissageActuel = $placesActuelles > 0
          ? round(($candidaturesActuelles / $placesActuelles) * 100, 2)
          : 0;

        // Session précédente
        $candidaturesPrecedentes = Candidature::whereHas('concours', function ($q) use ($sessionPrecedenteId) {
            $q->where('session_id', $sessionPrecedenteId);
        })->count();

        $concoursPrecedents = Concours::where('session_id', $sessionPrecedenteId)->get();
        $placesPrecedentes = $concoursPrecedents->sum('nbre_max_places');
        $tauxRemplissagePrecedent = $placesPrecedentes > 0
          ? round(($candidaturesPrecedentes / $placesPrecedentes) * 100, 2)
          : 0;

        // Évolution
        $evolutionCandidatures = $candidaturesPrecedentes > 0
          ? round((($candidaturesActuelles - $candidaturesPrecedentes) / $candidaturesPrecedentes) * 100, 1)
          : 0;
        $evolutionTaux = $tauxRemplissageActuel - $tauxRemplissagePrecedent;

        // Par concours
        $parConcours = [];
        foreach ($concoursActuels as $concoursActuel) {
            $candidaturesA = Candidature::where('concours_id', $concoursActuel->id)->count();

            // Trouver le concours équivalent dans la session précédente
            $concoursPrecedent = $concoursPrecedents->firstWhere('libelle_concours', $concoursActuel->libelle_concours);
            $candidaturesP = $concoursPrecedent
              ? Candidature::where('concours_id', $concoursPrecedent->id)->count()
              : 0;

            $evolution = $candidaturesP > 0
              ? round((($candidaturesA - $candidaturesP) / $candidaturesP) * 100, 1)
              : 0;

            $parConcours[] = [
                'concours' => $concoursActuel->libelle_concours,
                'actuelle' => $candidaturesA,
                'precedente' => $candidaturesP,
                'evolution' => ($evolution >= 0 ? '+' : '') . $evolution . '%',
            ];
        }

        return [
            'session_actuelle' => [
                'libelle' => 'Session ' . date('Y'),
                'candidatures' => $candidaturesActuelles,
                'taux_remplissage' => $tauxRemplissageActuel,
            ],
            'session_precedente' => [
                'libelle' => 'Session ' . (date('Y') - 1),
                'candidatures' => $candidaturesPrecedentes,
                'taux_remplissage' => $tauxRemplissagePrecedent,
            ],
            'evolution' => [
                'candidatures' => ($evolutionCandidatures >= 0 ? '+' : '') . $evolutionCandidatures . '%',
                'taux_remplissage' => ($evolutionTaux >= 0 ? '+' : '') . $evolutionTaux . ' points',
            ],
            'par_concours' => $parConcours,
        ];
    }

    /**
     * Statistiques des concours par école
     */
    public function getConcoursStatsByEcole(string $ecoleId): array
    {
        $concours = Concours::where('ecole_id', $ecoleId)
            ->withCount(['candidatures', 'paiements'])
            ->get();

        $total = $concours->count();
        $actifs = $concours->where('est_actif', true)->count();
        $enCours = $concours->where('est_actif', true)
            ->where('date_limite_depot', '>=', now())
            ->count();

        return [
            'total_concours' => $total,
            'concours_actifs' => $actifs,
            'concours_en_cours' => $enCours,
            'total_candidatures' => $concours->sum('candidatures_count'),
            'total_paiements' => $concours->sum('paiements_count'),
            'concours' => $concours->map(function ($c) {
                return [
                    'id' => $c->id,
                    'libelle' => $c->libelle_concours,
                    'date_limite_depot' => $c->date_limite_depot,
                    'candidatures' => $c->candidatures_count,
                    'paiements' => $c->paiements_count,
                ];
            }),
        ];
    }

    /**
     * Comparaison des écoles
     */
    public function compareEcoles(): array
    {
        return Ecole::withCount(['concours', 'departements'])
            ->with(['concours' => function ($q) {
                $q->withCount('candidatures');
            }])
            ->where('est_actif', true)
            ->get()
            ->map(function ($ecole) {
                $totalCandidatures = $ecole->concours->sum('candidatures_count');

                return [
                    'id' => $ecole->id,
                    'libelle' => $ecole->libelle_ecole,
                    'region' => $ecole->region?->label(),
                    'total_concours' => $ecole->concours_count,
                    'total_departements' => $ecole->departements_count,
                    'total_candidatures' => $totalCandidatures,
                ];
            })
            ->sortByDesc('total_candidatures')
            ->values()
            ->toArray();
    }
}
