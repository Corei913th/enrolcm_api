<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Utilisateur;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Paiement;
use App\Models\Note;
use App\Models\Concours;
use App\Models\Session;
use App\Models\Centre;
use App\Models\Filiere;
use App\Models\Epreuve;
use App\Models\Matiere;
use App\Models\PlanningEpreuve;
use App\Models\ResultatFinal;
use App\Models\DocumentRequis;
use App\Models\Document;
use App\Enums\StatutCandidature;
use App\Enums\StatutPaiement;
use App\Enums\StatutNote;
use App\Enums\RegionCameroun;
use App\Enums\TypeUtilisateur;
use App\Enums\TypeDocument;
use App\Enums\StatutVerificationDocument;
use App\Enums\NiveauScolaire;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ConcoursCompletSeeder extends Seeder
{
  private $faker;
  private $concoursId;
  private $nombreCandidats = 50; // Réduit pour performance
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

  public function run(): void
  {
    $this->faker = Faker::create('fr_FR');
    $this->concoursId = Concours::oldest()->first()?->id;

    $this->command->info("╔════════════════════════════════════════════════════════════╗");
    $this->command->info("║      GÉNÉRATION DE DONNÉES DE TEST POUR CONCOURS          ║");
    $this->command->info("╚════════════════════════════════════════════════════════════╝");
    $this->command->newLine();

    // Initialiser les dates du workflow
    $this->initialiserDatesWorkflow();

    DB::beginTransaction();
    try {
      // 1. Vérifier/Préparer le concours
      $concours = $this->preparerConcours();

      // 2. Préparer les données de base
      $this->preparerDonneesBase($concours);

      // 3. Créer les candidats et candidatures avec statuts variés
      $this->creerCandidatsEtCandidatures($concours);

      DB::commit();

      $this->afficherStatistiques();
    } catch (\Exception $e) {
      DB::rollBack();
      $this->command->error("❌ Erreur: " . $e->getMessage());
      $this->command->error($e->getTraceAsString());
      throw $e;
    }
  }

  private function initialiserDatesWorkflow(): void
  {
    $this->command->info("📅 Initialisation du calendrier du concours...");

    // Workflow sur 3 mois pour la simulation
    $this->dateOuvertureCandidatures = now()->subMonths(3);
    $this->dateLimiteCandidatures = now()->subMonths(2)->subDays(15);
    $this->dateValidationDocuments = now()->subMonths(2)->subDays(10);
    $this->dateValidationPaiements = now()->subMonths(2)->subDays(5);
    $this->dateExamen = now()->subMonth()->subDays(10);
    $this->dateSaisieNotes = now()->subMonth();

    $this->command->info("  ✓ Ouverture candidatures: {$this->dateOuvertureCandidatures->format('d/m/Y')}");
    $this->command->info("  ✓ Limite candidatures: {$this->dateLimiteCandidatures->format('d/m/Y')}");
    $this->command->info("  ✓ Validation documents: {$this->dateValidationDocuments->format('d/m/Y')}");
    $this->command->info("  ✓ Validation paiements: {$this->dateValidationPaiements->format('d/m/Y')}");
    $this->command->info("  ✓ Date examen: {$this->dateExamen->format('d/m/Y')}");
    $this->command->info("  ✓ Saisie notes: {$this->dateSaisieNotes->format('d/m/Y')}");
    $this->command->newLine();
  }

  private function preparerConcours(): Concours
  {
    $this->command->info("🎯 Préparation du concours...");

    $concours = Concours::find($this->concoursId);

    if (!$concours) {
      $this->command->error("❌ Concours {$this->concoursId} introuvable!");
      throw new \Exception("Concours introuvable");
    }

    // Mettre à jour les dates du concours
    $concours->update([
      'date_limite_depot' => $this->dateLimiteCandidatures,
      'date_examen' => $this->dateExamen,
      'est_actif' => true,
    ]);

    $this->command->info("  ✓ Concours: {$concours->libelle_concours}");
    $this->command->info("  ✓ École: " . ($concours->ecole->nom_ecole ?? 'N/A'));
    $this->command->newLine();

    return $concours;
  }

  private function preparerDonneesBase(Concours $concours): void
  {
    $this->command->info("📦 Préparation des données de base...");

    // Session
    $this->session = Session::where('est_actif', true)->first();
    if (!$this->session) {
      throw new \Exception("Aucune session active trouvée");
    }

    // Vérifier la relation concours-session
    if (!$concours->sessions()->where('sessions.id', $this->session->id)->exists()) {
      $concours->sessions()->attach($this->session->id);
    }

    // Centres
    $this->centres = Centre::where('est_actif', true)->limit(5)->get();
    if ($this->centres->isEmpty()) {
      throw new \Exception("Aucun centre actif trouvé");
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

    // Épreuves (via planning_epreuves)
    $this->epreuves = collect();
    $planningEpreuves = DB::table('planning_epreuves')
      ->where('concours_id', $concours->id)
      ->where('session_id', $this->session->id)
      ->get();

    if ($planningEpreuves->isNotEmpty()) {
      $epreuveIds = $planningEpreuves->pluck('epreuve_id')->unique();
      $this->epreuves = Epreuve::whereIn('id_epreuve', $epreuveIds)->get();
    }

    if ($this->epreuves->isEmpty()) {
      $this->command->warn("  ⚠ Aucune épreuve trouvée. Les notes ne seront pas créées.");
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
    $this->command->info("  ✓ Épreuves: {$this->epreuves->count()}");
    $this->command->info("  ✓ Documents requis: {$this->documentsRequis->count()}");
    $this->command->newLine();
  }

  private function creerCandidatsEtCandidatures(Concours $concours): void
  {
    $this->command->info("👥 Création des candidats et candidatures...");
    $this->command->info("  Nombre de candidats à créer: {$this->nombreCandidats}");

    $progressBar = $this->command->getOutput()->createProgressBar($this->nombreCandidats);
    $progressBar->start();

    for ($i = 0; $i < $this->nombreCandidats; $i++) {
      $this->creerCandidatComplet($concours, $i);
      $progressBar->advance();
    }

    $progressBar->finish();
    $this->command->newLine();
    $this->command->info("  ✓ {$this->nombreCandidats} candidats créés avec succès");
    $this->command->newLine();
  }

  private function creerCandidatComplet(Concours $concours, int $index): void
  {
    // Récupérer les critères d'éligibilité du concours
    $spec = $concours->specConcours;

    // 1. Créer l'utilisateur
    $email = "candidat.test.{$index}@enrolcm.test";
    $nom = $this->faker->lastName;
    $prenom = $this->faker->firstName;
    $userName = strtolower($prenom . '.' . $nom . $index);

    $utilisateur = Utilisateur::updateOrCreate(
      ['email' => $email],
      [
        'id' => Str::uuid(),
        'user_name' => $userName,
        'telephone' => $this->genererTelephoneCamerounais(),
        'mot_de_passe' => Hash::make('password'),
        'type_utilisateur' => TypeUtilisateur::CANDIDAT,
        'est_actif' => true,
        'email_verifie' => false,
        'created_at' => $this->dateOuvertureCandidatures,
      ]
    );

    // 2. Créer le candidat avec des données éligibles
    $sexe = $this->faker->randomElement(['M', 'F']);

    // Générer un âge éligible selon les critères du concours
    $ageMin = $spec->age_minimum ?? 17;
    $ageMax = $spec->age_maximum ?? 30;
    $age = $this->faker->numberBetween($ageMin, $ageMax);
    $dateNaissance = now()->subYears($age)->subDays($this->faker->numberBetween(0, 364));

    // Choisir une série de bac éligible
    $seriesAcceptees = $spec->series_bac_acceptees ?? ['C', 'D', 'E', 'F'];
    $serieBac = $this->faker->randomElement($seriesAcceptees);

    // Choisir une nationalité éligible
    $nationalitesAcceptees = $spec->nationalites_acceptees ?? ['Camerounaise'];
    $nationalite = $this->faker->randomElement($nationalitesAcceptees);

    $candidat = Candidat::updateOrCreate(
      ['utilisateur_id' => $utilisateur->id],
      [
        'nom_cand' => $nom,
        'prenom_cand' => $prenom,
        'sexe_cand' => $sexe,
        'date_naissance_cand' => $dateNaissance,
        'lieu_naissance_cand' => $this->faker->city,
        'age_cand' => $age,
        'nationalite_cand' => $nationalite,
        'adresse_cand' => $this->faker->address,
        'region' => $this->faker->randomElement(RegionCameroun::cases()),
        'departement' => $this->faker->city,
        'arrondissement' => $this->faker->city,
        'numero_cni' => $this->faker->unique()->numerify('###########'),
        'date_delivrance_cni' => $this->faker->dateTimeBetween('-5 years', '-1 year'),
        'nom_tuteur_cand' => $this->faker->name,
        'telephone_tuteur_cand' => $this->genererTelephoneCamerounais(),
        'nom_parent' => $this->faker->name,
        'telephone_parent' => $this->genererTelephoneCamerounais(),
        'nom_pere' => $this->faker->name('male'),
        'telephone_pere' => $this->genererTelephoneCamerounais(),
        'a_handicap' => $this->faker->boolean(5),
        'type_handicap' => null,
        'ethnie_cand' => $this->faker->randomElement(['Bamiléké', 'Beti', 'Douala', 'Fulani', 'Bassa']),
        'statut_matrimonial' => $this->faker->randomElement(['Célibataire', 'Marié(e)', 'Divorcé(e)']),
        'niveau_scolaire' => $this->faker->randomElement([NiveauScolaire::BACCALAUREAT->value, NiveauScolaire::LICENCE->value, NiveauScolaire::MASTER->value]),
        'etablissement_origine' => $this->faker->company . ' College',
        'ville_etablissement' => $this->faker->city,
        'serie_bac' => $serieBac,
        'annee_obtention_bac' => $this->faker->numberBetween(2018, 2024),
        'mention' => $this->faker->randomElement(['PASSABLE', 'ASSEZ_BIEN', 'BIEN', 'TRES_BIEN', 'EXCELLENT']),
        'created_at' => $this->dateOuvertureCandidatures,
      ]
    );

    // 3. Workflow réaliste avec statuts mixés
    // Avec validation automatique, moins de rejets manuels
    $statutsPossibles = [
      StatutCandidature::BROUILLON->value => 3,           // 3% brouillons
      StatutCandidature::SOUMISE->value => 7,             // 7% soumises
      StatutCandidature::DOCUMENTS_VERIFIES->value => 5,  // 5% docs vérifiés
      StatutCandidature::PAIEMENT_VERIFIE->value => 10,   // 10% paiement vérifié
      StatutCandidature::VALIDE->value => 73,             // 73% validées (auto-validation)
      StatutCandidature::REJETEE->value => 2,             // 2% rejetées (moins avec auto-validation)
    ];

    $statut = $this->choisirStatutPondere($statutsPossibles);

    // Dates cohérentes selon le workflow
    $dateInscription = $this->dateOuvertureCandidatures->copy()->addHours(rand(1, 48));
    $dateCandidature = $dateInscription;
    $dateValidation = null;
    $qrCode = null;

    // Générer QR code pour candidatures VALIDE
    if ($statut === StatutCandidature::VALIDE->value) {
      $dateValidation = $this->dateValidationPaiements;
      $qrCode = Str::uuid();
    }

    $candidature = Candidature::updateOrCreate(
      [
        'candidat_id' => $candidat->utilisateur_id,
        'concours_id' => $concours->id,
        'session_id' => $this->session->id,
      ],
      [
        'id' => Str::uuid(),
        'centre_examen_id' => $this->centres->random()->id,
        'centre_depot_id' => $this->centres->random()->id,
        'date_candidature' => $dateCandidature,
        'code_cand_temp' => $statut !== StatutCandidature::BROUILLON->value ?
          'TEMP-' . strtoupper(Str::random(10)) :
          null,
        'code_cand_def' => $statut === StatutCandidature::VALIDE->value ?
          'CAND-2026-' . str_pad($index + 1, 6, '0', STR_PAD_LEFT) :
          null,
        'numero_candidature' => $statut === StatutCandidature::VALIDE->value ?
          'NUM-' . str_pad($index + 1, 6, '0', STR_PAD_LEFT) :
          null,
        'qr_code' => $qrCode,
        'statut_candidature' => $statut,
        'date_inscription' => $statut !== StatutCandidature::BROUILLON->value ? $dateInscription : null,
        'date_validation' => $dateValidation,
        'motif_rejet' => $statut === StatutCandidature::REJETEE->value ?
          $this->faker->randomElement([
            'Documents incomplets',
            'Paiement non conforme',
            'Informations erronées',
            'Hors délai'
          ]) :
          null,
        'created_at' => $dateInscription,
        'updated_at' => $dateValidation ?? $dateInscription,
      ]
    );

    // 4. Créer le paiement (seulement si candidature soumise ou plus)
    if ($statut !== StatutCandidature::BROUILLON->value) {
      $this->creerPaiement($candidature, $statut);
    }

    // 5. Créer les documents (seulement si candidature soumise ou plus)
    if ($statut !== StatutCandidature::BROUILLON->value) {
      $this->creerDocuments($candidature, $statut);
    }

    // 6. Créer les notes (seulement pour les candidatures validées)
    if ($statut === StatutCandidature::VALIDE->value && !$this->epreuves->isEmpty()) {
      $this->creerNotes($candidature);
    }
  }

  private function creerPaiement(Candidature $candidature, string $statut): void
  {
    $montantConcours = 25000;

    // Statut paiement cohérent avec statut candidature
    $statutPaiement = match ($statut) {
      StatutCandidature::VALIDE->value => StatutPaiement::VERIFIED,
      StatutCandidature::PAIEMENT_VERIFIE->value => $this->faker->randomElement([
        StatutPaiement::OCR_VERIFIE,
        StatutPaiement::PENDING_MANUAL_REVIEW
      ]),
      StatutCandidature::DOCUMENTS_VERIFIES->value => $this->faker->randomElement([
        StatutPaiement::PENDING,
        StatutPaiement::OCR_VERIFIE
      ]),
      StatutCandidature::REJETEE->value => StatutPaiement::REJECTED,
      default => StatutPaiement::PENDING,
    };

    Paiement::create([
      'id' => Str::uuid(),
      'candidat_id' => $candidature->candidat_id,
      'concours_id' => $candidature->concours_id,
      'reference' => 'PAY-' . strtoupper(Str::random(12)),
      'montant' => $montantConcours,
      'preuve_paiement' => 'paiements/test/' . Str::uuid() . '.pdf',
      'montant_ocr' => $statutPaiement !== StatutPaiement::PENDING ?
        $montantConcours + $this->faker->numberBetween(-100, 100) :
        null,
      'date_ocr' => $statutPaiement !== StatutPaiement::PENDING ?
        $this->dateOuvertureCandidatures->copy()->addDays(rand(1, 10)) :
        null,
      'banque_ocr' => $statutPaiement !== StatutPaiement::PENDING ?
        $this->faker->randomElement(['BICEC', 'Afriland', 'UBA', 'Ecobank', 'SCB']) :
        null,
      'reference_ocr' => $statutPaiement !== StatutPaiement::PENDING ?
        'REF-' . $this->faker->numerify('##########') :
        null,
      'ocr_confidence' => $statutPaiement !== StatutPaiement::PENDING ?
        $this->faker->randomFloat(2, 0.75, 0.99) :
        null,
      'statut' => $statutPaiement,
      'validated_at' => $statutPaiement === StatutPaiement::VERIFIED ?
        $this->dateValidationPaiements :
        null,
      'validation_notes' => $statutPaiement === StatutPaiement::VERIFIED ?
        'Paiement validé automatiquement' :
        null,
      'motif_rejet' => $statutPaiement === StatutPaiement::REJECTED ?
        $this->faker->randomElement([
          'Montant incorrect',
          'Reçu illisible',
          'Référence invalide',
          'Compte bancaire non reconnu'
        ]) :
        null,
      'created_at' => $this->dateOuvertureCandidatures->copy()->addDays(rand(1, 10)),
      'updated_at' => $statutPaiement === StatutPaiement::VERIFIED ?
        $this->dateValidationPaiements :
        $this->dateOuvertureCandidatures->copy()->addDays(rand(1, 10)),
    ]);
  }

  private function creerDocuments(Candidature $candidature, string $statut): void
  {
    foreach ($this->documentsRequis as $docRequis) {
      // Déterminer le statut du document selon le statut de la candidature
      $statutDoc = match ($statut) {
        StatutCandidature::VALIDE->value => StatutVerificationDocument::VALIDE,
        StatutCandidature::DOCUMENTS_VERIFIES->value, StatutCandidature::PAIEMENT_VERIFIE->value => StatutVerificationDocument::VALIDE,
        StatutCandidature::REJETEE->value => $this->faker->randomElement([
          StatutVerificationDocument::REJETE,
          StatutVerificationDocument::EN_ATTENTE
        ]),
        default => StatutVerificationDocument::EN_ATTENTE,
      };

      Document::updateOrCreate(
        [
          'candidature_id' => $candidature->id,
          'document_requis_id' => $docRequis->id,
        ],
        [
          'id' => Str::uuid(),
          'type_document' => $docRequis->type_document,
          'fichier_url' => 'documents/test/' . Str::uuid() . '.pdf',
          'nom_original' => $docRequis->type_document->label() . '.pdf',
          'statut_verification' => $statutDoc,
          'date_verification' => $statutDoc === StatutVerificationDocument::VALIDE ?
            $this->dateValidationDocuments :
            null,
          'created_at' => $this->dateOuvertureCandidatures->copy()->addDays(rand(1, 5)),
          'updated_at' => $statutDoc === StatutVerificationDocument::VALIDE ?
            $this->dateValidationDocuments :
            $this->dateOuvertureCandidatures->copy()->addDays(rand(1, 5)),
        ]
      );
    }
  }

  private function creerNotes(Candidature $candidature): void
  {
    foreach ($this->epreuves as $epreuve) {
      // 85% des candidats validés ont des notes
      if ($this->faker->boolean(85)) {
        $note = $this->faker->randomFloat(2, 3, 20);

        // Statut de la note varié
        $statutsNotes = [
          StatutNote::SAISIE_TERMINEE->value => 70,  // 70% saisie terminée
          StatutNote::VALIDEE->value => 25,          // 25% validées
          StatutNote::EN_ATTENTE_SAISIE->value => 5, // 5% en attente
        ];
        $statutNote = $this->choisirStatutPondere($statutsNotes);

        Note::create([
          'id' => Str::uuid(),
          'candidature_id' => $candidature->id,
          'epreuve_id' => $epreuve->id_epreuve,
          'valeur' => $note,
          'date_saisie' => $this->dateSaisieNotes,
          'est_definitive' => $statutNote === StatutNote::VALIDEE->value,
          'est_eliminatoire' => $note < 7,
          'statut' => $statutNote,
          'created_at' => $this->dateSaisieNotes,
          'updated_at' => $this->dateSaisieNotes,
        ]);
      }
    }
  }

  private function choisirStatutPondere(array $statuts): string
  {
    $total = array_sum($statuts);
    $rand = $this->faker->numberBetween(1, $total);

    $cumul = 0;
    foreach ($statuts as $statut => $poids) {
      $cumul += $poids;
      if ($rand <= $cumul) {
        return $statut;
      }
    }

    return array_key_first($statuts);
  }

  private function genererTelephoneCamerounais(): string
  {
    $prefixes = [
      '650',
      '651',
      '652',
      '653',
      '654',
      '655',
      '656',
      '657',
      '658',
      '659',
      '670',
      '671',
      '672',
      '673',
      '674',
      '675',
      '676',
      '677',
      '678',
      '679',
      '680',
      '681',
      '682',
      '683',
      '684',
      '685',
      '686',
      '687',
      '688',
      '689',
      '690',
      '691',
      '692',
      '693',
      '694',
      '695',
      '696',
      '697',
      '698',
      '699'
    ];

    return $this->faker->randomElement($prefixes) . $this->faker->numerify('######');
  }

  private function genererNumeroCNI(): string
  {
    return $this->faker->unique()->numerify('###########');
  }

  private function afficherStatistiques(): void
  {
    $this->command->newLine();
    $this->command->info("╔════════════════════════════════════════════════════════════╗");
    $this->command->info("║                    RÉCAPITULATIF                           ║");
    $this->command->info("╚════════════════════════════════════════════════════════════╝");
    $this->command->newLine();

    $concours = Concours::find($this->concoursId);

    // Statistiques globales
    $totalCandidatures = Candidature::where('concours_id', $this->concoursId)->count();
    $totalDocuments = Document::whereHas('candidature', function ($query) {
      $query->where('concours_id', $this->concoursId);
    })->count();
    $totalPaiements = Paiement::where('concours_id', $this->concoursId)->count();
    $totalNotes = Note::whereHas('candidature', function ($query) {
      $query->where('concours_id', $this->concoursId);
    })->count();

    $this->command->info("🎯 Concours: {$concours->libelle_concours}");
    $this->command->info("🏫 École: " . ($concours->ecole->nom_ecole ?? 'N/A'));
    $this->command->newLine();
    $this->command->info("📊 Statistiques:");
    $this->command->info("  • Candidatures: {$totalCandidatures}");
    $this->command->info("  • Documents soumis: {$totalDocuments}");
    $this->command->info("  • Paiements: {$totalPaiements}");
    $this->command->info("  • Notes saisies: {$totalNotes}");
    $this->command->newLine();

    // Statistiques par statut de candidature
    $this->command->info("📝 Candidatures par statut:");
    $candidatures = Candidature::where('concours_id', $this->concoursId);
    $this->command->info("  • Brouillons: " . (clone $candidatures)->where('statut_candidature', StatutCandidature::BROUILLON)->count());
    $this->command->info("  • Soumises: " . (clone $candidatures)->where('statut_candidature', StatutCandidature::SOUMISE)->count());
    $this->command->info("  • Documents vérifiés: " . (clone $candidatures)->where('statut_candidature', StatutCandidature::DOCUMENTS_VERIFIES)->count());
    $this->command->info("  • Paiement vérifié: " . (clone $candidatures)->where('statut_candidature', StatutCandidature::PAIEMENT_VERIFIE)->count());
    $this->command->info("  • Validées: " . (clone $candidatures)->where('statut_candidature', StatutCandidature::VALIDE)->count());
    $this->command->info("  • Rejetées: " . (clone $candidatures)->where('statut_candidature', StatutCandidature::REJETEE)->count());
    $this->command->newLine();

    // Statistiques paiements
    $this->command->info("💰 Paiements par statut:");
    $paiements = Paiement::where('concours_id', $this->concoursId);
    $this->command->info("  • En attente: " . (clone $paiements)->where('statut', StatutPaiement::PENDING)->count());
    $this->command->info("  • OCR vérifié: " . (clone $paiements)->where('statut', StatutPaiement::OCR_VERIFIE)->count());
    $this->command->info("  • Révision manuelle: " . (clone $paiements)->where('statut', StatutPaiement::PENDING_MANUAL_REVIEW)->count());
    $this->command->info("  • Vérifiés: " . (clone $paiements)->where('statut', StatutPaiement::VERIFIED)->count());
    $this->command->info("  • Rejetés: " . (clone $paiements)->where('statut', StatutPaiement::REJECTED)->count());
    $this->command->newLine();

    // Statistiques notes
    if ($totalNotes > 0) {
      $this->command->info("📊 Notes:");
      $notes = Note::whereHas('candidature', function ($q) {
        $q->where('concours_id', $this->concoursId);
      });
      $this->command->info("  • Total notes saisies: " . $notes->count());
      $this->command->info("  • En attente: " . (clone $notes)->where('statut', StatutNote::EN_ATTENTE_SAISIE)->count());
      $this->command->info("  • Saisie terminée: " . (clone $notes)->where('statut', StatutNote::SAISIE_TERMINEE)->count());
      $this->command->info("  • Validées: " . (clone $notes)->where('statut', StatutNote::VALIDEE)->count());
      $this->command->info("  • Notes éliminatoires: " . (clone $notes)->where('est_eliminatoire', true)->count());
      $this->command->newLine();
    }

    $this->command->info("✅ Génération terminée avec succès!");
    $this->command->newLine();
  }
}
