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
                'id' => Str::uuid(),
                'libelle_role' => 'ADMIN',
                'desc_role' => 'Administrateur système avec tous les droits',
                'created_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'libelle_role' => 'CANDIDAT',
                'desc_role' => 'Candidat aux concours',
                'created_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'libelle_role' => 'CORRECTEUR',
                'desc_role' => 'Correcteur des épreuves',
                'created_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'libelle_role' => 'RESPONSABLE_CENTRE',
                'desc_role' => 'Responsable d\'un centre d\'examen',
                'created_at' => now(),
            ],
        ];

        DB::table('roles')->insert($roles);
    }
}
