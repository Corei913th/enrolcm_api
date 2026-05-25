<?php

namespace App\Http\Controllers\Planning;

use App\Exceptions\PlanningException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Planning\PlanifierEpreuveRequest;
use App\Http\Requests\Planning\UpdatePlanningRequest;
use App\Services\Domain\Examen\PlanningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanningEpreuveController extends Controller
{
    public function __construct(
        private readonly PlanningService $planningService
    ) {}

    /**
     * Liste des plannings avec query parameters (pour compatibilité frontend).
     *
     * GET /api/admin/examen/planning?concours_id=X&session_id=Y
     */
    public function indexWithQuery(Request $request): JsonResponse
    {
        $request->validate([
            'concours_id' => 'required|string',
            'session_id' => 'required|string',
        ]);

        return $this->index($request->concours_id, $request->session_id);
    }

    /**
     * Liste des plannings pour un concours/session.
     *
     * GET /api/admin/examen/planning/concours/{concoursId}/sessions/{sessionId}
     */
    public function index(string $concoursId, string $sessionId): JsonResponse
    {
        try {
            $plannings = $this->planningService->getSchedulesByConcoursSession($concoursId, $sessionId);

            return api_success($plannings, 'Liste des plannings');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Planifier une épreuve.
     *
     * POST /api/admin/examen/planning
     */
    public function store(PlanifierEpreuveRequest $request): JsonResponse
    {
        try {
            $planning = $this->planningService->scheduleExam($request->validated());

            return api_created($planning, 'Épreuve planifiée avec succès');
        } catch (PlanningException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Détails d'un planning.
     *
     * GET /api/admin/examen/planning/{planningId}
     */
    public function show(string $planningId): JsonResponse
    {
        try {
            $planning = $this->planningService->getScheduleById($planningId);

            return api_success($planning, 'Détails du planning');
        } catch (PlanningException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Mettre à jour un planning.
     *
     * PUT /api/admin/examen/planning/{planningId}
     */
    public function update(string $planningId, UpdatePlanningRequest $request): JsonResponse
    {
        try {
            $planning = $this->planningService->updateSchedule($planningId, $request->validated());

            return api_success($planning, 'Planning mis à jour avec succès');
        } catch (PlanningException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Supprimer un planning.
     *
     * DELETE /api/admin/examen/planning/{planningId}
     */
    public function destroy(string $planningId): JsonResponse
    {
        try {
            $this->planningService->deleteSchedule($planningId);

            return api_success(null, 'Planning supprimé avec succès');
        } catch (PlanningException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }
}
