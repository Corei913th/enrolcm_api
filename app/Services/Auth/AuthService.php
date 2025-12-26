<?php

namespace App\Services\Auth;

use App\Models\Utilisateur;
use App\Enums\TypeUtilisateur;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\CreateCandidatAccountDTO;
use App\DTOs\Auth\ChangePasswordDTO;
use App\Services\Candidats\CandidatService;


class AuthService
{

    public function __construct(
         private readonly CandidatService $candidatService,

    ) {}

    /**
     * Authentifier un utilisateur
     */
    public function login(LoginDTO $dto): array
    {
        $utilisateur = Utilisateur::where('user_name', $dto->user_name)->first();

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

        // Créer un token API
        $token = $utilisateur->createToken('auth-token')->plainTextToken;

        // Charger les relations selon le type d'utilisateur
        $relations = $this->getRelationsForUser($utilisateur);

        return [
            'user' => $utilisateur->load($relations),
            'token' => $token,
        ];
    }

    /**
     * Déconnecter un utilisateur
     */
    public function logout(Utilisateur $utilisateur): void
    {
        $utilisateur->currentAccessToken()->delete();
    }

    /**
     * Déconnecter de tous les appareils
     */
    public function logoutAll(Utilisateur $utilisateur): void
    {
        $utilisateur->tokens()->delete();
    }

    /**
     * Inscrire un nouveau candidat
     */
    public function createCandidatAccount(CreateCandidatAccountDTO $dto): Utilisateur
    {
        
        $user = $this->candidatService->createPartialCandidat($dto);

        return $user;
    }
    

    /**
     * Changer le mot de passe
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

        // Révoquer tous les tokens sauf le token actuel
        $currentToken = $utilisateur->currentAccessToken();
        $utilisateur->tokens()->where('id', '!=', $currentToken->id)->delete();
    }

    /**
     * Réinitialiser le mot de passe (sans ancien mot de passe)
     */
    public function resetPassword(string $user_name, string $newPassword): void
    {
        $utilisateur = Utilisateur::where('user_name', $user_name)->firstOrFail();

        $utilisateur->update([
            'mot_de_passe' => Hash::make($newPassword),
        ]);

        // Révoquer tous les tokens
        $utilisateur->tokens()->delete();
    }

    

    /**
     * Activer/Désactiver un compte
     */
    public function toggleAccountStatus(Utilisateur $utilisateur, bool $status): void
    {
        $utilisateur->update(['est_actif' => $status]);

        // Si désactivé, révoquer tous les tokens
        if (!$status) {
            $utilisateur->tokens()->delete();
        }
    }

    /**
     * Obtenir les relations à charger selon le type d'utilisateur
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
     * Obtenir les informations de l'utilisateur connecté
     */
    public function getCurrentUser(Utilisateur $utilisateur): Utilisateur
    {
        return $utilisateur->load($this->getRelationsForUser($utilisateur));
    }

    /**
     * Vérifier si l'user_name existe déjà
     */
    public function user_nameExists(string $user_name): bool
    {
        return Utilisateur::where('user_name', $user_name)->exists();
    }

    /**
     * Générer un code de vérification
     */
    public function generateVerificationCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
