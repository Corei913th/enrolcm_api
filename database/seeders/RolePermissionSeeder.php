<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les IDs des rôles
        $adminRole = DB::table('roles')->where('libelle_role', 'ADMIN')->first();
        $candidatRole = DB::table('roles')->where('libelle_role', 'CANDIDAT')->first();
        $correcteurRole = DB::table('roles')->where('libelle_role', 'CORRECTEUR')->first();
        $responsableRole = DB::table('roles')->where('libelle_role', 'RESPONSABLE_CENTRE')->first();

        // ADMIN : Toutes les permissions
        $allPermissions = DB::table('permissions')->pluck('id');
        foreach ($allPermissions as $permissionId) {
            DB::table('role_permission')->updateOrInsert(
                [
                    'role_id' => $adminRole->id,
                    'permission_id' => $permissionId,
                ],
                [
                    'created_at' => now(),
                ]
            );
        }

        // CANDIDAT : Permissions limitées
        $candidatPermissions = [
            'VIEW_CONCOURS',
            'VIEW_RESULTS',
        ];
        $this->assignPermissions($candidatRole->id, $candidatPermissions);

        // CORRECTEUR : Permissions de correction
        $correcteurPermissions = [
            'VIEW_CANDIDATURES',
            'VIEW_NOTES',
            'SAISIR_NOTES',
            'VIEW_CONCOURS',
        ];
        $this->assignPermissions($correcteurRole->id, $correcteurPermissions);

        // RESPONSABLE_CENTRE : Permissions de gestion du centre
        $responsablePermissions = [
            'VIEW_CANDIDATURES',
            'VALIDATE_CANDIDATURE',
            'REJECT_CANDIDATURE',
            'VIEW_CENTRES',
            'MANAGE_SALLES',
            'VIEW_CONCOURS',
            'VIEW_STATS',
        ];
        $this->assignPermissions($responsableRole->id, $responsablePermissions);
    }

    /**
     * Assigner des permissions à un rôle
     */
    private function assignPermissions(string $roleId, array $permissionNames): void
    {
        $permissions = DB::table('permissions')
            ->whereIn('libelle_permission', $permissionNames)
            ->pluck('id');

        foreach ($permissions as $permissionId) {
            DB::table('role_permission')->updateOrInsert(
                [
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ],
                [
                    'created_at' => now(),
                ]
            );
        }
    }
}
