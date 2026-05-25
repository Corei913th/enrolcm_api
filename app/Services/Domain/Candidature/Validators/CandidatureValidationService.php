<?php

namespace App\Services\Domain\Candidature\Validators;

use App\Enums\CanalNotification;
use App\Enums\PrioriteNotification;
use App\Enums\StatutCandidature;
use App\Enums\TypeNotification;
use App\Helpers\CandidatureHelper;
use App\Models\Candidature;
use App\Models\Notification;
use App\Services\Domain\Notification\NotificationService;
use App\Services\Infrastructure\QrCode\QrCodeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CandidatureValidationService
{
    public function __construct(
        private QrCodeService $qrCodeService,
        private NotificationService $notificationService
    ) {}

    /**
     * Check if candidature is ready for validation and validate if criteria are met
     *
     * @return bool True if candidature was validated, false if not ready
     */
    public function checkAndValidateIfReady(Candidature $candidature): bool
    {
        return runTransaction(function () use ($candidature) {
            $candidature = Candidature::where('id', $candidature->id)
                ->lockForUpdate()
                ->first();

            if (! $candidature) {
                Log::warning('Candidature not found after lock', ['id' => $candidature->id]);

                return false;
            }

            // Check if already validated (after lock)
            if ($candidature->statut_candidature === StatutCandidature::VALIDE) {
                Log::info("Candidature {$candidature->id} is already validated");

                return false;
            }

            if (! CandidatureHelper::hasValidPayment($candidature->id)) {
                Log::info("Candidature {$candidature->id} payment not verified");

                return false;
            }

            if (! CandidatureHelper::hasCompleteDocuments($candidature->id)) {
                Log::info("Candidature {$candidature->id} documents not complete");

                return false;
            }

            $fieldsCheck = CandidatureHelper::hasRequiredCandidateFields($candidature->id);
            if (! $fieldsCheck['valid']) {
                Log::info("Candidature {$candidature->id} missing required fields", [
                    'missing' => $fieldsCheck['missing'],
                ]);

                return false;
            }

            return $this->validateCandidature($candidature);
        }, 'CandidatureValidationService::checkAndValidateIfReady');
    }

    /**
     * Validate candidature and generate QR code
     */
    private function validateCandidature(Candidature $candidature): bool
    {
        try {
            DB::beginTransaction();

            // Generate numero_candidature if missing
            $numeroCandidature = $candidature->numero_candidature;
            if (empty($numeroCandidature)) {
                $numeroCandidature = $this->generateNextNumeroCandidature($candidature);
            }

            // Generate code_cand_def using the numero_candidature
            $codeCandDef = $this->generateCodeCandDef($candidature, $numeroCandidature);

            // Generate QR code
            $qrCode = $this->generateQrCode($candidature, $codeCandDef, $numeroCandidature);

            // Update candidature
            $candidature->update([
                'statut_candidature' => StatutCandidature::VALIDE,
                'date_validation' => now(),
                'numero_candidature' => $numeroCandidature,
                'code_cand_def' => $codeCandDef,
                'qr_code' => $qrCode,
            ]);

            // Dismiss payment pending alert if exists
            $this->dismissPaymentPendingAlert($candidature);

            // Send notifications
            $this->sendValidationNotifications($candidature);

            DB::commit();

            Log::info("Candidature {$candidature->id} validated successfully", [
                'code_cand_def' => $codeCandDef,
                'candidat_id' => $candidature->candidat_id,
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            logServiceError('Failed to validate candidature', $e, ['candidature_id' => $candidature->id]);
            throw $e;
        }
    }

    /**
     * Generate next available sequence number for this concours/session
     */
    private function generateNextNumeroCandidature(Candidature $candidature): string
    {
        $count = DB::table('candidatures')
            ->where('concours_id', $candidature->concours_id)
            ->where('session_id', $candidature->session_id)
            ->count();

        $sequence = $count + 1;
        $schoolCode = $candidature->concours?->ecole?->code_ecole ?? 'GEN';
        $paddedSequence = str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);

        return "{$schoolCode}-{$paddedSequence}";
    }

    /**
     * Generate definitive candidate code
     */
    private function generateCodeCandDef(Candidature $candidature, string $numero): string
    {
        $annee = date('Y');

        return "CAND-{$annee}-{$numero}";
    }

    /**
     * Generate QR code for candidature
     *
     * @param  int|string  $numeroCandidature
     * @return string Base64 encoded QR code
     */
    private function generateQrCode(Candidature $candidature, string $codeCandDef, $numeroCandidature): string
    {
        $data = json_encode([
            'code_cand_def' => $codeCandDef,
            'numero_candidature' => $numeroCandidature,
            'concours_id' => $candidature->concours_id,
            'session_id' => $candidature->session_id,
            'nom' => $candidature->candidat->nom_cand,
            'prenom' => $candidature->candidat->prenom_cand,
        ]);

        return $this->qrCodeService->generateBase64($data, 200);
    }

    /**
     * Dismiss payment pending alert
     */
    private function dismissPaymentPendingAlert(Candidature $candidature): void
    {
        $candidature->alerts()
            ->where('type', 'payment_pending')
            ->where('is_dismissed', false)
            ->update([
                'is_dismissed' => true,
                'dismissed_at' => now(),
            ]);
    }

    /**
     * Send validation notifications
     */
    private function sendValidationNotifications(Candidature $candidature): void
    {
        $candidat = $candidature->candidat;
        $utilisateur = $candidat->utilisateur;

        if (! $utilisateur) {
            Log::warning("Candidat {$candidat->utilisateur_id} has no associated user");

            return;
        }

        // Create in-app notification
        Notification::create([
            'utilisateur_id' => $utilisateur->id,
            'type_notification' => TypeNotification::CANDIDATURE_VALIDEE->value,
            'titre' => 'Candidature validée',
            'message' => "Félicitations ! Votre candidature a été validée avec succès. Votre fiche d'inscription et votre convocation sont désormais disponibles.",
            'canal' => CanalNotification::APP->value,
            'priorite' => PrioriteNotification::HAUTE->value,
            'est_lue' => false,
            'est_envoyee' => true,
            'date_envoi' => now(),
            'metadata' => [
                'candidature_id' => $candidature->id,
                'code_cand_def' => $candidature->code_cand_def,
                'concours_id' => $candidature->concours_id,
            ],
        ]);

        // Send email if email is verified
        if ($this->notificationService->canSendEmail($candidat)) {
            try {
                // TODO: Implement email sending via Mail facade
                // Mail::to($utilisateur->email)->send(new CandidatureValidatedMail($candidat, $candidature));

                Notification::create([
                    'utilisateur_id' => $utilisateur->id,
                    'type_notification' => TypeNotification::CANDIDATURE_VALIDEE->value,
                    'titre' => 'Candidature validée',
                    'message' => 'Félicitations ! Votre candidature a été validée avec succès.',
                    'canal' => CanalNotification::EMAIL->value,
                    'priorite' => PrioriteNotification::HAUTE->value,
                    'est_lue' => false,
                    'est_envoyee' => true,
                    'date_envoi' => now(),
                    'metadata' => [
                        'candidature_id' => $candidature->id,
                        'code_cand_def' => $candidature->code_cand_def,
                    ],
                ]);
            } catch (\Exception $e) {
                logServiceError('Failed to send candidature validation email', $e, ['candidature_id' => $candidature->id]);
            }
        }
    }
}
