<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterCandidatRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterCandidatDTO;
use App\DTOs\Auth\ChangePasswordDTO;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\UtilisateurResource;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

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
    public function register(RegisterCandidatRequest $request)
    {
        try {
            \Log::info('Register request data:', $request->validated());
            
            $dto = RegisterCandidatDTO::fromRequest($request);
            
            \Log::info('DTO created:', [
                'user_name' => $dto->user_name,
                'nationalite_cand' => $dto->nationalite_cand,
            ]);
            
            $result = $this->authService->registerCandidat($dto);

            return api_created([
                'user' => new UtilisateurResource($result['user']),
                'token' => $result['token'],
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

    /**
     * Vérifier l'user_name
     */
    public function verifyUserName(Request $request)
    {
        try {
            $this->authService->verifyuser_name($request->user());

            return api_updated(null, 'Nom d\'utilisateur vérifié avec succès');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Vérifier si un user_name existe
     */
    public function checkUserName(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string'
        ]);

        $exists = $this->authService->user_nameExists($request->user_name);

        return api_success(['exists' => $exists]);
    }
}
