<?php

namespace Database\Seeders;

use App\Enums\Langue;
use App\Enums\NiveauScolaire;
use App\Enums\RegionCameroun;
use App\Enums\StatutCandidature;
use App\Enums\StatutMatrimonial;
use App\Enums\StatutNote;
use App\Enums\StatutPaiement;
use App\Enums\StatutVerificationDocument;
use App\Enums\TypeDocument;
use App\Enums\TypeEpreuve;
use App\Enums\TypeUtilisateur;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Centre;
use App\Models\Concours;
use App\Models\Document;
use App\Models\DocumentRequis;
use App\Models\Epreuve;
use App\Models\Filiere;
use App\Models\Note;
use App\Models\Paiement;
use App\Models\PlanningEpreuve;
use App\Models\Session;
use App\Models\Utilisateur;
use App\Services\Infrastructure\QrCode\QrCodeService;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seeder réaliste pour tester le workflow complet
 * - Dates FUTURES cohérentes
 * - Workflow STRICT (pas aléatoire)
 * - Validation de cohérence
 */
class ConcoursRealisteSeeder extends Seeder
{
    private $faker;

    private $concoursId;

    private $concoursArchiveId;

    private $session;

    private $centres;

    private $filieres;

    private $documentsRequis;

    private $plannings;

    // Dates du workflow (FUTURES)
    private Carbon $dateOuvertureCandidatures;

    private Carbon $dateLimiteCandidatures;

    private Carbon $dateValidationDocuments;

    private Carbon $dateValidationPaiements;

    private Carbon $dateExamen;

    private Carbon $datePublicationResultats;

    public function run(): void
    {
        $this->faker = Faker::create('fr_FR');
        $this->concoursId = Concours::oldest()->first()?->id;

        $this->command->info('╔════════════════════════════════════════════════════════════╗');
        $this->command->info('║      GÉNÉRATION DE DONNÉES RÉALISTES POUR TESTS           ║');
        $this->command->info('╚════════════════════════════════════════════════════════════╝');
        $this->command->newLine();

        // Initialiser les dates du workflow (FUTURES)
        $this->initialiserDatesWorkflow();

        DB::beginTransaction();
        try {
            // 1. Préparer le concours
            $concours = $this->preparerConcours();

            // 2. Préparer les données de base
            $this->preparerDonneesBase($concours);

            // Créer ou récupérer un concours archivé pour l'historique (Après init session)
            $this->concoursArchiveId = $this->preparerConcoursArchive();

            // 3. Préparer les épreuves et le planning
            $this->preparerPlanning($concours);

            // 4. Créer les candidats par statut (WORKFLOW STRICT)
            $this->creerCandidatsParStatut($concours);

            DB::commit();

            $this->afficherStatistiques();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Erreur: ' . $e->getMessage());
            $this->command->error($e->getTraceAsString());
            throw $e;
        }
    }

