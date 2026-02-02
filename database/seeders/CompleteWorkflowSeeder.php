<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ecole;
use App\Models\SpecConcours;
use App\Models\Filiere;
use App\Models\Centre;
use App\Models\Epreuve;
use App\Models\DocumentRequis;
use App\Models\Utilisateur;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Session;
use App\Models\ConcoursPaiement;
use App\Enums\StatutCandidature;
use App\Enums\Genre;
use App\Enums\RegionCameroun;
use App\Enums\TypeDiplome;
use App\Enums\NiveauScolaire;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seeder complet pour simulation de workflow
 * 
 * Ce seeder crée tout le nécessaire SAUF:
 * - Le concours (doit être créé manuellement)
 * - La publication des résultats (doit être faite manuellement)
 * 
 * Usage:
 * php artisan db:seed --class=CompleteWorkflowSeeder
 * 
 * Workflow simulé:
 * 1. École avec configuration complète
 * 2. Session active
 * 3. Filières disponibles
 * 4. Centres d'examen
 * 5. Épreuves types
 * 6. Documents requis
 * 7. Candidats avec profils complets
 * 8. Candidatures validées
 * 9. Configuration de paiement
 * 
 * Après ce seeder:
 * - Créer le concours via l'interface admin
 * - Associer filières, centres, épreuves, documents
 * - Les candidats pourront s'inscrire
 * - Saisir les notes
 * - Calculer les résultats
 * - Publier les résultats (verrouille le concours + notifie candidats)
 */
class CompleteWorkflowSeeder extends Seeder
{
    private $session;
    private $ecole;
    private $filieres = [];
    private $centres = [];
    private $epreuves = [];
    private $documents = [];
    private $candidats = [];

    public function run(): void
    {
        $this->command->info('🚀 Démarrage du seeder de workflow complet...');

        $this->createEcole();
        $this->createSession();
        $this->createFilieres();
        $this->createCentres();
        $this->createEpreuves();
        $this->createDocuments();
        $this->createCandidats();

        $this->command->info('✅ Seeder terminé avec succès!');
        $this->command->newLine();
        $this->displaySummary();
    }

    private function createEcole(): void
    {
        $this->command->info('📚 Création de l\'école...');

        $this->ecole = Ecole::create([
            'nom_ecole' => 'École Nationale Supérieure Polytechnique',
            'sigle_ecole' => 'ENSP',
            'adresse_ecole' => 'BP 8390, Yaoundé',
            'telephone_ecole' => '+237 222 223 424',
            'email_ecole' => 'contact@ensp-yde.cm',
            'ville_ecole' => 'Yaoundé',
            'region_ecole' => RegionCameroun::CENTRE,
            'logo_ecole' => null,
            'description_ecole' => 'Grande école d\'ingénieurs formant des cadres de haut niveau',
            'est_active' => true,
        ]);

        $this->command->info("   ✓ École créée: {$this->ecole->nom_ecole}");
    }

    private function createSession(): void
    {
        $this->command->info('📅 Création de la session...');

        $this->session = Session::create([
            'libelle_session' => 'Session ' . now()->year,
            'date_ouverture_inscription' => now()->subDays(30),
            'date_fermeture_inscription' => now()->addDays(30),
            'est_active' => true,
        ]);

        $this->command->info("   ✓ Session créée: {$this->session->libelle_session}");
    }

    private function createFilieres(): void
    {
        $this->command->info('🎓 Création des filières...');

        $filieresData = [
            ['code' => 'GI', 'libelle' => 'Génie Informatique', 'desc' => 'Formation en développement logiciel et systèmes'],
            ['code' => 'GE', 'libelle' => 'Génie Électrique', 'desc' => 'Formation en électronique et électrotechnique'],
            ['code' => 'GM', 'libelle' => 'Génie Mécanique', 'desc' => 'Formation en mécanique et production'],
            ['code' => 'GC', 'libelle' => 'Génie Civil', 'desc' => 'Formation en construction et travaux publics'],
        ];

        foreach ($filieresData as $data) {
            $filiere = Filiere::create([
                'code_filiere' => $data['code'],
                'libelle_filiere' => $data['libelle'],
                'desc_filiere' => $data['desc'],
                'est_active' => true,
            ]);
            $this->filieres[] = $filiere;
            $this->command->info("   ✓ Filière: {$filiere->libelle_filiere}");
        }
    }

