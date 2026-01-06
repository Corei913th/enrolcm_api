<?php

namespace App\Http\Controllers\Concours;

use App\Http\Controllers\Controller;
use App\Services\Concours\SalleAffectationService;
use App\Http\Requests\Concours\AffecterSallesRequest;
use App\Http\Requests\Concours\ReaffecterCandidatRequest;
use App\Http\Requests\Concours\MarquerPresentRequest;
use App\Models\CandidatureSalle;
use App\Exceptions\ConcoursException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class SalleAffectationController extends Controller
{
  public function __construct(
    private readonly SalleAffectationService $affectationService
  ) {}

  /**
   * Affecter automatiquement les candidats aux salles pour une épreuve.
   *
   * Endpoint : POST /api/concours/{concoursId}/sessions/{sessionId}/planning/{planningId}/affecter-salles
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param string $planningId ID du planning d'épreuve
   * @param AffecterSallesRequest $request Requête validée
   *
   * @return JsonResponse Statistiques de l'affectation
   */
  public function affecterSalles(string $concoursId, string $sessionId, string $planningId, AffecterSallesRequest $request): JsonResponse
  {
    try {
      $stats = $this->affectationService->affecterCandidatsSalle(
        $planningId,
        $request->ordre_affectation ?? 'alphabetique'
      );

      return api_success($stats, 'Affectation aux salles terminée avec succès');
    } catch (ConcoursException $e) {
      return api_error($e->getMessage(), null, $e->getCode());
    }
  }

  /**
   * Réaffecter un candidat à une autre salle.
   *
   * Endpoint : PUT /api/concours/{concoursId}/sessions/{sessionId}/affectations/{affectationId}/reaffecter
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param string $affectationId ID de l'affectation
   * @param ReaffecterCandidatRequest $request Requête validée
   *
   * @return JsonResponse Affectation mise à jour
   */
  public function reaffecterCandidat(string $concoursId, string $sessionId, string $affectationId, ReaffecterCandidatRequest $request): JsonResponse
  {
    try {
      $affectation = $this->affectationService->reaffecterCandidat(
        $affectationId,
        $request->nouvelle_salle_id,
        $request->nouveau_numero_place
      );

      return api_success([
        'affectation' => $affectation->load(['candidature.candidat', 'salle']),
      ], 'Candidat réaffecté avec succès');
    } catch (ConcoursException $e) {
      return api_error($e->getMessage(), null, $e->getCode());
    }
  }

  /**
   * Marquer un candidat comme présent à l'examen.
   *
   * Endpoint : PUT /api/concours/{concoursId}/sessions/{sessionId}/affectations/{affectationId}/present
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param string $affectationId ID de l'affectation
   * @param MarquerPresentRequest $request Requête validée
   *
   * @return JsonResponse Affectation mise à jour
   */
  public function marquerPresent(string $concoursId, string $sessionId, string $affectationId, MarquerPresentRequest $request): JsonResponse
  {
    try {
      $affectation = $this->affectationService->marquerPresent(
        $affectationId,
        $request->heure_arrivee,
        $request->observations
      );

      return api_success([
        'affectation' => $affectation->load(['candidature.candidat', 'salle']),
      ], 'Présence marquée avec succès');
    } catch (ConcoursException $e) {
      return api_error($e->getMessage(), null, $e->getCode());
    }
  }

  /**
   * Obtenir le plan de salle pour une épreuve.
   *
   * Endpoint : GET /api/concours/{concoursId}/sessions/{sessionId}/planning/{planningId}/plan-salle
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param string $planningId ID du planning d'épreuve
   *
   * @return JsonResponse Plan de salle
   */
  public function getPlanSalle(string $concoursId, string $sessionId, string $planningId): JsonResponse
  {
    try {
      $plan = $this->affectationService->getPlanSalle($planningId);
      return api_success($plan, 'Plan de salle récupéré avec succès');
    } catch (ConcoursException $e) {
      return api_error($e->getMessage(), null, $e->getCode());
    }
  }

  /**
   * Obtenir les statistiques d'affectation pour une épreuve.
   *
   * Endpoint : GET /api/concours/{concoursId}/sessions/{sessionId}/planning/{planningId}/stats-affectation
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param string $planningId ID du planning d'épreuve
   *
   * @return JsonResponse Statistiques d'affectation
   */
  public function getStatistiquesAffectation(string $concoursId, string $sessionId, string $planningId): JsonResponse
  {
    try {
      $stats = $this->affectationService->getStatistiquesAffectation($planningId);
      return api_success($stats, 'Statistiques d\'affectation récupérées avec succès');
    } catch (ConcoursException $e) {
      return api_error($e->getMessage(), null, $e->getCode());
    }
  }

  /**
   * Lister toutes les affectations pour une épreuve.
   *
   * Endpoint : GET /api/concours/{concoursId}/sessions/{sessionId}/planning/{planningId}/affectations
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param string $planningId ID du planning d'épreuve
   * @param Request $request Paramètres de filtrage et pagination
   *
   * @return JsonResponse Liste des affectations
   */
  public function index(string $concoursId, string $sessionId, string $planningId, Request $request): JsonResponse
  {
    try {
      $query = CandidatureSalle::where('planning_epreuve_id', $planningId)
        ->with([
          'candidature.candidat',
          'salle',
          'planningEpreuve.epreuve'
        ]);

      // Filtres
      if ($request->has('salle_id')) {
        $query->where('salle_id', $request->salle_id);
      }

      if ($request->has('est_present')) {
        $query->where('est_present', $request->boolean('est_present'));
      }

      // Tri
      $query->orderBy('salle.numero_salle')
        ->orderBy('numero_place');

      // Pagination
      $perPage = $request->input('per_page', 50);
      $affectations = $query->paginate($perPage);

      return api_paginated($affectations, 'Affectations récupérées avec succès');
    } catch (\Exception $e) {
      return api_error('Erreur lors de la récupération des affectations: ' . $e->getMessage(), null, 500);
    }
  }
}
