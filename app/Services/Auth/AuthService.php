<?php

namespace App\Services\Auth;

use App\Models\Utilisateur;
use App\Enums\TypeUtilisateur;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\ChangePasswordDTO;
use App\Services\Users\UserService;

class AuthService
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    /**
     * Authentifier un utilisateur (hors candidat).
     *
     * @param LoginDTO $dto DTO contenant user_name et mot_de_passe
     *
     * @return array Tableau contenant :
     *   - user : Utilisateur avec relations chargées
     *   - access_token : Token d'accès
     *   - refresh_token : Token de rafraîchissement
     *   - token_type : Type du token (Bearer)
     *   - expires_in : Durée de validité en secondes
     *
     * @throws ValidationException Si identifiants incorrects ou compte désactivé
     */
    public function login(LoginDTO $dto): array
    {
        $utilisateur = Utilisateur::where('user_name', $dto->user_name)
            ->where('type_utilisateur', '!=', TypeUtilisateur::CANDIDAT)
            ->first();

        if (!$utilisateur || !Hash::check($dto->mot_de_passe, $utilisateur->mot_de_passe)) {
            throw ValidationException::withMessages([
                'user_name' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        if (!$utilisateur->est_actif) {
            throw ValidationException::withMessages([
                'user_name' => ['Votre compte est désactivé. Veuillez contacter l\'administrateur.'],
            ]);
        }

        $accessToken = $this->userService->generateToken($utilisateur, 'auth_token', 60); // 60 minutes
        $refreshToken = $this->userService->generateRefreshToken($utilisateur, 30); // 30 jours

        $relations = $this->getRelationsForUser($utilisateur);

        return [
            'user' => $utilisateur->load($relations),
            'access_token' => $accessToken['access_token'],
            'refresh_token' => $refreshToken['refresh_token'],
            'token_type' => $accessToken['token_type'],
            'expires_in' => $accessToken['expires_in'],
        ];
    }

    /**
     * Rafraîchir le token d'accès.
     *
     * @param string $refreshToken Token de rafraîchissement
     *
     * @return array Nouveau token d'accès et refresh token
     */
    public function refreshToken(string $refreshToken): array
    {
        return $this->userService->refreshAccessToken($refreshToken);
    }

    /**
     * Déconnecter un utilisateur (supprimer le token courant).
     *
     * @param Utilisateur $utilisateur Utilisateur connecté
     *
     * @return void
     */
    public function logout(Utilisateur $utilisateur): void
    {
        $utilisateur->currentAccessToken()->delete();
    }

    /**
     * Déconnecter un utilisateur de tous les appareils.
     *
     * @param Utilisateur $utilisateur Utilisateur connecté
     *
     * @return void
     */
    public function logoutAll(Utilisateur $utilisateur): void
    {
        $utilisateur->tokens()->delete();
    }

    /**
     * Changer le mot de passe de l'utilisateur.
     *
     * @param Utilisateur $utilisateur Utilisateur connecté
     * @param ChangePasswordDTO $dto DTO contenant old_password et new_password
     *
     * @return void
     *
     * @throws ValidationException Si l'ancien mot de passe est incorrect
     */
    public function changePassword(Utilisateur $utilisateur, ChangePasswordDTO $dto): void
    {
        if (!Hash::check($dto->old_password, $utilisateur->mot_de_passe)) {
            throw ValidationException::withMessages([
                'old_password' => ['L\'ancien mot de passe est incorrect.'],
            ]);
        }

        $utilisateur->update([
            'mot_de_passe' => Hash::make($dto->new_password),
        ]);

        $currentToken = $utilisateur->currentAccessToken();
        $utilisateur->tokens()->where('id', '!=', $currentToken->id)->delete();
    }

    /**
     * Réinitialiser le mot de passe (sans ancien mot de passe).
     *
     * @param string $user_name Nom d'utilisateur
     * @param string $newPassword Nouveau mot de passe
     *
     * @return void
     */
    public function resetPassword(string $user_name, string $newPassword): void
    {
        $utilisateur = Utilisateur::where('user_name', $user_name)->firstOrFail();

        $utilisateur->update([
            'mot_de_passe' => Hash::make($newPassword),
        ]);

        $utilisateur->tokens()->delete();
    }

    /**
     * Activer ou désactiver un compte utilisateur.
     *
     * @param Utilisateur $utilisateur Utilisateur concerné
     * @param bool $status True pour activer, False pour désactiver
     *
     * @return void
     */
    public function toggleAccountStatus(Utilisateur $utilisateur, bool $status): void
    {
        $utilisateur->update(['est_actif' => $status]);

        if (!$status) {
            $utilisateur->tokens()->delete();
        }
    }

    /**
     * Obtenir les relations à charger selon le type d'utilisateur.
     *
     * @param Utilisateur $utilisateur Utilisateur concerné
     *
     * @return array Liste des relations à charger
     */
    private function getRelationsForUser(Utilisateur $utilisateur): array
    {
        $relations = ['roles.permissions'];

        switch ($utilisateur->type_utilisateur) {
            case TypeUtilisateur::ADMIN:
                $relations[] = 'admin';
                break;
            case TypeUtilisateur::CANDIDAT:
                $relations[] = 'candidat';
                break;
            case TypeUtilisateur::CORRECTEUR:
                $relations[] = 'correcteur';
                break;
            case TypeUtilisateur::RESPONSABLE_CENTRE:
                $relations[] = 'responsableCentre';
                break;
        }

        return $relations;
    }

    /**
     * Obtenir les informations de l'utilisateur connecté.
     *
     * @param Utilisateur $utilisateur Utilisateur connecté
     *
     * @return Utilisateur Utilisateur avec relations chargées
     */
    public function getCurrentUser(Utilisateur $utilisateur): Utilisateur
    {
        return $utilisateur->load($this->getRelationsForUser($utilisateur));
    }

    /**
     * Vérifier si un user_name existe déjà.
     *
     * @param string $user_name Nom d'utilisateur
     *
     * @return bool True si existe, False sinon
     */
    public function user_nameExists(string $user_name): bool
    {
        return Utilisateur::where('user_name', $user_name)->exists();
    }

    /**
     * Générer un code de vérification aléatoire (6 chiffres).
     *
     * @return string Code de vérification
     */
    public function generateVerificationCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}