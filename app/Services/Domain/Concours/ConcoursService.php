<?php

namespace App\Services\Domain\Concours;

use App\DTOs\Concours\CreateConcoursDTO;
use App\DTOs\Concours\UpdateConcoursDTO;
use App\Exceptions\ConcoursException;
use App\Models\Concours;
use App\Models\Session;
use App\Services\Domain\Concours\Checkers\ConcoursReadinessChecker;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use App\Traits\HasAdvancedSearch;
use App\Traits\HasOptimizedUpdate;
use App\Traits\HasSmartCache;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ConcoursService
{
    use HasSmartCache, HasOptimizedUpdate, HasAdvancedSearch;

    public function __construct(
        private readonly ActivityLoggerService $logger,
        private readonly ConcoursReadinessChecker $readinessChecker
    ) {}

    /**
     * Récupérer un concours avec sa spécification (avec cache)
     */
    public function getWithSpecification(string $concoursId): ?Concours
    {
        return $this->rememberDetail($concoursId, function () use ($concoursId) {
            return Concours::with('specConcours')->find($concoursId);
        }, 'concours_with_spec');
    }

    /**
     * Vérifier si un concours existe et est actif (avec cache)
     */
    public function isActive(string $concoursId): bool
    {
        return $this->rememberStatic("concours_active_{$concoursId}", function () use ($concoursId) {
            return Concours::where('id', $concoursId)->where('est_actif', true)->exists();
        });
    }

    /**
     * Récupérer les frais d'inscription d'un concours
     */
    public function getInscriptionFees(Concours $concours): ?float
    {
        return $concours->frais_inscription ?? $concours->specConcours?->montant_frais_depot;
    }

    /**
     * Récupérer tous les concours avec filtres et pagination
     */
    public function getAll(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $page = request()->input('page', 1);

        return $this->rememberList($filters, $page, $perPage, function () use ($filters, $perPage) {
            $query = Concours::with(['ecole', 'specConcours', 'sessions']);


            $simpleFilters = [];
            if (isset($filters['ecole_id'])) {
                $simpleFilters['ecole_id'] = $filters['ecole_id'];
            }
            if (isset($filters['spec_concours_id'])) {
                $simpleFilters['spec_concours_id'] = $filters['spec_concours_id'];
            }
            if (isset($filters['est_actif'])) {
                $simpleFilters['est_actif'] = $filters['est_actif'];
            }

            $query = $this->applyFilters($query, $simpleFilters);


            if (isset($filters['search'])) {
                $query = $this->applySearch(
                    $query,
                    $filters['search'],
                    [
                        'libelle_concours' => 'partial',
                        'description' => 'partial',
                    ],
                    [
                        'ecole.libelle_ecole' => 'partial',
                    ]
                );
            }

            // Appliquer le tri
            $sortBy = $filters['sort_by'] ?? 'created_at';
            $sortOrder = $filters['sort_order'] ?? 'desc';
            $query = $this->applySort($query, $sortBy, $sortOrder, 'created_at', [
                'created_at',
                'libelle_concours',
                'nbre_max_places',
                'date_limite_depot',
                'date_examen'
            ]);

            return $query->paginate($perPage);
        }, 'concours_list');
    }

    /**
     * Récupérer les concours disponibles (actifs et prêts pour inscription)
     * 
     * Utilise ConcoursReadinessChecker pour filtrer uniquement les concours
     * qui remplissent TOUS les critères requis pour l'inscription des candidats
     */
    public function getAvailableConcours(int $perPage = 20): LengthAwarePaginator
    {
        $page = request()->input('page', 1);

        return $this->rememberList(['available' => true], $page, $perPage, function () use ($perPage, $page) {
            $concours = Concours::with([
                'ecole:id,libelle_ecole,code_ecole',
                'specConcours',
                'configurationPaiement',
                'sessions',
                'filieres',
                'centers',
                'documentsRequis'
            ])
                ->where('est_actif', true)
                ->whereDate('date_limite_depot', '>=', now())
                ->latest()
                ->get();

            $readyConcours = $concours->filter(function ($concours) {
                $result = $this->readinessChecker->check($concours);
                return $result['ready'];
            });


            $total = $readyConcours->count();
            $items = $readyConcours->forPage($page, $perPage)->values();

            return new LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }, 'concours_available');
    }

    /**
     * Récupérer un concours par ID
     */
    public function getById(string $id, bool $withRelations = false): Concours
    {
        if ($withRelations) {
            return $this->rememberDetail($id, function () use ($id) {
                return Concours::with([
                    'ecole',
                    'specConcours',
                    'sessions',
                    'centers',
                    'filieres',
                    'configurationPaiement'
                ])->findOrFail($id);
            }, 'concours_full');
        }

        try {
            return Concours::findOrFail($id);
        } catch (ModelNotFoundException) {
            throw ConcoursException::notFound($id);
        }
    }

    /**
     * Créer un concours
     */
    public function create(CreateConcoursDTO $dto): Concours
    {
        return runTransaction(function () use ($dto) {
            $concours = Concours::create($dto->toArray());

            $this->logger->logActivity('create', 'concours', $concours->id, [
                'libelle' => $concours->libelle_concours,
                'ecole_id' => $concours->ecole_id
            ]);

            $this->invalidateCacheAfterModification($concours->id);

            return $concours->fresh(['ecole', 'specConcours']);
        }, 'ConcoursService::create');
    }

    /**
     * Mettre à jour un concours
     */
    public function update(string $id, UpdateConcoursDTO $dto): Concours
    {
        return runTransaction(function () use ($id, $dto) {
            $concours = $this->getById($id);
            $updateData = $dto->toArray();

            if (!empty($updateData)) {
                $wasUpdated = $this->updateIfDirty($concours, $updateData);

                if ($wasUpdated) {
                    $this->logger->logActivity('update', 'concours', $id, [
                        'changes' => $this->getDirtyFields($concours)
                    ]);
                }
            }

            $this->invalidateCacheAfterModification($id);

            return $concours->fresh(['ecole', 'specConcours']);
        }, 'ConcoursService::update');
    }

    /**
     * Supprimer un concours
     */
    public function delete(string $id): void
    {
        runTransaction(function () use ($id) {
            $concours = $this->getById($id);

            $this->logger->logActivity('delete', 'concours', $id, [
                'libelle' => $concours->libelle_concours
            ]);

            $concours->delete();

            $this->invalidateCacheAfterModification($id);
        }, 'ConcoursService::delete');
    }

    /**
     * Activer un concours
     */
    public function activate(string $id, bool $activate = true): Concours
    {
        return runTransaction(function () use ($id, $activate) {
            $concours = $this->getById($id);
            $concours->update(['est_actif' => $activate]);

            $this->logger->logActivity(
                $activate ? 'activate' : 'deactivate',
                'concours',
                $id,
                ['est_actif' => $activate]
            );

            $this->invalidateCacheAfterModification($id);

            return $concours->fresh();
        }, "'ConcoursService::activate'");
    }

    /**
     * Désactiver un concours
     */
    public function deactivate(string $id): Concours
    {
        return $this->activate($id, false);
    }

    /**
     * Obtenir les statistiques d'un concours
     */
    public function getStats(string $id): array
    {
        return $this->rememberStatic("concours_stats_{$id}", function () use ($id) {
            $concours = $this->getById($id, true);

            return [
                'nombre_candidatures' => $concours->candidatures()->count(),
                'nombre_candidatures_validees' => $concours->candidatures()
                    ->where('statut_candidature', 'validee')
                    ->count(),
                'nombre_places_disponibles' => $concours->nbre_max_places,
                'nombre_centres' => $concours->centers()->count(),
                'nombre_filieres' => $concours->filieres()->count(),
            ];
        });
    }

    /**
     * Attacher une session à un concours
     */
    public function attachSession(string $concoursId, string $sessionId): void
    {
        runTransaction(function () use ($concoursId, $sessionId) {
            $concours = $this->getById($concoursId);
            Session::findOrFail($sessionId);

            $concours->sessions()->attach($sessionId);

            $this->logger->logActivity('attach_session', 'concours', $concoursId, [
                'session_id' => $sessionId
            ]);

            $this->invalidateCacheAfterModification($concoursId);
        });
    }

    /**
     * Détacher une session d'un concours
     */
    public function detachSession(string $concoursId, string $sessionId): void
    {
        runTransaction(function () use ($concoursId, $sessionId) {
            $concours = $this->getById($concoursId);
            $concours->sessions()->detach($sessionId);

            $this->logger->logActivity('detach_session', 'concours', $concoursId, [
                'session_id' => $sessionId
            ]);

            $this->invalidateCacheAfterModification($concoursId);
        });
    }

    /**
     * Changer l'état d'un concours dans une session
     */
    public function changeSessionState(string $concoursId, string $sessionId, string $state): void
    {
        runTransaction(function () use ($concoursId, $sessionId, $state) {
            $concours = $this->getById($concoursId);
            $concours->sessions()->updateExistingPivot($sessionId, ['etat' => $state]);

            $this->logger->logActivity('change_session_state', 'concours', $concoursId, [
                'session_id' => $sessionId,
                'etat' => $state
            ]);

            // Invalider le cache
            $this->invalidateCacheAfterModification($concoursId);
        });
    }

    /**
     * Valider la cohérence des places (somme filières vs total concours)
     */
    public function validatePlacesCoherence(string $concoursId, ?string $sessionId = null): void
    {
        $concours = $this->getById($concoursId);

        if (!$sessionId) {
            $session = $concours->sessions()->first();
            if (!$session) {
                throw new ConcoursException('Aucune session trouvée pour ce concours');
            }
            $sessionId = $session->id;
        }

        $totalPlacesFilieres = DB::table('concours_filiere')
            ->where('concours_id', $concoursId)
            ->where('session_id', $sessionId)
            ->sum('nombre_places');

        if ($totalPlacesFilieres > $concours->nbre_max_places) {
            throw new ConcoursException(
                "Incohérence: Total places filières ({$totalPlacesFilieres}) > Places concours ({$concours->nbre_max_places})"
            );
        }
    }

    /**
     * Obtenir un rapport détaillé sur la répartition des places
     */
    public function getPlacesReport(string $concoursId, ?string $sessionId = null): array
    {
        $concours = $this->getById($concoursId);

        if (!$sessionId) {
            $session = $concours->sessions()->first();
            if (!$session) {
                throw new ConcoursException('Aucune session trouvée pour ce concours');
            }
            $sessionId = $session->id;
        }

        return $this->rememberStatic("places_report_{$concoursId}_{$sessionId}", function () use ($concoursId, $sessionId, $concours) {
            $filieres = DB::table('concours_filiere')
                ->join('filieres', 'concours_filiere.filiere_id', '=', 'filieres.id')
                ->where('concours_filiere.concours_id', $concoursId)
                ->where('concours_filiere.session_id', $sessionId)
                ->select('filieres.libelle_filiere', 'concours_filiere.nombre_places')
                ->get();

            $totalPlacesFilieres = $filieres->sum('nombre_places');
            $placesRestantes = $concours->nbre_max_places - $totalPlacesFilieres;

            return [
                'concours' => [
                    'id' => $concours->id,
                    'libelle' => $concours->libelle_concours,
                    'nombre_places_total' => $concours->nbre_max_places,
                ],
                'filieres' => $filieres,
                'total_places_attribuees' => $totalPlacesFilieres,
                'places_restantes' => $placesRestantes,
                'coherent' => $placesRestantes >= 0,
            ];
        });
    }

    /**
     * Retourne les tags de cache pour le modèle
     */
    protected function getModelTags(): array
    {
        return ['concours', 'lists'];
    }
}
