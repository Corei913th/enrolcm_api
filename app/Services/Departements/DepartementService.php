<?php

namespace App\Services\Departements;

use App\DTOs\Departements\CreateDepartementDTO;
use App\Exceptions\Business\DepartementException;
use App\Models\Departement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartementService
{
    /**
     * Récupérer tous les départements avec filtres
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        try {
            $query = Departement::query()->with(['ecole', 'filieres']);

            // Filtre par statut
            if (isset($filters['est_actif'])) {
                $query->where('est_actif', $filters['est_actif']);
            }

            // Filtre par école
            if (isset($filters['ecole_id'])) {
                $query->where('ecole_id', $filters['ecole_id']);
            }

            // Recherche
            if (isset($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('libelle_departement', 'like', "%{$search}%")
                      ->orWhere('code_departement', 'like', "%{$search}%")
                      ->orWhere('desc_departement', 'like', "%{$search}%");
                });
            }

            $perPage = $filters['per_page'] ?? 15;
            
            return $query->orderBy('libelle_departement')->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des départements', [
                'error' => $e->getMessage(),
                'filters' => $filters
            ]);
            throw new DepartementException('Impossible de récupérer la liste des départements');
        }
    }

    /**
     * Récupérer un département par son ID
     */
    public function getById(string $id): Departement
    {
        try {
            $departement = Departement::with(['ecole', 'filieres'])->findOrFail($id);
            return $departement;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new DepartementException('Département non trouvé', 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du département', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            throw new DepartementException('Impossible de récupérer le département');
        }
    }

    /**
     * Récupérer un département par son code
     */
    public function getByCode(string $code): Departement
    {
        try {
            $departement = Departement::with(['ecole', 'filieres'])
                ->where('code_departement', $code)
                ->firstOrFail();
            return $departement;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new DepartementException('Département non trouvé avec ce code', 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du département par code', [
                'error' => $e->getMessage(),
                'code' => $code
            ]);
            throw new DepartementException('Impossible de récupérer le département');
        }
    }

    /**
     * Créer un nouveau département
     */
    public function create(CreateDepartementDTO $data): Departement
    {
        try {
            return DB::transaction(function () use ($data) {
                // Vérifier l'unicité du code
                if (Departement::where('code_departement', $data->code_departement)->exists()) {
                    throw new DepartementException('Ce code département existe déjà', 422);
                }

                $departement = Departement::create($data->toArray());

                Log::info('Département créé avec succès', [
                    'departement_id' => $departement->id,
                    'code_departement' => $departement->code_departement
                ]);

                return $departement->load(['ecole', 'filieres']);
            });
        } catch (DepartementException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du département', [
                'error' => $e->getMessage(),
                'data' => $data->toArray()
            ]);
            throw new DepartementException('Impossible de créer le département');
        }
    }

    /**
     * Mettre à jour un département
     */
    public function update(string $id, CreateDepartementDTO $data): Departement
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $departement = $this->getById($id);

                // Vérifier l'unicité du code si modifié
                if ($data->code_departement !== $departement->code_departement) {
                    if (Departement::where('code_departement', $data->code_departement)->where('id', '!=', $id)->exists()) {
                        throw new DepartementException('Ce code département existe déjà', 422);
                    }
                }

                $departement->update($data->toArray());

                Log::info('Département mis à jour avec succès', [
                    'departement_id' => $departement->id,
                    'code_departement' => $departement->code_departement
                ]);

                return $departement->fresh(['ecole', 'filieres']);
            });
        } catch (DepartementException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du département', [
                'error' => $e->getMessage(),
                'id' => $id,
                'data' => $data->toArray()
            ]);
            throw new DepartementException('Impossible de mettre à jour le département');
        }
    }

    /**
     * Supprimer un département
     */
    public function delete(string $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $departement = $this->getById($id);

                // Vérifier si le département a des filières
                if ($departement->filieres()->exists()) {
                    throw new DepartementException('Impossible de supprimer : des filières sont associées à ce département', 422);
                }

                $deleted = $departement->delete();

                Log::info('Département supprimé avec succès', [
                    'departement_id' => $id
                ]);

                return $deleted;
            });
        } catch (DepartementException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du département', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            throw new DepartementException('Impossible de supprimer le département');
        }
    }

    /**
     * Activer/Désactiver un département
     */
    public function toggleStatus(string $id): Departement
    {
        try {
            return DB::transaction(function () use ($id) {
                $departement = $this->getById($id);
                
                $departement->update([
                    'est_actif' => !$departement->est_actif
                ]);

                Log::info('Statut du département modifié', [
                    'departement_id' => $departement->id,
                    'nouveau_statut' => $departement->est_actif
                ]);

                return $departement->fresh(['ecole', 'filieres']);
            });
        } catch (DepartementException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors du changement de statut du département', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            throw new DepartementException('Impossible de modifier le statut du département');
        }
    }

    /**
     * Récupérer les départements actifs
     */
    public function getActive(): Collection
    {
        try {
            return Departement::where('est_actif', true)
                ->orderBy('libelle_departement')
                ->get();
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des départements actifs', [
                'error' => $e->getMessage()
            ]);
            throw new DepartementException('Impossible de récupérer les départements actifs');
        }
    }
}
