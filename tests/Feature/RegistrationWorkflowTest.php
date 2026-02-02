<?php

namespace Tests\Feature;

use App\Enums\StatutCandidature;
use App\Enums\StatutPaiement;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\Ecole;
use App\Models\Filiere;
use App\Models\Paiement;
use App\Models\Session;
use App\Models\SpecConcours;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationWorkflowTest extends TestCase
{
  use RefreshDatabase;

  private Concours $concours;
  private SpecConcours $specConcours;
  private Session $session;
  private Filiere $filiere;

  protected function setUp(): void
  {
    parent::setUp();
    Storage::fake('local');

    // Create test data
    $ecole = Ecole::factory()->create();

    $this->specConcours = SpecConcours::factory()
      ->withAgeRestriction(18, 30)
      ->withSeriesRestriction(['C', 'D', 'E'])
      ->withNationalityRestriction(['Camerounaise', 'Française'])
      ->create([
        'montant_frais_depot' => 50000
      ]);

    $this->session = Session::factory()->create([
      'est_actif' => true,
      'date_ouverture_inscription' => now()->subDays(30),
      'date_fermeture_inscription' => now()->addDays(30),
    ]);

    $this->concours = Concours::factory()->create([
      'ecole_id' => $ecole->id,
      'spec_concours_id' => $this->specConcours->id,
      'est_actif' => true,
      'date_limite_depot' => now()->addDays(60),
      'date_examen' => now()->addDays(90),
      'frais_inscription' => 50000, // Utiliser le même montant que le spec
    ]);

    // Attach session to concours
    $this->concours->sessions()->attach($this->session->id);

    $this->filiere = Filiere::factory()->create();
  }

  /** @test */
  public function it_completes_successful_registration_workflow()
  {
    // Step 1: Check eligibility
    $eligibilityResponse = $this->postJson('/api/v1/registration/check-eligibility', [
      'concours_id' => $this->concours->id,
      'date_naissance' => '2000-01-01',
      'serie_bac' => 'C',
      'nationalite' => 'Camerounaise',
    ]);

    $eligibilityResponse->assertStatus(200)
      ->assertJson([
        'success' => true,
        'data' => [
          'eligible' => true,
          'next_step' => 'payment_upload',
        ]
      ]);

    // Step 2: Upload payment proof
    $paymentFile = UploadedFile::fake()->image('payment_proof.jpg', 800, 600);

    $uploadResponse = $this->postJson('/api/v1/registration/upload-payment', [
      'concours_id' => $this->concours->id,
      'preuve' => $paymentFile,
      'eligibility_data' => json_encode([
        'date_naissance' => '2000-01-01',
        'serie_bac' => 'C',
        'nationalite' => 'Camerounaise',
      ]),
    ]);

    $uploadResponse->assertStatus(200)
      ->assertJsonStructure([
        'success',
        'data' => [
          'ocr_success',
          'preuve_path',
          'next_step',
        ]
      ]);

    $preuvePath = $uploadResponse->json('data.preuve_path');

    // Step 3: Complete registration
    $completeResponse = $this->postJson('/api/v1/registration/complete', [
      'concours_id' => $this->concours->id,
      'email' => 'test@example.com',
      'password' => 'password123',
      'filiere_id' => $this->filiere->id,
      'eligibility_data' => [
        'date_naissance' => '2000-01-01',
        'serie_bac' => 'C',
        'nationalite' => 'Camerounaise',
      ],
      'payment_data' => [
        'reference' => 'PRU123456',
        'montant' => 50000,
        'banque' => 'UBA',
        'date_paiement' => now()->subDays(5)->format('Y-m-d'),
        'preuve_path' => $preuvePath,
      ],
    ]);

    $completeResponse->assertStatus(201)
      ->assertJsonStructure([
        'success',
        'data' => [
          'token',
          'user' => ['id', 'email', 'type_utilisateur', 'email_verifie'],
          'candidat' => ['id', 'date_naissance', 'serie_bac', 'nationalite', 'filiere_id'],
          'candidature' => ['id', 'concours_id', 'session_id', 'statut_candidature', 'code_cand_temp'],
        ]
      ]);

    // Verify all records were created
    $this->assertDatabaseHas('utilisateurs', [
      'email' => 'test@example.com',
      'email_verifie' => false,
    ]);

    $utilisateur = Utilisateur::where('email', 'test@example.com')->first();
    $this->assertNotNull($utilisateur);

    $this->assertDatabaseHas('candidats', [
      'utilisateur_id' => $utilisateur->id,
      'serie_bac' => 'C',
      'nationalite_cand' => 'Camerounaise',
      'filiere_id' => $this->filiere->id,
    ]);

    $candidat = Candidat::where('utilisateur_id', $utilisateur->id)->first();
    $this->assertNotNull($candidat);

    $this->assertDatabaseHas('paiements', [
      'candidat_id' => $candidat->utilisateur_id,
      'concours_id' => $this->concours->id,
      'reference' => 'PRU123456',
      'montant' => 50000,
      'banque_ocr' => 'UBA',
      'statut' => StatutPaiement::PENDING_MANUAL_REVIEW->value,
    ]);

    $paiement = Paiement::where('reference', 'PRU123456')->first();
    $this->assertNotNull($paiement);

    $this->assertDatabaseHas('candidatures', [
      'candidat_id' => $candidat->utilisateur_id,
      'concours_id' => $this->concours->id,
      'session_id' => $this->session->id,
      'statut_candidature' => StatutCandidature::SOUMISE->value,
    ]);

    // Verify token was returned
    $this->assertNotEmpty($completeResponse->json('data.token'));
  }


  /** @test */
  public function it_completes_registration_with_manual_payment_entry()
  {
    // Step 1: Check eligibility
    $eligibilityResponse = $this->postJson('/api/v1/registration/check-eligibility', [
      'concours_id' => $this->concours->id,
      'date_naissance' => '1998-05-15',
      'serie_bac' => 'D',
      'nationalite' => 'Camerounaise',
    ]);

    $eligibilityResponse->assertStatus(200)
      ->assertJson([
        'success' => true,
        'data' => [
          'eligible' => true,
        ]
      ]);

    // Step 2: Upload payment proof (OCR will fail in test environment)
    $paymentFile = UploadedFile::fake()->image('payment_proof.jpg', 800, 600);

    $uploadResponse = $this->postJson('/api/v1/registration/upload-payment', [
      'concours_id' => $this->concours->id,
      'preuve' => $paymentFile,
      'eligibility_data' => json_encode([
        'date_naissance' => '1998-05-15',
        'serie_bac' => 'D',
        'nationalite' => 'Camerounaise',
      ]),
    ]);

    $uploadResponse->assertStatus(200);
    $preuvePath = $uploadResponse->json('data.preuve_path');

    // If OCR fails, we should get manual_entry as next step
    if ($uploadResponse->json('data.next_step') === 'manual_entry') {
      // Step 3: Validate manual payment data
      $validateResponse = $this->postJson('/api/v1/registration/validate-payment', [
        'concours_id' => $this->concours->id,
        'reference' => 'PRU789012',
        'montant' => 50000,
        'banque' => 'BICEC',
        'date_paiement' => now()->subDays(3)->format('Y-m-d'),
      ]);

      $validateResponse->assertStatus(200)
        ->assertJson([
          'success' => true,
          'data' => [
            'next_step' => 'account_creation',
          ]
        ]);
    }

    // Step 4: Complete registration with manual payment data
    $completeResponse = $this->postJson('/api/v1/registration/complete', [
      'concours_id' => $this->concours->id,
      'email' => 'manual@example.com',
      'password' => 'password123',
      'filiere_id' => $this->filiere->id,
      'eligibility_data' => [
        'date_naissance' => '1998-05-15',
        'serie_bac' => 'D',
        'nationalite' => 'Camerounaise',
      ],
      'payment_data' => [
        'reference' => 'PRU789012',
        'montant' => 50000,
        'banque' => 'BICEC',
        'date_paiement' => now()->subDays(3)->format('Y-m-d'),
        'preuve_path' => $preuvePath,
      ],
    ]);

    $completeResponse->assertStatus(201);

    // Verify manual payment data was used
    $this->assertDatabaseHas('paiements', [
      'reference' => 'PRU789012',
      'montant' => 50000,
      'banque_ocr' => 'BICEC',
      'statut' => StatutPaiement::PENDING_MANUAL_REVIEW->value,
    ]);

    $this->assertDatabaseHas('utilisateurs', [
      'email' => 'manual@example.com',
    ]);
  }

  /** @test */
  public function it_rejects_ineligible_candidate_at_eligibility_check()
  {
    // Test: Too young
    $response = $this->postJson('/api/v1/registration/check-eligibility', [
      'concours_id' => $this->concours->id,
      'date_naissance' => now()->subYears(16)->format('Y-m-d'), // 16 years old
      'serie_bac' => 'C',
      'nationalite' => 'Camerounaise',
    ]);

    $response->assertStatus(400)
      ->assertJson([
        'success' => false,
      ])
      ->assertJsonPath('errors.reasons.0', function ($value) {
        return str_contains($value, 'Âge minimum requis: 18 ans');
      });

    // Test: Invalid serie_bac
    $response = $this->postJson('/api/v1/registration/check-eligibility', [
      'concours_id' => $this->concours->id,
      'date_naissance' => '2000-01-01',
      'serie_bac' => 'A', // Not in accepted list
      'nationalite' => 'Camerounaise',
    ]);

    $response->assertStatus(400)
      ->assertJson([
        'success' => false,
      ])
      ->assertJsonFragment([
        'eligible' => false
      ]);

    // Test: Invalid nationality
    $response = $this->postJson('/api/v1/registration/check-eligibility', [
      'concours_id' => $this->concours->id,
      'date_naissance' => '2000-01-01',
      'serie_bac' => 'C',
      'nationalite' => 'Américaine', // Not in accepted list
    ]);

    $response->assertStatus(400)
      ->assertJson([
        'success' => false,
      ])
      ->assertJsonFragment([
        'eligible' => false
      ]);
  }

  /** @test */
  public function it_rejects_duplicate_email_at_registration()
  {
    // Create existing user
    Utilisateur::factory()->create([
      'email' => 'existing@example.com',
    ]);

    // Try to register with same email
    $paymentFile = UploadedFile::fake()->image('payment_proof.jpg');

    $uploadResponse = $this->postJson('/api/v1/registration/upload-payment', [
      'concours_id' => $this->concours->id,
      'preuve' => $paymentFile,
      'eligibility_data' => json_encode([
        'date_naissance' => '2000-01-01',
        'serie_bac' => 'C',
        'nationalite' => 'Camerounaise',
      ]),
    ]);

    $preuvePath = $uploadResponse->json('data.preuve_path');

    $response = $this->postJson('/api/v1/registration/complete', [
      'concours_id' => $this->concours->id,
      'email' => 'existing@example.com', // Duplicate email
      'password' => 'password123',
      'filiere_id' => $this->filiere->id,
      'eligibility_data' => [
        'date_naissance' => '2000-01-01',
        'serie_bac' => 'C',
        'nationalite' => 'Camerounaise',
      ],
      'payment_data' => [
        'reference' => 'PRU111111',
        'montant' => 50000,
        'banque' => 'UBA',
        'date_paiement' => now()->subDays(5)->format('Y-m-d'),
        'preuve_path' => $preuvePath,
      ],
    ]);

    $response->assertStatus(422)
      ->assertJsonValidationErrors(['email']);
  }

  /** @test */
  public function it_rejects_duplicate_payment_reference_at_registration()
  {
    // Create existing payment
    $existingCandidat = Candidat::factory()->create();
    Paiement::factory()->withoutCandidature()->create([
      'candidat_id' => $existingCandidat->utilisateur_id,
      'concours_id' => $this->concours->id,
      'reference' => 'PRU999999',
    ]);

    // Try to register with same payment reference
    $paymentFile = UploadedFile::fake()->image('payment_proof.jpg');

    $uploadResponse = $this->postJson('/api/v1/registration/upload-payment', [
      'concours_id' => $this->concours->id,
      'preuve' => $paymentFile,
      'eligibility_data' => json_encode([
        'date_naissance' => '2000-01-01',
        'serie_bac' => 'C',
        'nationalite' => 'Camerounaise',
      ]),
    ]);

    $preuvePath = $uploadResponse->json('data.preuve_path');

    $response = $this->postJson('/api/v1/registration/complete', [
      'concours_id' => $this->concours->id,
      'email' => 'newuser@example.com',
      'password' => 'password123',
      'filiere_id' => $this->filiere->id,
      'eligibility_data' => [
        'date_naissance' => '2000-01-01',
        'serie_bac' => 'C',
        'nationalite' => 'Camerounaise',
      ],
      'payment_data' => [
        'reference' => 'PRU999999', // Duplicate reference
        'montant' => 50000,
        'banque' => 'UBA',
        'date_paiement' => now()->subDays(5)->format('Y-m-d'),
        'preuve_path' => $preuvePath,
      ],
    ]);

    $response->assertStatus(422)
      ->assertJsonStructure([
        'success',
        'message',
        'errors'
      ]);

    // Verify no new records were created
    $this->assertDatabaseMissing('utilisateurs', [
      'email' => 'newuser@example.com',
    ]);
  }

  /** @test */
  public function it_rolls_back_transaction_on_database_error()
  {
    // This test verifies that if any part of the registration fails,
    // no partial data is left in the database

    $paymentFile = UploadedFile::fake()->image('payment_proof.jpg');

    $uploadResponse = $this->postJson('/api/v1/registration/upload-payment', [
      'concours_id' => $this->concours->id,
      'preuve' => $paymentFile,
      'eligibility_data' => json_encode([
        'date_naissance' => '2000-01-01',
        'serie_bac' => 'C',
        'nationalite' => 'Camerounaise',
      ]),
    ]);

    $preuvePath = $uploadResponse->json('data.preuve_path');

    // Try to register with invalid filiere_id (should cause transaction to fail)
    $response = $this->postJson('/api/v1/registration/complete', [
      'concours_id' => $this->concours->id,
      'email' => 'rollback@example.com',
      'password' => 'password123',
      'filiere_id' => '00000000-0000-0000-0000-000000000000', // Invalid UUID
      'eligibility_data' => [
        'date_naissance' => '2000-01-01',
        'serie_bac' => 'C',
        'nationalite' => 'Camerounaise',
      ],
      'payment_data' => [
        'reference' => 'PRU888888',
        'montant' => 50000,
        'banque' => 'UBA',
        'date_paiement' => now()->subDays(5)->format('Y-m-d'),
        'preuve_path' => $preuvePath,
      ],
    ]);

    // Should fail validation or return error
    $response->assertStatus(422);

    // Verify no records were created (transaction rolled back)
    $this->assertDatabaseMissing('utilisateurs', [
      'email' => 'rollback@example.com',
    ]);

    $this->assertDatabaseMissing('paiements', [
      'reference' => 'PRU888888',
    ]);
  }
}
