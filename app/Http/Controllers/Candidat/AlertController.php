<?php

namespace App\Http\Controllers\Candidat;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Services\Domain\Notification\AlertService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlertController extends Controller
{
  public function __construct(
    private readonly AlertService $alertService
  ) {}

  /**
   * Liste des alertes du candidat
   */
  public function index(Request $request): JsonResponse
  {
    try {
      $candidat = $request->user()->candidat;

      $alerts = $this->alertService->getAlertsByCandidat(
        $candidat->utilisateur_id,
        $request->only(['is_dismissed', 'severity', 'per_page'])
      );

      return api_success($alerts, 'Notifications récupérées avec succès');
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Marquer une alerte comme lue
   */
  public function dismiss(Request $request, string $alertId): JsonResponse
  {
    try {
      $candidat = $request->user()->candidat;
      $alert = Alert::findOrFail($alertId);

      if (!$this->alertService->alertBelongsToCandidat($alert, $candidat->utilisateur_id)) {
        return api_error('Accès non autorisé', null, 403);
      }

      $updatedAlert = $this->alertService->dismissAlert($alert);

      return api_success($updatedAlert, 'Notification marquée comme lue');
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Marquer toutes les alertes comme lues
   */
  public function dismissAll(Request $request): JsonResponse
  {
    try {
      $candidat = $request->user()->candidat;
      $count = $this->alertService->dismissAllAlerts($candidat->utilisateur_id);

      return api_success(['count' => $count], 'Toutes les notifications ont été marquées comme lues');
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }
}
