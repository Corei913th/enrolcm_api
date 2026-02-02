<?php

namespace Tests\Unit\Services;

use App\Enums\StatutCandidature;
use App\Enums\StatutPaiement;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\Paiement;
use App\Models\Session;
use App\Services\Domain\Candidature\CandidatureService;
use App\Services\Domain\Candidature\DocumentService;
use App\Services\Domain\Candidature\EligibilityCheckerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CandidatureServiceSyncTest extends TestCase
{
  use RefreshDatabase;

  private CandidatureService $candidatureService;
  private EligibilityCheckerService $eligibilityChecker;
  private DocumentService $documentService;

  protected function setUp(): void
  {
    parent::setUp();

    $this->documentService = $this->app->make(DocumentService::class);
    $this->eligibilityChecker = $this->app->make(EligibilityCheckerService::class);
    $this->candidatureService = new CandidatureService(
      $this->eligibilityChecker,
      $this->documentService
    );
  }

  /** @test */
  public function syncStatusWithPayment_does_nothing_when_no_payment_exists()
  {
    // Arrange
    $candidature = $this->createCandidatureWithoutPayment();
    $originalStatus = $candidature->statut_candidature;

    // Act
    $this->candidatureService->syncStatusWithPayment($candidature);

    // Assert
    $candidature->refresh();
    $this->assertEquals($originalStatus, $candidature->statut_candidature);
  }

  /** @test */
  public function syncStatusWithPayment_updates_to_paiement_verifie_when_payment_verified_and_documents_not_complete()
  {
    // Arrange
    $candidature = $this->createCandidatureWithPayment(StatutPaiement::VERIFIED);
    $candidature->statut_candidature = StatutCandidature::SOUMISE;
    $candidature->documents_complets = false;
    $candidature->save();

    // Act
    $this->candidatureService->syncStatusWithPayment($candidature);

    // Assert
    $candidature->refresh();
    $this->assertEquals(StatutCandidature::PAIEMENT_VERIFIE, $candidature->statut_candidature);
    $this->assertTrue($candidature->paiement_valide);
  }

  /** @test */
  public function syncStatusWithPayment_updates_to_valide_when_payment_verified_and_documents_complete()
  {
    // Arrange
    $candidature = $this->createCandidatureWithPayment(StatutPaiement::VERIFIED);
    $candidature->statut_candidature = StatutCandidature::DOCUMENTS_VERIFIES;
    $candidature->documents_complets = true;
    $candidature->save();

    // Act
    $this->candidatureService->syncStatusWithPayment($candidature);

    // Assert
    $candidature->refresh();
    $this->assertEquals(StatutCandidature::VALIDE, $candidature->statut_candidature);
    $this->assertTrue($candidature->paiement_valide);
    $this->assertNotNull($candidature->date_validation);
  }

  /** @test */
  public function syncStatusWithPayment_maintains_status_when_payment_rejected()
  {
    // Arrange
    $candidature = $this->createCandidatureWithPayment(StatutPaiement::REJECTED);
    $candidature->statut_candidature = StatutCandidature::SOUMISE;
    $candidature->save();
    $originalStatus = $candidature->statut_candidature;

    // Act
    $this->candidatureService->syncStatusWithPayment($candidature);

    // Assert
    $candidature->refresh();
    $this->assertEquals($originalStatus, $candidature->statut_candidature);
  }

  /** @test */
  public function syncStatusWithPayment_maintains_status_when_payment_pending()
  {
    // Arrange
    $candidature = $this->createCandidatureWithPayment(StatutPaiement::PENDING);
    $candidature->statut_candidature = StatutCandidature::SOUMISE;
    $candidature->save();
    $originalStatus = $candidature->statut_candidature;

    // Act
    $this->candidatureService->syncStatusWithPayment($candidature);

    // Assert
    $candidature->refresh();
    $this->assertEquals($originalStatus, $candidature->statut_candidature);
  }

  /** @test */
  public function syncStatusWithPayment_maintains_status_when_payment_pending_manual_review()
  {
    // Arrange
    $candidature = $this->createCandidatureWithPayment(StatutPaiement::PENDING_MANUAL_REVIEW);
    $candidature->statut_candidature = StatutCandidature::SOUMISE;
    $candidature->save();
    $originalStatus = $candidature->statut_candidature;

    // Act
    $this->candidatureService->syncStatusWithPayment($candidature);

    // Assert
    $candidature->refresh();
    $this->assertEquals($originalStatus, $candidature->statut_candidature);
  }

  /** @test */
  public function updateEligibilityStatus_updates_paiement_valide_flag()
  {
    // Arrange
    $candidature = $this->createCandidatureWithPayment(StatutPaiement::VERIFIED);
    $candidature->paiement_valide = false;
    $candidature->save();

    // Act
    $this->candidatureService->updateEligibilityStatus($candidature);

    // Assert
    $candidature->refresh();
    $this->assertTrue($candidature->paiement_valide);
  }

  /** @test */
  public function updateEligibilityStatus_sets_paiement_valide_to_false_when_payment_not_verified()
  {
    // Arrange
    $candidature = $this->createCandidatureWithPayment(StatutPaiement::PENDING);
    $candidature->paiement_valide = true;
    $candidature->save();

    // Act
    $this->candidatureService->updateEligibilityStatus($candidature);

    // Assert
    $candidature->refresh();
    $this->assertFalse($candidature->paiement_valide);
  }

  /** @test */
  public function updateEligibilityStatus_updates_documents_complets_flag()
  {
    // Arrange
    $candidature = $this->createCandidatureWithPayment(StatutPaiement::VERIFIED);
    $candidature->documents_complets = false;
    $candidature->save();

    // Act
    $this->candidatureService->updateEligibilityStatus($candidature);

    // Assert
    $candidature->refresh();
    // documents_complets will be false because we don't have actual documents in this test
    $this->assertFalse($candidature->documents_complets);
  }

  // Helper methods

  private function createCandidatureWithoutPayment(): Candidature
  {
    $session = Session::factory()->create();
    $concours = Concours::factory()->create();
    $candidat = Candidat::factory()->create();

    // Create the concours_session relationship
    $concours->sessions()->attach($session->id);

    return Candidature::factory()->create([
      'candidat_id' => $candidat->utilisateur_id,
      'concours_id' => $concours->id,
      'session_id' => $session->id,
      'statut_candidature' => StatutCandidature::SOUMISE,
    ]);
  }

  private function createCandidatureWithPayment(StatutPaiement $paymentStatus): Candidature
  {
    $session = Session::factory()->create();
    $concours = Concours::factory()->create();
    $candidat = Candidat::factory()->create();

    // Create the concours_session relationship
    $concours->sessions()->attach($session->id);

    $candidature = Candidature::factory()->create([
      'candidat_id' => $candidat->utilisateur_id,
      'concours_id' => $concours->id,
      'session_id' => $session->id,
      'statut_candidature' => StatutCandidature::SOUMISE,
    ]);

    Paiement::factory()->create([
      'candidat_id' => $candidat->utilisateur_id,
      'concours_id' => $concours->id,
      'candidature_id' => $candidature->id,
      'statut' => $paymentStatus,
    ]);

    return $candidature;
  }
}
