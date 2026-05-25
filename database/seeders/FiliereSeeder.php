<?php

namespace Database\Seeders;

use App\Models\Departement;
use App\Models\Filiere;
use Illuminate\Database\Seeder;

class FiliereSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📚 Génération des filières...');

        $departements = Departement::all();

        if ($departements->isEmpty()) {
            $this->command->error("❌ Aucun département trouvé! Veuillez d'abord exécuter le DepartementSeeder.");

            return;
        }

        $filieres = [
            // Génie Informatique
            'GI' => [
                ['code' => 'GI-GL', 'libelle' => 'Génie Logiciel', 'description' => 'Développement et architecture logicielle'],
                ['code' => 'GI-RS', 'libelle' => 'Réseaux et Systèmes', 'description' => 'Administration réseaux et systèmes'],
                ['code' => 'GI-IA', 'libelle' => 'Intelligence Artificielle', 'description' => 'IA et apprentissage automatique'],
            ],
            // Génie Électrique
            'GE' => [
                ['code' => 'GE-ELN', 'libelle' => 'Électronique', 'description' => 'Systèmes électroniques et embarqués'],
                ['code' => 'GE-ELT', 'libelle' => 'Électrotechnique', 'description' => 'Machines et installations électriques'],
                ['code' => 'GE-AUT', 'libelle' => 'Automatique', 'description' => 'Automatisation et contrôle'],
            ],
            // Génie Mécanique
            'GM' => [
                ['code' => 'GM-PROD', 'libelle' => 'Production Mécanique', 'description' => 'Fabrication et production'],
                ['code' => 'GM-ENE', 'libelle' => 'Énergétique', 'description' => 'Systèmes énergétiques'],
            ],
            // Génie Civil
            'GC' => [
                ['code' => 'GC-BAT', 'libelle' => 'Bâtiment', 'description' => 'Construction de bâtiments'],
                ['code' => 'GC-TP', 'libelle' => 'Travaux Publics', 'description' => 'Infrastructures publiques'],
            ],
            // Industries Alimentaires
            'IAA' => [
                ['code' => 'IAA-TA', 'libelle' => 'Technologie Alimentaire', 'description' => 'Transformation des aliments'],
                ['code' => 'IAA-QA', 'libelle' => 'Qualité Alimentaire', 'description' => 'Contrôle qualité et sécurité'],
            ],
            // Production Animale
            'PA' => [
                ['code' => 'PA-ELV', 'libelle' => 'Élevage', 'description' => 'Techniques d\'élevage'],
                ['code' => 'PA-SAN', 'libelle' => 'Santé Animale', 'description' => 'Santé et nutrition animale'],
            ],
            // Administration
            'ADM' => [
                ['code' => 'ADM-GEN', 'libelle' => 'Administration Générale', 'description' => 'Administration publique générale'],
                ['code' => 'ADM-TER', 'libelle' => 'Administration Territoriale', 'description' => 'Administration locale'],
            ],
            // Relations Internationales
            'RI' => [
                ['code' => 'RI-DIPL', 'libelle' => 'Diplomatie', 'description' => 'Carrière diplomatique'],
                ['code' => 'RI-COOP', 'libelle' => 'Coopération Internationale', 'description' => 'Coopération et développement'],
            ],
        ];

        $count = 0;
        foreach ($filieres as $codeDept => $filieresData) {
            $departement = $departements->firstWhere('code_departement', $codeDept);

            if (! $departement) {
                continue;
            }

            foreach ($filieresData as $fil) {
                Filiere::updateOrCreate(
                    [
                        'departement_id' => $departement->id,
                        'code_filiere' => $fil['code'],
                    ],
                    [
                        'libelle_filiere' => $fil['libelle'],
                        'desc_filiere' => $fil['description'],
                        'est_actif' => true,
                    ]
                );
                $count++;
            }
        }

        $this->command->info("✅ {$count} filières créées avec succès!");
    }
}
