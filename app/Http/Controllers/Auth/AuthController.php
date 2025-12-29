<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\ChangePasswordDTO;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\UtilisateurResource;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Connexion d'un utilisateur.
     *
     * Endpoint : POST /api/auth/login
     *
     * @param LoginRequest $request Requête validée contenant user_name et mot_de_passe
     *
     * @return \Illuminate\Http\JsonResponse Réponse JSON avec utilisateur et tokens
     *
     * @throws ValidationException Si la requête est invalide
     * @throws \Exception Si l'authentification échoue
     */
    public function login(LoginRequest $request)
    {
        try {
            $dto = LoginDTO::fromRequest($request);
            $result = $this->authService->login($dto);

            return api_success([
                'user' => new UtilisateurResource($result['user']),
                'access_token' => $result['access_token'],
                'refresh_token' => $result['refresh_token'],
                'token_type' => $result['token_type'],
                'expires_in' => $result['expires_in'],
            ], 'Connexion réussie');
        } catch (ValidationException $e) {
            return api_validation_error($e->errors(), $e->getMessage());
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Rafraîchir le token d'accès.
     *
     * Endpoint : POST /api/auth/refresh
     *
     * @param Request $request Requête contenant le refresh_token
     *
     * @return \Illuminate\Http\JsonResponse Réponse JSON avec nouveaux tokens
     *
     * @throws \Exception Si le refresh échoue
     */
    public function refresh(Request $request)
    {
        try {
            $refreshToken = $request->input('refresh_token');

            if (!$refreshToken) {
                return api_error('Refresh token manquant', null, 400);
            }

            $result = $this->authService->refreshToken($refreshToken);

            return api_success([
                'access_token' => $result['access_token'],
                'refresh_token' => $result['refresh_token'],
                'token_type' => $result['token_type'],
                'expires_in' => $result['expires_in'],
            ], 'Token rafraîchi avec succès');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 401);
        }
    }

    /**
     * Déconnexion de l'utilisateur courant.
     *
     * Endpoint : POST /api/auth/logout
     *
     * @param Request $request Requête contenant l'utilisateur connecté
     *
     * @return \Illuminate\Http\JsonResponse Réponse JSON succès
     */
    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return api_success(null, 'Déconnexion réussie');
    }

    /**
     * Déconnexion de tous les appareils.
     *
     * Endpoint : POST /api/auth/logout-all
     *
     * @param Request $request Requête contenant l'utilisateur connecté
     *
     * @return \Illuminate\Http\JsonResponse Réponse JSON succès
     */
    public function logoutAll(Request $request)
    {
        $this->authService->logoutAll($request->user());

        return api_success(null, 'Déconnexion de tous les appareils réussie');
    }

    /**
     * Obtenir l'utilisateur connecté.
     *
     * Endpoint : GET /api/auth/me
     *
     * @param Request $request Requête contenant l'utilisateur connecté
     *
     * @return \Illuminate\Http\JsonResponse Réponse JSON avec utilisateur
     */
    public function me(Request $request)
    {
        $user = $this->authService->getCurrentUser($request->user());

        return api_success(new UtilisateurResource($user));
    }

    /**
     * Changer le mot de passe de l'utilisateur connecté.
     *
     * Endpoint : POST /api/auth/change-password
     *
     * @param ChangePasswordRequest $request Requête validée contenant current_password et new_password
     *
     * @return \Illuminate\Http\JsonResponse Réponse JSON succès ou erreur
     *
     * @throws \Exception Si le changement échoue
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
