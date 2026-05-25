<?php

namespace App\Services\Domain\Concours;

use App\Exceptions\Business\SpecConcoursException;
use App\Models\SpecConcours;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use App\Traits\HasActivityLogger;
use App\Traits\HasOptimizedUpdate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class SpecService
{
    use HasActivityLogger, HasOptimizedUpdate;

    public function __construct(ActivityLoggerService $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Liste toutes les spécialités de concours avec filtres.
     *
     **/
    public function getAll(array $filters = [], int $perPage = 20)
    {
        $query = SpecConcours::query();

        if (isset($filters['est_actif'])) {
            $query->where('est_actif', $filters['est_actif']);
        }

        if (isset($filters['search'])) {
            $search = strtolower($filters['search']);
            $query->where(DB::raw('LOWER(nom_spec)'), 'like', "%{$search}%");
        }

        return $query->orderBy('nom_spec')->paginate($perPage);
        // return SpecConcoursResource::collection($specs)->response()->getData(true);
    }

    /**
     * Récupérer une spécialité par son ID.
     */
    public function getById(string $id)
    {
        try {
            return SpecConcours::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw SpecConcoursException::notFound($id);
        }
    }

    /**
     * Créer une nouvelle spécialité de concours.
     */
    public function create(array $data)
    {
        return runTransaction(function () use ($data) {
            try {
                $spec = SpecConcours::create($data);
                $this->logCreate('spec_concours', $spec->id, ['nom' => $spec->nom_spec]);

                return $spec;
            } catch (\Exception $e) {
                logServiceError('Erreur création spécialité', $e);
                throw new SpecConcoursException('Erreur lors de la création de la spécialité.', 500);
            }
        }, 'SpecService::create');
    }

    /**
     * Mettre à jour une spécialité de concours.
     */
    public function update(string $id, array $data)
    {
        return runTransaction(function () use ($id, $data) {
            try {
                $spec = $this->getById($id);
                $this->updateIfDirty($spec, $data);
                $this->logUpdate('spec_concours', $id);

                return $spec->fresh();
            } catch (SpecConcoursException $e) {
                throw $e;
            } catch (\Exception $e) {
                logServiceError('Erreur mise à jour spécialité', $e, ['id' => $id]);
                throw new SpecConcoursException('Erreur lors de la mise à jour de la spécialité.', 500);
            }
        }, 'SpecService::update');
    }

    /**
     * Supprimer une spécialité de concours.
     */
    public function delete(string $id)
    {
        return runTransaction(function () use ($id) {
            try {
                $spec = $this->getById($id);

                if (hasDependencies($spec, 'concours')) {
                    throw SpecConcoursException::hasActiveConcours($id);
                }

                $result = $spec->delete();
                $this->logDelete('spec_concours', $id);

                return $result;
            } catch (SpecConcoursException $e) {
                throw $e;
            } catch (\Exception $e) {
                logServiceError('Erreur suppression spécialité', $e, ['id' => $id]);
                throw new SpecConcoursException('Erreur lors de la suppression de la spécialité.', 500);
            }
        });
    }

    /**
     * Activer/Désactiver une spécialité.
     */
    public function toggleStatus(string $id)
    {
        return DB::transaction(function () use ($id) {
            try {
                $spec = $this->getById($id);
                $spec->est_actif = ! $spec->est_actif;
                $spec->save();

                return $spec;
            } catch (SpecConcoursException $e) {
                throw $e;
            } catch (\Exception $e) {
                logServiceError('Erreur changement statut spécialité', $e, ['id' => $id]);
                throw new SpecConcoursException('Impossible de modifier le statut de la spécialité.', 500);
            }
        });
    }
}
