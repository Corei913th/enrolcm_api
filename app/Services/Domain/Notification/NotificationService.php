<?php

namespace App\Services\Domain\Notification;

use App\Enums\CanalNotification;
use App\Enums\PrioriteNotification;
use App\Enums\TypeNotification;
use App\Mail\CandidatureRejectedMail;
use App\Mail\CandidatureValidatedMail;
use App\Mail\DocumentRejectedMail;
use App\Mail\DocumentVerifiedMail;
use App\Mail\PaymentPendingReviewMail;
use App\Mail\PaymentRejectedMail;
use App\Mail\PaymentVerifiedMail;
use App\Mail\ResultsPublishedMail;
use App\Mail\VerifyEmailMail;
use App\Mail\WelcomeMail;
use App\Models\Alert;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\Document;
use App\Models\Notification;
use App\Models\Paiement;
use App\Models\ResultatFinal;
use App\Models\Utilisateur;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use App\Services\Infrastructure\Pdf\FicheInscriptionPdfService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function __construct(
        private readonly ActivityLoggerService $logger
    ) {}

    /**
     * Envoie l'email de vérification personnalisé
     */
    public function sendEmailVerification(Utilisateur $utilisateur): void
    {
        try {
            Mail::to($utilisateur->email)->send(new VerifyEmailMail($utilisateur));

            Notification::create([
                'utilisateur_id' => $utilisateur->id,
                'type_notification' => TypeNotification::INFORMATION_GENERALE->value,
                'titre' => 'Email de vérification envoyé',
                'message' => "Un email de vérification a été envoyé à {$utilisateur->email}. Veuillez vérifier votre boîte de réception et cliquer sur le lien pour activer votre compte.",
                'canal' => CanalNotification::APP->value,
                'priorite' => PrioriteNotification::HAUTE->value,
                'est_lue' => false,
                'est_envoyee' => true,
                'date_envoi' => now(),
            ]);

            $this->logger->logActivity('email_verification_sent', 'utilisateur', $utilisateur->id, [
                'email' => $utilisateur->email,
            ]);
        } catch (\Exception $e) {
            $this->logger->logActivity('email_verification_failed', 'utilisateur', $utilisateur->id, [
                'email' => $utilisateur->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            Log::error("Échec envoi email vérification à {$utilisateur->email}: " . $e->getMessage(), [
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Envoie l'email de bienvenue après création du compte
     */
    public function sendWelcomeEmail(Utilisateur $utilisateur, Candidat $candidat, Concours $concours): void
    {
        try {
            Mail::to($utilisateur->email)->send(new WelcomeMail($utilisateur, $candidat, $concours));

            // Créer notification in-app
            Notification::create([
                'utilisateur_id' => $utilisateur->id,
                'type_notification' => TypeNotification::INFORMATION_GENERALE->value,
                'titre' => 'Bienvenue sur la plateforme',
                'message' => "Votre compte a été créé avec succès. Votre inscription au concours {$concours->libelle_concours} a été enregistrée et est en attente de validation du paiement.",
                'canal' => CanalNotification::APP->value,
                'priorite' => PrioriteNotification::NORMALE->value,
                'est_lue' => false,
                'est_envoyee' => true,
                'date_envoi' => now(),
                'metadata' => [
                    'concours_id' => $concours->id,
                    'candidat_id' => $candidat->id,
                ],
            ]);

            $this->logger->logActivity('welcome_email_sent', 'utilisateur', $utilisateur->id, [
                'email' => $utilisateur->email,
                'concours_id' => $concours->id,
            ]);
        } catch (\Exception $e) {
            $this->logger->logActivity('welcome_email_failed', 'utilisateur', $utilisateur->id, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Envoie une notification de paiement en attente
     * Notification in-app toujours créée
     * Email envoyé uniquement si email vérifié
     */
    public function notifyPaymentPendingReview(Candidat $candidat, Paiement $paiement): void
    {
        $utilisateur = $candidat->utilisateur;

        if (! $utilisateur) {
            $this->logger->logActivity('payment_pending_no_user', 'paiement', $paiement->id, [
                'candidat_id' => $candidat->id,
                'message' => "Candidat n'a pas d'utilisateur associé",
            ]);

            return;
        }

        Notification::create([
            'utilisateur_id' => $utilisateur->id,
            'type_notification' => TypeNotification::PAIEMENT_RECU->value,
            'titre' => 'Paiement en attente de validation',
            'message' => "Votre paiement (référence: {$paiement->reference}) a été enregistré et est en attente de validation par un administrateur. Vous serez notifié dès que votre paiement sera vérifié.",
            'canal' => CanalNotification::APP->value,
            'priorite' => PrioriteNotification::NORMALE->value,
            'est_lue' => false,
            'est_envoyee' => true,
            'date_envoi' => now(),
            'metadata' => [
                'paiement_id' => $paiement->id,
                'reference' => $paiement->reference,
                'concours_id' => $paiement->concours_id,
            ],
        ]);

        if ($this->canSendEmail($candidat)) {
            try {
                Mail::to($utilisateur->email)->send(new PaymentPendingReviewMail($utilisateur, $candidat, $paiement));

                $this->logger->logActivity('payment_pending_email_sent', 'paiement', $paiement->id, [
                    'candidat_id' => $candidat->id,
                    'utilisateur_id' => $utilisateur->id,
                    'email' => $utilisateur->email,
                ]);
            } catch (\Exception $e) {
                $this->logger->logActivity('payment_pending_email_failed', 'paiement', $paiement->id, [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Envoie une notification de paiement validé
     * Notification in-app toujours créée
     * Email envoyé uniquement si email vérifié
     */
    public function notifyPaymentVerified(Candidat $candidat, Paiement $paiement): void
    {
        $utilisateur = $candidat->utilisateur;

        if (! $utilisateur) {
            $this->logger->logActivity('payment_verified_no_user', 'paiement', $paiement->id, [
                'candidat_id' => $candidat->id,
                'message' => "Candidat n'a pas d'utilisateur associé",
            ]);

            return;
        }

        Notification::create([
            'utilisateur_id' => $utilisateur->id,
            'type_notification' => TypeNotification::PAIEMENT_VALIDE->value,
            'titre' => 'Paiement validé',
            'message' => "Votre paiement (référence: {$paiement->reference}) a été validé avec succès. Vous pouvez maintenant continuer votre inscription.",
            'canal' => CanalNotification::APP->value,
            'priorite' => PrioriteNotification::HAUTE->value,
            'est_lue' => false,
            'est_envoyee' => true,
            'date_envoi' => now(),
            'metadata' => [
                'paiement_id' => $paiement->id,
                'reference' => $paiement->reference,
                'concours_id' => $paiement->concours_id,
            ],
        ]);

        if ($this->canSendEmail($candidat)) {
            try {
                Mail::to($utilisateur->email)->send(new PaymentVerifiedMail($utilisateur, $candidat, $paiement));

                $this->logger->logActivity('payment_verified_email_sent', 'paiement', $paiement->id, [
                    'candidat_id' => $candidat->id,
                    'utilisateur_id' => $utilisateur->id,
                    'email' => $utilisateur->email,
                ]);
            } catch (\Exception $e) {
                $this->logger->logActivity('payment_verified_email_failed', 'paiement', $paiement->id, [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Envoie une notification de paiement rejeté
     * Notification in-app toujours créée
     * Email envoyé uniquement si email vérifié
     */
    public function notifyPaymentRejected(Candidat $candidat, Paiement $paiement, string $motif): void
    {
        $utilisateur = $candidat->utilisateur;

        if (! $utilisateur) {
            $this->logger->logActivity('payment_rejected_no_user', 'paiement', $paiement->id, [
                'candidat_id' => $candidat->id,
                'message' => "Candidat n'a pas d'utilisateur associé",
            ]);

            return;
        }

        Notification::create([
            'utilisateur_id' => $utilisateur->id,
            'type_notification' => TypeNotification::PAIEMENT_REJETE->value,
            'titre' => 'Paiement rejeté',
            'message' => "Votre paiement (référence: {$paiement->reference}) a été rejeté. Motif: {$motif}. Veuillez soumettre un nouveau paiement valide.",
            'canal' => CanalNotification::APP->value,
            'priorite' => PrioriteNotification::URGENTE->value,
            'est_lue' => false,
            'est_envoyee' => true,
            'date_envoi' => now(),
            'metadata' => [
                'paiement_id' => $paiement->id,
                'reference' => $paiement->reference,
                'motif_rejet' => $motif,
                'concours_id' => $paiement->concours_id,
            ],
        ]);

        // Envoyer email si vérifié
        if ($this->canSendEmail($candidat)) {
            try {
                Mail::to($utilisateur->email)->send(new PaymentRejectedMail($utilisateur, $candidat, $paiement, $motif));

                $this->logger->logActivity('payment_rejected_email_sent', 'paiement', $paiement->id, [
                    'candidat_id' => $candidat->id,
                    'utilisateur_id' => $utilisateur->id,
                    'email' => $utilisateur->email,
                    'motif' => $motif,
                ]);
            } catch (\Exception $e) {
                $this->logger->logActivity('payment_rejected_email_failed', 'paiement', $paiement->id, [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Vérifie si un candidat peut recevoir des emails
     */
    public function canSendEmail(Candidat $candidat): bool
    {
        return $candidat->utilisateur && $candidat->utilisateur->email_verifie;
    }

    /**
     * Récupère les alertes actives pour un candidat
     *
     * @return Collection<Alert>
     */
    public function getActiveAlerts(Candidat $candidat): Collection
    {

        $candidatureIds = $candidat->candidatures()->pluck('id');

        return Alert::whereIn('candidature_id', $candidatureIds)
            ->where('is_dismissed', false)
            ->orderBy('severity', 'desc') // critical, warning, info
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Crée une alerte pour paiement en attente
     */
    public function createPaymentPendingAlert(Candidature $candidature): Alert
    {

        $existingAlert = Alert::where('candidature_id', $candidature->id)
            ->where('type', 'payment_pending')
            ->where('is_dismissed', false)
            ->first();

        if ($existingAlert) {
            return $existingAlert;
        }

        return Alert::create([
            'candidature_id' => $candidature->id,
            'type' => 'payment_pending',
            'severity' => 'warning',
            'title' => 'Paiement en attente de validation',
            'message' => 'Votre paiement a été enregistré et est en cours de vérification par nos équipes. Vous serez notifié dès que votre paiement sera validé.',
        ]);
    }

    /**
     * Crée une alerte critique pour paiement rejeté
     */
    public function createPaymentRejectedAlert(Candidature $candidature, string $motif): Alert
    {

        Alert::where('candidature_id', $candidature->id)
            ->where('type', 'payment_pending')
            ->where('is_dismissed', false)
            ->update([
                'is_dismissed' => true,
                'dismissed_at' => now(),
            ]);

        return Alert::create([
            'candidature_id' => $candidature->id,
            'type' => 'payment_rejected',
            'severity' => 'critical',
            'title' => 'Paiement rejeté',
            'message' => "Votre paiement a été rejeté. Motif: {$motif}. Veuillez soumettre un nouveau paiement valide pour continuer votre inscription.",
        ]);
    }

    /**
     * Notifie le candidat que son document a été validé
     * Email envoyé uniquement si email vérifié
     */
    public function notifyDocumentVerified(Candidat $candidat, Document $document): void
    {
        $utilisateur = $candidat->utilisateur;

        if (! $utilisateur) {
            $this->logger->logActivity('document_verified_no_user', 'document', $document->id, [
                'candidat_id' => $candidat->id,
                'message' => 'Impossible de notifier - utilisateur introuvable',
            ]);

            return;
        }

        $documentRequis = $document->documentRequis;
        $nomDocument = $documentRequis ? $documentRequis->nom_document : 'Document';

        Notification::create([
            'utilisateur_id' => $utilisateur->id,
            'type_notification' => TypeNotification::DOCUMENT_VALIDE->value,
            'titre' => 'Document validé',
            'message' => "Votre document '{$nomDocument}' a été validé avec succès.",
            'canal' => CanalNotification::APP->value,
            'priorite' => PrioriteNotification::NORMALE->value,
            'est_lue' => false,
            'est_envoyee' => true,
            'date_envoi' => now(),
            'metadata' => [
                'document_id' => $document->id,
                'candidature_id' => $document->candidature_id,
            ],
        ]);

        if ($this->canSendEmail($candidat)) {
            try {
                Mail::to($utilisateur->email)->send(new DocumentVerifiedMail($utilisateur, $candidat, $document));

                $this->logger->logActivity('document_verified_email_sent', 'document', $document->id, [
                    'candidat_id' => $candidat->utilisateur_id,
                    'utilisateur_id' => $utilisateur->id,
                    'email' => $utilisateur->email,
                ]);
            } catch (\Exception $e) {
                $this->logger->logActivity('document_verified_email_failed', 'document', $document->id, [
                    'candidat_id' => $candidat->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Notifie le candidat que son document a été rejeté
     * Email envoyé uniquement si email vérifié
     */
    public function notifyDocumentRejected(Candidat $candidat, Document $document, string $motif): void
    {
        $utilisateur = $candidat->utilisateur;

        if (! $utilisateur) {
            $this->logger->logActivity('document_rejected_no_user', 'document', $document->id, [
                'candidat_id' => $candidat->id,
                'message' => 'Impossible de notifier - utilisateur introuvable',
            ]);

            return;
        }

        $documentRequis = $document->documentRequis;
        $nomDocument = $documentRequis ? $documentRequis->nom_document : 'Document';

        Notification::create([
            'utilisateur_id' => $utilisateur->id,
            'type_notification' => TypeNotification::DOCUMENT_REJETE->value,
            'titre' => 'Document rejeté',
            'message' => "Votre document '{$nomDocument}' a été rejeté. Motif: {$motif}. Veuillez soumettre un nouveau document conforme.",
            'canal' => CanalNotification::APP->value,
            'priorite' => PrioriteNotification::HAUTE->value,
            'est_lue' => false,
            'est_envoyee' => true,
            'date_envoi' => now(),
            'metadata' => [
                'document_id' => $document->id,
                'candidature_id' => $document->candidature_id,
                'motif' => $motif,
            ],
        ]);

        if ($this->canSendEmail($candidat)) {
            try {
                Mail::to($utilisateur->email)->send(new DocumentRejectedMail($utilisateur, $candidat, $document, $motif));

                $this->logger->logActivity('document_rejected_email_sent', 'document', $document->id, [
                    'candidat_id' => $candidat->id,
                    'utilisateur_id' => $utilisateur->id,
                    'email' => $utilisateur->email,
                    'motif' => $motif,
                ]);
            } catch (\Exception $e) {
                $this->logger->logActivity('document_rejected_email_failed', 'document', $document->id, [
                    'candidat_id' => $candidat->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Notifie le candidat que sa candidature a été validée
     * Email envoyé uniquement si email vérifié
     */
    public function notifyCandidatureValidated(Candidat $candidat, Candidature $candidature): void
    {
        $utilisateur = $candidat->utilisateur;

        if (! $utilisateur) {
            $this->logger->logActivity('candidature_validated_no_user', 'candidature', $candidature->id, [
                'candidat_id' => $candidat->id,
                'message' => 'Impossible de notifier - utilisateur introuvable',
            ]);

            return;
        }

        $concours = $candidature->concours;
        $nomConcours = $concours ? $concours->libelle_concours : 'Concours';

        Notification::create([
            'utilisateur_id' => $utilisateur->id,
            'type_notification' => TypeNotification::CANDIDATURE_VALIDEE->value,
            'titre' => 'Candidature validée',
            'message' => "Félicitations ! Votre candidature au concours '{$nomConcours}' a été validée. Vous recevrez prochainement votre convocation.",
            'canal' => CanalNotification::APP->value,
            'priorite' => PrioriteNotification::HAUTE->value,
            'est_lue' => false,
            'est_envoyee' => true,
            'date_envoi' => now(),
            'metadata' => [
                'candidature_id' => $candidature->id,
                'concours_id' => $candidature->concours_id,
                'code_candidat' => $candidature->code_cand_def ?? $candidature->code_cand_temp,
            ],
        ]);

        // Email notification (uniquement si email vérifié)
        if ($this->canSendEmail($candidat)) {
            try {
                $ficheService = app(FicheInscriptionPdfService::class);
                Mail::to($utilisateur->email)->send(new CandidatureValidatedMail($utilisateur, $candidat, $candidature, $ficheService));

                $this->logger->logActivity('candidature_validated_email_sent', 'candidature', $candidature->id, [
                    'candidat_id' => $candidat->id,
                    'utilisateur_id' => $utilisateur->id,
                    'email' => $utilisateur->email,
                ]);
            } catch (\Exception $e) {
                $this->logger->logActivity('candidature_validated_email_failed', 'candidature', $candidature->id, [
                    'candidat_id' => $candidat->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Notifie le candidat que sa candidature a été rejetée
     * Email envoyé uniquement si email vérifié
     */
    public function notifyCandidatureRejected(Candidat $candidat, Candidature $candidature, string $motif): void
    {
        $utilisateur = $candidat->utilisateur;

        if (! $utilisateur) {
            $this->logger->logActivity('candidature_rejected_no_user', 'candidature', $candidature->id, [
                'candidat_id' => $candidat->id,
                'message' => 'Impossible de notifier - utilisateur introuvable',
            ]);

            return;
        }

        $concours = $candidature->concours;
        $nomConcours = $concours ? $concours->libelle_concours : 'Concours';
        Notification::create([
            'utilisateur_id' => $utilisateur->id,
            'type_notification' => TypeNotification::CANDIDATURE_REJETEE->value,
            'titre' => 'Candidature rejetée',
            'message' => "Votre candidature au concours '{$nomConcours}' a été rejetée. Motif: {$motif}.",
            'canal' => CanalNotification::APP->value,
            'priorite' => PrioriteNotification::URGENTE->value,
            'est_lue' => false,
            'est_envoyee' => true,
            'date_envoi' => now(),
            'metadata' => [
                'candidature_id' => $candidature->id,
                'concours_id' => $candidature->concours_id,
                'motif' => $motif,
            ],
        ]);

        if ($this->canSendEmail($candidat)) {
            try {
                Mail::to($utilisateur->email)->send(new CandidatureRejectedMail($utilisateur, $candidat, $candidature, $motif));

                $this->logger->logActivity('candidature_rejected_email_sent', 'candidature', $candidature->id, [
                    'candidat_id' => $candidat->id,
                    'utilisateur_id' => $utilisateur->id,
                    'email' => $utilisateur->email,
                    'motif' => $motif,
                ]);
            } catch (\Exception $e) {
                $this->logger->logActivity('candidature_rejected_email_failed', 'candidature', $candidature->id, [
                    'candidat_id' => $candidat->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Notifie le candidat que les résultats sont disponibles
     * Email envoyé uniquement si email vérifié
     */
    public function notifyResultsPublished(Candidat $candidat, Candidature $candidature, ResultatFinal $resultat): void
    {
        $utilisateur = $candidat->utilisateur;

        if (! $utilisateur) {
            $this->logger->logActivity('results_published_no_user', 'resultat', $resultat->id, [
                'candidat_id' => $candidat->id,
                'message' => 'Impossible de notifier - utilisateur introuvable',
            ]);

            return;
        }

        $concours = $candidature->concours;
        $nomConcours = $concours ? $concours->libelle_concours : 'Concours';
        $typeNotification = match (true) {
            $resultat->est_admis => TypeNotification::ADMISSION,
            $resultat->decision === 'LISTE_ATTENTE' => TypeNotification::LISTE_ATTENTE,
            default => TypeNotification::RESULTATS_DISPONIBLES,
        };

        $titre = match (true) {
            $resultat->est_admis => 'Félicitations ! Vous êtes admis(e)',
            $resultat->decision === 'LISTE_ATTENTE' => 'Liste d\'attente',
            default => 'Résultats disponibles',
        };

        $message = match (true) {
            $resultat->est_admis => "Félicitations ! Vous êtes admis(e) au concours '{$nomConcours}' avec une moyenne de {$resultat->moyenne_generale}/20.",
            $resultat->decision === 'LISTE_ATTENTE' => "Vous êtes sur la liste d'attente pour le concours '{$nomConcours}'. Nous vous tiendrons informé(e) de l'évolution de votre situation.",
            default => "Les résultats du concours '{$nomConcours}' sont disponibles. Consultez votre espace candidat pour plus de détails.",
        };

        Notification::create([
            'utilisateur_id' => $utilisateur->id,
            'type_notification' => $typeNotification->value,
            'titre' => $titre,
            'message' => $message,
            'canal' => CanalNotification::APP->value,
            'priorite' => PrioriteNotification::URGENTE->value,
            'est_lue' => false,
            'est_envoyee' => true,
            'date_envoi' => now(),
            'metadata' => [
                'candidature_id' => $candidature->id,
                'concours_id' => $candidature->concours_id,
                'resultat_id' => $resultat->id,
                'est_admis' => $resultat->est_admis,
                'moyenne' => $resultat->moyenne_generale,
                'rang' => $resultat->rang,
            ],
        ]);

        if ($this->canSendEmail($candidat)) {
            try {
                Mail::to($utilisateur->email)->send(new ResultsPublishedMail($utilisateur, $candidat, $candidature, $resultat));

                $this->logger->logActivity('results_published_email_sent', 'resultat', $resultat->id, [
                    'candidat_id' => $candidat->id,
                    'utilisateur_id' => $utilisateur->id,
                    'email' => $utilisateur->email,
                    'est_admis' => $resultat->est_admis,
                ]);
            } catch (\Exception $e) {
                $this->logger->logActivity('results_published_email_failed', 'resultat', $resultat->id, [
                    'candidat_id' => $candidat->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
