<?php

namespace App\Http\Controllers\Candidats;

use App\DTOs\Candidats\LoginCandidatDTO;
use App\DTOs\Candidats\RegisterCandidatDTO;
use App\DTOs\Candidats\UpdateCandidatProfileDTO;
use App\DTOs\Candidats\VerifyPRUDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Candidats\LoginCandidatRequest;
use App\Http\Requests\Candidats\RegisterCandidatRequest;
use App\Http\Requests\Candidats\UpdateCandidatProfileRequest;
use App\Http\Requests\Candidats\VerifyPRURequest;
use App\Services\Domain\Candidat\CandidatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CandidatController extends Controller
{
    public function __construct(
        private readonly CandidatService $candidatService
    ) {}

    /**
     * Vérifier PRU (PUBLIC).
     */
    public function verifyPRU(VerifyPRURequest $request): JsonResponse
    {
        $dto = VerifyPRUDTO::fromRequest($request->validated());
        $result = $this->candidatService->verifyPRU($dto);

        if ($result['valid']) {
            return api_success($result, 'PRU valide');
        }

        return api_error($result['message'], null, 400);
    }

    /**
     * Créer compte candidat.
     */
    public function register(RegisterCandidatRequest $request): JsonResponse
    {
        $dto = RegisterCandidatDTO::fromRequest($request->validated());
        $result = $this->candidatService->register($dto);

        return api_created($result, 'Compte créé avec succès');
    }

    /**
     * Login candidat.
     */
    public function login(LoginCandidatRequest $request): JsonResponse
    {
        $dto = LoginCandidatDTO::fromRequest($request->validated());
        $result = $this->candidatService->login($dto);

        return api_success($result, 'Connexion réussie');
    }

    /**
     * Profil du candidat connecté.
     */
    public function me(Request $request): JsonResponse
    {
        $candidat = $this->candidatService->getByUserId($request->user()->id);

        return api_success($candidat);
    }

    /**
     * Mettre à jour profil du candidat connecté.
     */
    public function updateProfile(UpdateCandidatProfileRequest $request): JsonResponse
    {
        $dto = UpdateCandidatProfileDTO::fromRequest($request->validated());
        $candidat = $this->candidatService->updateProfile($request->user()->id, $dto);

        return api_success($candidat, 'Profil mis à jour avec succès');
    }

    /**
     * Liste des candidats (ADMIN).
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'region', 'est_actif']);
        $perPage = $request->input('per_page', 20);

        $candidats = $this->candidatService->getAll($filters, $perPage);

        return api_paginated($candidats, 'Liste des candidats');
    }

    /**
     * Détails d'un candidat (ADMIN).
     */
    public function show(string $id): JsonResponse
    {
        $candidat = $this->candidatService->getByUserId($id);

        return api_success($candidat);
    }

    /**
     * Statistiques candidats (ADMIN).
     */
    public function stats(): JsonResponse
    {
        $stats = $this->candidatService->getStats();

        return api_success($stats);
    }

    /**
     * Désactiver un candidat (ADMIN).
     */
    public function deactivate(string $id): JsonResponse
    {
        $this->candidatService->deactivate($id);

        return api_success(null, 'Candidat désactivé avec succès');
    }

    /**
     * Activer un candidat (ADMIN).
     */
    public function activate(string $id): JsonResponse
    {
        $this->candidatService->activate($id);

        return api_success(null, 'Candidat activé avec succès');
    }

    /**
     * Récupérer candidat par PRU (ADMIN).
     */
    public function getByPRU(string $pru): JsonResponse
    {
        $candidat = $this->candidatService->getByPRU($pru);

        return api_success($candidat);
    }
}
