<?php

namespace App\Services\Domain\Referentiel;

use App\Models\Centre;
use App\Traits\HasAdvancedSearch;
use App\Traits\HasSmartCache;
use App\Traits\HasOptimizedUpdate;
use App\Traits\HasActivityLogger;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Business\CentreException;

class CentreService
{
    use HasAdvancedSearch, HasSmartCache, HasOptimizedUpdate, HasActivityLogger;

    public function __construct(ActivityLoggerService $logger)
    {
        $this->logger = $logger;
    }

    protected function getModelTags(): array
    {
        return ['centres', 'lists'];
    }

    /**
     * Liste des centres avec pagination et filtres optimisés.
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Centre::query()
            ->select([
                'id',
                'type_centre',
                'departement',
                'arrondissement',
                'responsable_id',
                'region_id',
                'libelle_centre',
                'ville_centre',
                'capacite',
                'est_actif',
                'created_at',
                'updated_at'
            ])->with(['region:id,libelle,code']);

        // Recherche optimisée
        if (!empty($filters['search'])) {
            $this->applySearch(
                $query,
                $filters['search'],
                [
                    'libelle_centre' => 'words',
                    'ville_centre' => 'partial',
                    'adresse_centre' => 'partial',
                    'type_centre' => 'partial'
                ]
            );
        }

        // Filtres simples
        $simpleFilters = [];
        if (isset($filters['region_id'])) {
            $simpleFilters['region_id'] = $filters['region_id'];
        }
        if (isset($filters['est_actif'])) {
            $simpleFilters['est_actif'] = $filters['est_actif'];
        }
        $this->applyFilters($query, $simpleFilters);

        // Tri
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $this->applySort(
            $query,
            $sortBy,
            $sortOrder,
            'created_at',
            ['libelle_centre', 'ville_centre', 'capacite_totale', 'created_at']
        );

        return $query->paginate($perPage);
    }

    /**
     * Détails d'un centre.
     */
    public function getById(string $id): Centre
    {
        try {
            return Centre::with(['region', 'responsable', 'salles'])->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw CentreException::notFound($id);
        }
    }

    /**
     * Créer un centre.
     */
    public function create(array $data): Centre
    {
        return runTransaction(function () use ($data) {
            try {
                $centre = Centre::create($data);
                $this->logCreate('centre', $centre->id, ['libelle' => $centre->libelle_centre]);
                return $centre;
            } catch (\Exception $e) {
                throw new CentreException("Erreur lors de la création du centre : " . $e->getMessage(), 500);
            }
        }, 'CentreService::create');
    }

    /**
     * Mettre à jour un centre.
     */
    public function update(string $id, array $data): Centre
    {
        return runTransaction(function () use ($id, $data) {
            try {
                $centre = $this->getById($id);
                $this->updateWithCache($centre, $data);
                $this->logUpdate('centre', $id);
                return $centre->fresh();
            } catch (CentreException $e) {
                throw $e;
            } catch (\Exception $e) {
                throw new CentreException("Erreur lors de la mise à jour du centre : " . $e->getMessage(), 500);
            }
        }, 'CentreService::update');
    }

    /**
     * Supprimer un centre.
     */
    public function delete(string $id): bool
    {
        return runTransaction(function () use ($id) {
            try {
                $centre = $this->getById($id);
                if (hasDependencies($centre, 'candidatures')) {
                    throw CentreException::hasCandidatures($id);
                }
                $result = $centre->delete();
                $this->logDelete('centre', $id);
                return $result;
            } catch (CentreException $e) {
                throw $e;
            } catch (\Exception $e) {
                throw new CentreException("Erreur lors de la suppression du centre : " . $e->getMessage(), 500);
            }
        }, 'CentreService::delete');
    }

    /**
     * Liste des centres sans pagination (pour les dropdowns).
     */
    public function getActive(): Collection
    {
        return Centre::actif()->orderBy('libelle_centre')->get();
    }
}
