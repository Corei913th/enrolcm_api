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
     * Générer un token d'authentification pour un utilisateur.
     *
     * @param Utilisateur $utilisateur Utilisateur authentifié
     *
     * @return string Token d'authentification (Sanctum)
     */
    public function generateToken(Utilisateur $utilisateur): string
    {
        return $utilisateur->createToken('auth_token')->plainTextToken;
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
            $user = Utilisateur::create([
                'email' => $dto->email,
                'user_name' => $dto->user_name,
                'mot_de_passe' => Hash::make($dto->mot_de_passe),
                'type_utilisateur' => $dto->type_utilisateur,
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
        switch ($dto->type_utilisateur) {
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
     * @param string $roleName Nom du rôle à assigner
     *
     * @return void
     */
    private function assignRole(Utilisateur $user, string $roleName): void
    {
        $this->roleService->assignRole($user, $roleName);
    }
}