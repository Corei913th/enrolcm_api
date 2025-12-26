<?php

 namespace App\Services\Roles;

use App\Models\Role;
use App\Models\Utilisateur;

 class RoleService
{
    public function assignDefault(Utilisateur $user, string $roleName): void
    {
        $role = Role::where('libelle_role', $roleName)->first();

        if ($role) {
            $user->roles()->syncWithoutDetaching($role->id);
        }
    }
}
