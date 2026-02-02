<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Concours;
use App\Models\Session;
use App\Models\Ecole;
use App\Models\SpecConcours;
use App\Models\ConcoursPaiement;

class ConcoursSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $this->command->info("🚀 Création des concours...");

    // Récupérer les dépendances nécessaires
    $ecoles = Ecole::where('est_actif', true)->get();
    $sessions = Session::where('est_actif', true)->get();

    if ($ecoles->isEmpty()) {
      $this->command->warn("⚠️  Aucune école active trouvée. Création d'une école par défaut...");
      $ecole = Ecole::create([
        // ENSP - École Polytechnique de Yaoundé
        'code_ecole' => 'ENSP',
        'libelle_ecole' => 'École Nationale Supérieure Polytechnique de Yaoundé',
        'libelle_ecole_en' => 'National Advanced School of Engineering of Yaoundé',

        // Localisation
        'region' => 'CENTRE',
        'localisation' => 'Campus Université de Yaoundé I, Ngoa-Ekellé',
        'adresse_complete' => 'Ngoa-Ekellé, Yaoundé, Cameroun',
        'ville' => 'Yaoundé',

        // Contact
        'telephone_ecole' => '+237222234567',
        'fax' => '+237222234568',
        'telephone_2' => '+237222234569',
        'email_ecole' => 'contact@polytechnique.cm',
        'siteweb_ecole' => 'https://polytechnique.cm',
        'bp_ecole' => 'BP 8390',

        // Identité
        'devise' => 'Sapienta – Collativa – Cognitio',
        'slogan' => 'Former les ingénieurs de demain',

        // Tutelle
        'nom_institution_tutelle' => 'Ministère de l\'Enseignement Supérieur',
        'nom_institution_tutelle_en' => 'Ministry of Higher Education',
        'numero_agrement' => '0001/MINESUP/SG/DAUQ/SDEAC/SE',
        'date_creation' => '1971-06-04',

        // Statut
        'est_actif' => true,
        'mentions_legales' => 'Établissement public d\'enseignement supérieur sous tutelle du Ministère de l\'Enseignement Supérieur du Cameroun.',
      ]);
      $ecoles = collect([$ecole]);
    }

    if ($sessions->isEmpty()) {
      $this->command->warn("⚠️  Aucune session active trouvée. Création d'une session par défaut...");
      $session = Session::create([
        'id' => (string) Str::uuid(),
        'libelle_session' => 'Session ' . now()->year,
        'annee_session' => now()->year,
        'date_debut' => now()->startOfYear(),
        'date_fin' => now()->endOfYear(),
        'est_actif' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ]);
      $sessions = collect([$session]);
    }

    DB::beginTransaction();
    try {
      $concours = [];

      // Concours 1: Cycle Ingénieur
      $concours[] = $this->creerConcours(
        $ecoles->first(),
        'Concours d\'entrée en 1ère année Cycle Ingénieur',
        'Concours pour l\'admission en première année du cycle ingénieur',
        now()->addMonths(2),
        now()->addMonths(4),
        50000
      );

      // Concours 2: Cycle Technicien
      $concours[] = $this->creerConcours(
        $ecoles->first(),
        'Concours d\'entrée en 1ère année Cycle Technicien',
        'Concours pour l\'admission en première année du cycle technicien',
        now()->addMonths(2),
        now()->addMonths(4),
        35000
      );

      // Concours 3: Master
      if ($ecoles->count() > 1) {
        $concours[] = $this->creerConcours(
          $ecoles->skip(1)->first(),
          'Concours d\'entrée en Master',
          'Concours pour l\'admission en Master',
          now()->addMonths(3),
          now()->addMonths(5),
          75000
        );
      }

      DB::commit();

      $this->command->newLine();
      $this->command->info("✅ " . count($concours) . " concours créés avec succès!");
      $this->command->newLine();

      foreach ($concours as $c) {
        $this->command->info("  📋 {$c->libelle_concours}");
        $this->command->info("     ID: {$c->id}");
      }

      $this->command->newLine();
    } catch (\Exception $e) {
      DB::rollBack();
      $this->command->error("❌ Erreur: " . $e->getMessage());
      throw $e;
    }
  }

  private function creerConcours(
    Ecole $ecole,
    string $libelle,
    string $description,
    $dateDepot,
    $dateExamen,
    float $montantPaiement
  ): Concours {
    // 1. Créer la spécification du concours
    $spec = SpecConcours::create([
      'id' => (string) Str::uuid(),
      'nom_spec' => "Spécification - {$libelle}",
      'desc_infos_concours' => $description,
      'age_minimum' => 17,
      'age_maximum' => 30,
      'series_bac_acceptees' => ['C', 'D', 'E', 'F'],
      'nationalites_acceptees' => ['Camerounaise'],
      'documents_requis' => [
        'Copie CNI',
        'Acte de naissance',
        'Diplôme du Baccalauréat',
        'Relevé de notes du Bac',
        'Certificat de scolarité',
      ],
      'montant_frais_depot' => 0,
      'est_actif' => true,
      'created_at' => now(),
      'updated_at' => now(),
    ]);

    // 2. Créer le concours
    $concours = Concours::create([
      'id' => (string) Str::uuid(),
      'ecole_id' => $ecole->id,
      'spec_concours_id' => $spec->id,
      'libelle_concours' => $libelle,
      'description' => $description,
      'date_limite_depot' => $dateDepot,
      'date_examen' => $dateExamen,
      'nbre_max_places' => 100,
      'frais_inscription' => 0,
      'est_actif' => true,
      'created_at' => now(),
      'updated_at' => now(),
    ]);

    // 3. Créer la configuration de paiement
    ConcoursPaiement::create([
      'id' => (string) Str::uuid(),
      'concours_id' => $concours->id,
      'banque_nom' => 'BICEC',
      'numero_compte' => '10002000300040',
      'nom_beneficiaire' => $ecole->libelle_ecole,
      'montant' => $montantPaiement,
      'date_limite' => $dateDepot,
      'instructions' => "Effectuer le paiement de {$montantPaiement} FCFA au compte {$ecole->libelle_ecole}. Conserver le reçu pour l'inscription.",
      'est_actif' => true,
      'created_at' => now(),
      'updated_at' => now(),
    ]);

    // 4. Attacher des filières au concours
    $this->attacherFilieres($concours, $ecole);

    // 5. Attacher des sessions au concours
    $this->attacherSessions($concours);

    // 6. Attacher des centres au concours
    $this->attacherCentres($concours);

    // 7. Créer les documents requis
    $this->creerDocumentsRequis($concours);

    return $concours;
  }

  /**
   * Attache des filières au concours
   */
  private function attacherFilieres(Concours $concours, Ecole $ecole): void
  {
    // Récupérer la première session active
    $session = Session::where('est_actif', true)->first();

    if (!$session) {
      $this->command->warn("⚠️  Aucune session active trouvée. Impossible d'attacher des filières.");
      return;
    }

    // Récupérer les filières actives de l'école (via départements)
    $filieres = DB::table('filieres')
      ->join('departements', 'filieres.departement_id', '=', 'departements.id')
      ->where('departements.ecole_id', $ecole->id)
      ->where('filieres.est_actif', true)
      ->where('departements.est_actif', true)
      ->select('filieres.id')
      ->limit(5) // Limiter à 5 filières par concours
      ->get();

    if ($filieres->isEmpty()) {
      $this->command->warn("⚠️  Aucune filière active trouvée pour l'école {$ecole->libelle_ecole}");
      return;
    }

    // Attacher chaque filière avec un nombre de places et la session
    foreach ($filieres as $filiere) {
      $concours->filieres()->attach($filiere->id, [
        'session_id' => $session->id,
        'nombre_places' => 50, // 50 places par filière
        'created_at' => now(),
        'updated_at' => now(),
      ]);
    }

    $this->command->info("  ✓ {$filieres->count()} filières attachées au concours");
  }

  /**
   * Attache des sessions au concours
   */
  private function attacherSessions(Concours $concours): void
  {
    $sessions = Session::where('est_actif', true)->get();

    if ($sessions->isEmpty()) {
      $this->command->warn("⚠️  Aucune session active trouvée.");
      return;
    }

    foreach ($sessions as $session) {
      $concours->sessions()->attach($session->id, [
        'created_at' => now(),
        'updated_at' => now(),
      ]);
    }

    $this->command->info("  ✓ {$sessions->count()} session(s) attachée(s) au concours");
  }

  /**
   * Attache des centres au concours
   */
  private function attacherCentres(Concours $concours): void
  {
    // Les centres seront créés par CentreSeeder
    // Cette méthode peut être vide pour l'instant
    $this->command->info("  ✓ Centres seront attachés par CentreSeeder");
  }

  /**
   * Crée les documents requis pour le concours
   */
  private function creerDocumentsRequis(Concours $concours): void
  {
    // Les documents requis seront créés par DocumentRequisSeeder
    // Cette méthode peut être vide pour l'instant
    $this->command->info("  ✓ Documents requis seront créés par DocumentRequisSeeder");
  }
}
