<?php

namespace Database\Seeders;

use App\Models\Ecole;
use App\Enums\RegionCameroun;
use Illuminate\Database\Seeder;

class EcoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ecoles = [
            [
                'code_ecole' => 'ENSP',
                'libelle_ecole' => 'École Nationale Supérieure Polytechnique',
                'region' => RegionCameroun::CENTRE->value,
                'localisation' => 'Yaoundé',
                'email_ecole' => 'contact@ensp.cm',
                'telephone_ecole' => '+237222234567',
                'siteweb_ecole' => 'https://ensp.cm',
                'devise' => 'Excellence et Innovation',
                'bp_ecole' => 'BP 8390',
                'est_actif' => true,
            ],
            [
                'code_ecole' => 'ENSAI',
                'libelle_ecole' => 'École Nationale Supérieure des Sciences Agro-Industrielles',
                'region' => RegionCameroun::CENTRE->value,
                'localisation' => 'Ngaoundéré',
                'email_ecole' => 'contact@ensai.cm',
                'telephone_ecole' => '+237222345678',
                'siteweb_ecole' => 'https://ensai.cm',
                'devise' => 'Savoir et Développement',
                'bp_ecole' => 'BP 455',
                'est_actif' => true,
            ],
            [
                'code_ecole' => 'ENSET',
                'libelle_ecole' => 'École Normale Supérieure d\'Enseignement Technique',
                'region' => RegionCameroun::LITTORAL->value,
                'localisation' => 'Douala',
                'email_ecole' => 'contact@enset.cm',
                'telephone_ecole' => '+237233456789',
                'siteweb_ecole' => 'https://enset.cm',
                'devise' => 'Former pour Transformer',
                'bp_ecole' => 'BP 1872',
                'est_actif' => true,
            ],
        ];

        foreach ($ecoles as $ecole) {
            Ecole::create($ecole);
        }
    }
}
