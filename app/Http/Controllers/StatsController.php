<?php

namespace App\Http\Controllers;

use App\Services\StatsService;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function __construct(
        private readonly StatsService $statsService
    ) {}

    /**
     * Get stats per Filiere
     */
    public function filieres(): JsonResponse
    {
        $data = $this->statsService->getFilieresStats();

        return api_success($data, 'Statistiques des filières récupérées');
    }

    /**
     * Get stats per Niveau
     */
    public function niveaux(): JsonResponse
    {
        $data = $this->statsService->getNiveauxStats();

        return api_success($data, 'Statistiques des niveaux récupérées');
    }

    /**
     * Get stats per Departement
     */
    public function departements(): JsonResponse
    {
        $data = $this->statsService->getDepartementsStats();

        return api_success($data, 'Statistiques des départements récupérées');
    }

    /**
     * Get stats per Ecole
     */
    public function ecoles(): JsonResponse
    {
        $data = $this->statsService->getEcolesStats();

        return api_success($data, 'Statistiques des écoles récupérées');
    }

    /**
     * Get stats per Centre
     */
    public function centres(): JsonResponse
    {
        $data = $this->statsService->getCentresStats();

        return api_success($data, 'Statistiques par centre récupérées');
    }
}
