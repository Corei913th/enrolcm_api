<?php

namespace Database\Seeders;

use App\Enums\StatutCandidature;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Centre;
use App\Models\Concours;
use App\Models\Session;
use Illuminate\Database\Seeder;

/**
 * Seeder pour créer des candidatures pour un concours existant
 *
 * Usage:
 * php artisan db:seed --class=CandidaturesSeeder
 *
 * Ce seeder:
 * - Prend tous les candidats existants
 * - Les inscrit au concours spécifié
 * - Valide automatiquement leurs candidatures
 * - Répartit les candidats dans les centres disponibles
 */
class CandidaturesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📝 Création des candidatures...');

        // Get the first active concours and session
        $concours = Concours::where('est_actif', true)->first();
        $session = Session::where('est_active', true)->first();

        if (! $concours) {
            $this->command->error('❌ Aucun concours actif trouvé. Créez d\'abord un concours.');

            return;
        }

        if (! $session) {
            $this->command->error('❌ Aucune session active trouvée.');

            return;
        }

        $this->command->info("Concours: {$concours->libelle_concours}");
        $this->command->info("Session: {$session->libelle_session}");

        // Get all centres
        $centres = Centre::where('est_actif', true)->get();
        if ($centres->isEmpty()) {
            $this->command->error('❌ Aucun centre d\'examen trouvé.');

            return;
        }

        // Get all candidats
        $candidats = Candidat::all();
        if ($candidats->isEmpty()) {
            $this->command->error('❌ Aucun candidat trouvé. Exécutez d\'abord CompleteWorkflowSeeder.');

            return;
        }

        $created = 0;
        foreach ($candidats as $candidat) {
            // Check if candidature already exists
            $exists = Candidature::where('candidat_id', $candidat->utilisateur_id)
                ->where('concours_id', $concours->id)
                ->where('session_id', $session->id)
                ->exists();

            if ($exists) {
                continue;
            }

            // Assign random centre
            $centre = $centres->random();

            // Create candidature in SOUMISE status (respecting workflow)
            // Admin will need to validate them manually
            Candidature::create([
                'candidat_id' => $candidat->utilisateur_id,
                'concours_id' => $concours->id,
                'session_id' => $session->id,
                'centre_examen_id' => $centre->id,
                'statut_candidature' => StatutCandidature::SOUMISE,
                'code_cand_temp' => 'TEMP' . str_pad($created + 1, 6, '0', STR_PAD_LEFT),
                'code_cand_def' => null, // Will be assigned upon validation
                'date_inscription' => now()->subDays(rand(1, 30)),
            ]);

            $created++;
        }

        $this->command->info("✅ {$created} candidatures créées en statut SOUMISE");
        $this->command->newLine();
        $this->command->info('🎯 PROCHAINES ÉTAPES:');
        $this->command->info('1. Valider les candidatures via l\'interface admin');
        $this->command->info('2. Créer le planning des épreuves via l\'interface admin');
    }
}
