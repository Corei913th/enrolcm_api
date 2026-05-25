<?php

namespace Database\Seeders;

use App\Models\Niveau;
use Illuminate\Database\Seeder;

class NiveauSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📊 Génération des niveaux...');

        $niveaux = [
            [
                'code_niveau' => 'L1',
                'libelle_niveau' => 'Licence 1',
                'desc_niveau' => 'Première année de licence',
                'ordre' => 1,
            ],
            [
                'code_niveau' => 'L2',
                'libelle_niveau' => 'Licence 2',
                'desc_niveau' => 'Deuxième année de licence',
                'ordre' => 2,
            ],
            [
                'code_niveau' => 'L3',
                'libelle_niveau' => 'Licence 3',
                'desc_niveau' => 'Troisième année de licence',
                'ordre' => 3,
            ],
            [
                'code_niveau' => 'M1',
                'libelle_niveau' => 'Master 1',
                'desc_niveau' => 'Première année de master',
                'ordre' => 4,
            ],
            [
                'code_niveau' => 'M2',
                'libelle_niveau' => 'Master 2',
                'desc_niveau' => 'Deuxième année de master',
                'ordre' => 5,
            ],
            [
                'code_niveau' => 'ING1',
                'libelle_niveau' => 'Ingénieur 1ère année',
                'desc_niveau' => 'Première année cycle ingénieur',
                'ordre' => 1,
            ],
            [
                'code_niveau' => 'ING2',
                'libelle_niveau' => 'Ingénieur 2ème année',
                'desc_niveau' => 'Deuxième année cycle ingénieur',
                'ordre' => 2,
            ],
            [
                'code_niveau' => 'ING3',
                'libelle_niveau' => 'Ingénieur 3ème année',
                'desc_niveau' => 'Troisième année cycle ingénieur',
                'ordre' => 3,
            ],
        ];

        foreach ($niveaux as $niveau) {
            Niveau::updateOrCreate(
                ['code_niveau' => $niveau['code_niveau']],
                $niveau
            );
        }

        $this->command->info('✅ ' . count($niveaux) . ' niveaux créés avec succès!');
    }
}