    private function createCentres(): void
    {
        $this->command->info('🏢 Création des centres d\'examen...');

        $centresData = [
            ['libelle' => 'Campus Principal ENSP', 'ville' => 'Yaoundé', 'region' => RegionCameroun::CENTRE, 'capacite' => 200],
            ['libelle' => 'Lycée Général Leclerc', 'ville' => 'Yaoundé', 'region' => RegionCameroun::CENTRE, 'capacite' => 150],
            ['libelle' => 'Université de Douala', 'ville' => 'Douala', 'region' => RegionCameroun::LITTORAL, 'capacite' => 180],
            ['libelle' => 'Lycée de Bafoussam', 'ville' => 'Bafoussam', 'region' => RegionCameroun::OUEST, 'capacite' => 100],
        ];

        foreach ($centresData as $data) {
            $centre = Centre::create([
                'libelle_centre' => $data['libelle'],
                'ville_centre' => $data['ville'],
                'region' => $data['region'],
                'capacite' => $data['capacite'],
                'est_actif' => true,
            ]);
            $this->centres[] = $centre;
            $this->command->info("   ✓ Centre: {$centre->libelle_centre} ({$centre->ville_centre})");
        }
    }

    private function createEpreuves(): void
    {
        $this->command->info('📝 Création des épreuves types...');

        $epreuvesData = [
            ['intitule' => 'Mathématiques', 'type' => 'ECRIT', 'coef' => 4, 'duree' => 180],
            ['intitule' => 'Physique', 'type' => 'ECRIT', 'coef' => 3, 'duree' => 120],
            ['intitule' => 'Sciences de l\'Ingénieur', 'type' => 'ECRIT', 'coef' => 3, 'duree' => 120],
            ['intitule' => 'Français', 'type' => 'ECRIT', 'coef' => 2, 'duree' => 90],
            ['intitule' => 'Anglais', 'type' => 'ECRIT', 'coef' => 2, 'duree' => 90],
        ];

        foreach ($epreuvesData as $data) {
            $epreuve = Epreuve::create([
                'intitule' => $data['intitule'],
                'type_epreuve' => $data['type'],
                'coefficient' => $data['coef'],
                'duree_minutes' => $data['duree'],
                'est_eliminatoire' => false,
                'note_eliminatoire' => null,
                'est_active' => true,
            ]);
            $this->epreuves[] = $epreuve;
            $this->command->info("   ✓ Épreuve: {$epreuve->intitule} (coef {$epreuve->coefficient})");
        }
    }

    private function createDocuments(): void
    {
        $this->command->info('📄 Création des documents requis...');

        $documentsData = [
            ['libelle' => 'Acte de naissance', 'obligatoire' => true],
            ['libelle' => 'Relevé de notes du Baccalauréat', 'obligatoire' => true],
            ['libelle' => 'Certificat de nationalité', 'obligatoire' => true],
            ['libelle' => 'Photo d\'identité', 'obligatoire' => true],
            ['libelle' => 'Certificat médical', 'obligatoire' => false],
        ];

        foreach ($documentsData as $data) {
            $document = DocumentRequis::create([
                'libelle_document' => $data['libelle'],
                'description_document' => 'Document requis pour l\'inscription',
                'est_obligatoire' => $data['obligatoire'],
                'est_actif' => true,
            ]);
            $this->documents[] = $document;
            $this->command->info("   ✓ Document: {$document->libelle_document}");
        }
    }

