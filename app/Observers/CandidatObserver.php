<?php

namespace App\Observers;

use App\Models\Candidat;
use App\Services\Domain\Candidature\Validators\CandidatureValidationService;
use App\Services\Domain\Notification\Generators\AlertGeneratorService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CandidatObserver
{
    public function __construct(
        private readonly AlertGeneratorService $alertGenerator,
        private readonly CandidatureValidationService $validationService
    ) {}

    /**
     * Handle the Candidat "updated" event.
     */
    public function updated(Candidat $candidat): void
    {
        // 1. Invalidate candidate dashboard cache
        Cache::forget("candidat_dashboard_{$candidat->utilisateur_id}");

        // 2. Load candidatures to check for validation and clean alerts
        $candidat->load('candidatures');

        foreach ($candidat->candidatures as $candidature) {
            // Clean obsolete alerts (especially 'profile_incomplete')
            $this->alertGenerator->cleanObsoleteAlerts($candidature);

            // Try to auto-validate if all criteria match (e.g. documents + payment + now profile)
            try {
                $this->validationService->checkAndValidateIfReady($candidature);
            } catch (\Exception $e) {
                Log::error("Auto-validation failed for candidature {$candidature->id} after profile update: " . $e->getMessage());
            }
        }
    }
}
