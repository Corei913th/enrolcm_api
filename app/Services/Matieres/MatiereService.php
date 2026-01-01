<?php

namespace App\Services\Matieres;

use App\DTOs\Matieres\CreateMatiereDTO;
use App\Exceptions\Business\MatiereException;
use App\Models\Matiere;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MatiereService
{
    /**
     * Récupérer toutes les matières avec filtres
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        try {
            $query = Matiere::query()->with('niveaux');

            // Filtre par statut
            if (isset($filters['est_actif'])) {
                $query->where('est_actif', $filters['est_actif']);
            }

            // Recherche
            if (isset($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('libelle_matiere', 'like', "%{$search}%")
                      ->orWhere('code_matiere', 'like', "%{$search}%");
                });
            }

            $perPage = $filters['per_page'] ?? 15;
            
            return $query->orderBy('libelle_matiere')->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des matières', [
                'error' => $e->getMessage(),
                'filters' => $filters
            ]);
            throw new MatiereException('Impossible de récupérer la liste des matières');
        }
    }

    /**
     * Récupérer une matière par son ID
     */
    public function getById(string $id): Matiere
    {
        try {
            $matiere = Matiere::with('niveaux')->findOrFail($id);
            return $matiere;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new MatiereException('Matière non trouvée', 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de la matière', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            throw new MatiereException('Impossible de récupérer la matière');
        }
    }

    /**
     * Récupérer une matière par son code
     */
    public function getByCode(string $code): Matiere
    {
        try {
            $matiere = Matiere::with('niveaux')
                ->where('code_matiere', $code)
                ->firstOrFail();
            return $matiere;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new MatiereException('Matière non trouvée avec ce code', 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de la matière par code', [
                'error' => $e->getMessage(),
                'code' => $code
            ]);
            throw new MatiereException('Impossible de récupérer la matière');
        }
    }

    /**
     * Créer une nouvelle matière
     */
    public function create(CreateMatiereDTO $data): Matiere
    {
        try {
            return DB::transaction(function () use ($data) {
                // Vérifier l'unicité du code
                if (Matiere::where('code_matiere', $data->code_matiere)->exists()) {
                    throw new MatiereException('Ce code matière existe déjà', 422);
                }

                $matiere = Matiere::create($data->toArray());

                Log::info('Matière créée avec succès', [
                    'matiere_id' => $matiere->id,
                    'code_matiere' => $matiere->code_matiere
                ]);

                return $matiere->load('niveaux');
            });
        } catch (MatiereException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la matière', [
                'error' => $e->getMessage(),
                'data' => $data->toArray()
            ]);
            throw new MatiereException('Impossible de créer la matière');
        }
    }

    /**
     * Mettre à jour une matière
     */
    public function update(string $id, CreateMatiereDTO $data): Matiere
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $matiere = $this->getById($id);

                // Vérifier l'unicité du code si modifié
                if ($data->code_matiere !== $matiere->code_matiere) {
                    if (Matiere::where('code_matiere', $data->code_matiere)->where('id', '!=', $id)->exists()) {
                        throw new MatiereException('Ce code matière existe déjà', 422);
                    }
                }

                $matiere->update($data->toArray());

                Log::info('Matière mise à jour avec succès', [
                    'matiere_id' => $matiere->id,
                    'code_matiere' => $matiere->code_matiere
                ]);

                return $matiere->fresh('niveaux');
            });
        } catch (MatiereException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la matière', [
                'error' => $e->getMessage(),
                'id' => $id,
                'data' => $data->toArray()
            ]);
            throw new MatiereException('Impossible de mettre à jour la matière');
        }
    }

    /**
     * Supprimer une matière
     */
    public function delete(string $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $matiere = $this->getById($id);

                $deleted = $matiere->delete();

                Log::info('Matière supprimée avec succès', [
                    'matiere_id' => $id
                ]);

                return $deleted;
            });
        } catch (MatiereException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la matière', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
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
                    'est_actif' => !$matiere->est_actif
                ]);

                Log::info('Statut de la matière modifié', [
                    'matiere_id' => $matiere->id,
                    'nouveau_statut' => $matiere->est_actif
                ]);

                return $matiere->fresh('niveaux');
            });
        } catch (MatiereException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors du changement de statut de la matière', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
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
            Log::error('Erreur lors de la récupération des matières actives', [
                'error' => $e->getMessage()
            ]);
            throw new MatiereException('Impossible de récupérer les matières actives');
        }
    }
}
