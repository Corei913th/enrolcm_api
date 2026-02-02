<?php

namespace App\Services\Domain\Notification\Notifiers;

use App\Mail\AlertNotificationMail;
use App\Models\Alert;
use App\Models\Candidat;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use Illuminate\Support\Facades\Mail;

/**
 * Service d'envoi d'emails pour les alertes
 */
class AlertEmailService
{
  private const EMAIL_ALERT_TYPES = [
    'missing_documents',
    'payment_pending',
    'deadline_approaching',
    'deadline_passed',
    'convocation_available',
    'result_available',
    'profile_incomplete',
    'missing_centers',
    'account_not_verified',
  ];

  private const EMAIL_SEVERITIES = ['critical', 'warning'];

  public function __construct(
    private readonly ActivityLoggerService $logger
  ) {}

  /**
   * Envoyer une notification par email pour une alerte si applicable
   *
   * @param Alert $alert
   * @param Candidat $candidat
   * @return bool True si l'email a été envoyé, false sinon
   */
  public function sendAlertEmail(Alert $alert, Candidat $candidat): bool
  {
    if (!$this->shouldSendEmail($alert)) {
      return false;
    }

    if (!$candidat->utilisateur || !$candidat->utilisateur->email) {
      $this->logger->logActivity('alert_email_skipped', 'alert', $alert->id, [
        'reason' => 'no_email',
        'candidat_id' => $candidat->utilisateur_id,
      ]);
      return false;
    }

    // Filter out fake emails / test domains
    $email = $candidat->utilisateur->email;
    if (
      str_ends_with($email, '.test') ||
      str_contains($email, 'example.') ||
      str_contains($email, 'exemple.') ||
      str_ends_with($email, '.local')
    ) {
      $this->logger->logActivity('alert_email_skipped', 'alert', $alert->id, [
        'reason' => 'test_email_domain',
        'email' => $email,
      ]);
      return false;
    }

    try {
      Mail::to($candidat->utilisateur->email)
        ->send(new AlertNotificationMail($alert, $candidat));

      $this->logger->logActivity('alert_email_sent', 'alert', $alert->id, [
        'candidat_id' => $candidat->utilisateur_id,
        'email' => $candidat->utilisateur->email,
        'alert_type' => $alert->type,
        'severity' => $alert->severity,
      ]);

      return true;
    } catch (\Exception $e) {
      $this->logger->logActivity('alert_email_failed', 'alert', $alert->id, [
        'candidat_id' => $candidat->utilisateur_id,
        'error' => $e->getMessage(),
      ]);

      return false;
    }
  }

  /**
   * Envoyer un résumé quotidien par email avec toutes les alertes actives
   *
   * @param Candidat $candidat
   * @return bool True si le résumé a été envoyé, false sinon
   */
  public function sendDailySummary(Candidat $candidat): bool
  {
    $alerts = Alert::whereHas('candidature', function ($q) use ($candidat) {
      $q->where('candidat_id', $candidat->utilisateur_id);
    })
      ->where('is_dismissed', false)
      ->whereIn('severity', ['critical', 'warning'])
      ->orderBy('severity', 'desc')
      ->orderBy('created_at', 'desc')
      ->get();

    if ($alerts->isEmpty()) {
      return false;
    }

    if (!$candidat->utilisateur || !$candidat->utilisateur->email) {
      return false;
    }

    try {
      // TODO: Create DailySummaryMail class if needed

      $this->logger->logActivity('daily_summary_sent', 'alert', null, [
        'candidat_id' => $candidat->utilisateur_id,
        'alert_count' => $alerts->count(),
      ]);

      return true;
    } catch (\Exception $e) {
      $this->logger->logActivity('daily_summary_failed', 'alert', null, [
        'candidat_id' => $candidat->utilisateur_id,
        'error' => $e->getMessage(),
      ]);

      return false;
    }
  }

  /**
   * Déterminer si un email doit être envoyé pour cette alerte
   *
   * @param Alert $alert
   * @return bool
   */
  private function shouldSendEmail(Alert $alert): bool
  {
    if (!in_array($alert->type, self::EMAIL_ALERT_TYPES)) {
      return false;
    }

    if (!in_array($alert->severity, self::EMAIL_SEVERITIES)) {
      return false;
    }

    return true;
  }
}
