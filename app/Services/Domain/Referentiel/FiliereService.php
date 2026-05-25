<?php

namespace App\Services\Domain\Referentiel;

use App\DTOs\Filieres\CreateFiliereDTO;
use App\Exceptions\Business\FiliereException;
use App\Models\Filiere;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use App\Traits\HasActivityLogger;
use App\Traits\HasAdvancedSearch;
use App\Traits\HasOptimizedUpdate;
use App\Traits\HasServiceFinders;
use App\Traits\HasSmartCache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FiliereService
{
    use HasActivityLogger, HasAdvancedSearch, HasOptimizedUpdate, HasServiceFinders, HasSmartCache;

    protected string $exceptionClass = FiliereException::class;

    protected string $codeColumn = 'code_filiere';

    public function __construct(ActivityLoggerService $logger)
    {
        $this->logger = $logger;
    }

    protected function getModelTags(): array
    {
        return ['filieres', 'lists'];
    }

    /**
     * Récupérer toutes les filières avec filtres optimisés
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        try {
            $query = Filiere::query()
                ->select([
                    'id',
                    'departement_id',
                    'code_filiere',
                    'libelle_filiere',
                    'desc_filiere',
                    'est_actif',
                    'created_at',
                    'updated_at',
                ])
                ->with([
                    'departement:id,libelle_departement,code_departement,ecole_id',
                    'departement.ecole:id,libelle_ecole,code_ecole',
                ]);

            // Recherche optimisée multi-colonnes
            if (! empty($filters['search'])) {
                $this->applySearch(
                    $query,
                    $filters['search'],
                    [
                        'libelle_filiere' => 'words',
                        'code_filiere' => 'start',
                        'desc_filiere' => 'partial',
                    ],
                    [
                        'departement.libelle_departement' => 'partial',
                        'departement.ecole.libelle_ecole' => 'partial',
                    ]
                );
            }

            // Filtres simples
            $simpleFilters = [];
            if (isset($filters['est_actif'])) {
                $simpleFilters['est_actif'] = $filters['est_actif'];
            }
            if (isset($filters['departement_id'])) {
                $simpleFilters['departement_id'] = $filters['departement_id'];
            }
            $this->applyFilters($query, $simpleFilters);

            // Tri
            $sortBy = $filters['sort_by'] ?? 'libelle_filiere';
            $sortOrder = $filters['sort_order'] ?? 'asc';
            $this->applySort(
                $query,
                $sortBy,
                $sortOrder,
                'libelle_filiere',
                ['libelle_filiere', 'code_filiere', 'created_at']
            );

            $perPage = $filters['per_page'] ?? 15;

            return $query->paginate($perPage);
        } catch (\Exception $e) {
            logServiceError('Erreur lors de la récupération des filières', $e, ['filters' => $filters]);
            throw new FiliereException('Impossible de récupérer la liste des filières');
        }
    }

    /**
     * Récupérer une filière par son ID avec relations
     * Surcharge pour charger les relations par défaut
     */
    public function getById(string $id, array $relations = []): Filiere
    {
        $defaultRelations = ['departement', 'niveaux'];
        $allRelations = array_unique(array_merge($defaultRelations, $relations));

        return $this->getBy('id', $id, $allRelations);
    }

    /**
     * Récupérer une filière par son code avec relations
     * Surcharge pour charger les relations par défaut
     */
    public function getByCode(string $code, array $relations = []): Filiere
    {
        $defaultRelations = ['departement', 'niveaux'];
        $allRelations = array_unique(array_merge($defaultRelations, $relations));

        return $this->getBy($this->codeColumn, $code, $allRelations);
    }

    /**
     * Créer une nouvelle filière
     */
    public function create(CreateFiliereDTO $data): Filiere
    {
        try {
            return runTransaction(function () use ($data) {

                if (
                    Filiere::where('departement_id', $data->departement_id)
                        ->where('code_filiere', $data->code_filiere)
                        ->exists()
                ) {
                    throw new FiliereException(
                        'Cette filière existe déjà dans ce département',
                        422
                    );
                }

                $filiere = Filiere::create($data->toArray());
                $this->logCreate('filiere', $filiere->id, ['code' => $filiere->code_filiere]);

                return $filiere->load(['departement', 'niveaux']);
            }, 'FiliereService::create');
        } catch (QueryException $e) {
            logServiceError('Erreur lors de la création de la filière', $e, ['data' => $data->toArray()]);
            if ($e->getCode() === '23000') {
                throw new FiliereException(
                    'Cette filière existe déjà dans ce département',
                    422
                );
            }
            throw $e;
        }
    }

    /**
     * Mettre à jour une filière
     */
    public function update(string $id, CreateFiliereDTO $data): Filiere
    {
        try {
            return runTransaction(function () use ($id, $data) {
                $filiere = $this->getById($id);

                $departementId = $data->departement_id ?? $filiere->departement_id;
                $code = $data->code_filiere ?? $filiere->code_filiere;

                if (
                    Filiere::where('departement_id', $departementId)
                        ->where('code_filiere', $code)
                        ->where('id', '!=', $id)
                        ->exists()
                ) {
                    throw new FiliereException(
                        'Cette filière existe déjà dans ce département',
                        422
                    );
                }

                $this->updateWithCache($filiere, $data->toArray());
                $this->logUpdate('filiere', $id);

                return $filiere->fresh(['departement', 'niveaux']);
            }, 'FiliereService::update');
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                throw new FiliereException(
                    'Cette filière existe déjà dans ce département',
                    422
                );
            }
            throw $e;
        }
    }

    /**
     * Supprimer une filière
     */
    public function delete(string $id): bool
    {
        try {
            return runTransaction(function () use ($id) {
                $filiere = $this->getById($id);

                if (hasDependencies($filiere, 'niveaux')) {
                    throw new FiliereException('Impossible de supprimer : des niveaux sont associés à cette filière', 422);
                }

                $deleted = $filiere->delete();
                $this->logDelete('filiere', $id);

                return $deleted;
            }, 'FiliereService::delete');
        } catch (FiliereException $e) {
            throw $e;
        } catch (\Exception $e) {
            logServiceError('Erreur lors de la suppression de la filière', $e, ['id' => $id]);
            throw new FiliereException('Impossible de supprimer la filière');
        }
    }

    /**
     * Activer/Désactiver une filière
     */
    public function toggleStatus(string $id): Filiere
    {
        try {
            return DB::transaction(function () use ($id) {
                $filiere = $this->getById($id);

                $filiere->update([
                    'est_actif' => ! $filiere->est_actif,
                ]);

                Log::info('Statut de la filière modifié', [
                    'filiere_id' => $filiere->id,
                    'nouveau_statut' => $filiere->est_actif,
                ]);

                return $filiere->fresh(['departement', 'niveaux']);
            });
        } catch (FiliereException $e) {
            throw $e;
        } catch (\Exception $e) {
            logServiceError('Erreur lors du changement de statut de la filière', $e, ['id' => $id]);
            throw new FiliereException('Impossible de modifier le statut de la filière');
        }
    }

    /**
     * Récupérer les filières actives
     */
    public function getActive(): Collection
    {
        try {
            return Filiere::where('est_actif', true)
                ->orderBy('libelle_filiere')
                ->get();
        } catch (\Exception $e) {
            logServiceError('Erreur lors de la récupération des filières actives', $e);
            throw new FiliereException('Impossible de récupérer les filières actives');
        }
    }
}
