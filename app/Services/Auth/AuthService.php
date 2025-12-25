<?php

namespace App\Services\Auth;

use App\Models\Utilisateur;
use App\Models\Candidat;
use App\Models\Admin;
use App\Models\Correcteur;
use App\Models\ResponsableCentre;
use App\Models\PaymentReceipt;
use App\Enums\TypeUtilisateur;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Role;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterCandidatDTO;
use App\DTOs\Auth\ChangePasswordDTO;

class AuthService
{
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
     * Inscrire un nouveau candidat avec reçu de paiement
     */
    public function registerCandidat(RegisterCandidatDTO $dto): array
    {
        DB::beginTransaction();

        try {
            
            if (Utilisateur::where('user_name', $dto->user_name)->exists()) {
                throw new \Exception('Ce numéro de reçu a déjà été utilisé pour créer un compte.');
            }

           
            $paymentReceipt = PaymentReceipt::where('numero_recu', $dto->user_name)->first();
            if (!$paymentReceipt) {
                throw new \Exception('Aucun reçu trouvé avec ce numéro. Veuillez d\'abord uploader votre reçu.');
            }

            
            $utilisateur = Utilisateur::create([
                'user_name' => $dto->user_name, // Le numéro de reçu
                'mot_de_passe' => Hash::make($dto->mot_de_passe),
                'type_utilisateur' => TypeUtilisateur::CANDIDAT,
                'est_actif' => true,
                'email_verifie' => false,
            ]);

            
            $candidat = Candidat::create([
                'utilisateur_id' => $utilisateur->id,
                'nationalite_cand' => $dto->nationalite_cand,
                'numero_recu' => $dto->user_name, // Même numéro de reçu
            ]);

            
            $paymentReceipt->update([
                'candidat_id' => $candidat->utilisateur_id,
            ]);

            
            $this->assignDefaultRole($utilisateur, 'candidat');

            DB::commit();

            // 7. Créer un token
            $token = $utilisateur->createToken('auth-token')->plainTextToken;

            return [
                'user' => $utilisateur->load('candidat'),
                'token' => $token,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Créer un utilisateur admin/correcteur/responsable
     */
    public function createUser(array $data, string $type): Utilisateur
    {
        DB::beginTransaction();

        try {
            // Créer l'utilisateur
            $utilisateur = Utilisateur::create([
                'user_name' => $data['user_name'],
                'mot_de_passe' => Hash::make($data['mot_de_passe']),
                'telephone' => $data['telephone'] ?? null,
                'type_utilisateur' => $type,
                'est_actif' => $data['est_actif'] ?? true,
                'user_name_verifie' => $data['user_name_verifie'] ?? false,
            ]);

            // Créer le profil spécifique selon le type
            switch ($type) {
                case TypeUtilisateur::ADMIN:
                    Admin::create([
                        'utilisateur_id' => $utilisateur->id,
                        'matricule' => $data['matricule'] ?? null,
                    ]);
                    $this->assignDefaultRole($utilisateur, 'admin');
                    break;

                case TypeUtilisateur::CORRECTEUR:
                    Correcteur::create([
                        'utilisateur_id' => $utilisateur->id,
                        'specialite' => $data['specialite'] ?? null,
                        'matricule_enseignant' => $data['matricule_enseignant'] ?? null,
                    ]);
                    $this->assignDefaultRole($utilisateur, 'correcteur');
                    break;

                case TypeUtilisateur::RESPONSABLE_CENTRE:
                    ResponsableCentre::create([
                        'utilisateur_id' => $utilisateur->id,
                        'code_agent' => $data['code_agent'] ?? null,
                    ]);
                    $this->assignDefaultRole($utilisateur, 'responsable_centre');
                    break;
            }

            DB::commit();

            return $utilisateur->fresh($this->getRelationsForUser($utilisateur));
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
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
     * Vérifier l'user_name
     */
    public function verifyuser_name(Utilisateur $utilisateur): void
    {
        if ($utilisateur->hasVerifieduser_name()) {
            throw ValidationException::withMessages([
                'user_name' => ['L\'user_name est déjà vérifié.'],
            ]);
        }

        $utilisateur->markuser_nameAsVerified();
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
     * Assigner un rôle par défaut
     */
    private function assignDefaultRole(Utilisateur $utilisateur, string $roleName): void
    {
        $role = Role::where('libelle_role', $roleName)->first();

        if ($role) {
            $utilisateur->roles()->attach($role->id);
        }
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
