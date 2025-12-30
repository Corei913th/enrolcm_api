<?php

namespace App\Http\Controllers\Candidats;

use App\Http\Controllers\Controller;
use App\Services\Candidats\CandidatService;
use App\Http\Requests\Candidats\VerifyPRURequest;
use App\Http\Requests\Candidats\RegisterCandidatRequest;
use App\Http\Requests\Candidats\LoginCandidatRequest;
use App\Http\Requests\Candidats\UpdateCandidatProfileRequest;
use App\DTOs\Candidats\VerifyPRUDTO;
use App\DTOs\Candidats\RegisterCandidatDTO;
use App\DTOs\Candidats\LoginCandidatDTO;
use App\DTOs\Candidats\UpdateCandidatProfileDTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CandidatController extends Controller
{
    public function __construct(
        private readonly CandidatService $candidatService
    ) {}

    /**
     * Vérifier PRU (PUBLIC - avant création compte)
     * POST /api/candidates/verify-pru
     */
    public function verifyPRU(VerifyPRURequest $request): JsonResponse
    {
        try {
            $dto = VerifyPRUDTO::fromRequest($request->validated());
            $result = $this->candidatService->verifyPRU($dto);

            if ($result['valid']) {
                return api_success($result, 'PRU valide');
            }

            return api_error($result['message'], null, 400);
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Créer compte candidat (PUBLIC - après paiement validé)
     * POST /api/candidates/register
     */
    public function register(RegisterCandidatRequest $request): JsonResponse
    {
        try {
            $dto = RegisterCandidatDTO::fromRequest($request->validated());
            $result = $this->candidatService->register($dto);

            return api_created($result, 'Compte créé avec succès');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Login candidat (PUBLIC)
     * POST /api/candidates/login
     */
    public function login(LoginCandidatRequest $request): JsonResponse
    {
        try {
            $dto = LoginCandidatDTO::fromRequest($request->validated());
            $result = $this->candidatService->login($dto);

            return api_success($result, 'Connexion réussie');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 401);
        }
    }

    /**
     * Profil du candidat connecté
     * GET /api/candidates/me
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $candidat = $this->candidatService->getById($request->user()->id);
            return api_success($candidat);
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 404);
        }
    }

    /**
     * Mettre à jour profil
     * PUT /api/candidates/me
     */
    public function updateProfile(UpdateCandidatProfileRequest $request): JsonResponse
    {
        try {
            $dto = UpdateCandidatProfileDTO::fromRequest($request->validated());
            $candidat = $this->candidatService->updateProfile($request->user()->id, $dto);

            return api_success($candidat, 'Profil mis à jour avec succès');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Liste des candidats (ADMIN)
     * GET /api/candidates
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'region', 'est_actif']);
        $perPage = $request->input('per_page', 20);

        $candidats = $this->candidatService->getAll($filters, $perPage);

        return api_paginated($candidats, 'Liste des candidats');
    }

    /**
     * Détails d'un candidat (ADMIN)
     * GET /api/candidates/{id}
     */
    public function show(string $id): JsonResponse
    {
        try {
            $candidat = $this->candidatService->getById($id);
            return api_success($candidat);
        } catch (\Exception $e) {
            return api_error('Candidat introuvable', null, 404);
        }
    }

    /**
     * Statistiques candidats (ADMIN)
     * GET /api/candidates/stats
     */
    public function stats(): JsonResponse
    {
        $stats = $this->candidatService->getStats();
        return api_success($stats);
    }

    /**
     * Désactiver un candidat (ADMIN)
     * POST /api/candidates/{id}/deactivate
     */
    public function deactivate(string $id): JsonResponse
    {
        try {
            $this->candidatService->deactivate($id);
            return api_success(null, 'Candidat désactivé avec succès');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Activer un candidat (ADMIN)
     * POST /api/candidates/{id}/activate
     */
    public function activate(string $id): JsonResponse
    {
        try {
            $this->candidatService->activate($id);
            return api_success(null, 'Candidat activé avec succès');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Récupérer candidat par PRU (ADMIN)
     * GET /api/candidates/pru/{pru}
     */
    public function getByPRU(string $pru): JsonResponse
    {
        try {
            $candidat = $this->candidatService->getByPRU($pru);
            return api_success($candidat);
        } catch (\Exception $e) {
            return api_error('Candidat introuvable', null, 404);
        }
    }
}
