<?php

namespace App\Http\Controllers\Admin\Stats;

use App\Http\Controllers\Controller;
use App\Services\Application\Stats\StatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsController extends Controller
{
  public function __construct(
    private readonly StatsService $statsService
  ) {}

  /**
   * Statistiques globales (écoles, départements, filières, niveaux)
   * GET /api/admin/stats/global
   */
  public function global(): JsonResponse
  {
    $stats = $this->statsService->getGlobalStats();

    return api_success($stats, 'Statistiques globales récupérées avec succès');
  }

  /**
   * Statistiques des écoles
   * GET /api/admin/stats/ecoles
   */
  public function ecolesStats(): JsonResponse
  {
    $stats = $this->statsService->getEcolesStats();

    return api_success($stats, 'Statistiques des écoles récupérées avec succès');
  }

  /**
   * Statistiques des départements
   * GET /api/admin/stats/departements
   */
  public function departementsStats(): JsonResponse
  {
    $stats = $this->statsService->getDepartementsStats();

    return api_success($stats, 'Statistiques des départements récupérées avec succès');
  }

  /**
   * Statistiques des filières
   * GET /api/admin/stats/filieres
   */
  public function filieresStats(): JsonResponse
  {
    $stats = $this->statsService->getFilieresStats();

    return api_success($stats, 'Statistiques des filières récupérées avec succès');
  }

  /**
   * Statistiques des niveaux
   * GET /api/admin/stats/niveaux
   */
  public function niveauxStats(): JsonResponse
  {
    $stats = $this->statsService->getNiveauxStats();

    return api_success($stats, 'Statistiques des niveaux récupérées avec succès');
  }

  /**
   * Statistiques des centres
   * GET /api/admin/stats/centres-stats
   */
  public function centresStats(): JsonResponse
  {
    $stats = $this->statsService->getCentresStats();

    return api_success($stats, 'Statistiques des centres récupérées avec succès');
  }

  /**
   * Statistiques des concours (globales)
   * GET /api/admin/stats/concours-stats
   */
  public function concoursStats(): JsonResponse
  {
    $stats = $this->statsService->getConcoursStatsGlobalDetailed();

    return api_success($stats, 'Statistiques des concours récupérées avec succès');
  }

  /**
   * Statistiques globales pour le dashboard
   * GET /api/admin/stats/dashboard
   */
  public function dashboard(): JsonResponse
  {
    $stats = $this->statsService->getDashboardStats();

    return api_success($stats, 'Statistiques du dashboard récupérées avec succès');
  }

  /**
   * Statistiques détaillées pour un concours
   * GET /api/admin/stats/concours/{concoursId}
   */
  public function concours(string $concoursId): JsonResponse
  {
    $stats = $this->statsService->getConcoursStats($concoursId);

    return api_success($stats, 'Statistiques du concours récupérées avec succès');
  }

  /**
   * Statistiques par centre d'examen
   * GET /api/admin/stats/centres
   */
  public function centres(Request $request): JsonResponse
  {
    $stats = $this->statsService->getStatsByCentre(
      $request->query('concours_id'),
      $request->query('session_id')
    );

    return api_success($stats, 'Statistiques par centre récupérées avec succès');
  }

  /**
   * Statistiques par région
   * GET /api/admin/stats/regions
   */
  public function regions(Request $request): JsonResponse
  {
    $stats = $this->statsService->getStatsByRegionGlobal(
      $request->query('concours_id'),
      $request->query('session_id')
    );

    return api_success($stats, 'Statistiques par région récupérées avec succès');
  }

  /**
   * Widgets pour le dashboard
   * GET /api/admin/stats/widgets
   */
  public function widgets(): JsonResponse
  {
    $widgets = $this->statsService->getWidgets();

    return api_success($widgets, 'Widgets récupérés avec succès');
  }

  /**
   * Statistiques par école
   * GET /api/admin/stats/ecoles
   */
  public function ecoles(Request $request): JsonResponse
  {
    $stats = $this->statsService->getStatsByEcole(
      $request->query('session_id')
    );

    return api_success($stats, 'Statistiques par école récupérées avec succès');
  }

  /**
   * Statistiques des paiements
   * GET /api/admin/stats/paiements
   */
  public function paiements(Request $request): JsonResponse
  {
    $stats = $this->statsService->getStatsPaiements(
      $request->query('date_debut'),
      $request->query('date_fin'),
      $request->query('concours_id')
    );

    return api_success($stats, 'Statistiques des paiements récupérées avec succès');
  }

  /**
   * Statistiques des documents
   * GET /api/admin/stats/documents
   */
  public function documents(Request $request): JsonResponse
  {
    $stats = $this->statsService->getStatsDocuments(
      $request->query('concours_id')
    );

    return api_success($stats, 'Statistiques des documents récupérées avec succès');
  }

  /**
   * Statistiques temporelles (timeline)
   * GET /api/admin/stats/timeline
   */
  public function timeline(Request $request): JsonResponse
  {
    $stats = $this->statsService->getStatsTimeline(
      $request->query('concours_id'),
      $request->query('date_debut'),
      $request->query('date_fin'),
      $request->query('granularite', 'jour')
    );

    return api_success($stats, 'Statistiques temporelles récupérées avec succès');
  }

  /**
   * Statistiques comparatives entre sessions
   * GET /api/admin/stats/comparatives
   */
  public function comparatives(Request $request): JsonResponse
  {
    $sessionActuelle = $request->query('session_actuelle');
    $sessionPrecedente = $request->query('session_precedente');

    if (!$sessionActuelle || !$sessionPrecedente) {
      return api_error('Les paramètres session_actuelle et session_precedente sont requis', 400);
    }

    $stats = $this->statsService->getStatsComparatives($sessionActuelle, $sessionPrecedente);

    return api_success($stats, 'Statistiques comparatives récupérées avec succès');
  }
}
