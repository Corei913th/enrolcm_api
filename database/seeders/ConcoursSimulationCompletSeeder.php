<?php

namespace Database\Seeders;

use App\Enums\NiveauScolaire;
use App\Enums\RegionCameroun;
use App\Enums\StatutCandidature;
use App\Enums\StatutNote;
use App\Enums\StatutPaiement;
use App\Enums\StatutVerificationDocument;
use App\Enums\TypeDocument;
use App\Enums\TypeUtilisateur;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Centre;
use App\Models\Concours;
use App\Models\Document;
use App\Models\DocumentRequis;
use App\Models\Epreuve;
use App\Models\Filiere;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\Paiement;
use App\Models\PlanningEpreuve;
use App\Models\ResultatFinal;
use App\Models\Session;
use App\Models\Utilisateur;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seeder de simulation complète d'un concours pour présentation
 *
 * Ce seeder crée un concours complet avec :
 * - Candidats avec filières
 * - Candidatures valides avec QR codes, convocations, fiches d'inscription
 * - Documents soumis et validés
 * - Paiements validés
 * - Planning d'épreuves
 * - Saisie des notes
 * - Calcul des résultats
 * - Publication des résultats
 *
 * Usage:
 * php artisan db:seed --class=ConcoursSimulationCompletSeeder
 *
 * Avec paramètres personnalisés:
 * php artisan db:seed --class=ConcoursSimulationCompletSeeder --concours-id=xxx --nombre-candidats=50
 */
class ConcoursSimulationCompletSeeder extends Seeder
{
    private $faker;

    private $concoursId;

    private $nombreCandidats = 10; // Réduit pour test

    private $session;

    private $centres;

    private $filieres;

    private $epreuves;

    private $documentsRequis;

    // Dates du workflow (cohérentes)
    private Carbon $dateOuvertureCandidatures;

    private Carbon $dateLimiteCandidatures;

    private Carbon $dateValidationDocuments;

    private Carbon $dateValidationPaiements;

    private Carbon $dateExamen;

    private Carbon $dateSaisieNotes;

    private Carbon $datePublicationResultats;

    public function run(): void
    {
        $this->faker = Faker::create('fr_FR');

        // Récupérer les paramètres (utiliser des valeurs par défaut si non fournis)
        $this->concoursId = Concours::oldest()->first()?->id;
        $this->nombreCandidats = 10; // Réduit pour test

        $this->command->info('╔════════════════════════════════════════════════════════════╗');
        $this->command->info("║   SIMULATION COMPLÈTE D'UN CONCOURS POUR PRÉSENTATION     ║");
        $this->command->info('╚════════════════════════════════════════════════════════════╝');
        $this->command->newLine();

        // Initialiser les dates du workflow
        $this->initialiserDatesWorkflow();

        DB::beginTransaction();
        try {
            // 1. Vérifier/Préparer le concours
            $concours = $this->preparerConcours();

            // 2. Préparer les données de base
            $this->preparerDonneesBase($concours);

            // 3. Créer les candidats et candidatures
            $this->creerCandidatsEtCandidatures($concours);

            // 4. Créer les épreuves et planning
            $this->creerEpreuvesEtPlanning($concours);

            // 5. Saisir les notes
            $this->saisirNotes($concours);

            // 6. Calculer les résultats SANS les publier
            $this->calculerResultatsSansPublier($concours);

            DB::commit();

            $this->afficherRecapitulatif($concours);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Erreur: ' . $e->getMessage());
            $this->command->error($e->getTraceAsString());
            throw $e;
        }
    }

    private function initialiserDatesWorkflow(): void
    {
        $this->command->info('📅 Initialisation du calendrier du concours...');

        // Workflow sur 3 mois pour la simulation
        $this->dateOuvertureCandidatures = now()->subMonths(3);
        $this->dateLimiteCandidatures = now()->subMonths(2)->subDays(15);
        $this->dateValidationDocuments = now()->subMonths(2)->subDays(10);
        $this->dateValidationPaiements = now()->subMonths(2)->subDays(5);
        $this->dateExamen = now()->subMonth()->subDays(10);
        $this->dateSaisieNotes = now()->subMonth();
        $this->datePublicationResultats = now()->subDays(5);

        $this->command->info("  ✓ Ouverture candidatures: {$this->dateOuvertureCandidatures->format('d/m/Y')}");
        $this->command->info("  ✓ Limite candidatures: {$this->dateLimiteCandidatures->format('d/m/Y')}");
        $this->command->info("  ✓ Validation documents: {$this->dateValidationDocuments->format('d/m/Y')}");
        $this->command->info("  ✓ Validation paiements: {$this->dateValidationPaiements->format('d/m/Y')}");
        $this->command->info("  ✓ Date examen: {$this->dateExamen->format('d/m/Y')}");
        $this->command->info("  ✓ Saisie notes: {$this->dateSaisieNotes->format('d/m/Y')}");
        $this->command->info("  ✓ Publication résultats: {$this->datePublicationResultats->format('d/m/Y')}");
        $this->command->newLine();
    }

