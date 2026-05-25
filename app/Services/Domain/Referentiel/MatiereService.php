<?php

namespace App\Services\Domain\Referentiel;

use App\DTOs\Matieres\CreateMatiereDTO;
use App\Exceptions\Business\MatiereException;
use App\Models\Matiere;
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

class MatiereService
{
    use HasActivityLogger, HasAdvancedSearch, HasOptimizedUpdate, HasServiceFinders, HasSmartCache;

    protected string $exceptionClass = MatiereException::class;

    protected string $codeColumn = 'code_matiere';

    public function __construct(ActivityLoggerService $logger)
    {
        $this->logger = $logger;
    }

    protected function getModelTags(): array
    {
        return ['matieres', 'lists'];
    }

    /**
     * Récupérer toutes les matières avec filtres optimisés
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        try {
            $query = Matiere::query()
                ->select([
                    'id',
                    'code_matiere',
                    'libelle_matiere',
                    'est_actif',
                    'created_at',
                    'updated_at',
                ]);

            // Recherche optimisée
            if (! empty($filters['search'])) {
                $this->applySearch(
                    $query,
                    $filters['search'],
                    [
                        'libelle_matiere' => 'words',
                        'code_matiere' => 'start',
                    ]
                );
            }

            // Filtres simples
            $simpleFilters = [];
            if (isset($filters['est_actif'])) {
                $simpleFilters['est_actif'] = $filters['est_actif'];
            }
            $this->applyFilters($query, $simpleFilters);

            // Tri
            $sortBy = $filters['sort_by'] ?? 'libelle_matiere';
            $sortOrder = $filters['sort_order'] ?? 'asc';
            $this->applySort(
                $query,
                $sortBy,
                $sortOrder,
                'libelle_matiere',
                ['libelle_matiere', 'code_matiere', 'created_at']
            );

            $perPage = $filters['per_page'] ?? 15;

            return $query->paginate($perPage);
        } catch (\Exception $e) {
            logServiceError('Erreur lors de la récupération des matières', $e, ['filters' => $filters]);
            throw new MatiereException('Impossible de récupérer la liste des matières');
        }
    }

    /**
     * Récupérer une matière par son ID avec relations
     * Surcharge pour charger les relations par défaut
     */
    public function getById(string $id, array $relations = []): Matiere
    {
        $defaultRelations = ['niveaux'];
        $allRelations = array_unique(array_merge($defaultRelations, $relations));

        return $this->getBy('id', $id, $allRelations);
    }

    /**
     * Récupérer une matière par son code avec relations
     * Surcharge pour charger les relations par défaut
     */
    public function getByCode(string $code, array $relations = []): Matiere
    {
        $defaultRelations = ['niveaux'];
        $allRelations = array_unique(array_merge($defaultRelations, $relations));

        return $this->getBy($this->codeColumn, $code, $allRelations);
    }

    /**
     * Créer une nouvelle matière
     */
    public function create(CreateMatiereDTO $data): Matiere
    {
        try {
            return runTransaction(function () use ($data) {
                if (codeExists(Matiere::class, 'code_matiere', $data->code_matiere)) {
                    throw new MatiereException('Ce code matière existe déjà', 422);
                }

                $matiere = Matiere::create($data->toArray());
                $this->logCreate('matiere', $matiere->id, ['code' => $matiere->code_matiere]);

                return $matiere->load('niveaux');
            }, 'MatiereService::create');
        } catch (MatiereException $e) {
            throw $e;
        } catch (\Exception $e) {
            logServiceError('Erreur lors de la création de la matière', $e, ['data' => $data->toArray()]);
            throw new MatiereException('Impossible de créer la matière');
        }
    }

    /**
     * Mettre à jour une matière
     */
    public function update(string $id, CreateMatiereDTO $data): Matiere
    {
        try {
            return runTransaction(function () use ($id, $data) {
                $matiere = $this->getById($id);

                if ($data->code_matiere !== $matiere->code_matiere) {
                    if (codeExists(Matiere::class, 'code_matiere', $data->code_matiere, $id)) {
                        throw new MatiereException('Ce code matière existe déjà', 422);
                    }
                }

                $this->updateWithCache($matiere, $data->toArray());
                $this->logUpdate('matiere', $id);

                return $matiere->fresh('niveaux');
            }, 'MatiereService::update');
        } catch (MatiereException $e) {
            throw $e;
        } catch (\Exception $e) {
            logServiceError('Erreur lors de la mise à jour de la matière', $e, ['id' => $id, 'data' => $data->toArray()]);
            throw new MatiereException('Impossible de mettre à jour la matière');
        }
    }

    /**
     * Supprimer une matière
     */
    public function delete(string $id): bool
    {
        try {
            return runTransaction(function () use ($id) {
                $matiere = $this->getById($id);

                $deleted = $matiere->delete();
                $this->logDelete('matiere', $id);

                return $deleted;
            }, 'MatiereService::delete');
        } catch (MatiereException $e) {
            throw $e;
        } catch (\Exception $e) {
            logServiceError('Erreur lors de la suppression de la matière', $e, ['id' => $id]);
            throw new MatiereException('Impossible de supprimer la matière');
        }
    }

    /**
     * Activer/Désactiver une matière
     */
    public function toggleStatus(string $id): Matiere
    {
        try {
            return DB::transaction(function () use ($id) {
                $matiere = $this->getById($id);

                $matiere->update([
                    'est_actif' => ! $matiere->est_actif,
                ]);

                Log::info('Statut de la matière modifié', [
                    'matiere_id' => $matiere->id,
                    'nouveau_statut' => $matiere->est_actif,
                ]);

                return $matiere->fresh('niveaux');
            });
        } catch (MatiereException $e) {
            throw $e;
        } catch (\Exception $e) {
            logServiceError('Erreur lors du changement de statut de la matière', $e, ['id' => $id]);
            throw new MatiereException('Impossible de modifier le statut de la matière');
        }
    }

    /**
     * Récupérer les matières actives
     */
    public function getActive(): Collection
    {
        try {
            return Matiere::where('est_actif', true)
                ->orderBy('libelle_matiere')
                ->get();
        } catch (\Exception $e) {
            logServiceError('Erreur lors de la récupération des matières actives', $e);
            throw new MatiereException('Impossible de récupérer les matières actives');
        }
    }
}
