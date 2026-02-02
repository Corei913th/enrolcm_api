<?php

namespace Tests\Feature\Registration;

use App\Enums\StatutCandidature;
use App\Enums\StatutPaiement;
use App\Models\Concours;
use App\Models\Filiere;
use App\Models\Session;
use App\Models\SpecConcours;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationWorkflowTest extends TestCase
{
  use RefreshDatabase;

  private Concours $concours;
  private Session $session;
  private Filiere $filiere;

  protected function setUp(): void
  {
    parent::setUp();

    Storage::fake('public');

    $this->filiere = Filiere::factory()->create();
    $this->session = Session::factory()->create(['est_actif' => true]);

    // Créer la spec d'abord
    $spec = SpecConcours::create([
      'nom_spec' => 'Spec Test',
      'desc_infos_concours' => 'Description test',
      'documents_requis' => [],
      'montant_frais_depot' => 50000,
      'age_minimum' => 18,
      'age_maximum' => 30,
      'series_bac_acceptees' => ['S', 'C', 'D'],
      'nationalites_acceptees' => ['Camerounaise', 'Française'],
      'est_actif' => true,
    ]);

    // Créer le concours avec la spec
    $this->concours = Concours::factory()->create([
      'spec_concours_id' => $spec->id,
      'frais_inscription' => 50000,
      'date_limite_depot' => now()->addMonth(),
      'nbre_max_places' => 200, // Capacité globale du concours
    ]);

    // Attacher la filière avec la session
    $this->concours->filieres()->attach($this->filiere->id, [
      'session_id' => $this->session->id,
      'capacite_max' => 200,
    ]);
  }

  /** @test */
  public function etape_1_verification_eligibilite_candidat_eligible()
  {
    $response = $this->postJson('/api/v1/registration/check-eligibility', [
      'concours_id' => $this->concours->id,
      'session_id' => $this->session->id,
      'date_naissance' => '2000-01-01',
      'serie_bac' => 'S',
      'annee_bac' => 2018,
      'nationalite' => 'Camerounaise',
      'filiere_id' => $this->filiere->id,
    ]);

    $response->assertOk()
      ->assertJson([
        'success' => true,
        'data' => [
          'eligible' => true,
        ],
      ]);
  }

  /** @test */
  public function etape_1_verification_eligibilite_candidat_trop_jeune()
  {
    $response = $this->postJson('/api/v1/registration/check-eligibility', [
      'concours_id' => $this->concours->id,
      'session_id' => $this->session->id,
      'date_naissance' => '2010-01-01', // 16 ans
      'serie_bac' => 'S',
      'annee_bac' => 2024,
      'nationalite' => 'Camerounaise',
      'filiere_id' => $this->filiere->id,
    ]);

    $response->assertStatus(422)
      ->assertJson([
        'success' => false,
        'data' => [
          'eligible' => false,
        ],
      ])
      ->assertJsonPath('data.raisons_ineligibilite.0', fn($raison) => str_contains($raison, 'Âge'));
  }

  /** @test */
  public function etape_1_verification_eligibilite_serie_bac_non_acceptee()
  {
    $response = $this->postJson('/api/v1/registration/check-eligibility', [
      'concours_id' => $this->concours->id,
      'session_id' => $this->session->id,
      'date_naissance' => '2000-01-01',
      'serie_bac' => 'A', // Non acceptée
      'annee_bac' => 2018,
      'nationalite' => 'Camerounaise',
      'filiere_id' => $this->filiere->id,
    ]);

    $response->assertStatus(422)
      ->assertJsonPath('data.eligible', false)
      ->assertJsonPath('data.raisons_ineligibilite.0', fn($raison) => str_contains($raison, 'baccalauréat'));
  }

  /** @test */
  public function etape_2_upload_paiement_avec_validation_automatique()
  {
    $file = UploadedFile::fake()->image('recu.jpg');

    // Simuler OCR réussi: pas de données de paiement envoyées
    // L'OCR va extraire automatiquement les données
    $response = $this->postJson('/api/v1/registration/upload-payment', [
      'concours_id' => $this->concours->id,
      'session_id' => $this->session->id,
      'preuve_paiement' => $file,
      'eligibility_data' => [
        'nom' => 'Doe',
        'prenom' => 'John',
        'date_naissance' => '2000-01-01',
        'serie_bac' => 'S',
        'annee_bac' => 2018,
        'filiere_id' => $this->filiere->id,
      ],
    ]);

    $response->assertOk()
      ->assertJsonStructure([
        'success',
        'data' => [
          'paiement_id',
          'statut',
          'auto_valide',
          'token_temporaire',
          'ocr_success',
          'ocr_data',
          'message',
        ],
      ]);

    // Vérifier qu'un paiement a été créé (avec données OCR)
    $this->assertDatabaseHas('paiements', [
      'concours_id' => $this->concours->id,
      'statut' => StatutPaiement::VERIFIED->value,
    ]);

    Storage::disk('public')->assertExists('paiements/preuves/' . $file->hashName());
  }

  /** @test */
  public function etape_2_upload_paiement_montant_incorrect_en_attente()
  {
    $file = UploadedFile::fake()->image('recu.jpg');

    // Simuler échec OCR: saisie manuelle avec montant incorrect
    $response = $this->postJson('/api/v1/registration/upload-payment', [
      'concours_id' => $this->concours->id,
      'session_id' => $this->session->id,
      'reference_paiement' => 'POLY-2026-12345',
      'montant' => 40000, // Trop bas (>5% de différence)
      'date_paiement' => now()->format('Y-m-d'),
      'numero_compte' => 'CM21 1234 5678 9012',
      'preuve_paiement' => $file,
      'eligibility_data' => [
        'nom' => 'Doe',
        'prenom' => 'John',
        'date_naissance' => '2000-01-01',
        'serie_bac' => 'S',
        'annee_bac' => 2018,
        'filiere_id' => $this->filiere->id,
      ],
    ]);

    $response->assertOk()
      ->assertJsonPath('data.auto_valide', false)
      ->assertJsonPath('data.statut', StatutPaiement::PENDING->value);
  }

  /** @test */
  public function etape_2_upload_paiement_saisie_manuelle_complete()
  {
    $file = UploadedFile::fake()->image('recu.jpg');

    // Simuler échec OCR: saisie manuelle complète avec toutes les données
    $response = $this->postJson('/api/v1/registration/upload-payment', [
      'concours_id' => $this->concours->id,
      'session_id' => $this->session->id,
      'reference_paiement' => 'POLY-2026-12345',
      'montant' => 50000,
      'date_paiement' => now()->format('Y-m-d'),
      'numero_compte' => 'CM21 1234 5678 9012',
      'preuve_paiement' => $file,
      'eligibility_data' => [
        'nom' => 'Doe',
        'prenom' => 'John',
        'date_naissance' => '2000-01-01',
        'serie_bac' => 'S',
        'annee_bac' => 2018,
        'filiere_id' => $this->filiere->id,
      ],
    ]);

    $response->assertOk()
      ->assertJsonPath('data.auto_valide', true)
      ->assertJsonPath('data.statut', StatutPaiement::VERIFIED->value);

    $this->assertDatabaseHas('paiements', [
      'concours_id' => $this->concours->id,
      'reference' => 'POLY-2026-12345',
      'montant' => 50000,
      'statut' => StatutPaiement::VERIFIED->value,
    ]);
  }

  /** @test */
  public function etape_3_completion_inscription_avec_token_valide()
  {
    // Simuler étape 2 (paiement uploadé avec OCR réussi)
    $file = UploadedFile::fake()->image('recu.jpg');
    $uploadResponse = $this->postJson('/api/v1/registration/upload-payment', [
      'concours_id' => $this->concours->id,
      'session_id' => $this->session->id,
      'preuve_paiement' => $file,
      'eligibility_data' => [
        'nom' => 'Doe',
        'prenom' => 'John',
        'date_naissance' => '2000-01-01',
        'serie_bac' => 'S',
        'annee_bac' => 2018,
        'filiere_id' => $this->filiere->id,
      ],
    ]);

    $token = $uploadResponse->json('data.token_temporaire');

    // Étape 3: Compléter inscription
    $response = $this->postJson('/api/v1/registration/complete', [
      'token_temporaire' => $token,
      'email' => 'john.doe@example.com',
      'telephone' => '+237690000000',
      'password' => 'Password123',
      'password_confirmation' => 'Password123',
    ]);

    $response->assertCreated()
      ->assertJsonStructure([
        'success',
        'data' => [
          'user',
          'candidat',
          'candidature',
          'auth_token',
          'message',
        ],
      ]);

    $this->assertDatabaseHas('users', [
      'email' => 'john.doe@example.com',
      'telephone' => '+237690000000',
      'role' => 'CANDIDAT',
    ]);

    $this->assertDatabaseHas('candidats', [
      'nom_cand' => 'Doe',
      'prenom_cand' => 'John',
    ]);

    $this->assertDatabaseHas('candidatures', [
      'concours_id' => $this->concours->id,
      'session_id' => $this->session->id,
      'statut_candidature' => StatutCandidature::VALIDE->value,
      'paiement_valide' => true,
    ]);
  }

  /** @test */
  public function etape_3_completion_inscription_token_invalide()
  {
    $response = $this->postJson('/api/v1/registration/complete', [
      'token_temporaire' => 'invalid_token',
      'email' => 'john.doe@example.com',
      'telephone' => '+237690000000',
      'password' => 'Password123',
      'password_confirmation' => 'Password123',
    ]);

    $response->assertStatus(400)
      ->assertJson([
        'success' => false,
        'message' => 'Token d\'inscription invalide ou expiré',
      ]);
  }

  /** @test */
  public function workflow_complet_inscription_candidat()
  {
    // Étape 1: Vérifier éligibilité
    $eligibilityResponse = $this->postJson('/api/v1/registration/check-eligibility', [
      'concours_id' => $this->concours->id,
      'session_id' => $this->session->id,
      'date_naissance' => '2000-01-01',
      'serie_bac' => 'S',
      'annee_bac' => 2018,
      'nationalite' => 'Camerounaise',
      'filiere_id' => $this->filiere->id,
    ]);

    $eligibilityResponse->assertOk()
      ->assertJsonPath('data.eligible', true);

    // Étape 2: Upload paiement (OCR automatique)
    $file = UploadedFile::fake()->image('recu.jpg');
    $paymentResponse = $this->postJson('/api/v1/registration/upload-payment', [
      'concours_id' => $this->concours->id,
      'session_id' => $this->session->id,
      'preuve_paiement' => $file,
      'eligibility_data' => [
        'nom' => 'Doe',
        'prenom' => 'John',
        'date_naissance' => '2000-01-01',
        'serie_bac' => 'S',
        'annee_bac' => 2018,
        'filiere_id' => $this->filiere->id,
      ],
    ]);

    $paymentResponse->assertOk()
      ->assertJsonPath('data.auto_valide', true);

    $token = $paymentResponse->json('data.token_temporaire');

    // Étape 3: Compléter inscription
    $completeResponse = $this->postJson('/api/v1/registration/complete', [
      'token_temporaire' => $token,
      'email' => 'john.doe@example.com',
      'telephone' => '+237690000000',
      'password' => 'Password123',
      'password_confirmation' => 'Password123',
    ]);

    $completeResponse->assertCreated()
      ->assertJsonPath('data.candidature.statut_candidature', StatutCandidature::VALIDE->value);

    // Vérifier que tout est bien créé
    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseCount('candidats', 1);
    $this->assertDatabaseCount('candidatures', 1);
    $this->assertDatabaseCount('paiements', 1);
  }
}
