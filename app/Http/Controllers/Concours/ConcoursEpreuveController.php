<?php

namespace App\Http\Controllers\Concours;

use App\Http\Controllers\Controller;
use App\Services\Domain\Examen\EpreuveService;
use App\Http\Resources\EpreuveResource;
use Illuminate\Http\JsonResponse;

class ConcoursEpreuveController extends Controller
{
  public function __construct(
    private readonly EpreuveService $epreuveService
  ) {}

  /**
   * Liste des épreuves attachées à un concours.
   *
   * GET /api/admin/concours/{concoursId}/epreuves
   */
  public function index(string $concoursId): JsonResponse
  {
    try {
      $epreuves = $this->epreuveService->getEpreuvesByConcours($concoursId);
      return api_success(EpreuveResource::collection($epreuves), 'Liste des épreuves');
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Liste des épreuves pour un concours à une session spécifique.
   *
   * GET /api/admin/concours/{concoursId}/sessions/{sessionId}/epreuves
   */
  public function indexBySession(string $concoursId, string $sessionId): JsonResponse
  {
    try {
      $epreuves = $this->epreuveService->getEpreuvesByConcoursSession($concoursId, $sessionId);
      return api_success(EpreuveResource::collection($epreuves), 'Liste des épreuves');
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Liste des épreuves disponibles (non attachées) pour un concours.
   *
   * GET /api/admin/concours/{concoursId}/epreuves/disponibles
   */
  public function disponibles(string $concoursId): JsonResponse
  {
    try {
      $epreuves = $this->epreuveService->getEpreuvesDisponibles($concoursId);
      return api_success(EpreuveResource::collection($epreuves), 'Liste des épreuves disponibles');
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Attacher une épreuve à un concours (crée un planning).
   *
   * POST /api/admin/concours/{concoursId}/epreuves
   */
  public function attach(string $concoursId, \Illuminate\Http\Request $request): JsonResponse
  {
    $request->validate([
      'epreuve_id' => 'required|exists:epreuves,id_epreuve',
      'date_epreuve' => 'nullable|date|after_or_equal:today',
      'heure_debut' => 'nullable|date_format:H:i',
      'heure_fin' => 'nullable|date_format:H:i|after:heure_debut',
    ]);

    try {
      $epreuve = $this->epreuveService->attachEpreuveToConcours(
        $concoursId,
        $request->input('epreuve_id'),
        $request->input('date_epreuve'),
        $request->input('heure_debut'),
        $request->input('heure_fin')
      );
      return api_success(new EpreuveResource($epreuve), 'Épreuve planifiée avec succès');
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Détacher une épreuve d'un concours.
   *
   * DELETE /api/admin/concours/{concoursId}/epreuves/{epreuveId}
   */
  public function detach(string $concoursId, string $epreuveId): JsonResponse
  {
    try {
      $this->epreuveService->detachEpreuveFromConcours($concoursId, $epreuveId);
      return api_success(null, 'Épreuve détachée avec succès');
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Mettre à jour les paramètres de planning d'une épreuve.
   *
   * PUT /api/admin/concours/{concoursId}/epreuves/{epreuveId}
   */
  public function updateParams(string $concoursId, string $epreuveId, \Illuminate\Http\Request $request): JsonResponse
  {
    $request->validate([
      'date_epreuve' => 'nullable|date|after_or_equal:today',
      'heure_debut' => 'nullable|date_format:H:i',
      'heure_fin' => 'nullable|date_format:H:i|after:heure_debut',
    ]);

    try {
      $epreuve = $this->epreuveService->updateEpreuveParams(
        $concoursId,
        $epreuveId,
        $request->only(['date_epreuve', 'heure_debut', 'heure_fin'])
      );
      return api_success(new EpreuveResource($epreuve), 'Paramètres de l\'épreuve mis à jour');
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }
}