    private function preparerConcours(): Concours
    {
        $this->command->info('🎯 Préparation du concours...');

        $concours = Concours::find($this->concoursId);

        if (! $concours) {
            $this->command->error("❌ Concours {$this->concoursId} introuvable!");
            throw new \Exception('Concours introuvable');
        }

        // Mettre à jour les dates du concours
        $concours->update([
            'date_limite_depot' => $this->dateLimiteCandidatures,
            'date_examen' => $this->dateExamen,
            'est_actif' => true,
        ]);

        $this->command->info("  ✓ Concours: {$concours->libelle_concours}");
        $this->command->info("  ✓ École: {$concours->ecole->nom_ecole}");
        $this->command->newLine();

        return $concours;
    }

    private function preparerDonneesBase(Concours $concours): void
    {
        $this->command->info('📦 Préparation des données de base...');

        // Session
        $this->session = Session::where('est_actif', true)->first();
        if (! $this->session) {
            throw new \Exception('Aucune session active trouvée');
        }

        // Vérifier la relation concours-session
        if (! $concours->sessions()->where('sessions.id', $this->session->id)->exists()) {
            $concours->sessions()->attach($this->session->id);
        }

        // Centres
        $this->centres = Centre::where('est_actif', true)->limit(5)->get();
        if ($this->centres->isEmpty()) {
            throw new \Exception('Aucun centre actif trouvé');
        }

        // Filières du concours
        $this->filieres = $concours->filieres;
        if ($this->filieres->isEmpty()) {
            // Attacher des filières si aucune n'existe
            $filieresDisponibles = Filiere::where('est_actif', true)->limit(3)->get();
            foreach ($filieresDisponibles as $filiere) {
                $concours->filieres()->attach($filiere->id, [
                    'nombre_places' => 50,
                ]);
            }
            $this->filieres = $concours->filieres()->get();
        }

        // Documents requis
        $this->documentsRequis = DocumentRequis::where('concours_id', $concours->id)->get();
        if ($this->documentsRequis->isEmpty()) {
            // Créer des documents requis par défaut
            $typesDocuments = [
                TypeDocument::CNI,
                TypeDocument::ACTE_NAISSANCE,
                TypeDocument::ATTESTATION_BAC,
                TypeDocument::PHOTO_IDENTITE,
            ];

            foreach ($typesDocuments as $type) {
                DocumentRequis::create([
                    'id' => Str::uuid(),
                    'concours_id' => $concours->id,
                    'type_document' => $type,
                    'est_obligatoire' => true,
                    'description' => "Document requis: {$type->label()}",
                ]);
            }

            $this->documentsRequis = DocumentRequis::where('concours_id', $concours->id)->get();
        }

        $this->command->info("  ✓ Session: {$this->session->libelle_session}");
        $this->command->info("  ✓ Centres: {$this->centres->count()}");
        $this->command->info("  ✓ Filières: {$this->filieres->count()}");
        $this->command->info("  ✓ Documents requis: {$this->documentsRequis->count()}");
        $this->command->newLine();
    }

