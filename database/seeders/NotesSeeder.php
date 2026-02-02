<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Candidature;
use App\Models\Epreuve;
use App\Models\Note;
use App\Enums\StatutCandidature;

/**
 * Seeder pour générer des notes pour les candidats validés
 * 
 * Usage:
 * php artisan db:seed --class=NotesSeeder
 * 
 * Ce seeder:
 * - Prend toutes les candidatures VALIDE
 * - Génère des notes aléatoires pour toutes les épreuves
 * - Permet le calcul des résultats
 */
class NotesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📝 Génération des notes...');

        // Get all validated candidatures
        $candidatures = Candidature::where('statut_candidature', StatutCandidature::VALIDE->value)
            ->get();

        if ($candidatures->isEmpty()) {
            $this->command->error('❌ Aucune candidature validée trouvée.');
            $this->command->info('💡 Exécutez d\'abord CandidaturesSeeder puis validez des candidatures via l\'interface admin.');
            return;
        }

        // Get all active epreuves
        $epreuves = Epreuve::where('est_actif', true)->get();

        if ($epreuves->isEmpty()) {
            $this->command->error('❌ Aucune épreuve trouvée.');
            return;
        }

        $this->command->info("Candidatures validées: {$candidatures->count()}");
        $this->command->info("Épreuves: {$epreuves->count()}");

        $notesCreated = 0;

        Note::withoutEvents(function () use ($candidatures, $epreuves, &$notesCreated) {
            foreach ($candidatures as $candidature) {
                foreach ($epreuves as $epreuve) {
                    // Check if note already exists
                    $exists = Note::where('candidature_id', $candidature->id)
                        ->where('epreuve_id', $epreuve->id_epreuve)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    // Generate realistic random note (8-18 range like ConcoursRealisteSeeder)
                    $valeur = round(rand(800, 1800) / 100, 2);

                    Note::updateOrCreate(
                        [
                            'candidature_id' => $candidature->id,
                            'epreuve_id' => $epreuve->id_epreuve,
                        ],
                        [
                            'valeur' => $valeur,
                            'date_saisie' => now(),
                            'est_definitive' => true,
                            'est_eliminatoire' => false,
                            'statut' => \App\Enums\StatutNote::VALIDEE,
                        ]
                    );

                    $notesCreated++;
                }

                if ($candidatures->search($candidature) % 10 === 0) {
                    $this->command->info("   ✓ Notes générées pour {$candidatures->search($candidature)} candidats...");
                }
            }
        });

        $this->command->info("✅ {$notesCreated} notes créées");
        $this->command->newLine();
        $this->command->info('🎯 PROCHAINE ÉTAPE:');
        $this->command->info('Calculer les résultats via l\'interface admin');
    }
}
