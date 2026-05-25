<?php

namespace App\Services\Domain\Notification\Generators;

use App\Helpers\CandidatureHelper;
use App\Models\Alert;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Services\Domain\Notification\Notifiers\AlertEmailService;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use Carbon\Carbon;

/**
 * Générateur automatique d'alertes pour les candidats
 */
class AlertGeneratorService
{
    public function __construct(
        private readonly ActivityLoggerService $logger,
        private readonly AlertEmailService $emailService
    ) {}

    /**
     * Générer toutes les alertes pour un candidat
     *
     * @return array<Alert|null>
     */
    public function generateCandidateAlerts(Candidat $candidat): array
    {
        $alerts = [];

        if ($this->isProfileIncomplete($candidat)) {
            $alerts[] = $this->createProfileIncompleteAlert($candidat);
        } else {
            Alert::whereHas('candidature', function ($q) use ($candidat) {
                $q->where('candidat_id', $candidat->utilisateur_id);
            })
                ->where('type', 'profile_incomplete')
                ->where('is_dismissed', false)
                ->delete();
        }

        if ($this->isAccountNotVerified($candidat)) {
            $alerts[] = $this->createAccountVerificationAlert($candidat);
        } else {
            Alert::whereHas('candidature', function ($q) use ($candidat) {
                $q->where('candidat_id', $candidat->utilisateur_id);
            })
                ->where('type', 'account_not_verified')
                ->where('is_dismissed', false)
                ->delete();
        }

        foreach ($candidat->candidatures as $candidature) {
            $alerts = array_merge($alerts, $this->generateCandidatureAlerts($candidature));
        }

        $this->logger->logActivity('alerts_generated', 'alert', null, [
            'candidat_id' => $candidat->utilisateur_id,
            'alert_count' => count($alerts),
        ]);

        return $alerts;
    }

    /**
     * Générer les alertes pour une candidature
     *
     * @return array<Alert|null>
     */
    public function generateCandidatureAlerts(Candidature $candidature): array
    {
        $alerts = [];

        // NOTE: On vérifie TOUJOURS les documents et le paiement, même si le statut est VALIDE.
        // Cela permet de bloquer les actions (téléchargement convocation, etc.) tant que le dossier n'est pas complet.

        if (! $candidature->documents_complets) {
            $alerts[] = $this->createMissingDocumentsAlert($candidature);
        }

        if (! $candidature->paiement_valide) {
            $alerts[] = $this->createPaymentPendingAlert($candidature);
        }

        if ($this->isDeadlineApproaching($candidature)) {
            $alerts[] = $this->createDeadlineApproachingAlert($candidature);
        }

        if ($this->isDeadlinePassed($candidature)) {
            $alerts[] = $this->createDeadlinePassedAlert($candidature);
        }

        if ($this->areCentersNotSelected($candidature)) {
            $alerts[] = $this->createMissingCentersAlert($candidature);
        }

        if ($this->isConvocationAvailable($candidature)) {
            $alerts[] = $this->createConvocationAvailableAlert($candidature);
        }

        if ($this->isResultAvailable($candidature)) {
            $alerts[] = $this->createResultAvailableAlert($candidature);
        }

        return array_filter($alerts);
    }

    /**
     * Nettoyer les alertes obsolètes quand les conditions sont résolues
     *
     * @return int Nombre d'alertes supprimées
     */
    public function cleanObsoleteAlerts(Candidature $candidature): int
    {
        $typesToDelete = [];

        if ($candidature->documents_complets) {
            $typesToDelete[] = 'missing_documents';
        }

        if ($candidature->paiement_valide) {
            $typesToDelete[] = 'payment_pending';
        }

        if (! $this->areCentersNotSelected($candidature)) {
            $typesToDelete[] = 'missing_centers';
        }

        if ($candidature->candidat && $candidature->candidat->utilisateur && $candidature->candidat->utilisateur->hasVerifiedEmail()) {
            $typesToDelete[] = 'account_not_verified';
        }

        $profileCheck = CandidatureHelper::hasRequiredCandidateFields($candidature->id);
        if ($profileCheck['valid']) {
            $typesToDelete[] = 'profile_incomplete';
        }

        if (empty($typesToDelete)) {
            return 0;
        }

        return Alert::where('candidature_id', $candidature->id)
            ->whereIn('type', $typesToDelete)
            ->where('is_dismissed', false)
            ->delete();
    }

