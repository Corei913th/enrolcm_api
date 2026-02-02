<?php

namespace App\Services\Domain\Concours;

use App\Models\Concours;
use App\Models\Centre;
use App\Exceptions\ConcoursException;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use App\Traits\HasSmartCache;
use App\Traits\HasAdvancedSearch;
use App\Traits\HasServiceFinders;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CentreService
{
    use HasSmartCache, HasAdvancedSearch, HasServiceFinders;

    protected string $modelClass = Centre::class;

    public function __construct(
        private readonly ActivityLoggerService $logger
    ) {}

    protected function getModelTags(): array
    {
        return ['centres', 'concours_centres'];
    }

    /**
     * Attacher un centre à un concours
     */
    public function attachCentre(string $concoursId, string $centreId, bool $estActif = true)
    {
        return runTransaction(function () use ($concoursId, $centreId, $estActif) {
            $concours = Concours::findOrFail($concoursId);
            Centre::findOrFail($centreId); // Vérifier que le centre existe

            if ($concours->centers()->where('centre_id', $centreId)->exists()) {
                throw new ConcoursException("Le centre est déjà rattaché à ce concours.", 409);
            }

            $concours->centers()->attach($centreId, [
                'id' => Str::uuid(),
                'est_actif' => $estActif,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->logger->logActivity('attach_centre', 'concours', $concoursId, [
                'centre_id' => $centreId,
                'est_actif' => $estActif
            ]);

            // Invalider le cache
            $this->flushModelCache();

            return $concours->centers()->where('centre_id', $centreId)->first();
        }, "CentreService::attachCentre");
    }

    /**
     * Détacher un centre d'un concours
     */
    public function detachCentre(string $concoursId, string $centreId): int
    {
        return runTransaction(function () use ($concoursId, $centreId) {
            $concours = Concours::findOrFail($concoursId);

            if (!$concours->centers()->where('centre_id', $centreId)->exists()) {
                throw new ConcoursException("Le centre n'est pas rattaché à ce concours.", 404);
            }

            $result = $concours->centers()->detach($centreId);

            $this->logger->logActivity('detach_centre', 'concours', $concoursId, [
                'centre_id' => $centreId
            ]);

            // Invalider le cache
            $this->flushModelCache();

            return $result;
        }, "CentreService::detachCentre");
    }

    /**
     * Lister les centres d'un concours (avec cache)
     */
    public function listCentres(string $concoursId)
    {
        return $this->rememberStatic("centres_concours_{$concoursId}", function () use ($concoursId) {
            return Concours::query()
                ->select('id')
                ->with([
                    'centers' => function ($query) {
                        $query->select([
                            'centres.id',
                            'centres.libelle_centre',
                            'centres.ville_centre',
                            'centres.departement',
                            'centres.arrondissement',
                            'centres.region_id',
                            'centres.est_actif',
                            'centres.capacite'
                        ]);
                    },
                    'centers.region:id,libelle,code'
                ])
                ->findOrFail($concoursId)
                ->centers;
        });
    }

    /**
     * Mettre à jour le statut d'un centre pour un concours
     */
    public function updateCentreStatus(string $concoursId, string $centreId, bool $estActif)
    {
        return runTransaction(function () use ($concoursId, $centreId, $estActif) {
            $concours = Concours::findOrFail($concoursId);

            if (!$concours->centers()->where('centre_id', $centreId)->exists()) {
                throw new ConcoursException("Le centre n'est pas rattaché à ce concours.", 404);
            }

            $concours->centers()->updateExistingPivot($centreId, [
                'est_actif' => $estActif,
                'updated_at' => now(),
            ]);

            $this->logger->logActivity('update_centre_status', 'concours', $concoursId, [
                'centre_id' => $centreId,
                'est_actif' => $estActif
            ]);

            // Invalider le cache
            $this->flushModelCache();

            return $concours->centers()->where('centre_id', $centreId)->first();
        }, "CentreService::updateCentreStatus");
    }

    /**
     * Synchroniser les centres d'un concours
     */
    public function syncCentres(string $concoursId, array $centreIds): array
    {
        return runTransaction(function () use ($concoursId, $centreIds) {
            $concours = Concours::findOrFail($concoursId);

            $syncData = [];
            foreach ($centreIds as $id) {
                $syncData[$id] = [
                    'id' => Str::uuid(),
                    'est_actif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $result = $concours->centers()->sync($syncData);

            $this->logger->logActivity('sync_centres', 'concours', $concoursId, [
                'centre_ids' => $centreIds,
                'attached' => $result['attached'] ?? [],
                'detached' => $result['detached'] ?? []
            ]);

            // Invalider le cache
            $this->flushModelCache();

            return $result;
        }, "CentreService::syncCentres");
    }

    /**
     * Obtenir les centres actifs pour un concours (avec cache)
     */
    public function getActiveCentres(string $concoursId)
    {
        return $this->rememberStatic("centres_actifs_concours_{$concoursId}", function () use ($concoursId) {
            return Concours::query()
                ->select('id')
                ->with([
                    'centers' => function ($query) {
                        $query->select([
                            'centres.id',
                            'centres.libelle_centre',
                            'centres.ville_centre',
                            'centres.departement',
                            'centres.capacite',
                            'centres.type_centre',
                            //'centres.region_id'
                        ])
                            ->wherePivot('est_actif', true);
                    }
                ])
                ->findOrFail($concoursId)
                ->centers;
        });
    }
}
