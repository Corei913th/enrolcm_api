<?php

namespace Tests\Unit\Services;

use App\Enums\StatutCandidature;
use App\Enums\StatutPaiement;
use App\Models\Concours;
use App\Models\ConcoursPaiement;
use App\Models\DocumentRequis;
use App\Models\Centre;
use App\Models\Filiere;
use App\Models\Paiement;
use App\Models\Session;
use App\Models\SpecConcours;
use App\Services\Domain\Registration\RegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationServiceTest extends TestCase
{
  use RefreshDatabase;

  private RegistrationService $service;
  private Concours $concours;
  private Session $session;
  private Filiere $filiere;
  private SpecConcours $spec;

  protected function setUp(): void
  {
    parent::setUp();

    $this->service = $this->app->make(RegistrationService::class);

    $this->filiere = Filiere::factory()->create();
    $this->session = Session::factory()->create(['est_actif' => true]);

    $this->concours = Concours::factory()->create([
      'frais_inscription' => 50000,
      'date_limite_depot' => now()->addMonth(),
      'nbre_max_places' => 1000, // Capacité suffisante
    ]);

    $this->spec = SpecConcours::factory()->create([
      'age_minimum' => 18,
      'age_maximum' => 30,
      'series_bac_acceptees' => ['A', 'C', 'D'],
      'nationalites_acceptees' => ['Camerounaise', 'Française'],
    ]);

    // Lier la spec au concours
    $this->concours->update(['spec_concours_id' => $this->spec->id]);
    $this->concours->refresh(); // Recharger pour avoir la relation spec

    $this->concours->filieres()->attach($this->filiere->id, [
      'session_id' => $this->session->id,
      'nombre_places' => 1000, // Définir la capacité dans la table pivot
    ]);
    $this->concours->sessions()->attach($this->session->id);

    // Setup pour ConcoursReadinessChecker
    ConcoursPaiement::factory()->create([
      'concours_id' => $this->concours->id,
      'est_actif' => true,
    ]);

    $centre = Centre::factory()->create(['est_actif' => true]);
    $this->concours->centers()->attach($centre->id, [
      'id' => \Illuminate\Support\Str::uuid()->toString(),
      'est_actif' => true
    ]);

    DocumentRequis::factory()->create([
      'concours_id' => $this->concours->id,
      'est_actif' => true,
    ]);
  }

  /** @test */
  public function check_eligibility_retourne_eligible_avec_criteres_valides()
  {
    $data = [
      'date_naissance' => now()->subYears(22)->format('Y-m-d'),
      'serie_bac' => 'C',
      'annee_bac' => 2018,
      'nationalite' => 'Camerounaise',
      'filiere_id' => $this->filiere->id,
    ];

    $this->concours->refresh()->load('specConcours'); // Recharger et charger la relation spec
    $result = $this->service->checkEligibility($this->concours, $this->session->id, $data);

    $this->assertTrue($result['eligible']);
    $this->assertEmpty($result['raisons_ineligibilite']);
    $this->assertArrayHasKey('capacite', $result);
  }

  /** @test */
  public function check_eligibility_retourne_non_eligible_si_trop_jeune()
  {
    $data = [
      'date_naissance' => now()->subYears(16)->format('Y-m-d'),
      'serie_bac' => 'C',
      'annee_bac' => 2024,
      'nationalite' => 'Camerounaise',
      'filiere_id' => $this->filiere->id,
    ];

    $this->concours->refresh()->load('specConcours'); // Recharger et charger la relation spec
    $result = $this->service->checkEligibility($this->concours, $this->session->id, $data);

    $this->assertFalse($result['eligible']);
    $this->assertNotEmpty($result['raisons_ineligibilite']);
    $this->assertStringContainsString('Âge minimum', $result['raisons_ineligibilite'][0]);
  }

  /** @test */
  public function check_eligibility_retourne_non_eligible_si_serie_bac_non_acceptee()
  {
    $data = [
      'date_naissance' => now()->subYears(22)->format('Y-m-d'),
      'serie_bac' => 'A',
      'annee_bac' => 2018,
      'nationalite' => 'Camerounaise',
      'filiere_id' => $this->filiere->id,
    ];

    $this->concours->refresh()->load('specConcours'); // Recharger et charger la relation spec
    $result = $this->service->checkEligibility($this->concours, $this->session->id, $data);

    $this->assertFalse($result['eligible']);
    $this->assertStringContainsString('baccalauréat', $result['raisons_ineligibilite'][0]);
  }

  /** @test */
  public function upload_payment_cree_paiement_et_retourne_token()
  {
    $data = [
      'session_id' => $this->session->id,
      'reference_paiement' => 'POLY-2026-12345',
      'montant' => 50000,
      'date_paiement' => now()->format('Y-m-d'),
      'preuve_paiement_path' => 'paiements/preuves/test.jpg',
      'eligibility_data' => [
        'nom' => 'Doe',
        'prenom' => 'John',
        'date_naissance' => '2000-01-01',
        'serie_bac' => 'C',
        'annee_bac' => 2018,
        'filiere_id' => $this->filiere->id,
      ],
    ];

    $result = $this->service->uploadPayment($this->concours, $data);

    $this->assertArrayHasKey('upload_id', $result);
    $this->assertArrayHasKey('statut', $result);
    $this->assertArrayHasKey('auto_valide', $result);
    $this->assertArrayHasKey('ocr_success', $result);

    // Dans la nouvelle version, uploadPayment ne crée pas encore en DB
    // $this->assertDatabaseHas('paiements', [
    //   'concours_id' => $this->concours->id,
    //   'reference' => 'POLY-2026-12345',
    //   'montant' => 50000,
    // ]);

    // Vérifier que c'est en cache
    $uploadId = $result['upload_id'];
    $this->assertNotNull(Cache::get("registration_upload_{$uploadId}"));
  }

  /** @test */
  public function upload_payment_valide_automatiquement_si_donnees_correctes()
  {
    $data = [
      'session_id' => $this->session->id,
      'reference_paiement' => 'POLY-2026-12345',
      'montant' => 50000,
      'date_paiement' => now()->format('Y-m-d'),
      'preuve_paiement_path' => 'paiements/preuves/test.jpg',
      'eligibility_data' => [
        'nom' => 'Doe',
        'prenom' => 'John',
        'date_naissance' => '2000-01-01',
        'serie_bac' => 'C',
        'annee_bac' => 2018,
        'filiere_id' => $this->filiere->id,
      ],
    ];

    $result = $this->service->uploadPayment($this->concours, $data);

    $this->assertTrue($result['auto_valide']);
    $this->assertEquals(StatutPaiement::VERIFIED, $result['statut']);
  }

  /** @test */
  public function upload_payment_met_en_attente_si_montant_incorrect()
  {
    $data = [
      'session_id' => $this->session->id,
      'reference_paiement' => 'POLY-2026-12345',
      'montant' => 40000, // Trop bas
      'date_paiement' => now()->format('Y-m-d'),
      'preuve_paiement_path' => 'paiements/preuves/test.jpg',
      'eligibility_data' => [
        'nom' => 'Doe',
        'prenom' => 'John',
        'date_naissance' => '2000-01-01',
        'serie_bac' => 'C',
        'annee_bac' => 2018,
        'filiere_id' => $this->filiere->id,
      ],
    ];

    $result = $this->service->uploadPayment($this->concours, $data);

    $this->assertFalse($result['auto_valide']);
    $this->assertEquals(StatutPaiement::PENDING, $result['statut']);
  }

  /** @test */
  public function complete_registration_cree_user_candidat_et_candidature()
  {
    Mail::fake();

    // Créer token temporaire en cache
    $token = 'reg_test_token_123';
    Cache::put("registration_{$token}", [
      'concours_id' => $this->concours->id,
      'session_id' => $this->session->id,
      'preuve_paiement_path' => 'test.jpg',
      'eligibility_data' => [
        'concours_id' => $this->concours->id,
        'nom' => 'Doe',
        'prenom' => 'John',
        'date_naissance' => '2000-01-01',
        'serie_bac' => 'C',
        'annee_bac' => 2018,
        'filiere_id' => $this->filiere->id,
      ],
      'payment_data' => [
        'reference' => 'POLY-2026-12345',
        'montant' => 50000,
        'date_paiement' => now()->format('Y-m-d'),
        'statut' => StatutPaiement::VERIFIED->value,
        'validation_notes' => 'Auto-validé pour test',
      ],
    ], now()->addMinutes(30));

    $data = [
      'email' => 'john.doe@example.com',
      'telephone' => '+237690000000',
      'password' => 'Password123',
    ];

    $result = $this->service->completeRegistration($token, $data);

    // Vérifier la structure de la réponse
    $this->assertArrayHasKey('user', $result);
    $this->assertArrayHasKey('candidat', $result);
    $this->assertArrayHasKey('candidature', $result);
    $this->assertArrayHasKey('auth_token', $result);

    // Vérifier User créé
    $this->assertDatabaseHas('utilisateurs', [
      'email' => 'john.doe@example.com',
      'telephone' => '+237690000000',
      'type_utilisateur' => 'CANDIDAT',
    ]);

    // Vérifier Candidat créé
    $user = \App\Models\Utilisateur::where('email', 'john.doe@example.com')->first();
    $this->assertDatabaseHas('candidats', [
      'utilisateur_id' => $user->id,
      'nom_cand' => 'Doe',
      'prenom_cand' => 'John',
    ]);

    // Vérifier Candidature créée avec statut VALIDE
    $this->assertDatabaseHas('candidatures', [
      'candidat_id' => $user->id,
      'concours_id' => $this->concours->id,
      'session_id' => $this->session->id,
      'statut_candidature' => StatutCandidature::SOUMISE->value,
      'paiement_valide' => true,
    ]);

    // Vérifier que le paiement est lié au candidat
    $this->assertDatabaseHas('paiements', [
      'candidat_id' => $user->id,
      'reference' => 'POLY-2026-12345',
    ]);

    // Vérifier que le cache est nettoyé
    $this->assertNull(Cache::get("registration_{$token}"));
  }

  /** @test */
  public function complete_registration_echoue_si_token_invalide()
  {
    $this->expectException(\DomainException::class);
    $this->expectExceptionMessage('Token d\'inscription invalide ou expiré');

    $data = [
      'email' => 'john.doe@example.com',
      'telephone' => '+237690000000',
      'password' => 'Password123',
    ];

    $this->service->completeRegistration('invalid_token', $data);
  }

  /** @test */
  public function complete_registration_echoue_si_paiement_non_valide()
  {
    // Créer un paiement NON validé
    $paiement = Paiement::create([
      'concours_id' => $this->concours->id,
      'candidat_id' => null,
      'reference' => 'POLY-2026-12345',
      'montant' => 50000,
      'date_ocr' => now(),
      'preuve_paiement' => 'test.jpg',
      'statut' => StatutPaiement::PENDING, // ❌ Non validé
      'validation_notes' => 'En attente de validation',
    ]);

    $token = 'reg_test_token_123';
    Cache::put("registration_{$token}", [
      'concours_id' => $this->concours->id,
      'session_id' => $this->session->id,
      'eligibility_data' => [
        'concours_id' => $this->concours->id,
        'nom' => 'Doe',
        'prenom' => 'John',
        'date_naissance' => '2000-01-01',
        'serie_bac' => 'S',
        'annee_bac' => 2018,
        'filiere_id' => $this->filiere->id,
      ],
      'payment_data' => [
        'reference' => 'POLY-2026-12345',
        'montant' => 50000,
        'date_paiement' => now()->format('Y-m-d'),
        'statut' => StatutPaiement::PENDING->value,
        'validation_notes' => 'En attente de validation',
      ],
    ], now()->addMinutes(30));

    $data = [
      'email' => 'john.doe@example.com',
      'telephone' => '+237690000000',
      'password' => 'Password123',
    ];

    $this->expectException(\DomainException::class);
    $this->expectExceptionMessage('Données de paiement manquantes');

    $this->service->completeRegistration($token, $data);
  }
}
