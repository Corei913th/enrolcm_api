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
        // Seeders des rôles et permissions
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
        ]);

        $this->call(UserSeeder::class);

        // Seed écoles pour les tests
        $this->call([
            EcoleSeeder::class,
            PaymentReceiptSeeder::class,
        ]);

        $this->command->info('✓ Base de données peuplée avec succès !');
    }
}
