<?php

namespace App\Services\Domain\Concours\Validators;

use App\Models\PlanningEpreuve;
use Carbon\Carbon;

class PlanningEpreuveValidator
{
    /**
     * Validate planning before save
     *
     * @throws \Exception If validation fails
     */
    public function validateBeforeSave(PlanningEpreuve $planning): void
    {
        $this->validateCoefficient($planning);
        $this->validateDuration($planning);
        $this->validateSchedule($planning);
    }

    /**
     * Validate coefficient
     *
     * @throws \Exception If coefficient is invalid
     */
    private function validateCoefficient(PlanningEpreuve $planning): void
    {

        if (! $planning->coefficient && $planning->epreuve) {
            $planning->coefficient = $planning->epreuve->coefficient_defaut;
        }

        if ($planning->coefficient && $planning->coefficient <= 0) {
            throw new \Exception('Coefficient must be greater than 0');
        }

        if ($planning->coefficient && $planning->coefficient > 10) {
            \Log::warning('Very high coefficient detected', [
                'planning_id' => $planning->id,
                'epreuve_id' => $planning->epreuve_id,
                'coefficient' => $planning->coefficient,
            ]);
        }
    }

    /**
     * Validate duration matches schedule
     *
     * @throws \Exception If duration doesn't match
     */
    private function validateDuration(PlanningEpreuve $planning): void
    {
        if (! $planning->heure_debut || ! $planning->heure_fin || ! $planning->epreuve) {
            return;
        }

        $actualDuration = Carbon::parse($planning->heure_debut)
            ->diffInMinutes(Carbon::parse($planning->heure_fin));

        $expectedDuration = $planning->epreuve->duree_en_minute;

        // Tolerance: ±15 minutes
        if ($expectedDuration && abs($actualDuration - $expectedDuration) > 15) {
            throw new \Exception(
                "Scheduled duration ({$actualDuration} min) does not match " .
                  "exam duration ({$expectedDuration} min). " .
                  'Maximum allowed difference: 15 minutes.'
            );
        }
    }

    /**
     * Validate schedule times
     *
     * @throws \Exception If schedule is invalid
     */
    private function validateSchedule(PlanningEpreuve $planning): void
    {
        if (! $planning->heure_debut || ! $planning->heure_fin) {
            return;
        }

        $start = Carbon::parse($planning->heure_debut);
        $end = Carbon::parse($planning->heure_fin);

        if ($end->lte($start)) {
            throw new \Exception(
                'End time must be after start time'
            );
        }
    }
}
