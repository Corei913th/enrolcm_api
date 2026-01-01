<?php

namespace App\Services\Niveaux;

use App\DTOs\Niveaux\CreateNiveauDTO;
use App\Exceptions\Business\NiveauException;
use App\Models\Niveau;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NiveauService
{
    /**
     * Récupérer tous les niveaux avec filtres
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        try {
            $query = Niveau::query()->with(['filiere', 'matieres']);

            // Filtre par statut
            if (isset($filters['est_actif'])) {
                $query->where('est_actif', $filters['est_actif']);
            }

            // Filtre par filière
            if (isset($filters['filiere_id'])) {
                $query->where('filiere_id', $filters['filiere_id']);
            }

            // Recherche
            if (isset($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('libelle_niveau', 'like', "%{$search}%")
                      ->orWhere('code_niveau', 'like', "%{$search}%")
                      ->orWhere('desc_niveau', 'like', "%{$search}%");
                });
            }

            $perPage = $filters['per_page'] ?? 15;
            
            return $query->orderBy('ordre')->orderBy('libelle_niveau')->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des niveaux', [
                'error' => $e->getMessage(),
                'filters' => $filters
            ]);
            throw new NiveauException('Impossible de récupérer la liste des niveaux');
        }
    }

    /**
     * Récupérer un niveau par son ID
     */
    public function getById(string $id): Niveau
    {
        try {
            $niveau = Niveau::with(['filiere', 'matieres'])->findOrFail($id);
            return $niveau;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new NiveauException('Niveau non trouvé', 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du niveau', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            throw new NiveauException('Impossible de récupérer le niveau');
        }
    }

    /**
     * Récupérer un niveau par son code
     */
    public function getByCode(string $code): Niveau
    {
        try {
            $niveau = Niveau::with(['filiere', 'matieres'])
                ->where('code_niveau', $code)
                ->firstOrFail();
            return $niveau;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new NiveauException('Niveau non trouvé avec ce code', 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du niveau par code', [
                'error' => $e->getMessage(),
                'code' => $code
            ]);
            throw new NiveauException('Impossible de récupérer le niveau');
        }
    }

    /**
     * Créer un nouveau niveau
     */
    public function create(CreateNiveauDTO $data): Niveau
    {
        try {
            return DB::transaction(function () use ($data) {
                // Vérifier l'unicité du code
                if (Niveau::where('code_niveau', $data->code_niveau)->exists()) {
                    throw new NiveauException('Ce code niveau existe déjà', 422);
                }

                $niveau = Niveau::create($data->toArray());

                Log::info('Niveau créé avec succès', [
                    'niveau_id' => $niveau->id,
                    'code_niveau' => $niveau->code_niveau
                ]);

                return $niveau->load(['filiere', 'matieres']);
            });
        } catch (NiveauException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du niveau', [
                'error' => $e->getMessage(),
                'data' => $data->toArray()
            ]);
            throw new NiveauException('Impossible de créer le niveau');
        }
    }

    /**
     * Mettre à jour un niveau
     */
    public function update(string $id, CreateNiveauDTO $data): Niveau
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $niveau = $this->getById($id);

                // Vérifier l'unicité du code si modifié
                if ($data->code_niveau !== $niveau->code_niveau) {
                    if (Niveau::where('code_niveau', $data->code_niveau)->where('id', '!=', $id)->exists()) {
                        throw new NiveauException('Ce code niveau existe déjà', 422);
                    }
                }

                $niveau->update($data->toArray());

                Log::info('Niveau mis à jour avec succès', [
                    'niveau_id' => $niveau->id,
                    'code_niveau' => $niveau->code_niveau
                ]);

                return $niveau->fresh(['filiere', 'matieres']);
            });
        } catch (NiveauException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du niveau', [
                'error' => $e->getMessage(),
                'id' => $id,
                'data' => $data->toArray()
            ]);
            throw new NiveauException('Impossible de mettre à jour le niveau');
        }
    }

    /**
     * Supprimer un niveau
     */
    public function delete(string $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $niveau = $this->getById($id);

                $deleted = $niveau->delete();

                Log::info('Niveau supprimé avec succès', [
                    'niveau_id' => $id
                ]);

                return $deleted;
            });
        } catch (NiveauException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du niveau', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
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
                    'est_actif' => !$niveau->est_actif
                ]);

                Log::info('Statut du niveau modifié', [
                    'niveau_id' => $niveau->id,
                    'nouveau_statut' => $niveau->est_actif
                ]);

                return $niveau->fresh(['filiere', 'matieres']);
            });
        } catch (NiveauException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors du changement de statut du niveau', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
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
            Log::error('Erreur lors de la récupération des niveaux actifs', [
                'error' => $e->getMessage()
            ]);
            throw new NiveauException('Impossible de récupérer les niveaux actifs');
        }
    }
}
