<?php

namespace App\Observers;

use App\Enums\StatutCandidature;
use App\Models\Candidature;
use App\Services\Domain\Candidature\Validators\CandidatureValidationService;
use App\Services\Domain\Notification\Generators\AlertGeneratorService;
use App\Services\Domain\Notification\NotificationService;
use App\Services\Infrastructure\Logger\ActivityLoggerService;

class CandidatureObserver
{
    public function __construct(
        private readonly AlertGeneratorService $alertGenerator,
        private readonly NotificationService $notificationService,
        private readonly ActivityLoggerService $logger,
        private readonly CandidatureValidationService $validationService
    ) {}

    /**
     * Handle the Candidature "created" event
     */
    public function created(Candidature $candidature): void
    {
        $candidature->load(['concours', 'candidat']);
        $this->alertGenerator->generateCandidatureAlerts($candidature);

        $this->logger->logActivity('candidature_created', 'candidature', $candidature->id, [
            'candidat_id' => $candidature->candidat_id,
            'concours_id' => $candidature->concours_id,
            'statut' => $candidature->statut_candidature?->value,
        ]);
    }

    /**
     * Handle the Candidature "updated" event
     * Optimisé pour auto-validation : gestion des transitions flexibles
     */
    public function updated(Candidature $candidature): void
    {
        $this->alertGenerator->cleanObsoleteAlerts($candidature);

        if ($candidature->wasChanged(['documents_complets', 'paiement_valide', 'centre_examen_id', 'centre_depot_id'])) {
            $candidature->load(['concours', 'candidat']);
            $this->alertGenerator->generateCandidatureAlerts($candidature);

            // Try to auto-validate if all criteria match
            try {
                $this->validationService->checkAndValidateIfReady($candidature);
            } catch (\Exception $e) {
                $this->logger->logActivity('candidature_auto_validation_failed', 'candidature', $candidature->id, [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($candidature->isDirty('statut_candidature')) {
            $oldStatus = $candidature->getOriginal('statut_candidature');
            $newStatus = $candidature->statut_candidature;

            if ($newStatus === StatutCandidature::VALIDE && ! $candidature->region_figee) {
                $candidature->region_figee = $candidature->candidat?->region;
                $candidature->date_figement_region = now();
                $candidature->saveQuietly(); // Save without triggering events again
            }

            $this->logger->logActivity('candidature_status_changed', 'candidature', $candidature->id, [
                'old_status' => $oldStatus,
                'new_status' => $newStatus?->value,
                'is_backward_transition' => $this->isBackwardTransition($oldStatus, $newStatus?->value),
            ]);

            if (! $candidature->relationLoaded('candidat')) {
                $candidature->load('candidat');
            }

            if ($candidature->candidat) {
                $this->handleCandidatureNotification($candidature, $oldStatus, $newStatus);
            }
        }
    }

    /**
     * Gérer les notifications selon le type de transition
     */
    private function handleCandidatureNotification(Candidature $candidature, StatutCandidature|string|null $oldStatus, ?StatutCandidature $newStatus): void
    {
        match (true) {
            $newStatus === StatutCandidature::VALIDE => $this->notificationService->notifyCandidatureValidated($candidature->candidat, $candidature),

            $newStatus === StatutCandidature::REJETEE => $this->notificationService->notifyCandidatureRejected(
                $candidature->candidat,
                $candidature,
                $candidature->motif_rejet ?? 'Candidature rejetée'
            ),

            $this->isBackwardTransition($oldStatus, $newStatus) => $this->notificationService->notifyCandidatureRejected(
                $candidature->candidat,
                $candidature,
                'Votre dossier nécessite une correction. Veuillez vérifier les informations.'
            ),
            default => null,
        };
    }

    /**
     * Vérifier si la transition est un retour en arrière (correction)
     */
    private function isBackwardTransition(StatutCandidature|string|null $oldStatus, StatutCandidature|string|null $newStatus): bool
    {
        if (! $oldStatus || ! $newStatus) {
            return false;
        }

        // Normaliser en string pour la hiérarchie
        $oldValue = $oldStatus instanceof StatutCandidature ? $oldStatus->value : $oldStatus;
        $newValue = $newStatus instanceof StatutCandidature ? $newStatus->value : $newStatus;

        $hierarchy = [
            StatutCandidature::BROUILLON->value => 1,
            StatutCandidature::SOUMISE->value => 2,
            StatutCandidature::DOCUMENTS_VERIFIES->value => 3,
            StatutCandidature::PAIEMENT_VERIFIE->value => 4,
            StatutCandidature::VALIDE->value => 5,
        ];

        return isset($hierarchy[$oldValue], $hierarchy[$newValue])
          && $hierarchy[$newValue] < $hierarchy[$oldValue];
    }

    /**
     * Handle the Candidature "deleting" event.
     */
    public function deleting(Candidature $candidature): void
    {
        // Only for soft deletes (not force delete)
        if (! $candidature->isForceDeleting()) {
            // Mark as cancelled before soft delete
            $candidature->statut_candidature = StatutCandidature::ANNULEE;
            $candidature->saveQuietly(); // Save without triggering events
        }
    }

    /**
     * Handle the Candidature "restoring" event.
     */
    public function restoring(Candidature $candidature): void
    {
        if ($candidature->statut_candidature === StatutCandidature::ANNULEE) {
            throw new \LogicException('Impossible de restaurer une candidature annulée');
        }
    }
}
