<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Application\Dashboard\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    /**
     * Statistiques globales du système (Super Admin).
     */
    public function globalStats(): JsonResponse
    {
        $stats = $this->dashboardService->getGlobalStats();

        return api_success($stats, 'Statistiques globales récupérées');
    }

    /**
     * Statistiques d'une école spécifique (Admin École).
     */
    public function ecoleStats(string $ecoleId): JsonResponse
    {
        $stats = $this->dashboardService->getEcoleStats($ecoleId);

        return api_success($stats, 'Statistiques de l\'école récupérées');
    }

    /**
     * Statistiques de l'école de l'admin connecté.
     */
    public function myEcoleStats(Request $request): JsonResponse
    {
        $user = $request->user();
        $admin = $user->admin;

        if (! $admin || ! $admin->ecole_id) {
            return api_error('Aucune école assignée à cet administrateur', null, 403);
        }

        $stats = $this->dashboardService->getEcoleStats($admin->ecole_id);

        return api_success($stats, 'Statistiques de votre école récupérées');
    }
}