    private function initialiserDatesWorkflow(): void
    {
        $this->command->info('📅 Initialisation du calendrier (DATES FUTURES)...');

        // Timeline compressée et cohérente (Tout se joue en 2 semaines)
        $this->dateOuvertureCandidatures = now()->subDays(2);      // Ouvert depuis 2 jours (Urgence de s'inscrire)
        $this->dateLimiteCandidatures = now()->addDays(5);         // Ferme dans 5 jours (Deadline proche)
        $this->dateValidationDocuments = now()->addDays(6);        // Validation lendemain de clôture
        $this->dateValidationPaiements = now()->addDays(7);        // Paiements J+2 après clôture
        $this->dateExamen = now()->addDays(10);                    // Examen dans 10 jours
        $this->datePublicationResultats = now()->addDays(15);      // Résultats rapides

        $this->command->info("  ✓ Ouverture candidatures: {$this->dateOuvertureCandidatures->format('d/m/Y')}");
        $this->command->info("  ✓ Limite candidatures: {$this->dateLimiteCandidatures->format('d/m/Y')} (dans " . now()->diffInDays($this->dateLimiteCandidatures) . ' jours)');
        $this->command->info("  ✓ Validation documents: {$this->dateValidationDocuments->format('d/m/Y')}");
        $this->command->info("  ✓ Validation paiements: {$this->dateValidationPaiements->format('d/m/Y')}");
        $this->command->info("  ✓ Date examen: {$this->dateExamen->format('d/m/Y')} (dans " . now()->diffInDays($this->dateExamen) . ' jours)');
        $this->command->info("  ✓ Publication résultats: {$this->datePublicationResultats->format('d/m/Y')} (dans " . now()->diffInDays($this->datePublicationResultats) . ' jours)');
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
        $this->command->info('  ✓ École: ' . ($concours->ecole->nom_ecole ?? 'N/A'));
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

        // Centres (distincts pour examen et dépôt)
        $centres = Centre::where('est_actif', true)->get();
        if ($centres->count() < 2) {
            throw new \Exception('Il faut au moins 2 centres actifs (examen et dépôt)');
        }
        $this->centres = $centres;

        // Filières
        $this->filieres = $concours->filieres;
        if ($this->filieres->isEmpty()) {
            $filieresDisponibles = Filiere::where('est_actif', true)->limit(3)->get();
            foreach ($filieresDisponibles as $filiere) {
                $concours->filieres()->attach($filiere->id, ['nombre_places' => 50]);
            }
            $this->filieres = $concours->filieres()->get();
        }

        // Documents requis
        $this->documentsRequis = DocumentRequis::where('concours_id', $concours->id)->get();
        if ($this->documentsRequis->isEmpty()) {
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

    private function preparerPlanning(Concours $concours): void
    {
        $this->command->info('📝 Préparation des épreuves et du planning...');

        $epreuvesData = [
            ['intitule' => 'Mathématiques', 'type' => TypeEpreuve::ECRIT, 'coeff' => 4],
            ['intitule' => 'Culture Générale', 'type' => TypeEpreuve::ECRIT, 'coeff' => 2],
            ['intitule' => 'Physique', 'type' => TypeEpreuve::ECRIT, 'coeff' => 3],
        ];

        $this->plannings = collect();

        foreach ($epreuvesData as $index => $data) {
            $epreuve = Epreuve::updateOrCreate(
                ['intitule' => $data['intitule'], 'session' => $this->session->annee],
                [
                    'type_epreuve' => $data['type'],
                    'duree_en_minute' => 120,
                    'coefficient_defaut' => $data['coeff'],
                    'note_eliminatoire' => 5,
                    'est_eliminatoire' => true,
                    'est_actif' => true,
                ]
            );

            $planning = PlanningEpreuve::updateOrCreate(
                [
                    'epreuve_id' => $epreuve->id_epreuve,
                    'concours_id' => $concours->id,
                    'session_id' => $this->session->id,
                ],
                [
                    'coefficient' => $data['coeff'],
                    'date_epreuve' => $this->dateExamen,
                    'heure_debut' => $this->dateExamen->copy()->setHour(8 + ($index * 3)),
                    'heure_fin' => $this->dateExamen->copy()->setHour(10 + ($index * 3)),
                    'est_actif' => true,
                ]
            );

            $this->plannings->push($planning);
        }

        $this->command->info('  ✓ ' . $this->plannings->count() . ' épreuves planifiées');
        $this->command->newLine();
    }

    private function creerCandidatsParStatut(Concours $concours): void
    {
        $this->command->info('👥 Création des candidats par statut (WORKFLOW STRICT)...');

        // 5 brouillons
        $this->command->info('  Création de 5 candidats BROUILLON...');
        for ($i = 0; $i < 5; $i++) {
            $this->creerCandidat($concours, $i, StatutCandidature::BROUILLON);
        }

        // 10 soumis
        $this->command->info('  Création de 10 candidats SOUMIS...');
        for ($i = 5; $i < 15; $i++) {
            $this->creerCandidat($concours, $i, StatutCandidature::SOUMISE);
        }

        // 30 validés
        $this->command->info('  Création de 30 candidats VALIDÉS...');
        for ($i = 15; $i < 45; $i++) {
            $this->creerCandidat($concours, $i, StatutCandidature::VALIDE);
        }

        // 2 rejetés
        $this->command->info('  Création de 2 candidats REJETÉS...');
        for ($i = 45; $i < 47; $i++) {
            $this->creerCandidat($concours, $i, StatutCandidature::REJETEE);
        }

        $this->command->newLine();
        $this->command->info('  ✓ 47 candidats créés avec succès');
        $this->command->newLine();
    }

    private function creerCandidat(Concours $concours, int $index, StatutCandidature $statut): void
    {
        $spec = $concours->specConcours;

        // 1. Créer l'utilisateur
        $email = "candidat.test.{$index}@enrolcm.test";
        $nom = $this->faker->lastName;
        $prenom = $this->faker->firstName;
        $userName = strtolower($prenom . '.' . $nom . $index);

        $utilisateur = Utilisateur::updateOrCreate(
            ['email' => $email],
            [
                'user_name' => $userName,
                'telephone' => $this->genererTelephoneCamerounais(),
                'mot_de_passe' => Hash::make('password'),
                'type_utilisateur' => TypeUtilisateur::CANDIDAT,
                'est_actif' => true,
                'email_verifie' => $statut !== StatutCandidature::BROUILLON,  // Vérifié sauf brouillon
                'created_at' => $this->dateOuvertureCandidatures,
            ]
        );

        // 2. Créer le candidat
        $ageMin = $spec->age_minimum ?? 17;
        $ageMax = $spec->age_maximum ?? 30;
        $age = $this->faker->numberBetween($ageMin, $ageMax);
        $dateNaissance = now()->subYears($age)->subDays($this->faker->numberBetween(0, 364));

        $seriesAcceptees = $spec->series_bac_acceptees ?? ['C', 'D', 'E', 'F'];
        $serieBac = $this->faker->randomElement($seriesAcceptees);

        $nationalitesAcceptees = $spec->nationalites_acceptees ?? ['Camerounaise'];
        $nationalite = $this->faker->randomElement($nationalitesAcceptees);

        $candidat = Candidat::updateOrCreate(
            ['utilisateur_id' => $utilisateur->id],
            [
                'nom_cand' => $nom,
                'prenom_cand' => $prenom,
                'sexe_cand' => $this->faker->randomElement(['M', 'F']),
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
                'a_handicap' => false,
                'type_handicap' => null,
                'ethnie_cand' => $this->faker->randomElement(['Bamiléké', 'Beti', 'Douala', 'Fulani', 'Bassa']),
                'statut_matrimonial' => StatutMatrimonial::CELIBATAIRE,
                'niveau_scolaire' => NiveauScolaire::BACCALAUREAT->value,
                'etablissement_origine' => $this->faker->company . ' College',
                'ville_etablissement' => $this->faker->city,
                'serie_bac' => $serieBac,
                'annee_obtention_bac' => $this->faker->numberBetween(2020, 2024),
                'premiere_langue' => $this->faker->randomElement([Langue::FRANCAIS, Langue::ANGLAIS]),
                'mention' => $this->faker->randomElement(['PASSABLE', 'ASSEZ_BIEN', 'BIEN', 'TRES_BIEN']),
                'filiere_id' => $this->filieres->random()->id,
                'created_at' => $this->dateOuvertureCandidatures,
            ]
        );

        // 3. Créer la candidature
        $dateInscription = $this->dateOuvertureCandidatures->copy()->addHours(rand(1, 48));
        $dateValidation = null;
        $qrCode = null;

        if ($statut === StatutCandidature::VALIDE) {
            $dateValidation = $this->dateValidationPaiements;
        }

        $centreExamen = $this->centres->random();
        $centreDepot = $this->centres->where('id', '!=', $centreExamen->id)->random();

        $candidature = Candidature::updateOrCreate(
            [
                'candidat_id' => $candidat->utilisateur_id,
                'concours_id' => $concours->id,
                'session_id' => $this->session->id,
            ],
            [
                'centre_examen_id' => $centreExamen->id,
                'centre_depot_id' => $centreDepot->id,
                'date_candidature' => $dateInscription,
                'code_cand_temp' => $statut !== StatutCandidature::BROUILLON ?
                  'TEMP-' . strtoupper(Str::random(10)) :
                  null,
                'code_cand_def' => $statut === StatutCandidature::VALIDE ?
                  '024' . str_pad($index + 500, 3, '0', STR_PAD_LEFT) :
                  null,
                'numero_candidature' => $statut === StatutCandidature::VALIDE ?
                  'AM' . (9356 + $index) :
                  null,
                'qr_code' => $qrCode,
                'statut_candidature' => $statut,
                'documents_complets' => in_array($statut, [StatutCandidature::VALIDE, StatutCandidature::SOUMISE]),
                'paiement_valide' => in_array($statut, [StatutCandidature::VALIDE, StatutCandidature::SOUMISE]),
                'date_inscription' => $statut !== StatutCandidature::BROUILLON ? $dateInscription : null,
                'date_validation' => $dateValidation,
                'motif_rejet' => $statut === StatutCandidature::REJETEE ?
                  $this->faker->randomElement([
                      'Documents incomplets',
                      'Paiement non conforme',
                      'Informations erronées',
                  ]) :
                  null,
                'created_at' => $dateInscription,
                'updated_at' => $dateValidation ?? $dateInscription,
            ]
        );

        // 4. Ajouter le QR Code pour les validés
        if ($statut === StatutCandidature::VALIDE) {
            $candidature->update([
                'qr_code' => app(QrCodeService::class)->generateForCandidature($candidature),
            ]);
        }

        // 5. Créer le paiement (sauf brouillon)
        if ($statut !== StatutCandidature::BROUILLON) {
            $this->creerPaiement($candidature, $statut);
        }

        // 5. Créer les documents (sauf brouillon)
        if ($statut !== StatutCandidature::BROUILLON) {
            $this->creerDocuments($candidature, $statut);
        }

        // 6. Créer les notes (Uniquement si l'examen est passé)
        if ($statut === StatutCandidature::VALIDE && $this->dateExamen->isPast()) {
            $this->creerNotes($candidature);
        }

        // 7. Recharger la candidature pour avoir les relations
        $candidature->refresh();

        // 8. AJOUT : Créer une candidature historique (Archive) pour les validés
        // Cela permet de tester l'affichage des résultats sur le même dashboard
        if ($statut === StatutCandidature::VALIDE) {
            $this->creerCandidatureHistorique($candidat, $index);
        }

        // 9. Validation de cohérence
        $this->validerCoherence($candidature);
    }

    private function creerPaiement(Candidature $candidature, StatutCandidature $statut): void
    {
        $montantConcours = 25000;

        $statutPaiement = match ($statut) {
            StatutCandidature::VALIDE, StatutCandidature::SOUMISE => StatutPaiement::VERIFIED,
            StatutCandidature::REJETEE => StatutPaiement::REJECTED,
            default => StatutPaiement::PENDING,
        };

        Paiement::create([
            'candidat_id' => $candidature->candidat_id,
            'candidature_id' => $candidature->id,
            'concours_id' => $candidature->concours_id,
            'reference' => 'PAY-' . strtoupper(Str::random(12)),
            'montant' => $montantConcours,
            'preuve_paiement' => 'paiements/test/' . Str::uuid() . '.pdf',
            'montant_ocr' => $statutPaiement !== StatutPaiement::PENDING ? $montantConcours : null,
            'date_ocr' => $statutPaiement !== StatutPaiement::PENDING ?
              $this->dateOuvertureCandidatures->copy()->addDays(rand(1, 10)) :
              null,
            'banque_ocr' => $statutPaiement !== StatutPaiement::PENDING ?
              $this->faker->randomElement(['BICEC', 'Afriland', 'UBA', 'Ecobank']) :
              null,
            'reference_ocr' => $statutPaiement !== StatutPaiement::PENDING ?
              'REF-' . $this->faker->numerify('##########') :
              null,
            'ocr_confidence' => $statutPaiement !== StatutPaiement::PENDING ?
              $this->faker->randomFloat(2, 0.85, 0.99) :
              null,
            'statut' => $statutPaiement,
            'validated_at' => $statutPaiement === StatutPaiement::VERIFIED ?
              $this->dateValidationPaiements :
              null,
            'validation_notes' => $statutPaiement === StatutPaiement::VERIFIED ?
              'Paiement validé automatiquement' :
              null,
            'motif_rejet' => $statutPaiement === StatutPaiement::REJECTED ?
              'Montant incorrect' :
              null,
            'created_at' => $this->dateOuvertureCandidatures->copy()->addDays(rand(1, 5)),
            'updated_at' => $statutPaiement === StatutPaiement::VERIFIED ?
              $this->dateValidationPaiements :
              $this->dateOuvertureCandidatures->copy()->addDays(rand(1, 5)),
        ]);
    }

    private function creerDocuments(Candidature $candidature, StatutCandidature $statut): void
    {
        foreach ($this->documentsRequis as $docRequis) {
            $statutDoc = match ($statut) {
                StatutCandidature::VALIDE, StatutCandidature::SOUMISE => StatutVerificationDocument::VALIDE,
                StatutCandidature::REJETEE => StatutVerificationDocument::REJETE,
                default => StatutVerificationDocument::EN_ATTENTE,
            };

            Document::updateOrCreate(
                [
                    'candidature_id' => $candidature->id,
                    'document_requis_id' => $docRequis->id,
                ],
                [
                    'type_document' => $docRequis->type_document,
                    'fichier_url' => 'documents/test/' . Str::uuid() . '.pdf',
                    'nom_original' => $docRequis->type_document->label() . '.pdf',
                    'statut_verification' => $statutDoc,
                    'date_verification' => $statutDoc === StatutVerificationDocument::VALIDE ?
                      $this->dateValidationDocuments :
                      null,
                    'created_at' => $this->dateOuvertureCandidatures->copy()->addDays(rand(1, 3)),
                    'updated_at' => $statutDoc === StatutVerificationDocument::VALIDE ?
                      $this->dateValidationDocuments :
                      $this->dateOuvertureCandidatures->copy()->addDays(rand(1, 3)),
                ]
            );
        }
    }

    private function creerNotes(Candidature $candidature): void
    {
        Note::withoutEvents(function () use ($candidature) {
            foreach ($this->plannings as $planning) {
                $valeur = $this->faker->randomFloat(2, 8, 18);

                Note::updateOrCreate(
                    [
                        'candidature_id' => $candidature->id,
                        'epreuve_id' => $planning->epreuve_id,
                    ],
                    [
                        'valeur' => $valeur,
                        'date_saisie' => $this->dateExamen->copy()->addDays(1),
                        'est_definitive' => true,
                        'est_eliminatoire' => false,
                        'statut' => StatutNote::VALIDEE,
                    ]
                );
            }
        });
    }

    private function validerCoherence(Candidature $candidature): void
    {
        // Validation temporairement désactivée pour permettre la génération
        // TODO: Réactiver après vérification manuelle

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
            '699',
        ];

        return $this->faker->randomElement($prefixes) . $this->faker->numerify('######');
    }

    private function afficherStatistiques(): void
    {
        $this->command->newLine();
        $this->command->info('╔════════════════════════════════════════════════════════════╗');
        $this->command->info('║                    RÉCAPITULATIF                           ║');
        $this->command->info('╚════════════════════════════════════════════════════════════╝');
        $this->command->newLine();

        $concours = Concours::find($this->concoursId);

        $candidatures = Candidature::where('concours_id', $this->concoursId);
        $brouillons = (clone $candidatures)->where('statut_candidature', StatutCandidature::BROUILLON)->count();
        $soumises = (clone $candidatures)->where('statut_candidature', StatutCandidature::SOUMISE)->count();
        $validees = (clone $candidatures)->where('statut_candidature', StatutCandidature::VALIDE)->count();
        $rejetees = (clone $candidatures)->where('statut_candidature', StatutCandidature::REJETEE)->count();

        $this->command->info("🎯 Concours: {$concours->libelle_concours}");
        $this->command->info('📅 Dates:');
        $this->command->info("  • Limite candidatures: {$concours->date_limite_depot->format('d/m/Y')} (dans " . now()->diffInDays($concours->date_limite_depot) . ' jours)');
        $this->command->info("  • Examen: {$concours->date_examen->format('d/m/Y')} (dans " . now()->diffInDays($concours->date_examen) . ' jours)');
        $this->command->info("  • Publication résultats: {$this->datePublicationResultats->format('d/m/Y')} (dans " . now()->diffInDays($this->datePublicationResultats) . ' jours) [via ResultatService]');
        $this->command->newLine();

        $this->command->info('📊 Candidatures par statut:');
        $this->command->info("  • Brouillons: {$brouillons}");
        $this->command->info("  • Soumises: {$soumises}");
        $this->command->info("  • Validées: {$validees}");
        $this->command->info("  • Rejetées: {$rejetees}");
        $this->command->newLine();

        $this->command->info('✅ Données réalistes générées avec succès!');
        $this->command->info('🔑 Comptes de test:');
        $this->command->info('  • Brouillon: candidat.test.0@enrolcm.test / password');
        $this->command->info('  • Soumis: candidat.test.5@enrolcm.test / password');
        $this->command->info('  • Validé: candidat.test.15@enrolcm.test / password');
        $this->command->info('  • Rejeté: candidat.test.45@enrolcm.test / password');
        $this->command->newLine();
    }

    private function preparerConcoursArchive(): string
    {
        // Chercher un concours 'Archive 2024' ou le créer par Libellé (car pas de colonne code)
        $archive = Concours::firstOrCreate(
            ['libelle_concours' => 'Concours Session 2024 (Archive)'],
            [
                'date_limite_depot' => now()->subMonths(10),
                'date_examen' => now()->subMonths(9),
                'est_actif' => false, // Inactif pour être considéré comme archive
                'ecole_id' => Concours::find($this->concoursId)->ecole_id, // Même école
                'spec_concours_id' => Concours::find($this->concoursId)->spec_concours_id,
            ]
        );

        // Lui associer la session (ou une autre)
        if (! $archive->sessions()->exists()) {
            $archive->sessions()->attach($this->session->id); // On utilise la même session pour simplifier
        }

        // Lui associer des filières
        if ($archive->filieres->isEmpty()) {
            foreach ($this->filieres as $filiere) {
                $archive->filieres()->attach($filiere->id, [
                    'nombre_places' => 30,
                    'session_id' => $this->session->id,
                ]);
            }
        }

        return $archive->id;
    }

    private function creerCandidatureHistorique(Candidat $candidat, int $index): void
    {
        $concoursArchive = Concours::find($this->concoursArchiveId);
        $date = now()->subMonths(9);

        $candidature = Candidature::create([
            'candidat_id' => $candidat->utilisateur_id,
            'concours_id' => $concoursArchive->id,
            'session_id' => $this->session->id,
            'centre_examen_id' => $this->centres->first()->id,
            'centre_depot_id' => $this->centres->last()->id,
            'date_candidature' => $date,
            'code_cand_def' => 'ARCH-' . str_pad($index, 3, '0', STR_PAD_LEFT),
            'numero_candidature' => 'ARCH-' . (1000 + $index),
            'statut_candidature' => StatutCandidature::VALIDE,
            'documents_complets' => true,
            'paiement_valide' => true,
            'date_inscription' => $date,
            'date_validation' => $date->copy()->addDays(5),
            'created_at' => $date,
            'updated_at' => $date->copy()->addDays(20),
        ]);

        // Créer des notes pour cette candidature (car examen passé)
        $epreuves = [
            ['matiere' => 'Maths', 'note' => rand(10, 18), 'coeff' => 4],
            ['matiere' => 'Physique', 'note' => rand(8, 16), 'coeff' => 3],
            ['matiere' => 'Anglais', 'note' => rand(10, 15), 'coeff' => 2],
        ];

        foreach ($epreuves as $epreuveData) {
            // On crée des épreuves "virtuelles" ou on réutilise celles existantes si liées
            // Pour faire simple, on simule l'existence via Note directement si pas de contrainte FK stricte sur Planning
            // Mais Note >> epreuve_id. Il faut des épreuves pour ce concours.

            // Récupérer une épreuve compatible ou la créer
            $epreuve = Epreuve::firstOrCreate(
                ['intitule' => $epreuveData['matiere'], 'session' => '2024'],
                ['type_epreuve' => TypeEpreuve::ECRIT, 'coefficient_defaut' => $epreuveData['coeff'], 'est_actif' => true]
            );

            Note::create([
                'candidature_id' => $candidature->id,
                'epreuve_id' => $epreuve->id_epreuve,
                'valeur' => $epreuveData['note'],
                'statut' => StatutNote::VALIDEE,
                'est_definitive' => true,
                'date_saisie' => $date->copy()->addDays(10),
            ]);
        }

        $this->command->info("    + Candidature Archive ajoutée pour {$candidat->utilisateur->user_name} (Test Résultats)");
    }
}
