<?php

namespace App\Services\Users;

use App\DTOs\Users\CreateUserDTO;
use App\Enums\TypeUtilisateur;
use App\Models\Admin;
use App\Models\Correcteur;
use App\Models\ResponsableCentre;
use App\Models\Utilisateur;
use App\Services\Roles\RoleService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class UserService
{
    public function __construct(
        private readonly RoleService $roleService
    ) {}

    /**
     * Créer un utilisateur candidat avec PRU comme username.
     *
     * @param string $pru PRU du candidat (utilisé comme username)
     * @param string $email Email du candidat
     * @param string $password Mot de passe en clair (sera hashé)
     * @param string $telephone Numéro de téléphone du candidat
     *
     * @return Utilisateur Utilisateur candidat créé
     */
    public function createCandidatUser(string $pru, string $email, string $password, string $telephone): Utilisateur
    {
        return Utilisateur::create([
            'user_name' => $pru,
            'email' => $email,
            'mot_de_passe' => Hash::make($password),
            'telephone' => $telephone,
            'type_utilisateur' => TypeUtilisateur::CANDIDAT,
            'est_actif' => true,
            'email_verifie' => false,
        ]);
    }

    /**
     * Vérifier si un email existe déjà.
     *
     * @param string $email Email à vérifier
     *
     * @return bool True si l'email existe déjà, False sinon
     */
    public function emailExists(string $email): bool
    {
        return Utilisateur::where('email', $email)->exists();
    }

    /**
     * Authentifier un candidat avec PRU + mot de passe.
     *
     * @param string $pru PRU du candidat
     * @param string $password Mot de passe en clair
     *
     * @return Utilisateur|null Utilisateur si authentifié, null sinon
     */
    public function authenticateCandidat(string $pru, string $password): ?Utilisateur
    {
        $utilisateur = Utilisateur::where('user_name', $pru)
            ->where('type_utilisateur', TypeUtilisateur::CANDIDAT)
            ->where('est_actif', true)
            ->first();

        if (!$utilisateur || !Hash::check($password, $utilisateur->mot_de_passe)) {
            return null;
        }

        return $utilisateur;
    }

    /**
     * Générer un token d'authentification avec expiration pour un utilisateur.
     *
     * @param Utilisateur $utilisateur Utilisateur authentifié
     * @param string $tokenName Nom du token (par défaut 'auth_token')
     * @param int $expiresInMinutes Durée de validité en minutes (par défaut 60)
     *
     * @return array ['access_token' => string, 'expires_at' => Carbon]
     */
    public function generateToken(Utilisateur $utilisateur, string $tokenName = 'auth_token', int $expiresInMinutes = 60): array
    {
        // Supprimer les anciens tokens du même type
        $utilisateur->tokens()->where('name', $tokenName)->delete();

        $expiresAt = now()->addMinutes($expiresInMinutes);

        $token = $utilisateur->createToken($tokenName, ['*'], $expiresAt);

        return [
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt->toIso8601String(),
            'expires_in' => $expiresInMinutes * 60, // en secondes
        ];
    }

    /**
     * Générer un refresh token pour un utilisateur.
     *
     * @param Utilisateur $utilisateur Utilisateur authentifié
     * @param int $expiresInDays Durée de validité en jours (par défaut 30)
     *
     * @return array ['refresh_token' => string, 'expires_at' => Carbon]
     */
    public function generateRefreshToken(Utilisateur $utilisateur, int $expiresInDays = 30): array
    {
        // Supprimer les anciens refresh tokens
        $utilisateur->tokens()->where('name', 'refresh_token')->delete();

        $expiresAt = now()->addDays($expiresInDays);
        $token = $utilisateur->createToken('refresh_token', ['refresh'], $expiresAt);

        return [
            'refresh_token' => $token->plainTextToken,
            'expires_at' => $expiresAt->toIso8601String(),
            'expires_in' => $expiresInDays * 24 * 60 * 60, // en secondes
        ];
    }

    /**
     * Rafraîchir un access token à partir d'un refresh token.
     *
     * @param string $refreshToken Refresh token
     *
     * @return array ['access_token' => string, 'refresh_token' => string, 'expires_at' => Carbon]
     * @throws \Exception Si le refresh token est invalide ou expiré
     */
    public function refreshAccessToken(string $refreshToken): array
    {
        // Extraire l'ID du token et le token lui-même
        [$id, $token] = explode('|', $refreshToken, 2);

        $tokenModel = PersonalAccessToken::find($id);

        if (!$tokenModel || !hash_equals($tokenModel->token, hash('sha256', $token))) {
            throw new \Exception('Refresh token invalide');
        }

        if ($tokenModel->name !== 'refresh_token') {
            throw new \Exception('Ce token n\'est pas un refresh token');
        }

        if ($tokenModel->expires_at && $tokenModel->expires_at->isPast()) {
            throw new \Exception('Refresh token expiré');
        }

        $utilisateur = $tokenModel->tokenable;

        if (!$utilisateur->est_actif) {
            throw new \Exception('Utilisateur désactivé');
        }

        // Générer un nouveau access token
        $accessToken = $this->generateToken($utilisateur);
        // Générer un nouveau refresh token
        $newRefreshToken = $this->generateRefreshToken($utilisateur);

        return array_merge($accessToken, $newRefreshToken);
    }

    /**
     * Révoquer tous les tokens d'un utilisateur (logout).
     *
     * @param Utilisateur $utilisateur Utilisateur concerné
     *
     * @return bool True si révocation réussie
     */
    public function revokeAllTokens(Utilisateur $utilisateur): bool
    {
        $utilisateur->tokens()->delete();
        return true;
    }

    /**
     * Révoquer un token spécifique.
     *
     * @param Utilisateur $utilisateur Utilisateur concerné
     * @param string $tokenId ID du token à révoquer
     *
     * @return bool True si révocation réussie
     */
    public function revokeToken(Utilisateur $utilisateur, string $tokenId): bool
    {
        return $utilisateur->tokens()->where('id', $tokenId)->delete() > 0;
    }

    /**
     * Désactiver un utilisateur.
     *
     * @param string $utilisateurId ID de l'utilisateur
     *
     * @return bool True si désactivation réussie
     */
    public function deactivate(string $utilisateurId): bool
    {
        return DB::transaction(function () use ($utilisateurId) {
            $utilisateur = Utilisateur::findOrFail($utilisateurId);
            $utilisateur->update(['est_actif' => false]);
            $utilisateur->tokens()->delete();
            return true;
        });
    }

    /**
     * Activer un utilisateur.
     *
     * @param string $utilisateurId ID de l'utilisateur
     *
     * @return bool True si activation réussie
     */
    public function activate(string $utilisateurId): bool
    {
        $utilisateur = Utilisateur::findOrFail($utilisateurId);
        $utilisateur->update(['est_actif' => true]);
        return true;
    }

    /**
     * Créer un utilisateur staff (admin, responsable centre, correcteur).
     *
     * @param CreateUserDTO $dto DTO contenant les informations du staff
     *
     * @return Utilisateur Utilisateur staff créé
     */
    public function createStaff(CreateUserDTO $dto): Utilisateur
    {
        return DB::transaction(function () use ($dto) {
            $typeUtilisateur = TypeUtilisateur::from($dto->type_utilisateur);

            $user = Utilisateur::create([
                'email' => $dto->email,
                'user_name' => $dto->user_name,
                'mot_de_passe' => Hash::make($dto->mot_de_passe),
                'type_utilisateur' => $typeUtilisateur,
                'email_verifie' => false,
                'telephone' => $dto->telephone,
            ]);

            $this->completeStaffWithRole($dto, $user);

            return $user;
        });
    }



    /**
     * Compléter la création d'un staff avec son rôle spécifique.
     *
     * @param CreateUserDTO $dto DTO contenant les données du staff
     * @param Utilisateur $user Utilisateur créé
     *
     * @return void
     */
    public function completeStaffWithRole(CreateUserDTO $dto, Utilisateur $user): void
    {
        $typeUtilisateur = TypeUtilisateur::from($dto->type_utilisateur);

        switch ($typeUtilisateur) {
            case TypeUtilisateur::ADMIN:
                Admin::create([
                    'utilisateur_id' => $user->id,
                    'matricule' => $dto->matricule
                ]);
                $this->assignRole($user, TypeUtilisateur::ADMIN);
                break;

            case TypeUtilisateur::RESPONSABLE_CENTRE:
                ResponsableCentre::create([
                    'utilisateur_id' => $user->id,
                    'code_agent' => $dto->code_agent
                ]);
                $this->assignRole($user, TypeUtilisateur::RESPONSABLE_CENTRE);
                break;

            case TypeUtilisateur::CORRECTEUR:
                Correcteur::create([
                    'utilisateur_id' => $user->id,
                    'matricule_enseignant' => $dto->matricule_enseignant,
                    'specialite' => $dto->specialite,
                ]);
                $this->assignRole($user, TypeUtilisateur::CORRECTEUR);
                break;
        }
    }



    /**
     * Assigner un rôle à un utilisateur.
     *
     * @param Utilisateur $user Utilisateur concerné
     * @param TypeUtilisateur $roleName Nom du rôle à assigner
     *
     * @return void
     */
    private function assignRole(Utilisateur $user, TypeUtilisateur $roleName): void
    {
        $this->roleService->assignRole($user, $roleName);
    }
}