    private function createCandidats(): void
    {
        $this->command->info('👥 Création des candidats...');

        $regions = [
            RegionCameroun::CENTRE,
            RegionCameroun::LITTORAL,
            RegionCameroun::OUEST,
            RegionCameroun::SUD,
            RegionCameroun::EST,
        ];

        $noms = ['Nkomo', 'Mbarga', 'Tchoua', 'Fouda', 'Kamga', 'Ngono', 'Biya', 'Ateba', 'Essomba', 'Mvondo'];
        $prenoms = ['Jean', 'Marie', 'Paul', 'Grace', 'Emmanuel', 'Sarah', 'David', 'Rachel', 'Samuel', 'Esther'];

        for ($i = 1; $i <= 50; $i++) {
            $nom = $noms[array_rand($noms)];
            $prenom = $prenoms[array_rand($prenoms)];
            $email = strtolower($prenom . '.' . $nom . $i . '@example.cm');
            $genre = $i % 2 === 0 ? Genre::MASCULIN : Genre::FEMININ;
            $region = $regions[array_rand($regions)];

            // Create user
            $utilisateur = Utilisateur::create([
                'user_name' => strtolower($prenom . $nom . $i),
                'email' => $email,
                'password' => Hash::make('password123'),
                'telephone' => '+237 6' . rand(70000000, 79999999),
                'role' => 'CANDIDAT',
                'est_actif' => true,
                'email_verifie' => true,
            ]);

            // Create candidat profile
            $candidat = Candidat::create([
                'utilisateur_id' => $utilisateur->id,
                'nom_cand' => $nom,
                'prenom_cand' => $prenom,
                'sexe_cand' => $genre,
                'date_naissance_cand' => now()->subYears(rand(18, 25))->format('Y-m-d'),
                'lieu_naissance_cand' => $region->label(),
                'nationalite_cand' => 'Camerounaise',
                'region' => $region,
                'adresse_cand' => 'BP ' . rand(1000, 9999) . ', ' . $region->label(),
                'telephone_tuteur_cand' => '+237 6' . rand(70000000, 79999999),
                'niveau_scolaire' => NiveauScolaire::TERMINALE,
                'diplome_admission' => TypeDiplome::BACCALAUREAT,
                'serie_bac' => 'C',
                'annee_obtention_bac' => now()->year - 1,
                'filiere_id' => $this->filieres[array_rand($this->filieres)]->id,
            ]);

            $this->candidats[] = $candidat;

            if ($i % 10 === 0) {
                $this->command->info("   ✓ {$i} candidats créés...");
            }
        }

        $this->command->info("   ✓ Total: " . count($this->candidats) . " candidats créés");
    }

    private function displaySummary(): void
    {
        $this->command->info('📊 RÉSUMÉ DE LA SIMULATION');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info("École: {$this->ecole->nom_ecole}");
        $this->command->info("Session: {$this->session->libelle_session}");
        $this->command->info("Filières: " . count($this->filieres));
        $this->command->info("Centres: " . count($this->centres));
        $this->command->info("Épreuves: " . count($this->epreuves));
        $this->command->info("Documents: " . count($this->documents));
        $this->command->info("Candidats: " . count($this->candidats));
        $this->command->newLine();
        $this->command->info('🎯 PROCHAINES ÉTAPES POUR LA SIMULATION:');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('1. Créer un concours via l\'interface admin');
        $this->command->info('   - Associer à l\'école: ' . $this->ecole->nom_ecole);
        $this->command->info('   - Associer à la session: ' . $this->session->libelle_session);
        $this->command->info('   - Définir le nombre de places');
        $this->command->newLine();
        $this->command->info('2. Configurer le concours:');
        $this->command->info('   - Associer les filières (avec nombre de places par filière)');
        $this->command->info('   - Associer les centres d\'examen');
        $this->command->info('   - Associer les épreuves');
        $this->command->info('   - Associer les documents requis');
        $this->command->info('   - Configurer les règles de paiement');
        $this->command->newLine();
        $this->command->info('3. Les candidats s\'inscrivent (ou créer candidatures via seeder)');
        $this->command->newLine();
        $this->command->info('4. Valider les candidatures');
        $this->command->newLine();
        $this->command->info('5. Créer le planning des épreuves');
        $this->command->newLine();
        $this->command->info('6. Saisir les notes des candidats');
        $this->command->newLine();
        $this->command->info('7. Calculer les résultats');
        $this->command->newLine();
        $this->command->info('8. Déterminer les admissions par filière');
        $this->command->newLine();
        $this->command->info('9. PUBLIER LES RÉSULTATS');
        $this->command->info('   ⚠️  Ceci va:');
        $this->command->info('   - Verrouiller le concours (est_actif = false)');
        $this->command->info('   - Notifier tous les candidats validés');
        $this->command->info('   - Rendre les résultats consultables');
        $this->command->newLine();
        $this->command->info('💡 CREDENTIALS DE TEST:');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('Email: (voir candidats créés)');
        $this->command->info('Password: password123');
    }
}
