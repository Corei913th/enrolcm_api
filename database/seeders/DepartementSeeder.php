<?php

namespace Database\Seeders;

use App\Models\Departement;
use App\Models\Ecole;
use Illuminate\Database\Seeder;

class DepartementSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏛️ Génération des départements...');

        // Récupérer les écoles
        $ecoles = Ecole::all();

        if ($ecoles->isEmpty()) {
            $this->command->error("❌ Aucune école trouvée! Veuillez d'abord exécuter le EcoleSeeder.");

            return;
        }

        $departements = [
            // ENSP - École Polytechnique
            [
                'ecole_code' => 'ENSP',
                'departements' => [
                    ['code' => 'GI', 'libelle' => 'Génie Informatique', 'description' => 'Formation en informatique et systèmes d\'information'],
                    ['code' => 'GE', 'libelle' => 'Génie Électrique', 'description' => 'Formation en électricité et électronique'],
                    ['code' => 'GM', 'libelle' => 'Génie Mécanique', 'description' => 'Formation en mécanique et production'],
                    ['code' => 'GC', 'libelle' => 'Génie Civil', 'description' => 'Formation en construction et travaux publics'],
                    ['code' => 'GCH', 'libelle' => 'Génie Chimique', 'description' => 'Formation en procédés chimiques et industriels'],
                ],
            ],
            // ENSAI - Sciences Agro-Industrielles
            [
                'ecole_code' => 'ENSAI',
                'departements' => [
                    ['code' => 'IAA', 'libelle' => 'Industries Alimentaires et Agricoles', 'description' => 'Transformation et conservation des produits agricoles'],
                    ['code' => 'GRN', 'libelle' => 'Gestion des Ressources Naturelles', 'description' => 'Gestion durable des ressources'],
                    ['code' => 'PA', 'libelle' => 'Production Animale', 'description' => 'Élevage et production animale'],
                    ['code' => 'PV', 'libelle' => 'Production Végétale', 'description' => 'Agriculture et cultures'],
                ],
            ],
            // ENSET - Enseignement Technique
            [
                'ecole_code' => 'ENSET',
                'departements' => [
                    ['code' => 'ETI', 'libelle' => 'Enseignement Technique Industriel', 'description' => 'Formation des enseignants en techniques industrielles'],
                    ['code' => 'ETC', 'libelle' => 'Enseignement Technique Commercial', 'description' => 'Formation des enseignants en techniques commerciales'],
                    ['code' => 'ETAA', 'libelle' => 'Enseignement Technique Agricole', 'description' => 'Formation des enseignants en techniques agricoles'],
                ],
            ],
            // ENAM - Administration et Magistrature
            [
                'ecole_code' => 'ENAM',
                'departements' => [
                    ['code' => 'ADM', 'libelle' => 'Administration Générale', 'description' => 'Formation des administrateurs civils'],
                    ['code' => 'MAG', 'libelle' => 'Magistrature', 'description' => 'Formation des magistrats'],
                    ['code' => 'FIN', 'libelle' => 'Finances Publiques', 'description' => 'Formation des contrôleurs financiers'],
                    ['code' => 'DIPL', 'libelle' => 'Diplomatie', 'description' => 'Formation des diplomates'],
                ],
            ],
            // IRIC - Relations Internationales
            [
                'ecole_code' => 'IRIC',
                'departements' => [
                    ['code' => 'RI', 'libelle' => 'Relations Internationales', 'description' => 'Diplomatie et coopération internationale'],
                    ['code' => 'CI', 'libelle' => 'Commerce International', 'description' => 'Commerce et affaires internationales'],
                    ['code' => 'COM', 'libelle' => 'Communication Internationale', 'description' => 'Communication et médias internationaux'],
                ],
            ],
            // ENIEG - Génie Civil
            [
                'ecole_code' => 'ENIEG',
                'departements' => [
                    ['code' => 'GC', 'libelle' => 'Génie Civil', 'description' => 'Construction et infrastructures'],
                    ['code' => 'TP', 'libelle' => 'Travaux Publics', 'description' => 'Routes, ponts et ouvrages d\'art'],
                    ['code' => 'URB', 'libelle' => 'Urbanisme', 'description' => 'Aménagement urbain et territorial'],
                ],
            ],
        ];

        $count = 0;
        foreach ($departements as $ecoleData) {
            $ecole = $ecoles->firstWhere('code_ecole', $ecoleData['ecole_code']);

            if (! $ecole) {
                $this->command->warn("⚠️  École {$ecoleData['ecole_code']} non trouvée, départements ignorés.");

                continue;
            }

            foreach ($ecoleData['departements'] as $dept) {
                Departement::updateOrCreate(
                    [
                        'ecole_id' => $ecole->id,
                        'code_departement' => $dept['code'],
                    ],
                    [
                        'libelle_departement' => $dept['libelle'],
                        'desc_departement' => $dept['description'],
                        'est_actif' => true,
                    ]
                );
                $count++;
            }
        }

        $this->command->info("✅ {$count} départements créés avec succès!");
    }
}
