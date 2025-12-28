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
                'directeur_nom' => 'Pr. Jean KOULIDIATI',
                'directeur_email' => 'directeur@ensp.cm',
                'directeur_telephone' => '+237222234568',
                'type_etablissement' => 'public',
                'numero_agrement' => 'AGR-ENSP-001',
                'date_creation' => '1971-01-01',
                'description' => 'Grande école d\'ingénieurs du Cameroun',
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
                'directeur_nom' => 'Dr. Marie NDJOUENKEU',
                'directeur_email' => 'directeur@ensai.cm',
                'directeur_telephone' => '+237222345679',
                'type_etablissement' => 'public',
                'numero_agrement' => 'AGR-ENSAI-002',
                'date_creation' => '1990-01-01',
                'description' => 'Formation en sciences agro-industrielles',
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
                'directeur_nom' => 'Pr. Paul TCHOUANKEU',
                'directeur_email' => 'directeur@enset.cm',
                'directeur_telephone' => '+237233456790',
                'type_etablissement' => 'public',
                'numero_agrement' => 'AGR-ENSET-003',
                'date_creation' => '1980-01-01',
                'description' => 'Formation des enseignants techniques',
                'est_actif' => true,
            ],
        ];

        foreach ($ecoles as $ecole) {
            Ecole::create($ecole);
        }
    }
}
