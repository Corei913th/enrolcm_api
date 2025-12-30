<?php

namespace App\Services\Filieres;

use App\DTOs\Filieres\CreateFiliereDTO;
use App\Exceptions\Business\FiliereException;
use App\Models\Filiere;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FiliereService
{
    /**
     * Récupérer toutes les filières avec filtres
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        try {
            $query = Filiere::query()->with(['departement', 'niveaux']);

            // Filtre par statut
            if (isset($filters['est_actif'])) {
                $query->where('est_actif', $filters['est_actif']);
            }

            // Filtre par département
            if (isset($filters['departement_id'])) {
                $query->where('departement_id', $filters['departement_id']);
            }

            // Recherche
            if (isset($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('libelle_filiere', 'like', "%{$search}%")
                      ->orWhere('code_filiere', 'like', "%{$search}%")
                      ->orWhere('desc_filiere', 'like', "%{$search}%");
                });
            }

            $perPage = $filters['per_page'] ?? 15;
            
            return $query->orderBy('libelle_filiere')->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des filières', [
                'error' => $e->getMessage(),
                'filters' => $filters
            ]);
            throw new FiliereException('Impossible de récupérer la liste des filières');
        }
    }

    /**
     * Récupérer une filière par son ID
     */
    public function getById(string $id): Filiere
    {
        try {
            $filiere = Filiere::with(['departement', 'niveaux'])->findOrFail($id);
            return $filiere;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new FiliereException('Filière non trouvée', 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de la filière', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            throw new FiliereException('Impossible de récupérer la filière');
        }
    }

    /**
     * Récupérer une filière par son code
     */
    public function getByCode(string $code): Filiere
    {
        try {
            $filiere = Filiere::with(['departement', 'niveaux'])
                ->where('code_filiere', $code)
                ->firstOrFail();
            return $filiere;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new FiliereException('Filière non trouvée avec ce code', 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de la filière par code', [
                'error' => $e->getMessage(),
                'code' => $code
            ]);
            throw new FiliereException('Impossible de récupérer la filière');
        }
    }

    /**
     * Créer une nouvelle filière
     */
    public function create(CreateFiliereDTO $data): Filiere
    {
        try {
            return DB::transaction(function () use ($data) {
                // Vérifier l'unicité du code
                if (Filiere::where('code_filiere', $data->code_filiere)->exists()) {
                    throw new FiliereException('Cette filière existe déjà', 422);
                }

                $filiere = Filiere::create($data->toArray());

                Log::info('Filière créée avec succès', [
                    'filiere_id' => $filiere->id,
                    'code_filiere' => $filiere->code_filiere
                ]);

                return $filiere->load(['departement', 'niveaux']);
            });
        } catch (FiliereException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la filière', [
                'error' => $e->getMessage(),
                'data' => $data->toArray()
            ]);
            throw new FiliereException('Impossible de créer la filière');
        }
    }

    /**
     * Mettre à jour une filière
     */
    public function update(string $id, CreateFiliereDTO $data): Filiere
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $filiere = $this->getById($id);

                // Vérifier l'unicité du code si modifié
                if ($data->code_filiere !== $filiere->code_filiere) {
                    if (Filiere::where('code_filiere', $data->code_filiere)->where('id', '!=', $id)->exists()) {
                        throw new FiliereException('Cette filière existe déjà', 422);
                    }
                }

                $filiere->update($data->toArray());

                Log::info('Filière mise à jour avec succès', [
                    'filiere_id' => $filiere->id,
                    'code_filiere' => $filiere->code_filiere
                ]);

                return $filiere->fresh(['departement', 'niveaux']);
            });
        } catch (FiliereException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la filière', [
                'error' => $e->getMessage(),
                'id' => $id,
                'data' => $data->toArray()
            ]);
            throw new FiliereException('Impossible de mettre à jour la filière');
        }
    }

    /**
     * Supprimer une filière
     */
    public function delete(string $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $filiere = $this->getById($id);

                // Vérifier si la filière a des niveaux
                if ($filiere->niveaux()->exists()) {
                    throw new FiliereException('Impossible de supprimer : des niveaux sont associés à cette filière', 422);
                }

                $deleted = $filiere->delete();

                Log::info('Filière supprimée avec succès', [
                    'filiere_id' => $id
                ]);

                return $deleted;
            });
        } catch (FiliereException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la filière', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
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
                    'est_actif' => !$filiere->est_actif
                ]);

                Log::info('Statut de la filière modifié', [
                    'filiere_id' => $filiere->id,
                    'nouveau_statut' => $filiere->est_actif
                ]);

                return $filiere->fresh(['departement', 'niveaux']);
            });
        } catch (FiliereException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors du changement de statut de la filière', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
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
            Log::error('Erreur lors de la récupération des filières actives', [
                'error' => $e->getMessage()
            ]);
            throw new FiliereException('Impossible de récupérer les filières actives');
        }
    }
}