    /**
     * Vérifier si le profil du candidat est incomplet
     */
    private function isProfileIncomplete(Candidat $candidat): bool
    {
        $requiredFields = [
            'nom_cand',
            'prenom_cand',
            'date_naissance_cand',
            'lieu_naissance_cand',
            'sexe_cand',
            'nationalite_cand',
            'adresse_cand',
            'numero_cni',
        ];

        foreach ($requiredFields as $field) {
            if (empty($candidat->$field)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Créer une alerte de profil incomplet
     */
    private function createProfileIncompleteAlert(Candidat $candidat): ?Alert
    {
        $exists = Alert::whereHas('candidature', function ($q) use ($candidat) {
            $q->where('candidat_id', $candidat->utilisateur_id);
        })
            ->where('type', 'profile_incomplete')
            ->where('is_dismissed', false)
            ->exists();

        if ($exists) {
            return null;
        }

        $candidature = $candidat->candidatures()->first();
        if (! $candidature) {
            return null;
        }

        $alert = Alert::create([
            'candidature_id' => $candidature->id,
            'type' => 'profile_incomplete',
            'severity' => 'warning',
            'title' => 'Profil incomplet',
            'message' => 'Veuillez compléter votre profil avec toutes les informations requises (nom, prénom, date de naissance, CNI, etc.).',
        ]);

        $this->emailService->sendAlertEmail($alert, $candidat);

        return $alert;
    }

    /**
     * Créer une alerte de documents manquants
     */
    private function createMissingDocumentsAlert(Candidature $candidature): ?Alert
    {
        $exists = Alert::where('candidature_id', $candidature->id)
            ->where('type', 'missing_documents')
            ->where('is_dismissed', false)
            ->exists();

        if ($exists) {
            return null;
        }

        $alert = Alert::create([
            'candidature_id' => $candidature->id,
            'type' => 'missing_documents',
            'severity' => 'critical',
            'title' => 'Documents requis manquants',
            'message' => 'Certains documents requis n\'ont pas encore été déposés. Veuillez les télécharger avant la date limite.',
        ]);

        $this->emailService->sendAlertEmail($alert, $candidature->candidat);

        return $alert;
    }

    /**
     * Créer une alerte de paiement en attente
     */
    private function createPaymentPendingAlert(Candidature $candidature): ?Alert
    {
        $exists = Alert::where('candidature_id', $candidature->id)
            ->where('type', 'payment_pending')
            ->where('is_dismissed', false)
            ->exists();

        if ($exists) {
            return null;
        }

        $alert = Alert::create([
            'candidature_id' => $candidature->id,
            'type' => 'payment_pending',
            'severity' => 'critical',
            'title' => 'Paiement en attente de validation',
            'message' => 'Votre paiement est en cours de vérification. Vous serez notifié une fois validé.',
        ]);

        $this->emailService->sendAlertEmail($alert, $candidature->candidat);

        return $alert;
    }

    /**
     * Vérifier si la date limite approche (moins de 7 jours)
     */
    private function isDeadlineApproaching(Candidature $candidature): bool
    {
        if (! $candidature->concours || ! $candidature->concours->date_limite_depot) {
            return false;
        }

        $deadline = Carbon::parse($candidature->concours->date_limite_depot);
        $daysRemaining = now()->diffInDays($deadline, false);

        return $daysRemaining > 0 && $daysRemaining <= 7;
    }

    /**
     * Créer une alerte de date limite proche
     */
    private function createDeadlineApproachingAlert(Candidature $candidature): ?Alert
    {
        $exists = Alert::where('candidature_id', $candidature->id)
            ->where('type', 'deadline_approaching')
            ->where('is_dismissed', false)
            ->exists();

        if ($exists) {
            return null;
        }

        $deadline = Carbon::parse($candidature->concours->date_limite_depot);
        $daysRemaining = now()->diffInDays($deadline, false);

        $alert = Alert::create([
            'candidature_id' => $candidature->id,
            'type' => 'deadline_approaching',
            'severity' => 'warning',
            'title' => 'Date limite proche',
            'message' => "La date limite de dépôt est dans {$daysRemaining} jour(s). Assurez-vous que votre dossier est complet.",
        ]);

        $this->emailService->sendAlertEmail($alert, $candidature->candidat);

        return $alert;
    }

    /**
     * Vérifier si la date limite est dépassée
     */
    private function isDeadlinePassed(Candidature $candidature): bool
    {
        if (! $candidature->concours || ! $candidature->concours->date_limite_depot) {
            return false;
        }

        return now()->isAfter($candidature->concours->date_limite_depot);
    }

    /**
     * Créer une alerte de date limite dépassée
     */
    private function createDeadlinePassedAlert(Candidature $candidature): ?Alert
    {
        $exists = Alert::where('candidature_id', $candidature->id)
            ->where('type', 'deadline_passed')
            ->where('is_dismissed', false)
            ->exists();

        if ($exists) {
            return null;
        }

        $alert = Alert::create([
            'candidature_id' => $candidature->id,
            'type' => 'deadline_passed',
            'severity' => 'critical',
            'title' => 'Date limite dépassée',
            'message' => 'La date limite de dépôt est dépassée. Contactez l\'administration si nécessaire.',
        ]);

        $this->emailService->sendAlertEmail($alert, $candidature->candidat);

        return $alert;
    }

    /**
     * Vérifier si les centres ne sont pas sélectionnés
     */
    private function areCentersNotSelected(Candidature $candidature): bool
    {
        return empty($candidature->centre_examen_id) || empty($candidature->centre_depot_id);
    }

    /**
     * Créer une alerte de centres manquants
     */
    private function createMissingCentersAlert(Candidature $candidature): ?Alert
    {
        $exists = Alert::where('candidature_id', $candidature->id)
            ->where('type', 'missing_centers')
            ->where('is_dismissed', false)
            ->exists();

        if ($exists) {
            return null;
        }

        $alert = Alert::create([
            'candidature_id' => $candidature->id,
            'type' => 'missing_centers',
            'severity' => 'warning',
            'title' => 'Centres non sélectionnés',
            'message' => 'Veuillez sélectionner votre centre d\'examen et votre centre de dépôt de documents.',
        ]);

        $this->emailService->sendAlertEmail($alert, $candidature->candidat);

        return $alert;
    }

    /**
     * Vérifier si la convocation est disponible
     */
    private function isConvocationAvailable(Candidature $candidature): bool
    {
        return $candidature->convocation !== null && ! $candidature->convocation->est_telechargee;
    }

    /**
     * Créer une alerte de convocation disponible
     */
    private function createConvocationAvailableAlert(Candidature $candidature): ?Alert
    {
        $exists = Alert::where('candidature_id', $candidature->id)
            ->where('type', 'convocation_available')
            ->where('is_dismissed', false)
            ->exists();

        if ($exists) {
            return null;
        }

        $alert = Alert::create([
            'candidature_id' => $candidature->id,
            'type' => 'convocation_available',
            'severity' => 'info',
            'title' => 'Convocation disponible',
            'message' => 'Votre convocation est disponible. Téléchargez-la et présentez-la le jour de l\'examen.',
        ]);

        $this->emailService->sendAlertEmail($alert, $candidature->candidat);

        return $alert;
    }

    /**
     * Vérifier si le résultat est disponible
     */
    private function isResultAvailable(Candidature $candidature): bool
    {
        return $candidature->resultatFinal !== null
          && $candidature->resultatFinal->date_publication !== null;
    }

    /**
     * Créer une alerte de résultat disponible
     */
    private function createResultAvailableAlert(Candidature $candidature): ?Alert
    {
        $exists = Alert::where('candidature_id', $candidature->id)
            ->where('type', 'result_available')
            ->where('is_dismissed', false)
            ->exists();

        if ($exists) {
            return null;
        }

        $decision = $candidature->resultatFinal->decision ?? 'REFUSEE';
        $severity = $decision === 'ADMIS' ? 'info' : 'warning';

        $alert = Alert::create([
            'candidature_id' => $candidature->id,
            'type' => 'result_available',
            'severity' => $severity,
            'title' => 'Résultats publiés',
            'message' => 'Les résultats du concours sont disponibles. Consultez votre espace candidat.',
        ]);

        $this->emailService->sendAlertEmail($alert, $candidature->candidat);

        return $alert;
    }

    /**
     * Vérifier si le compte n'est pas vérifié
     */
    private function isAccountNotVerified(Candidat $candidat): bool
    {
        return $candidat->utilisateur && ! $candidat->utilisateur->hasVerifiedEmail();
    }

    /**
     * Créer une alerte de compte non vérifié
     */
    private function createAccountVerificationAlert(Candidat $candidat): ?Alert
    {
        $exists = Alert::whereHas('candidature', function ($q) use ($candidat) {
            $q->where('candidat_id', $candidat->utilisateur_id);
        })
            ->where('type', 'account_not_verified')
            ->where('is_dismissed', false)
            ->exists();

        if ($exists) {
            return null;
        }

        $candidature = $candidat->candidatures()->latest()->first();
        if (! $candidature) {
            return null;
        }

        $alert = Alert::create([
            'candidature_id' => $candidature->id,
            'type' => 'account_not_verified',
            'severity' => 'warning',
            'title' => 'Compte non vérifié',
            'message' => 'Votre adresse email n\'est pas vérifiée. Vous ne recevrez aucune notification ni confirmation par mail tant que ce n\'est pas fait. Vérifiez vos spams !',
        ]);

        // Send email (even if unverified, to try to reach them)
        $this->emailService->sendAlertEmail($alert, $candidat);

        return $alert;
    }
}
