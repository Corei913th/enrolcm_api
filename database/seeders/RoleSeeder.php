<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'libelle_role' => 'ADMIN',
                'desc_role' => 'Administrateur système avec tous les droits',
            ],
            [
                'libelle_role' => 'CANDIDAT',
                'desc_role' => 'Candidat aux concours',
            ],
            [
                'libelle_role' => 'CORRECTEUR',
                'desc_role' => 'Correcteur des épreuves',
            ],
            [
                'libelle_role' => 'RESPONSABLE_CENTRE',
                'desc_role' => 'Responsable d\'un centre d\'examen',
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['libelle_role' => $role['libelle_role']],
                [
                    'id' => (string) Str::uuid(),
                    'desc_role' => $role['desc_role'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
