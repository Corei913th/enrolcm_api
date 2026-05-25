<?php

namespace App\Services\Domain\Referentiel;

use App\DTOs\Niveaux\CreateNiveauDTO;
use App\Exceptions\Business\NiveauException;
use App\Models\Niveau;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use App\Traits\HasActivityLogger;
use App\Traits\HasAdvancedSearch;
use App\Traits\HasOptimizedUpdate;
use App\Traits\HasServiceFinders;
use App\Traits\HasSmartCache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NiveauService
{
    use HasActivityLogger, HasAdvancedSearch, HasOptimizedUpdate, HasServiceFinders, HasSmartCache;

    protected string $exceptionClass = NiveauException::class;

    protected string $codeColumn = 'code_niveau';

    public function __construct(ActivityLoggerService $logger)
    {
        $this->logger = $logger;
    }

    protected function getModelTags(): array
    {
        return ['niveaux', 'lists'];
    }

    /**
     * Récupérer tous les niveaux avec filtres optimisés
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        try {
            $query = Niveau::query()
                ->select([
                    'id',
                    'filiere_id',
                    'code_niveau',
                    'libelle_niveau',
                    'desc_niveau',
                    'ordre',
                    'est_actif',
                    'created_at',
                    'updated_at',
                ])
                ->with(['filiere:id,libelle_filiere,code_filiere']);

            // Recherche optimisée
            if (! empty($filters['search'])) {
                $this->applySearch(
                    $query,
                    $filters['search'],
                    [
                        'libelle_niveau' => 'words',
                        'code_niveau' => 'start',
                        'desc_niveau' => 'partial',
                    ],
                    [
                        'filiere.libelle_filiere' => 'partial',
                    ]
                );
            }

            // Filtres simples
            $simpleFilters = [];
            if (isset($filters['est_actif'])) {
                $simpleFilters['est_actif'] = $filters['est_actif'];
            }
            if (isset($filters['filiere_id'])) {
                $simpleFilters['filiere_id'] = $filters['filiere_id'];
            }
            $this->applyFilters($query, $simpleFilters);

            // Tri
            $sortBy = $filters['sort_by'] ?? 'ordre';
            $sortOrder = $filters['sort_order'] ?? 'asc';
            $this->applySort(
                $query,
                $sortBy,
                $sortOrder,
                'ordre',
                ['ordre', 'libelle_niveau', 'code_niveau', 'created_at']
            );

            // Tri secondaire par libellé si tri par ordre
            if ($sortBy === 'ordre') {
                $query->orderBy('libelle_niveau', 'asc');
            }

            $perPage = $filters['per_page'] ?? 15;

            return $query->paginate($perPage);
        } catch (\Exception $e) {
            logServiceError('Erreur lors de la récupération des niveaux', $e, ['filters' => $filters]);
            throw new NiveauException('Impossible de récupérer la liste des niveaux');
        }
    }

    /**
     * Récupérer un niveau par son ID avec relations
     * Surcharge pour charger les relations par défaut
     */
    public function getById(string $id, array $relations = []): Niveau
    {
        $defaultRelations = ['filiere', 'matieres'];
        $allRelations = array_unique(array_merge($defaultRelations, $relations));

        return $this->getBy('id', $id, $allRelations);
    }

    /**
     * Récupérer un niveau par son code avec relations
     * Surcharge pour charger les relations par défaut
     */
    public function getByCode(string $code, array $relations = []): Niveau
    {
        $defaultRelations = ['filiere', 'matieres'];
        $allRelations = array_unique(array_merge($defaultRelations, $relations));

        return $this->getBy($this->codeColumn, $code, $allRelations);
    }

    /**
     * Créer un nouveau niveau
     */
    public function create(CreateNiveauDTO $data): Niveau
    {
        try {
            return runTransaction(function () use ($data) {
                if (codeExists(Niveau::class, 'code_niveau', $data->code_niveau)) {
                    throw new NiveauException('Ce code niveau existe déjà', 422);
                }

                $niveau = Niveau::create($data->toArray());
                $this->logCreate('niveau', $niveau->id, ['code' => $niveau->code_niveau]);

                return $niveau->load(['filiere', 'matieres']);
            }, 'NiveauService::create');
        } catch (NiveauException $e) {
            throw $e;
        } catch (\Exception $e) {
            logServiceError('Erreur lors de la création du niveau', $e, ['data' => $data->toArray()]);
            throw new NiveauException('Impossible de créer le niveau');
        }
    }

    /**
     * Mettre à jour un niveau
     */
    public function update(string $id, CreateNiveauDTO $data): Niveau
    {
        try {
            return runTransaction(function () use ($id, $data) {
                $niveau = $this->getById($id);

                if ($data->code_niveau !== $niveau->code_niveau) {
                    if (codeExists(Niveau::class, 'code_niveau', $data->code_niveau, $id)) {
                        throw new NiveauException('Ce code niveau existe déjà', 422);
                    }
                }

                $this->updateWithCache($niveau, $data->toArray());
                $this->logUpdate('niveau', $id);

                return $niveau->fresh(['filiere', 'matieres']);
            }, 'NiveauService::update');
        } catch (NiveauException $e) {
            throw $e;
        } catch (\Exception $e) {
            logServiceError('Erreur lors de la mise à jour du niveau', $e, ['id' => $id, 'data' => $data->toArray()]);
            throw new NiveauException('Impossible de mettre à jour le niveau');
        }
    }

    /**
     * Supprimer un niveau
     */
    public function delete(string $id): bool
    {
        try {
            return runTransaction(function () use ($id) {
                $niveau = $this->getById($id);

                $deleted = $niveau->delete();
                $this->logDelete('niveau', $id);

                return $deleted;
            }, 'NiveauService::delete');
        } catch (NiveauException $e) {
            throw $e;
        } catch (\Exception $e) {
            logServiceError('Erreur lors de la suppression du niveau', $e, ['id' => $id]);
            throw new NiveauException('Impossible de supprimer le niveau');
        }
    }

    /**
     * Activer/Désactiver un niveau
     */
    public function toggleStatus(string $id): Niveau
    {
        try {
            return DB::transaction(function () use ($id) {
                $niveau = $this->getById($id);

                $niveau->update([
                    'est_actif' => ! $niveau->est_actif,
                ]);

                Log::info('Statut du niveau modifié', [
                    'niveau_id' => $niveau->id,
                    'nouveau_statut' => $niveau->est_actif,
                ]);

                return $niveau->fresh(['filiere', 'matieres']);
            });
        } catch (NiveauException $e) {
            throw $e;
        } catch (\Exception $e) {
            logServiceError('Erreur lors du changement de statut du niveau', $e, ['id' => $id]);
            throw new NiveauException('Impossible de modifier le statut du niveau');
        }
    }

    /**
     * Récupérer les niveaux actifs
     */
    public function getActive(): Collection
    {
        try {
            return Niveau::where('est_actif', true)
                ->orderBy('ordre')
                ->orderBy('libelle_niveau')
                ->get();
        } catch (\Exception $e) {
            logServiceError('Erreur lors de la récupération des niveaux actifs', $e);
            throw new NiveauException('Impossible de récupérer les niveaux actifs');
        }
    }
}
