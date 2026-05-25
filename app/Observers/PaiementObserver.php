<?php

namespace App\Observers;

use App\Enums\StatutPaiement;
use App\Models\Candidature;
use App\Models\Paiement;
use App\Services\Domain\Notification\NotificationService;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use Illuminate\Support\Facades\Cache;

class PaiementObserver
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly ActivityLoggerService $logger
    ) {}

    /**
     * Logger la création d'un paiement
     * Et déclencher les actions si auto-validé dès la création
     */
    public function created(Paiement $paiement): void
    {
        $this->logger->logActivity('payment_created', 'paiement', $paiement->id, [
            'reference' => $paiement->reference,
            'montant' => $paiement->montant,
            'concours_id' => $paiement->concours_id,
            'statut' => $paiement->statut?->value ?? 'N/A',
        ]);

        // Si créé directement en VERIFIED (ex: auto-validation)
        if ($paiement->statut === StatutPaiement::VERIFIED) {
            $this->handlePaymentVerified($paiement);
        }
    }

    /**
     * Logger et notifier automatiquement lors du changement de statut
     */
    public function updated(Paiement $paiement): void
    {
        if ($paiement->isDirty('statut')) {
            $oldStatus = $paiement->getOriginal('statut');
            $newStatus = $paiement->statut;

            $this->logger->logActivity('payment_status_changed', 'paiement', $paiement->id, [
                'reference' => $paiement->reference,
                'old_status' => $oldStatus,
                'new_status' => $newStatus?->value ?? 'N/A',
                'is_auto_validated' => $oldStatus === StatutPaiement::OCR_VERIFIE->value && $newStatus === StatutPaiement::VERIFIED,
            ]);

            // Charger les relations si nécessaire
            if (! $paiement->relationLoaded('candidat')) {
                $paiement->load('candidat');
            }

            // Actions basées sur le statut
            if ($newStatus === StatutPaiement::VERIFIED) {
                $this->handlePaymentVerified($paiement);
            }

            // Notifier seulement si action requise ou validation finale (pour les autres cas)
            if ($paiement->candidat && $newStatus !== StatutPaiement::VERIFIED) {
                $this->handlePaymentNotification($paiement, $oldStatus, $newStatus);
            }
        }

        // Check linkage if just linked
        if ($paiement->wasChanged('candidature_id') && $paiement->candidature_id) {
            if ($paiement->statut === StatutPaiement::VERIFIED) {
                $this->updateCandidatureStatus($paiement->candidature_id, true);
            }
        }
    }

    /**
     * Actions à effectuer quand un paiement est validé
     */
    private function handlePaymentVerified(Paiement $paiement): void
    {
        // 1. Update Candidature (Trigger CandidatureObserver)
        if ($paiement->candidature_id) {
            $this->updateCandidatureStatus($paiement->candidature_id, true);
        } elseif ($paiement->candidat_id) {
            // Try to find candidature if not linked yet (failsafe)
            $candidature = Candidature::where('candidat_id', $paiement->candidat_id)
                ->where('concours_id', $paiement->concours_id)
                ->latest()
                ->first();

            if ($candidature) {
                // Update linkage first - this might trigger updated() again but logic handles it
                $paiement->updateQuietly(['candidature_id' => $candidature->id]);
                $this->updateCandidatureStatus($candidature->id, true);
            }
        }

        // 2. Notify User
        if ($paiement->candidat) {
            $this->notificationService->notifyPaymentVerified($paiement->candidat, $paiement);
        }

        // 3. Clear Cache
        if ($paiement->candidat_id) {
            Cache::forget("dashboard_stats_{$paiement->candidat_id}");
        }
    }

    /**
     * Update candidature status safely
     */
    private function updateCandidatureStatus(string $candidatureId, bool $isValid): void
    {
        $candidature = Candidature::find($candidatureId);
        if ($candidature && $candidature->paiement_valide !== $isValid) {
            $candidature->update(['paiement_valide' => $isValid]);
            // This update triggers CandidatureObserver::updated
        }
    }

    /**
     * Gérer les notifications selon le type de transition
     */
    private function handlePaymentNotification(Paiement $paiement, StatutPaiement|string|null $oldStatus, ?StatutPaiement $newStatus): void
    {
        // Normaliser oldStatus pour les comparaisons
        $oldEnum = $oldStatus instanceof StatutPaiement ? $oldStatus : (is_string($oldStatus) ? StatutPaiement::tryFrom($oldStatus) : null);

        match (true) {
            $oldEnum === StatutPaiement::OCR_VERIFIE && $newStatus === StatutPaiement::VERIFIED => $this->notificationService->notifyPaymentVerified($paiement->candidat, $paiement),

            $newStatus === StatutPaiement::PENDING_MANUAL_REVIEW => $this->notificationService->notifyPaymentPendingReview($paiement->candidat, $paiement),

            $oldEnum === StatutPaiement::PENDING_MANUAL_REVIEW && $newStatus === StatutPaiement::VERIFIED => $this->notificationService->notifyPaymentVerified($paiement->candidat, $paiement),

            $newStatus === StatutPaiement::REJECTED => $this->notificationService->notifyPaymentRejected(
                $paiement->candidat,
                $paiement,
                $paiement->motif_rejet ?? 'Paiement rejeté'
            ),

            default => null,
        };
    }

    /**
     * Logger la suppression d'un paiement
     */
    public function deleted(Paiement $paiement): void
    {
        $this->logger->logActivity('payment_deleted', 'paiement', $paiement->id, [
            'reference' => $paiement->reference,
            'montant' => $paiement->montant,
        ]);
    }
}
