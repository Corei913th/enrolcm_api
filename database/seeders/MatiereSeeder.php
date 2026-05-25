<?php

namespace Database\Seeders;

use App\Models\Matiere;
use App\Models\Niveau;
use Illuminate\Database\Seeder;

class MatiereSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📖 Génération des matières...');

        $niveaux = Niveau::all();

        if ($niveaux->isEmpty()) {
            $this->command->error("❌ Aucun niveau trouvé! Veuillez d'abord exécuter le NiveauSeeder.");

            return;
        }

        // Matières communes pour tous les niveaux
        $matieresCommunes = [
            ['code' => 'MATH', 'libelle' => 'Mathématiques'],
            ['code' => 'PHYS', 'libelle' => 'Physique'],
            ['code' => 'CHIM', 'libelle' => 'Chimie'],
            ['code' => 'INFO', 'libelle' => 'Informatique'],
            ['code' => 'ANG', 'libelle' => 'Anglais'],
            ['code' => 'FRA', 'libelle' => 'Français'],
        ];

        // Matières spécifiques par niveau
        $matieresSpecifiques = [
            'ING1' => [
                ['code' => 'ALGO', 'libelle' => 'Algorithmique'],
                ['code' => 'ELEC', 'libelle' => 'Électronique'],
                ['code' => 'MECA', 'libelle' => 'Mécanique'],
            ],
            'ING2' => [
                ['code' => 'BDD', 'libelle' => 'Bases de Données'],
                ['code' => 'RESEAU', 'libelle' => 'Réseaux'],
                ['code' => 'SYST', 'libelle' => 'Systèmes'],
            ],
            'ING3' => [
                ['code' => 'IA', 'libelle' => 'Intelligence Artificielle'],
                ['code' => 'SECU', 'libelle' => 'Sécurité'],
                ['code' => 'PROJ', 'libelle' => 'Projet'],
            ],
            'L1' => [
                ['code' => 'INTRO-INFO', 'libelle' => 'Introduction à l\'Informatique'],
                ['code' => 'STAT', 'libelle' => 'Statistiques'],
            ],
            'M1' => [
                ['code' => 'RECH', 'libelle' => 'Méthodologie de Recherche'],
                ['code' => 'GEST-PROJ', 'libelle' => 'Gestion de Projet'],
            ],
        ];

        $count = 0;

        // Créer les matières communes pour les niveaux ingénieur
        $niveauxIngenieur = $niveaux->whereIn('code_niveau', ['ING1', 'ING2', 'ING3']);
        foreach ($niveauxIngenieur as $niveau) {
            foreach ($matieresCommunes as $matiere) {
                Matiere::updateOrCreate(
                    [
                        'niveau_id' => $niveau->id,
                        'code_matiere' => $matiere['code'],
                    ],
                    [
                        'libelle_matiere' => $matiere['libelle'],
                        'coefficient' => 2,
                        'est_actif' => true,
                    ]
                );
                $count++;
            }
        }

        // Créer les matières spécifiques
        foreach ($matieresSpecifiques as $codeNiveau => $matieres) {
            $niveau = $niveaux->firstWhere('code_niveau', $codeNiveau);

            if (! $niveau) {
                continue;
            }

            foreach ($matieres as $matiere) {
                Matiere::updateOrCreate(
                    [
                        'niveau_id' => $niveau->id,
                        'code_matiere' => $matiere['code'],
                    ],
                    [
                        'libelle_matiere' => $matiere['libelle'],
                        'coefficient' => 3,
                        'est_actif' => true,
                    ]
                );
                $count++;
            }
        }

        $this->command->info("✅ {$count} matières créées avec succès!");
    }
}
