<?php

namespace App\Services\Ecoles;

use App\DTOs\Ecoles\CreateEcoleDTO;
use App\Exceptions\Business\EcoleException;
use App\Models\Ecole;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class EcoleService
{
    public function __construct(
        private readonly EcoleFileService $fileService
    ) {}
    /**
     * Get paginated list of schools with optional filters
     *
     * @param array $filters Available filters: 'est_actif', 'search', etc.
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        try {
            $query = Ecole::query()->with('departements');

            
            if (isset($filters['est_actif'])) {
                $query->where('est_actif', $filters['est_actif']);
            }

            
            if (isset($filters['region'])) {
                $region = preg_replace('/\s+/', '', strtolower($filters['region']));
                $query->whereRaw("REPLACE(LOWER(region), ' ', '') LIKE ?", ["%{$region}%"]);
            }

            // Recherche fulltext-like sur plusieurs colonnes
            if (isset($filters['search'])) {
                $search = preg_replace('/\s+/', '', strtolower($filters['search']));
                $query->where(function ($q) use ($search) {
                    $q->whereRaw("REPLACE(LOWER(libelle_ecole), ' ', '') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("REPLACE(LOWER(code_ecole), ' ', '') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("REPLACE(LOWER(localisation), ' ', '') LIKE ?", ["%{$search}%"]);
                });
            }

            $perPage = $filters['per_page'] ?? 15;

            return $query->orderBy('libelle_ecole')->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des écoles', [
                'error' => $e->getMessage(),
                'filters' => $filters
            ]);
            throw new EcoleException('Impossible de récupérer la liste des écoles');
        }
    }

    /**
     * Get school by ID with departments relationship loaded
     *
     * @param string $id School UUID
     * @return Ecole
     * @throws EcoleException If school not found
     */
    public function getById(string $id): Ecole
    {
        try {
            $ecole = Ecole::with('departements')->findOrFail($id);
            return $ecole;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EcoleException('École non trouvée', 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de l\'école', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            throw new EcoleException('Impossible de récupérer l\'école');
        }
    }

    /**
     * Get a school by its code
     *
     * @param string $code School code
     * @return Ecole School model
     * @throws EcoleException If school not found
     */
    public function getByCode(string $code): Ecole
    {
        try {
            $ecole = Ecole::with('departements')->where('code_ecole', $code)->firstOrFail();
            return $ecole;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EcoleException('École non trouvée avec ce code', 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de l\'école par code', [
                'error' => $e->getMessage(),
                'code' => $code
            ]);
            throw new EcoleException('Impossible de récupérer l\'école');
        }
    }

    /**
     * Create a new school
     *
     * @param CreateEcoleDTO $data Validated school data
     * @return Ecole Created school with relationships loaded
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

                Log::info('School created', [
                    'ecole_id' => $ecole->id,
                    'code_ecole' => $ecole->code_ecole
                ]);

                return $ecole->load('departements');
            });
        } catch (EcoleException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'école', [
                'error' => $e->getMessage(),
                'data' => $data->toArray()
            ]);
            throw new EcoleException('Impossible de créer l\'école');
        }
    }

    /**
     * Create a school with file uploads in a single transaction
     *
     * @param CreateEcoleDTO $data Validated school data
     * @param array $files Optional files to upload: 'logo', 'embleme', 'header_frame'
     * @return Ecole Created school with uploaded files
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
            Log::error('Failed to create school with files', [
                'error' => $e->getMessage(),
                'data' => $data->toArray()
            ]);
            throw new EcoleException('Cannot create school with files');
        }
    }

    /**
     * Update an existing school
     *
     * @param string $id School UUID
     * @param CreateEcoleDTO $data Updated school data
     * @return Ecole Updated school with relationships loaded
     * @throws EcoleException If school not found or update fails
     */
    public function update(string $id, CreateEcoleDTO $data): Ecole
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $ecole = $this->getById($id);

                if ($data->code_ecole !== $ecole->code_ecole) {
                    if (Ecole::where('code_ecole', $data->code_ecole)->where('id', '!=', $id)->exists()) {
                        throw new EcoleException('Code école already exists', 422);
                    }
                }

                $ecole->update($data->toArray());

                Log::info('School updated', [
                    'ecole_id' => $ecole->id,
                    'code_ecole' => $ecole->code_ecole
                ]);

                return $ecole->fresh('departements');
            });
        } catch (EcoleException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de l\'école', [
                'error' => $e->getMessage(),
                'id' => $id,
                'data' => $data->toArray()
            ]);
            throw new EcoleException('Impossible de mettre à jour l\'école');
        }
    }

    /**
     * Update a school with file uploads in a single transaction
     *
     * @param string $id School UUID
     * @param CreateEcoleDTO $data Updated school data
     * @param array $files Optional files to upload: 'logo', 'embleme', 'header_frame'
     * @return Ecole Updated school with uploaded files
     * @throws EcoleException If update or file upload fails
     */
    public function updateWithFiles(string $id, CreateEcoleDTO $data, array $files = []): Ecole
    {
        try {
            return DB::transaction(function () use ($id, $data, $files) {
                $ecole = $this->update($id, $data);

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
            Log::error('Erreur lors de la mise à jour de l\'école avec fichiers', [
                'error' => $e->getMessage(),
                'id' => $id,
                'data' => $data->toArray()
            ]);
            throw new EcoleException('Impossible de mettre à jour l\'école avec les fichiers');
        }
    }

    /**
     * Delete a school
     *
     * @param string $id School UUID
     * @return bool True if deletion successful
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

                $deleted = $ecole->delete();

                Log::info('School deleted', [
                    'ecole_id' => $id
                ]);

                return $deleted;
            });
        } catch (EcoleException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'école', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            throw new EcoleException('Impossible de supprimer l\'école');
        }
    }

    /**
     * Delete a school and all its associated files
     *
     * @param string $id School UUID
     * @return bool True if deletion successful
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
            Log::error('Failed to delete school with files', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            throw new EcoleException('Cannot delete school with files');
        }
    }

    /**
     * Toggle school active status
     *
     * @param string $id School UUID
     * @return Ecole Updated school with toggled status
     * @throws EcoleException If school not found or update fails
     */
    public function toggleStatus(string $id): Ecole
    {
        try {
            return DB::transaction(function () use ($id) {
                $ecole = $this->getById($id);

                $ecole->update([
                    'est_actif' => !$ecole->est_actif
                ]);

                Log::info('School status toggled', [
                    'ecole_id' => $ecole->id,
                    'new_status' => $ecole->est_actif
                ]);

                return $ecole->fresh('departements');
            });
        } catch (EcoleException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors du changement de statut de l\'école', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            throw new EcoleException('Impossible de modifier le statut de l\'école');
        }
    }

    /**
     * Update file information for a school
     *
     * @param string $id School UUID
     * @param string $fileType File type: 'logo', 'embleme', 'header_frame'
     * @param array $fileInfo File information array with 'path' and 'original_name'
     * @return Ecole Updated school
     * @throws EcoleException If update fails
     */
    public function updateFileInfo(string $id, string $fileType, array $fileInfo): Ecole
    {
        try {
            return DB::transaction(function () use ($id, $fileType, $fileInfo) {
                $ecole = $this->getById($id);

                $pathField = $fileType . '_path';
                $nameField = $fileType . '_original_name';

                $ecole->update([
                    $pathField => $fileInfo['path'],
                    $nameField => $fileInfo['original_name'],
                ]);

                Log::info('File info updated', [
                    'ecole_id' => $ecole->id,
                    'file_type' => $fileType,
                    'path' => $fileInfo['path']
                ]);

                return $ecole->fresh();
            });
        } catch (EcoleException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to update file info', [
                'error' => $e->getMessage(),
                'id' => $id,
                'file_type' => $fileType
            ]);
            throw new EcoleException('Cannot update file info');
        }
    }

    /**
     * Clear file information for a school (set to null)
     *
     * @param string $id School UUID
     * @param string $fileType File type: 'logo', 'embleme', 'header_frame'
     * @return Ecole Updated school with cleared file info
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

                Log::info('File info cleared', [
                    'ecole_id' => $ecole->id,
                    'file_type' => $fileType
                ]);

                return $ecole->fresh();
            });
        } catch (EcoleException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to clear file info', [
                'error' => $e->getMessage(),
                'id' => $id,
                'file_type' => $fileType
            ]);
            throw new EcoleException('Cannot clear file info');
        }
    }

    /**
     * Get all active schools ordered by name
     *
     * @return Collection Collection of active schools
     * @throws EcoleException If retrieval fails
     */
    public function getActive(): Collection
    {
        try {
            return Ecole::where('est_actif', true)
                ->orderBy('libelle_ecole')
                ->get();
        } catch (\Exception $e) {
            Log::error('Failed to get active schools', [
                'error' => $e->getMessage()
            ]);
            throw new EcoleException('Cannot get active schools');
        }
    }
}
