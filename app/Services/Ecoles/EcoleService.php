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
    /**
     * Récupérer toutes les écoles avec filtres
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        try {
            $query = Ecole::query()->with('departements');

            // Filtre par statut
            if (isset($filters['est_actif'])) {
                $query->where('est_actif', $filters['est_actif']);
            }

            // Filtre par région
            if (isset($filters['region'])) {
                $query->where('region', $filters['region']);
            }

            // Recherche
            if (isset($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('libelle_ecole', 'like', "%{$search}%")
                      ->orWhere('code_ecole', 'like', "%{$search}%")
                      ->orWhere('localisation', 'like', "%{$search}%");
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
     * Récupérer une école par son ID
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
     * Récupérer une école par son code
     */
    public function getByCode(string $code): Ecole
    {
        try {
            $ecole = Ecole::with('departements')->byCode($code)->firstOrFail();
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
     * Créer une nouvelle école
     */
    public function create(CreateEcoleDTO $data): Ecole
    {
        try {
            return DB::transaction(function () use ($data) {
                // Vérifier l'unicité du code
                if (Ecole::where('code_ecole', $data->code_ecole)->exists()) {
                    throw new EcoleException('Ce code école existe déjà', 422);
                }

                $ecole = Ecole::create($data->toArray());

                Log::info('École créée avec succès', [
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
     * Mettre à jour une école
     */
    public function update(string $id, CreateEcoleDTO $data): Ecole
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $ecole = $this->getById($id);

                // Vérifier l'unicité du code si modifié
                if ($data->code_ecole !== $ecole->code_ecole) {
                    if (Ecole::where('code_ecole', $data->code_ecole)->where('id', '!=', $id)->exists()) {
                        throw new EcoleException('Ce code école existe déjà', 422);
                    }
                }

                $ecole->update($data->toArray());

                Log::info('École mise à jour avec succès', [
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
     * Supprimer une école
     */
    public function delete(string $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $ecole = $this->getById($id);

                // Vérifier si l'école a des départements
                if ($ecole->departements()->exists()) {
                    throw new EcoleException('Impossible de supprimer une école ayant des départements', 422);
                }

                $deleted = $ecole->delete();

                Log::info('École supprimée avec succès', [
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
     * Activer/Désactiver une école
     */
    public function toggleStatus(string $id): Ecole
    {
        try {
            return DB::transaction(function () use ($id) {
                $ecole = $this->getById($id);
                
                $ecole->update([
                    'est_actif' => !$ecole->est_actif
                ]);

                Log::info('Statut de l\'école modifié', [
                    'ecole_id' => $ecole->id,
                    'nouveau_statut' => $ecole->est_actif
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
     * Récupérer les écoles actives
     */
    public function getActive(): Collection
    {
        try {
            return Ecole::where('est_actif', true)
                ->orderBy('libelle_ecole')
                ->get();
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des écoles actives', [
                'error' => $e->getMessage()
            ]);
            throw new EcoleException('Impossible de récupérer les écoles actives');
        }
    }
}
