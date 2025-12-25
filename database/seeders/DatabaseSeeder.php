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
        $this->call([
            // Ajouter vos seeders ici
            // RoleSeeder::class,
            // UtilisateurSeeder::class,
            // CandidatSeeder::class,
            PaymentReceiptSeeder::class,
        ]);

        $this->command->info('✓ Base de données peuplée avec succès !');
    }
}
