<?php

namespace App\Services\Domain\Notification;

use App\Models\Alert;
use App\Traits\HasActivityLogger;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use Illuminate\Pagination\LengthAwarePaginator;

class AlertService
{
  use HasActivityLogger;

  public function __construct(ActivityLoggerService $logger)
  {
    $this->logger = $logger;
  }
  /**
   * Récupérer toutes les alertes d'un candidat
   */
  public function getAlertsByCandidat(string $candidatId, array $filters = []): LengthAwarePaginator
  {
    $query = Alert::whereHas('candidature', function ($q) use ($candidatId) {
      $q->where('candidat_id', $candidatId);
    })
      ->with('candidature:id,concours_id,code_cand_def')
      ->orderBy('created_at', 'desc');

    // Filtres
    if (isset($filters['is_dismissed'])) {
      $query->where('is_dismissed', $filters['is_dismissed']);
    }

    if (isset($filters['severity'])) {
      $query->where('severity', $filters['severity']);
    }

    return $query->paginate($filters['per_page'] ?? 20);
  }

  /**
   * Marquer une alerte comme lue
   */
  public function dismissAlert(Alert $alert): Alert
  {
    $alert->update([
      'is_dismissed' => true,
      'dismissed_at' => now(),
    ]);

    $this->logOperation('dismiss_alert', 'alert', $alert->id);

    return $alert->fresh();
  }

  /**
   * Marquer toutes les alertes d'un candidat comme lues
   */
  public function dismissAllAlerts(string $candidatId): int
  {
    $count = Alert::whereHas('candidature', function ($query) use ($candidatId) {
      $query->where('candidat_id', $candidatId);
    })
      ->where('is_dismissed', false)
      ->update([
        'is_dismissed' => true,
        'dismissed_at' => now(),
      ]);

    $this->logOperation('dismiss_all_alerts', 'candidat', $candidatId, ['count' => $count]);

    return $count;
  }

  /**
   * Vérifier qu'une alerte appartient au candidat
   */
  public function alertBelongsToCandidat(Alert $alert, string $candidatId): bool
  {
    return $alert->candidature && $alert->candidature->candidat_id === $candidatId;
  }
}
