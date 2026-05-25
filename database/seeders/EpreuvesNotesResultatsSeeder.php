<?php

namespace Database\Seeders;

use App\Enums\StatutCandidature;
use App\Enums\StatutNote;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\Epreuve;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\PlanningEpreuve;
use App\Models\ResultatFinal;
use App\Models\Session;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EpreuvesNotesResultatsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🎯 Génération des épreuves, notes, résultats et admissions...');

        $concours = Concours::with('specConcours')->get();
        $session = Session::where('est_actif', true)->first();

        if ($concours->isEmpty() || ! $session) {
            $this->command->error('❌ Aucun concours ou session trouvé!');

            return;
        }

        DB::beginTransaction();
        try {
            foreach ($concours as $concoursItem) {
                $this->command->info("\n📋 Traitement du concours: {$concoursItem->libelle_concours}");

                // Vérifier et créer la relation concours-session si elle n'existe pas
                if (! $concoursItem->sessions()->where('sessions.id', $session->id)->exists()) {
                    $this->command->info('   ⚙️  Création de la relation concours-session...');
                    $concoursItem->sessions()->attach($session->id);
                }

                // 1. Créer les épreuves et le planning
                $epreuves = $this->creerEpreuvesEtPlanning($concoursItem, $session);
                $this->command->info("   ✓ {$epreuves->count()} épreuves créées");

                // 2. Créer les notes pour les candidatures validées
                $notesCount = $this->creerNotes($concoursItem, $epreuves);
                $this->command->info("   ✓ {$notesCount} notes créées");

                // 3. Calculer les résultats finaux
                $resultatsCount = $this->calculerResultats($concoursItem);
                $this->command->info("   ✓ {$resultatsCount} résultats calculés");

                // 4. Déterminer les admissions
                $admisCount = $this->determinerAdmissions($concoursItem);
                $this->command->info("   ✓ {$admisCount} candidats admis");
            }

            DB::commit();
            $this->command->newLine();
            $this->command->info('✅ Génération terminée avec succès!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Erreur: ' . $e->getMessage());
            throw $e;
        }
    }

    private function creerEpreuvesEtPlanning(Concours $concours, Session $session)
    {
        // Récupérer des matières pour créer les épreuves
        $matieres = Matiere::inRandomOrder()->limit(5)->get();

        if ($matieres->isEmpty()) {
            $this->command->warn('⚠️  Aucune matière trouvée. Création de matières par défaut...');
            $matieres = collect([
                Matiere::create(['id' => Str::uuid(), 'libelle_matiere' => 'Mathématiques', 'code_matiere' => 'MATH', 'est_actif' => true]),
                Matiere::create(['id' => Str::uuid(), 'libelle_matiere' => 'Physique', 'code_matiere' => 'PHY', 'est_actif' => true]),
                Matiere::create(['id' => Str::uuid(), 'libelle_matiere' => 'Chimie', 'code_matiere' => 'CHI', 'est_actif' => true]),
            ]);
        }

        $epreuves = collect();
        $dateExamen = now()->addMonths(2);

        foreach ($matieres as $index => $matiere) {
            // Créer l'épreuve
            $epreuve = Epreuve::create([
                'id_epreuve' => Str::uuid(),
                'intitule' => "Épreuve de {$matiere->libelle_matiere}",
                'session' => $session->libelle_session,
                'type_epreuve' => 'ECRIT',
                'duree_en_minute' => 180,
                'note_eliminatoire' => 7.0,
                'coefficient_defaut' => $index === 0 ? 3.0 : 2.0,
                'est_eliminatoire' => true,
                'est_actif' => true,
            ]);

            // Créer le planning
            PlanningEpreuve::create([
                'id' => Str::uuid(),
                'concours_id' => $concours->id,
                'session_id' => $session->id,
                'epreuve_id' => $epreuve->id_epreuve,
                'date_epreuve' => $dateExamen->copy()->addDays($index),
                'heure_debut' => '08:00:00',
                'heure_fin' => '11:00:00',
                'coefficient' => $epreuve->coefficient_defaut,
                'est_actif' => true,
            ]);

            $epreuves->push($epreuve);
        }

        return $epreuves;
    }

    private function creerNotes(Concours $concours, $epreuves)
    {
        $candidatures = Candidature::where('concours_id', $concours->id)
            ->where('statut_candidature', StatutCandidature::VALIDE)
            ->get();

        $notesCount = 0;

        foreach ($candidatures as $candidature) {
            foreach ($epreuves as $epreuve) {
                // 90% des candidats ont des notes
                if (rand(1, 100) <= 90) {
                    // Générer une note réaliste (distribution normale autour de 12/20)
                    $note = $this->genererNoteRealiste();

                    Note::create([
                        'id' => Str::uuid(),
                        'candidature_id' => $candidature->id,
                        'epreuve_id' => $epreuve->id_epreuve,
                        'valeur' => $note,
                        'date_saisie' => now()->subDays(rand(1, 10)),
                        'est_definitive' => true,
                        'est_eliminatoire' => $note < $epreuve->note_eliminatoire,
                        'statut' => StatutNote::VALIDEE,
                    ]);

                    $notesCount++;
                }
            }
        }

        return $notesCount;
    }

    private function genererNoteRealiste(): float
    {
        // Distribution normale autour de 12/20
        // 70% entre 10 et 15
        // 15% entre 7 et 10
        // 10% entre 15 et 18
        // 5% entre 18 et 20

        $rand = rand(1, 100);

        if ($rand <= 70) {
            // 70% : notes moyennes (10-15)
            return round(10 + (rand(0, 500) / 100), 2);
        } elseif ($rand <= 85) {
            // 15% : notes faibles (7-10)
            return round(7 + (rand(0, 300) / 100), 2);
        } elseif ($rand <= 95) {
            // 10% : bonnes notes (15-18)
            return round(15 + (rand(0, 300) / 100), 2);
        } else {
            // 5% : excellentes notes (18-20)
            return round(18 + (rand(0, 200) / 100), 2);
        }
    }

    private function calculerResultats(Concours $concours)
    {
        $candidatures = Candidature::where('concours_id', $concours->id)
            ->where('statut_candidature', StatutCandidature::VALIDE)
            ->with(['notes.epreuve'])
            ->get();

        $resultatsCount = 0;

        foreach ($candidatures as $candidature) {
            $notes = $candidature->notes;

            if ($notes->isEmpty()) {
                continue;
            }

            // Calculer la moyenne pondérée
            $totalPoints = 0;
            $totalCoefficients = 0;
            $noteEliminatoire = false;

            foreach ($notes as $note) {
                $coefficient = $note->epreuve->coefficient_defaut ?? 1;
                $totalPoints += $note->valeur * $coefficient;
                $totalCoefficients += $coefficient;

                if ($note->est_eliminatoire) {
                    $noteEliminatoire = true;
                }
            }

            $moyenne = $totalCoefficients > 0 ? $totalPoints / $totalCoefficients : 0;

            // Déterminer l'admission (seuil à 10/20 et pas de note éliminatoire)
            $estAdmis = $moyenne >= 10 && ! $noteEliminatoire;

            ResultatFinal::create([
                'id' => Str::uuid(),
                'candidature_id' => $candidature->id,
                'moyenne_generale' => round($moyenne, 2),
                'rang' => 0, // Sera calculé après
                'est_admis' => $estAdmis,
                'date_publication' => now(),
            ]);

            $resultatsCount++;
        }

        // Calculer les rangs
        $this->calculerRangs($concours);

        return $resultatsCount;
    }

    private function calculerRangs(Concours $concours)
    {
        $resultats = ResultatFinal::whereHas('candidature', function ($q) use ($concours) {
            $q->where('concours_id', $concours->id);
        })
            ->orderByDesc('moyenne_generale')
            ->get();

        $rang = 1;
        foreach ($resultats as $resultat) {
            $resultat->update(['rang' => $rang]);
            $rang++;
        }
    }

    private function determinerAdmissions(Concours $concours)
    {
        return ResultatFinal::whereHas('candidature', function ($q) use ($concours) {
            $q->where('concours_id', $concours->id);
        })
            ->where('est_admis', true)
            ->count();
    }
}
