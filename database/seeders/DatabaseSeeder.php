<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Données de base (pas de dépendances)
        $this->call([
            RegionSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);

        // 2. Relations entre rôles et permissions (dépend de RoleSeeder et PermissionSeeder)
        $this->call(RolePermissionSeeder::class);

        // 3. Écoles (pas de dépendances)
        $this->call(EcoleSeeder::class);

        // 4. Départements (dépend de EcoleSeeder)
        $this->call(DepartementSeeder::class);

        // 5. Filières (dépend de DepartementSeeder)
        $this->call(FiliereSeeder::class);

        // 6. Niveaux (pas de dépendances)
        $this->call(NiveauSeeder::class);

        // 7. Matières (dépend de NiveauSeeder)
        $this->call(MatiereSeeder::class);

        // 8. Sessions (pas de dépendances)
        $this->call([
            SessionSeeder::class,
        ]);

        // 9. Concours (dépend de EcoleSeeder et SessionSeeder)
        $this->call(ConcoursSeeder::class);

        // 10. Utilisateurs (dépend de RoleSeeder via RoleService)
        $this->call(UserSeeder::class);

        // 11. Centres (dépend de RegionSeeder et EcoleSeeder)
        $this->call(CentreSeeder::class);

        // 12. Documents requis (dépend de ConcoursSeeder)
        $this->call(DocumentRequisSeeder::class);

        // 13. Concours complet avec candidats (optionnel - dépend de tout)
        // Décommenter pour générer des données de test complètes
        // $this->call(ConcoursCompletSeeder::class);

        // 14. Épreuves, notes, résultats et admissions (dépend de ConcoursCompletSeeder)
        // Génère tout le workflow pour tester les fiches PDF
        // $this->call(EpreuvesNotesResultatsSeeder::class);

        $this->command->info('✓ Base de données peuplée avec succès !');
    }
}
