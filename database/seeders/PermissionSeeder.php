<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Gestion des utilisateurs
            ['libelle_permission' => 'MANAGE_USERS', 'desc_permission' => 'Gérer les utilisateurs'],
            ['libelle_permission' => 'VIEW_USERS', 'desc_permission' => 'Voir les utilisateurs'],
            ['libelle_permission' => 'CREATE_USER', 'desc_permission' => 'Créer un utilisateur'],
            ['libelle_permission' => 'UPDATE_USER', 'desc_permission' => 'Modifier un utilisateur'],
            ['libelle_permission' => 'DELETE_USER', 'desc_permission' => 'Supprimer un utilisateur'],

            // Gestion des candidatures
            ['libelle_permission' => 'VIEW_CANDIDATURES', 'desc_permission' => 'Voir les candidatures'],
            ['libelle_permission' => 'VALIDATE_CANDIDATURE', 'desc_permission' => 'Valider une candidature'],
            ['libelle_permission' => 'REJECT_CANDIDATURE', 'desc_permission' => 'Rejeter une candidature'],
            ['libelle_permission' => 'MANAGE_CANDIDATURES', 'desc_permission' => 'Gérer les candidatures'],

            // Gestion des concours
            ['libelle_permission' => 'VIEW_CONCOURS', 'desc_permission' => 'Voir les concours'],
            ['libelle_permission' => 'CREATE_CONCOURS', 'desc_permission' => 'Créer un concours'],
            ['libelle_permission' => 'UPDATE_CONCOURS', 'desc_permission' => 'Modifier un concours'],
            ['libelle_permission' => 'DELETE_CONCOURS', 'desc_permission' => 'Supprimer un concours'],
            ['libelle_permission' => 'MANAGE_CONCOURS', 'desc_permission' => 'Gérer les concours'],

            // Gestion des notes
            ['libelle_permission' => 'VIEW_NOTES', 'desc_permission' => 'Voir les notes'],
            ['libelle_permission' => 'SAISIR_NOTES', 'desc_permission' => 'Saisir les notes'],
            ['libelle_permission' => 'VALIDATE_NOTES', 'desc_permission' => 'Valider les notes'],
            ['libelle_permission' => 'MANAGE_NOTES', 'desc_permission' => 'Gérer les notes'],

            // Gestion des centres
            ['libelle_permission' => 'VIEW_CENTRES', 'desc_permission' => 'Voir les centres'],
            ['libelle_permission' => 'MANAGE_CENTRES', 'desc_permission' => 'Gérer les centres'],
            ['libelle_permission' => 'MANAGE_SALLES', 'desc_permission' => 'Gérer les salles'],

            // Gestion des résultats
            ['libelle_permission' => 'VIEW_RESULTS', 'desc_permission' => 'Voir les résultats'],
            ['libelle_permission' => 'PUBLISH_RESULTS', 'desc_permission' => 'Publier les résultats'],
            ['libelle_permission' => 'MANAGE_RESULTS', 'desc_permission' => 'Gérer les résultats'],

            // Statistiques et rapports
            ['libelle_permission' => 'VIEW_STATS', 'desc_permission' => 'Voir les statistiques'],
            ['libelle_permission' => 'EXPORT_DATA', 'desc_permission' => 'Exporter les données'],

            // Gestion des rôles et permissions
            ['libelle_permission' => 'MANAGE_ROLES', 'desc_permission' => 'Gérer les rôles'],
            ['libelle_permission' => 'MANAGE_PERMISSIONS', 'desc_permission' => 'Gérer les permissions'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['libelle_permission' => $permission['libelle_permission']],
                [
                    'id' => (string) Str::uuid(),
                    'desc_permission' => $permission['desc_permission'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
