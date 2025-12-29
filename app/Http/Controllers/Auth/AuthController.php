<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\CreateCandidatAccountRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\CreateCandidatAccountDTO;
use App\DTOs\Auth\ChangePasswordDTO;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\UtilisateurResource;

class AuthController extends Controller
{

    public function __construct(
        private readonly AuthService $authService
        )
    {  }

    /**
     * Connexion
     */
    public function login(LoginRequest $request)
    {
        try {
            $dto = LoginDTO::fromRequest($request);
            $result = $this->authService->login($dto);

            return api_success([
                'user' => new UtilisateurResource($result['user']),
                'token' => $result['token'],
            ], 'Connexion réussie');
        } catch (ValidationException $e) {
            return api_validation_error($e->errors(), $e->getMessage());
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Inscription candidat
     */
    public function register(CreateCandidatAccountRequest $request)
    {
        try {
            $dto = CreateCandidatAccountDTO::fromRequest($request);
            $result = $this->authService->createCandidatAccount($dto);

            return api_created([
                'user' => new UtilisateurResource($result),
            ], 'Inscription réussie');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return api_success(null, 'Déconnexion réussie');
    }

    /**
     * Déconnexion de tous les appareils
     */
    public function logoutAll(Request $request)
    {
        $this->authService->logoutAll($request->user());

        return api_success(null, 'Déconnexion de tous les appareils réussie');
    }

    /**
     * Obtenir l'utilisateur connecté
     */
    public function me(Request $request)
    {
        $user = $this->authService->getCurrentUser($request->user());

        return api_success(new UtilisateurResource($user));
    }

    /**
     * Changer le mot de passe
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $dto = ChangePasswordDTO::fromRequest($request);
            $this->authService->changePassword($request->user(), $dto);

            return api_updated(null, 'Mot de passe modifié avec succès');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

}
