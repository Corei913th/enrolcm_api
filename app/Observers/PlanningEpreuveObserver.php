<?php

namespace App\Observers;

use App\Models\Concours;
use App\Models\PlanningEpreuve;
use Illuminate\Support\Facades\Log;

class PlanningEpreuveObserver
{
    /**
     * Handle the PlanningEpreuve "created" event.
     * Synchronise date_examen du concours avec la première épreuve
     */
    public function created(PlanningEpreuve $planning): void
    {
        $this->synchronizeDateExamen($planning->concours_id);
    }

    /**
     * Handle the PlanningEpreuve "updated" event.
     * Synchronise date_examen du concours si la date a changé
     */
    public function updated(PlanningEpreuve $planning): void
    {
        if ($planning->isDirty('date_epreuve') || $planning->isDirty('est_actif')) {
            $this->synchronizeDateExamen($planning->concours_id);
        }
    }

    /**
     * Handle the PlanningEpreuve "deleted" event.
     * Synchronise date_examen du concours après suppression
     */
    public function deleted(PlanningEpreuve $planning): void
    {
        $this->synchronizeDateExamen($planning->concours_id);
    }

    /**
     * Synchroniser date_examen du concours avec la première épreuve planifiée
     */
    private function synchronizeDateExamen(string $concoursId): void
    {
        try {
            $concours = Concours::find($concoursId);

            if (! $concours) {
                return;
            }

            // Récupérer la date de la première épreuve active
            $premiereEpreuve = PlanningEpreuve::where('concours_id', $concoursId)
                ->where('est_actif', true)
                ->orderBy('date_epreuve')
                ->orderBy('heure_debut')
                ->first();

            $oldDate = $concours->date_examen;
            $newDate = $premiereEpreuve?->date_epreuve;

            if ($oldDate != $newDate) {
                $concours->date_examen = $newDate;
                $concours->saveQuietly();

                Log::info('Date examen synchronisée', [
                    'concours_id' => $concoursId,
                    'old_date' => $oldDate?->format('Y-m-d'),
                    'new_date' => $newDate?->format('Y-m-d'),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erreur synchronisation date_examen', [
                'concours_id' => $concoursId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
