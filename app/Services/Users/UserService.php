<?php

namespace App\Services\Users;


use App\DTOs\Auth\CreateCandidatAccountDTO;
use App\DTOs\Users\CreateUserDTO;
use App\Enums\TypeUtilisateur;
use App\Models\Admin;
use App\Models\Correcteur;
use App\Models\ResponsableCentre;
use App\Models\Role;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\DB;


class UserService
{
    public function createCandidatAccount(CreateCandidatAccountDTO $dto): Utilisateur
    {
        return DB::transaction(function () use ($dto) {
            $user = Utilisateur::create($dto->toArray());
            return $user;
        });
    }

    public function createStaff(CreateUserDTO $dto): Utilisateur
    {
        return DB::transaction(function () use ($dto) {
            $user = Utilisateur::create([
                'email' => $dto->email,
                'user_name' => $dto->user_name,
                'mot_de_passe' => $dto->mot_de_passe,
                'type_utilisateur' => $dto->type_utilisateur,
                'email_verifie' => $dto->email_verifie
            ]);

            $this->completeStaffWithRole($dto, $user);

            return $user;
        });
    }

    public function completeStaffWithRole(CreateUserDTO $dto, Utilisateur $user){
         switch($dto->type_utilisateur) {
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
     * Assigner un rôle 
     */
    private function assignRole(Utilisateur $staff, string $roleName): void
    {
        $role = Role::where('libelle_role', $roleName)->first();

        if ($role) {
            $staff->roles()->attach($role->id);
        }
    }
}