    private function creerCandidatsEtCandidatures(Concours $concours): void
    {
        $this->command->info('👥 Création des candidats et candidatures...');
        $this->command->info("  Nombre de candidats à créer: {$this->nombreCandidats}");

        $progressBar = $this->command->getOutput()->createProgressBar($this->nombreCandidats);
        $progressBar->start();

        for ($i = 0; $i < $this->nombreCandidats; $i++) {
            $email = "candidat.simulation.{$i}@enrolcm.test";

            // Créer ou mettre à jour l'utilisateur
            $nom = $this->faker->lastName;
            $prenom = $this->faker->firstName;
            $userName = strtolower($prenom . '.' . $nom . $i);

            $utilisateur = Utilisateur::updateOrCreate(
                ['email' => $email],
                [
                    'id' => Str::uuid(),
                    'user_name' => $userName,
                    'telephone' => $this->faker->phoneNumber,
                    'mot_de_passe' => Hash::make('password'),
                    'type_utilisateur' => TypeUtilisateur::CANDIDAT,
                    'email_verifie' => false,
                    'created_at' => $this->dateOuvertureCandidatures,
                ]
            );

            // Créer ou mettre à jour le candidat
            $sexe = $this->faker->randomElement(['M', 'F']);
            $dateNaissance = $this->faker->dateTimeBetween('-25 years', '-18 years');
            $age = Carbon::parse($dateNaissance)->age;

            $candidat = Candidat::updateOrCreate(
                ['utilisateur_id' => $utilisateur->id],
                [
                    'nom_cand' => $nom,
                    'prenom_cand' => $prenom,
                    'sexe_cand' => $sexe,
                    'date_naissance_cand' => $dateNaissance,
                    'lieu_naissance_cand' => $this->faker->city,
                    'age_cand' => $age,
                    'nationalite_cand' => 'Camerounaise',
                    'adresse_cand' => $this->faker->address,
                    'numero_cni' => $this->faker->unique()->numerify('###########'),
                    'date_delivrance_cni' => $this->faker->dateTimeBetween('-5 years', '-1 year'),
                    'region' => $this->faker->randomElement(RegionCameroun::cases()),
                    'departement' => $this->faker->city,
                    'arrondissement' => $this->faker->city,
                    'nom_tuteur_cand' => $this->faker->name,
                    'telephone_tuteur_cand' => $this->faker->phoneNumber,
                    'nom_parent' => $this->faker->name,
                    'telephone_parent' => $this->faker->phoneNumber,
                    'nom_pere' => $this->faker->name('male'),
                    'telephone_pere' => $this->faker->phoneNumber,
                    'a_handicap' => false,
                    'ethnie_cand' => $this->faker->randomElement(['Bamiléké', 'Beti', 'Douala', 'Fulani', 'Bassa']),
                    'statut_matrimonial' => 'Célibataire',
                    'niveau_scolaire' => NiveauScolaire::BACCALAUREAT->value,
                    'serie_bac' => $this->faker->randomElement(['C', 'D', 'E']),
                    'annee_obtention_bac' => $this->faker->numberBetween(2018, 2024),
                    'mention' => $this->faker->randomElement(['PASSABLE', 'ASSEZ_BIEN', 'BIEN', 'TRES_BIEN']),
                    'created_at' => $this->dateOuvertureCandidatures,
                ]
            );

            // Créer ou mettre à jour la candidature
            $centreExamen = $this->centres->random();
            $centreDepot = $this->centres->random();

            $candidature = Candidature::updateOrCreate(
                [
                    'candidat_id' => $candidat->utilisateur_id,
                    'concours_id' => $concours->id,
                    'session_id' => $this->session->id,
                ],
                [
                    'id' => Str::uuid(),
                    'centre_examen_id' => $centreExamen->id,
                    'centre_depot_id' => $centreDepot->id,
                    'date_candidature' => $this->dateOuvertureCandidatures->copy()->addHours(rand(1, 48)),
                    'code_cand_temp' => 'TEMP-' . strtoupper(Str::random(10)),
                    'code_cand_def' => 'CAND-2024-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                    'numero_candidature' => 'NUM-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                    'qr_code' => Str::uuid(),
                    'statut_candidature' => StatutCandidature::VALIDE,
                    // 'statut_inscription' => 'ACTIF', // Supprimé par migration
                    'date_inscription' => $this->dateOuvertureCandidatures->copy()->addDays(1),
                    'date_validation' => $this->dateValidationPaiements,
                    'created_at' => $this->dateOuvertureCandidatures->copy()->addHours(rand(1, 48)),
                    'updated_at' => $this->dateValidationPaiements,
                ]
            );

            // Créer les documents
            $this->creerDocuments($candidature);

            // Créer le paiement
            $this->creerPaiement($candidature);

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine();
        $this->command->info("  ✓ {$this->nombreCandidats} candidats créés avec succès");
        $this->command->newLine();
    }

    private function creerDocuments(Candidature $candidature): void
    {
        foreach ($this->documentsRequis as $docRequis) {
            Document::updateOrCreate(
                [
                    'candidature_id' => $candidature->id,
                    'document_requis_id' => $docRequis->id,
                ],
                [
                    'id' => Str::uuid(),
                    'type_document' => $docRequis->type_document,
                    'fichier_url' => 'documents/simulation/' . Str::uuid() . '.pdf',
                    'nom_original' => $docRequis->type_document->label() . '.pdf',
                    'statut_verification' => StatutVerificationDocument::VALIDE,
                    'date_verification' => $this->dateValidationDocuments,
                    'created_at' => $this->dateOuvertureCandidatures->copy()->addDays(rand(1, 5)),
                    'updated_at' => $this->dateValidationDocuments,
                ]
            );
        }
    }

    private function creerPaiement(Candidature $candidature): void
    {
        Paiement::create([
            'id' => Str::uuid(),
            'candidat_id' => $candidature->candidat_id,
            'concours_id' => $candidature->concours_id,
            'reference' => 'PAY-' . strtoupper(Str::random(12)),
            'montant' => 25000,
            'preuve_paiement' => 'paiements/simulation/' . Str::uuid() . '.pdf',
            'montant_ocr' => 25000,
            'date_ocr' => $this->dateOuvertureCandidatures->copy()->addDays(rand(1, 10)),
            'banque_ocr' => $this->faker->randomElement(['BICEC', 'Afriland', 'UBA', 'Ecobank', 'SCB']),
            'reference_ocr' => 'REF-' . $this->faker->numerify('##########'),
            'ocr_confidence' => 0.95,
            'statut' => StatutPaiement::VERIFIED,
            'validated_at' => $this->dateValidationPaiements,
            'validation_notes' => 'Paiement validé automatiquement',
            'created_at' => $this->dateOuvertureCandidatures->copy()->addDays(rand(1, 10)),
            'updated_at' => $this->dateValidationPaiements,
        ]);
    }

    private function creerEpreuvesEtPlanning(Concours $concours): void
    {
        $this->command->info('📝 Création des épreuves et du planning...');

        // Récupérer ou créer les matières
        $matieres = Matiere::whereIn('code_matiere', ['MATH', 'PHYS', 'FRAN', 'ANG'])
            ->get();

        if ($matieres->count() < 4) {
            $this->command->warn('  ⚠ Création des matières manquantes...');
            $matieresData = [
                ['code_matiere' => 'MATH', 'libelle_matiere' => 'Mathématiques'],
                ['code_matiere' => 'PHYS', 'libelle_matiere' => 'Physique'],
                ['code_matiere' => 'FRAN', 'libelle_matiere' => 'Français'],
                ['code_matiere' => 'ANG', 'libelle_matiere' => 'Anglais'],
            ];

            foreach ($matieresData as $data) {
                Matiere::firstOrCreate(
                    ['code_matiere' => $data['code_matiere']],
                    [
                        'id' => Str::uuid(),
                        'libelle_matiere' => $data['libelle_matiere'],
                        'est_actif' => true,
                    ]
                );
            }

            $matieres = Matiere::whereIn('code_matiere', ['MATH', 'PHYS', 'FRAN', 'ANG'])->get();
        }

        $this->epreuves = collect();
        $heureDebut = 8;

        foreach ($matieres as $index => $matiere) {
            // Créer l'épreuve
            $epreuve = Epreuve::create([
                'id_epreuve' => Str::uuid(),
                'concours_id' => $concours->id,
                'matiere_id' => $matiere->id,
                'coefficient' => $this->faker->randomElement([2, 3, 4]),
                'duree_minutes' => 120,
                'note_eliminatoire' => 5,
                'est_actif' => true,
            ]);

            $this->epreuves->push($epreuve);

            // Créer le planning pour chaque centre
            foreach ($this->centres as $centre) {
                PlanningEpreuve::create([
                    'id' => Str::uuid(),
                    'epreuve_id' => $epreuve->id_epreuve,
                    'centre_id' => $centre->id,
                    'date_epreuve' => $this->dateExamen->copy()->addDays($index),
                    'heure_debut' => sprintf('%02d:00:00', $heureDebut),
                    'heure_fin' => sprintf('%02d:00:00', $heureDebut + 2),
                    'salle' => 'Salle ' . chr(65 + $index),
                ]);
            }
        }

        $this->command->info("  ✓ {$this->epreuves->count()} épreuves créées");
        $this->command->info("  ✓ Planning créé pour {$this->centres->count()} centres");
        $this->command->newLine();
    }

    private function saisirNotes(Concours $concours): void
    {
        $this->command->info('📊 Saisie des notes...');

        $candidatures = Candidature::where('concours_id', $concours->id)
            ->where('statut', StatutCandidature::VALIDE)
            ->get();

        $totalNotes = $candidatures->count() * $this->epreuves->count();
        $progressBar = $this->command->getOutput()->createProgressBar($totalNotes);
        $progressBar->start();

        foreach ($candidatures as $candidature) {
            foreach ($this->epreuves as $epreuve) {
                // Générer une note réaliste (distribution normale autour de 10)
                $note = max(0, min(20, $this->faker->numberBetween(3, 18) + $this->faker->randomFloat(2, -2, 2)));

                Note::create([
                    'id' => Str::uuid(),
                    'candidature_id' => $candidature->id,
                    'epreuve_id' => $epreuve->id_epreuve,
                    'valeur' => round($note, 2),
                    'statut' => StatutNote::VALIDEE,
                    'created_at' => $this->dateSaisieNotes,
                    'updated_at' => $this->dateSaisieNotes,
                ]);

                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->command->newLine();
        $this->command->info("  ✓ {$totalNotes} notes saisies");
        $this->command->newLine();
    }

    private function calculerResultatsSansPublier(Concours $concours): void
    {
        $this->command->info('🏆 Calcul des résultats (sans publication)...');

        $candidatures = Candidature::where('concours_id', $concours->id)
            ->where('statut_candidature', StatutCandidature::VALIDE)
            ->with(['notes.epreuve'])
            ->get();

        $progressBar = $this->command->getOutput()->createProgressBar($candidatures->count());
        $progressBar->start();

        foreach ($candidatures as $candidature) {
            $totalPoints = 0;
            $totalCoefficients = 0;
            $estAdmis = true;
            $estEliminatoire = false;

            foreach ($candidature->notes as $note) {
                $coefficient = $note->epreuve->coefficient;
                $totalPoints += $note->valeur * $coefficient;
                $totalCoefficients += $coefficient;

                // Vérifier la note éliminatoire
                if ($note->valeur < $note->epreuve->note_eliminatoire) {
                    $estAdmis = false;
                    $estEliminatoire = true;
                }
            }

            $moyenne = $totalCoefficients > 0 ? $totalPoints / $totalCoefficients : 0;

            // Appliquer un seuil d'admission (ex: 10/20)
            if ($moyenne < 10) {
                $estAdmis = false;
            }

            // Créer le résultat SANS le publier
            ResultatFinal::create([
                'id' => Str::uuid(),
                'candidature_id' => $candidature->id,
                'moyenne_generale' => round($moyenne, 2),
                'rang' => 0, // Sera calculé après
                'est_admis' => $estAdmis,
                'est_publie' => false, // ❌ NON PUBLIÉ
                'date_publication' => null, // ❌ PAS DE DATE DE PUBLICATION
                'created_at' => $this->dateSaisieNotes->copy()->addDays(2), // Calculé 2 jours après saisie
                'updated_at' => $this->dateSaisieNotes->copy()->addDays(2),
            ]);

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine();

        // Calculer les rangs par filière
        $this->calculerRangs($concours);

        $this->command->info('  ✓ Résultats calculés (non publiés)');
        $this->command->newLine();
    }

    private function calculerEtPublierResultats(Concours $concours): void
    {
        $this->command->info('🏆 Calcul et publication des résultats...');

        $candidatures = Candidature::where('concours_id', $concours->id)
            ->where('statut_candidature', StatutCandidature::VALIDE)
            ->with(['notes.epreuve'])
            ->get();

        $progressBar = $this->command->getOutput()->createProgressBar($candidatures->count());
        $progressBar->start();

        foreach ($candidatures as $candidature) {
            $totalPoints = 0;
            $totalCoefficients = 0;
            $estAdmis = true;

            foreach ($candidature->notes as $note) {
                $coefficient = $note->epreuve->coefficient;
                $totalPoints += $note->valeur * $coefficient;
                $totalCoefficients += $coefficient;

                // Vérifier la note éliminatoire
                if ($note->valeur < $note->epreuve->note_eliminatoire) {
                    $estAdmis = false;
                }
            }

            $moyenne = $totalCoefficients > 0 ? $totalPoints / $totalCoefficients : 0;

            // Appliquer un seuil d'admission (ex: 10/20)
            if ($moyenne < 10) {
                $estAdmis = false;
            }

            ResultatFinal::create([
                'id' => Str::uuid(),
                'candidature_id' => $candidature->id,
                'moyenne_generale' => round($moyenne, 2),
                'rang' => 0, // Sera calculé après
                'est_admis' => $estAdmis,
                'est_publie' => true,
                'date_publication' => $this->datePublicationResultats,
                'created_at' => $this->datePublicationResultats,
            ]);

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine();

        // Calculer les rangs par filière
        $this->calculerRangs($concours);

        $this->command->info('  ✓ Résultats calculés et publiés');
        $this->command->newLine();
    }

    private function calculerRangs(Concours $concours): void
    {
        $this->command->info('  Calcul des rangs par filière...');

        foreach ($this->filieres as $filiere) {
            $resultats = ResultatFinal::whereHas('candidature', function ($query) use ($concours, $filiere) {
                $query->where('concours_id', $concours->id)
                    ->where('filiere_id', $filiere->id);
            })
                ->orderBy('moyenne_generale', 'desc')
                ->get();

            $rang = 1;
            foreach ($resultats as $resultat) {
                $resultat->update(['rang' => $rang++]);
            }
        }

        $this->command->info('  ✓ Rangs calculés');
    }

    private function afficherRecapitulatif(Concours $concours): void
    {
        $this->command->newLine();
        $this->command->info('╔════════════════════════════════════════════════════════════╗');
        $this->command->info('║                    RÉCAPITULATIF                           ║');
        $this->command->info('╚════════════════════════════════════════════════════════════╝');
        $this->command->newLine();

        $totalCandidatures = Candidature::where('concours_id', $concours->id)->count();
        $totalDocuments = Document::whereHas('candidature', function ($query) use ($concours) {
            $query->where('concours_id', $concours->id);
        })->count();
        $totalPaiements = Paiement::where('concours_id', $concours->id)->count();
        $totalNotes = Note::whereHas('candidature', function ($query) use ($concours) {
            $query->where('concours_id', $concours->id);
        })->count();
        $totalResultats = ResultatFinal::whereHas('candidature', function ($query) use ($concours) {
            $query->where('concours_id', $concours->id);
        })->count();
        $totalAdmis = ResultatFinal::whereHas('candidature', function ($query) use ($concours) {
            $query->where('concours_id', $concours->id);
        })->where('est_admis', true)->count();
        $totalPublies = ResultatFinal::whereHas('candidature', function ($query) use ($concours) {
            $query->where('concours_id', $concours->id);
        })->where('est_publie', true)->count();

        $this->command->info("🎯 Concours: {$concours->libelle_concours}");
        $this->command->info("🏫 École: {$concours->ecole->nom_ecole}");
        $this->command->newLine();
        $this->command->info('📊 Statistiques:');
        $this->command->info("  • Candidatures: {$totalCandidatures}");
        $this->command->info("  • Documents soumis: {$totalDocuments}");
        $this->command->info("  • Paiements validés: {$totalPaiements}");
        $this->command->info("  • Épreuves: {$this->epreuves->count()}");
        $this->command->info("  • Notes saisies: {$totalNotes}");
        $this->command->info("  • Résultats calculés: {$totalResultats}");
        $this->command->info("  • Résultats publiés: {$totalPublies}");
        $this->command->info("  • Candidats admis: {$totalAdmis}");
        if ($totalResultats > 0) {
            $this->command->info("  • Taux d'admission: " . round(($totalAdmis / $totalResultats) * 100, 2) . '%');
        }
        $this->command->newLine();

        if ($totalPublies === 0) {
            $this->command->warn('⚠️  Les résultats sont calculés mais NON PUBLIÉS');
            $this->command->info('   Pour publier les résultats, utilisez le service de publication');
        }

        $this->command->newLine();
        $this->command->info('✅ Simulation complète terminée avec succès!');
        $this->command->newLine();
    }
}
