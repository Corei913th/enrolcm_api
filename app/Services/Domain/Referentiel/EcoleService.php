<?php

namespace App\Services\Domain\Referentiel;

use App\DTOs\Ecoles\CreateEcoleDTO;
use App\DTOs\Ecoles\UpdateEcoleDTO;
use App\Exceptions\Business\EcoleException;
use App\Models\Ecole;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use App\Services\Infrastructure\Storage\EcoleFileStorageService as EcoleFileService;
use App\Traits\HasAdvancedSearch;
use App\Traits\HasOptimizedUpdate;
use App\Traits\HasServiceFinders;
use App\Traits\HasSmartCache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EcoleService
{
    use HasAdvancedSearch, HasOptimizedUpdate, HasServiceFinders, HasSmartCache;

    protected string $exceptionClass = EcoleException::class;

    protected string $codeColumn = 'code_ecole';

    protected function getModelTags(): array
    {
        return ['ecoles', 'lists'];
    }

    public function __construct(
        private readonly EcoleFileService $fileService,
        private readonly ActivityLoggerService $logger
    ) {}

    /**
     * Get paginated list of schools with optional filters (optimized)
     *
     * @param  array  $filters  Available filters: 'est_actif', 'search', 'region', etc.
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        try {
            $query = Ecole::query()
                ->select([
                    '*',
                ]);

            // Recherche optimisée multi-colonnes
            if (! empty($filters['search'])) {
                $this->applySearch(
                    $query,
                    $filters['search'],
                    [
                        'libelle_ecole' => 'words',
                        'code_ecole' => 'start',
                        'localisation' => 'partial',
                        'region' => 'partial',
                    ]
                );
            }

            // Filtres simples
            $simpleFilters = [];
            if (isset($filters['est_actif'])) {
                $simpleFilters['est_actif'] = $filters['est_actif'];
            }
            if (isset($filters['region'])) {
                $simpleFilters['region'] = $filters['region'];
            }
            $this->applyFilters($query, $simpleFilters);

            // Tri
            $sortBy = $filters['sort_by'] ?? 'libelle_ecole';
            $sortOrder = $filters['sort_order'] ?? 'asc';
            $this->applySort(
                $query,
                $sortBy,
                $sortOrder,
                'libelle_ecole',
                ['libelle_ecole', 'code_ecole', 'region', 'created_at']
            );

            $perPage = $filters['per_page'] ?? 15;

            return $query->paginate($perPage);
        } catch (\Exception $e) {
            throw new EcoleException('Impossible de récupérer la liste des écoles');
        }
    }

    /**
     * Get school by ID with departments relationship loaded
     * Surcharge pour ajouter les relations par défaut
     *
     * @param  string  $id  School UUID
     * @param  array  $relations  Relations supplémentaires à charger
     *
     * @throws EcoleException If school not found
     */
    public function getById(string $id, bool $withrelations = true, array $relations = []): Ecole
    {
        // Toujours charger les départements + relations supplémentaires
        $defaultRelations = ['departements'];
        $allRelations = array_unique(array_merge($defaultRelations, $relations));

        return $this->getBy('id', $id, $withrelations ? $allRelations : []);
    }

    /**
     * Get a school by its code with departments relationship loaded
     * Surcharge pour ajouter les relations par défaut
     *
     * @param  string  $code  School code
     * @param  array  $relations  Relations supplémentaires à charger
     * @return Ecole School model
     *
     * @throws EcoleException If school not found
     */
    public function getByCode(string $code, array $relations = []): Ecole
    {
        // Toujours charger les départements + relations supplémentaires
        $defaultRelations = ['departements'];
        $allRelations = array_unique(array_merge($defaultRelations, $relations));

        return $this->getBy($this->codeColumn, $code, $allRelations);
    }

    /**
     * Create a new school
     *
     * @param  CreateEcoleDTO  $data  Validated school data
     * @return Ecole Created school with relationships loaded
     *
     * @throws EcoleException If validation fails or creation error
     */
    public function create(CreateEcoleDTO $data): Ecole
    {
        try {
            return DB::transaction(function () use ($data) {
                if (Ecole::where('code_ecole', $data->code_ecole)->exists()) {
                    throw new EcoleException('Code école already exists', 422);
                }

                $ecole = Ecole::create($data->toArray());

                return $ecole->load('departements');
            });
        } catch (EcoleException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new EcoleException('Impossible de créer l\'école');
        }
    }

    /**
     * Create a school with file uploads in a single transaction
     *
     * @param  CreateEcoleDTO  $data  Validated school data
     * @param  array  $files  Optional files to upload: 'logo', 'embleme', 'header_frame'
     * @return Ecole Created school with uploaded files
     *
     * @throws EcoleException If creation or file upload fails
     */
    public function createWithFiles(CreateEcoleDTO $data, array $files = []): Ecole
    {
        try {
            return DB::transaction(function () use ($data, $files) {
                $ecole = $this->create($data);

                foreach (['logo', 'embleme', 'header_frame'] as $fileType) {
                    if (isset($files[$fileType])) {
                        $fileInfo = $this->fileService->uploadFile($ecole, $files[$fileType], $fileType);
                        $ecole = $this->updateFileInfo($ecole->id, $fileType, $fileInfo);
                    }
                }

                return $ecole->fresh('departements');
            });
        } catch (EcoleException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new EcoleException('Cannot create school with files');
        }
    }

    /**
     * Update an existing school
     *
     * @param  string  $id  School UUID
     * @param  UpdateEcoleDTO  $data  Updated school data
     * @return Ecole Updated school with relationships loaded
     *
     * @throws EcoleException If school not found or update fails
     */
    public function update(string $id, UpdateEcoleDTO $data): Ecole
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $ecole = $this->getById($id);

                $updateData = array_filter($data->toArray(), fn ($value) => $value !== null);

                if (isset($updateData['code_ecole']) && $updateData['code_ecole'] !== $ecole->code_ecole) {
                    if (Ecole::where('code_ecole', $updateData['code_ecole'])->where('id', '!=', $id)->exists()) {
                        throw new EcoleException('Code école already exists', 422);
                    }
                }

                $this->updateIfDirty($ecole, $updateData);

                return $ecole->fresh('departements');
            });
        } catch (EcoleException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new EcoleException('Impossible de mettre à jour l\'école: ' . $e->getMessage());
        }
    }

    /**
     * Update a school with file uploads in a single transaction
     *
     * @param  string  $id  School UUID
     * @param  UpdateEcoleDTO  $data  Updated school data
     * @param  array  $files  Optional files to upload: 'logo', 'embleme', 'header_frame'
     * @return Ecole Updated school with uploaded files
     *
     * @throws EcoleException If update or file upload fails
     */
    public function updateWithFiles(string $id, UpdateEcoleDTO $data, array $files = []): Ecole
    {
        try {
            $this->logger->logOperation('EcoleService::updateWithFiles', 'started', [
                'ecole_id' => $id,
                'files_count' => count($files),
                'file_types' => array_keys($files),
            ]);

            return DB::transaction(function () use ($id, $data, $files) {
                $ecole = $this->update($id, $data);

                $this->logger->logOperation('EcoleService::updateWithFiles', 'ecole_updated', [
                    'ecole_id' => $ecole->id,
                ]);

                foreach (['logo', 'embleme', 'header_frame'] as $fileType) {
                    if (isset($files[$fileType])) {
                        $this->logger->logOperation('EcoleService::uploadFile', 'uploading', [
                            'ecole_id' => $ecole->id,
                            'file_type' => $fileType,
                        ]);

                        $fileInfo = $this->fileService->uploadFile($ecole, $files[$fileType], $fileType);

                        $this->logger->logOperation('EcoleService::updateFileInfo', 'updating_db', [
                            'ecole_id' => $ecole->id,
                            'file_type' => $fileType,
                            'path' => $fileInfo['path'],
                            'url' => $fileInfo['url'],
                        ]);

                        $ecole = $this->updateFileInfo($ecole->id, $fileType, $fileInfo);
                    }
                }

                $this->logger->logOperation('EcoleService::updateWithFiles', 'completed', [
                    'ecole_id' => $ecole->id,
                ]);

                return $ecole->fresh('departements');
            });
        } catch (EcoleException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->logger->logError($e, 'EcoleService::updateWithFiles - ecole_id: ' . $id);
            throw new EcoleException('Impossible de mettre à jour l\'école avec les fichiers: ' . $e->getMessage());
        }
    }

    /**
     * Delete a school
     *
     * @param  string  $id  School UUID
     * @return bool True if deletion successful
     *
     * @throws EcoleException If school has departments or deletion fails
     */
    public function delete(string $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $ecole = $this->getById($id);

                if ($ecole->departements()->exists()) {
                    throw new EcoleException('Cannot delete school with departments', 422);
                }

                return $ecole->delete();
            });
        } catch (EcoleException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new EcoleException('Impossible de supprimer l\'école');
        }
    }

    /**
     * Delete a school and all its associated files
     *
     * @param  string  $id  School UUID
     * @return bool True if deletion successful
     *
     * @throws EcoleException If deletion fails
     */
    public function deleteWithFiles(string $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $ecole = $this->getById($id);

                $this->fileService->deleteAllFiles($ecole);

                return $this->delete($id);
            });
        } catch (EcoleException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new EcoleException('Cannot delete school with files');
        }
    }

    /**
     * Toggle school active status
     *
     * @param  string  $id  School UUID
     * @return Ecole Updated school with toggled status
     *
     * @throws EcoleException If school not found or update fails
     */
    public function toggleStatus(string $id): Ecole
    {
        try {
            return DB::transaction(function () use ($id) {
                $ecole = $this->getById($id);

                $ecole->update([
                    'est_actif' => ! $ecole->est_actif,
                ]);

                return $ecole->fresh('departements');
            });
        } catch (EcoleException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new EcoleException('Impossible de modifier le statut de l\'école');
        }
    }

    /**
     * Update file information for a school
     *
     * @param  string  $id  School UUID
     * @param  string  $fileType  File type: 'logo', 'embleme', 'header_frame'
     * @param  array  $fileInfo  File information array with 'path', 'original_name', and 'url'
     * @return Ecole Updated school
     *
     * @throws EcoleException If update fails
     */
    public function updateFileInfo(string $id, string $fileType, array $fileInfo): Ecole
    {
        try {
            return DB::transaction(function () use ($id, $fileType, $fileInfo) {
                $ecole = $this->getById($id);

                $pathField = $fileType . '_path';
                $nameField = $fileType . '_original_name';
                $urlField = $fileType . '_url';

                $updateData = [
                    $pathField => $fileInfo['path'],
                    $nameField => $fileInfo['original_name'],
                ];

                // Ajouter l'URL si elle est fournie
                if (isset($fileInfo['url'])) {
                    $updateData[$urlField] = $fileInfo['url'];
                }

                $ecole->update($updateData);

                return $ecole->fresh();
            });
        } catch (EcoleException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new EcoleException('Cannot update file info');
        }
    }

    /**
     * Clear file information for a school (set to null)
     *
     * @param  string  $id  School UUID
     * @param  string  $fileType  File type: 'logo', 'embleme', 'header_frame'
     * @return Ecole Updated school with cleared file info
     *
     * @throws EcoleException If update fails
     */
    public function clearFileInfo(string $id, string $fileType): Ecole
    {
        try {
            return DB::transaction(function () use ($id, $fileType) {
                $ecole = $this->getById($id);

                $pathField = $fileType . '_path';
                $nameField = $fileType . '_original_name';

                $ecole->update([
                    $pathField => null,
                    $nameField => null,
                ]);

                return $ecole->fresh();
            });
        } catch (EcoleException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new EcoleException('Cannot clear file info');
        }
    }

    /**
     * Get all active schools ordered by name
     *
     * @return Collection Collection of active schools
     *
     * @throws EcoleException If retrieval fails
     */
    public function getActive(): Collection
    {
        try {
            return Ecole::where('est_actif', true)
                ->orderBy('libelle_ecole')
                ->get();
        } catch (\Exception $e) {
            throw new EcoleException('Cannot get active schools');
        }
    }

    /**
     * Récupérer les concours d'une école
     *
     * @param  string  $ecoleId  ID de l'école
     * @param  array  $filters  Filtres optionnels (est_actif, session_id, etc.)
     * @return Collection
     */
    public function getConcours(string $ecoleId, array $filters = [])
    {
        $ecole = Ecole::findOrFail($ecoleId);

        $query = $ecole->concours()
            ->with(['sessions', 'specConcours', 'configurationPaiement']);

        // Filtres
        if (isset($filters['est_actif'])) {
            $query->where('est_actif', $filters['est_actif']);
        }

        if (isset($filters['session_id'])) {
            $query->whereHas('sessions', function ($q) use ($filters) {
                $q->where('sessions.id', $filters['session_id']);
            });
        }

        return $query->orderBy('date_limite_depot', 'desc')->get();
    }

    /**
     * Récupérer les statistiques des concours d'une école
     *
     * @param  string  $ecoleId  ID de l'école
     */
    public function getConcoursStats(string $ecoleId): array
    {
        $ecole = Ecole::findOrFail($ecoleId);

        return [
            'total_concours' => $ecole->concours()->count(),
            'concours_actifs' => $ecole->concours()->where('est_actif', true)->count(),
            'concours_en_cours' => $ecole->concours()
                ->where('est_actif', true)
                ->where('date_limite_depot', '>=', now())
                ->count(),
            'total_candidatures' => $ecole->concours()
                ->withCount('candidatures')
                ->get()
                ->sum('candidatures_count'),
        ];
    }
}
