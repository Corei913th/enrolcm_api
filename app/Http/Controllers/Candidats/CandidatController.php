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
        private readonly CandidatService  $candidatService
    ) {}

    /**
     * Vérifier PRU (PUBLIC - avant création compte).
     * @param VerifyPRURequest $request Requête validée contenant PRU et concours_id
     * @return JsonResponse Réponse JSON indiquant validité du PRU
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
     * Créer compte candidat (PUBLIC - après paiement validé).
     * @param RegisterCandidatRequest $request Requête validée contenant les informations du candidat
     * @return JsonResponse Réponse JSON avec compte créé ou erreur
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
     * Login candidat.
     * @param LoginCandidatRequest $request Requête validée contenant PRU et mot de passe
     * @return JsonResponse Réponse JSON avec utilisateur et token ou erreur
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
     * Profil du candidat connecté.
     * @param Request $request Requête contenant l'utilisateur connecté
     * @return JsonResponse Réponse JSON avec profil candidat ou erreur
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $candidat = $this->candidatService->getByUserId($request->user()->id);
            return api_success($candidat);
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 404);
        }
    }

    /**
     * Mettre à jour profil du candidat connecté.
     * @param UpdateCandidatProfileRequest $request Requête validée contenant les nouvelles données
     * @return JsonResponse Réponse JSON avec profil mis à jour ou erreur
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
     * Liste des candidats (ADMIN).
     * @param Request $request Requête avec filtres et pagination
     * @return JsonResponse Réponse JSON paginée des candidats
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
     * @param string $id ID du candidat
     * @return JsonResponse Réponse JSON avec détails du candidat ou erreur
     */
    public function show(string $id): JsonResponse
    {
        try {
            $candidat = $this->candidatService->getByUserId($id);
            return api_success($candidat);
        } catch (\Exception $e) {
            return api_error('Candidat introuvable', null, 404);
        }
    }

    /**
     * Statistiques candidats (ADMIN).
     * @return JsonResponse Réponse JSON avec statistiques des candidats
     */
    public function stats(): JsonResponse
    {
        $stats = $this->candidatService->getStats();
        return api_success($stats);
    }

    /**
     * Désactiver un candidat (ADMIN).
     * @param string $id ID du candidat
     * @return JsonResponse Réponse JSON avec succès ou erreur
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
     * Activer un candidat (ADMIN).
     * @param string $id ID du candidat
     * @return JsonResponse Réponse JSON avec succès ou erreur
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
     * Récupérer candidat par PRU (ADMIN).
     * @param string $pru PRU du candidat
     * @return JsonResponse Réponse JSON avec candidat ou erreur
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