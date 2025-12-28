<?php

namespace App\Services\Roles;

use App\Models\Role;
use App\Models\Permission;
use App\Models\Utilisateur;
use Illuminate\Support\Collection;

class RoleService
{
    /**
     * Assigner un rôle par défaut à un utilisateur
     */
    public function assignDefault(Utilisateur $user, string $roleName): void
    {
        $role = Role::where('libelle_role', $roleName)->first();

        if ($role) {
            $user->roles()->syncWithoutDetaching($role->id);
        }
    }

    /**
     * Assigner un rôle à un utilisateur
     */
    public function assignRole(Utilisateur $user, string $roleName): bool
    {
        $role = Role::where('libelle_role', $roleName)->first();

        if (!$role) {
            return false;
        }

        $user->roles()->syncWithoutDetaching($role->id);
        return true;
    }

    /**
     * Retirer un rôle à un utilisateur
     */
    public function removeRole(Utilisateur $user, string $roleName): bool
    {
        $role = Role::where('libelle_role', $roleName)->first();

        if (!$role) {
            return false;
        }

        $user->roles()->detach($role->id);
        return true;
    }

    /**
     * Synchroniser les rôles d'un utilisateur (remplace tous les rôles)
     */
    public function syncRoles(Utilisateur $user, array $roleNames): void
    {
        $roleIds = Role::whereIn('libelle_role', $roleNames)->pluck('id');
        $user->roles()->sync($roleIds);
    }

    /**
     * Vérifier si un utilisateur a un rôle spécifique
     */
    public function hasRole(Utilisateur $user, string $roleName): bool
    {
        return $user->roles()->where('libelle_role', $roleName)->exists();
    }

    /**
     * Vérifier si un utilisateur a au moins un des rôles
     */
    public function hasAnyRole(Utilisateur $user, array $roleNames): bool
    {
        return $user->roles()->whereIn('libelle_role', $roleNames)->exists();
    }

    /**
     * Vérifier si un utilisateur a tous les rôles
     */
    public function hasAllRoles(Utilisateur $user, array $roleNames): bool
    {
        $userRoles = $user->roles()->pluck('libelle_role')->toArray();
        return empty(array_diff($roleNames, $userRoles));
    }

    /**
     * Obtenir tous les rôles d'un utilisateur
     */
    public function getUserRoles(Utilisateur $user): Collection
    {
        return $user->roles;
    }

    /**
     * Obtenir tous les rôles disponibles
     */
    public function getAllRoles(): Collection
    {
        return Role::all();
    }

    /**
     * Obtenir un rôle par son nom
     */
    public function getRoleByName(string $roleName): ?Role
    {
        return Role::where('libelle_role', $roleName)->first();
    }

    /**
     * Vérifier si un utilisateur a une permission spécifique
     */
    public function hasPermission(Utilisateur $user, string $permissionName): bool
    {
        return $user->roles()
            ->whereHas('permissions', function ($query) use ($permissionName) {
                $query->where('libelle_permission', $permissionName);
            })
            ->exists();
    }

    /**
     * Vérifier si un utilisateur a au moins une des permissions
     */
    public function hasAnyPermission(Utilisateur $user, array $permissionNames): bool
    {
        return $user->roles()
            ->whereHas('permissions', function ($query) use ($permissionNames) {
                $query->whereIn('libelle_permission', $permissionNames);
            })
            ->exists();
    }

    /**
     * Obtenir toutes les permissions d'un utilisateur (via ses rôles)
     */
    public function getUserPermissions(Utilisateur $user): Collection
    {
        return Permission::whereHas('roles', function ($query) use ($user) {
            $query->whereIn('roles.id', $user->roles->pluck('id'));
        })->get();
    }

    /**
     * Assigner une permission à un rôle (admin uniquement)
     */
    public function assignPermissionToRole(string $roleName, string $permissionName): bool
    {
        $role = Role::where('libelle_role', $roleName)->first();
        $permission = Permission::where('libelle_permission', $permissionName)->first();

        if (!$role || !$permission) {
            return false;
        }

        $role->permissions()->syncWithoutDetaching($permission->id);
        return true;
    }

    /**
     * Retirer une permission d'un rôle
     */
    public function removePermissionFromRole(string $roleName, string $permissionName): bool
    {
        $role = Role::where('libelle_role', $roleName)->first();
        $permission = Permission::where('libelle_permission', $permissionName)->first();

        if (!$role || !$permission) {
            return false;
        }

        $role->permissions()->detach($permission->id);
        return true;
    }

    /**
     * Obtenir toutes les permissions d'un rôle
     */
    public function getRolePermissions(string $roleName): Collection
    {
        $role = Role::where('libelle_role', $roleName)->first();

        if (!$role) {
            return collect();
        }

        return $role->permissions;
    }

    /**
     * Obtenir tous les utilisateurs ayant un rôle spécifique
     */
    public function getUsersByRole(string $roleName): Collection
    {
        $role = Role::where('libelle_role', $roleName)->first();

        if (!$role) {
            return collect();
        }

        return $role->utilisateurs;
    }
}
