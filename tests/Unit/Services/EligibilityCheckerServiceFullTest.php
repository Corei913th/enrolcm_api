<?php

namespace Tests\Unit\Services;

use App\Enums\StatutCandidature;
use App\Enums\StatutPaiement;
use App\Enums\StatutVerificationDocument;
use App\Models\Candidature;
use App\Models\Paiement;
use App\Services\Domain\Candidature\DocumentService;
use App\Services\Domain\Candidature\Checkers\EligibilityChecker;
use Tests\TestCase;

// DocumentStatutDTO is in the global namespace
require_once __DIR__ . '/../../../app/DTOs/Documents/DocumentStatutDTO.php';

class EligibilityCheckerServiceFullTest extends TestCase
{
  private EligibilityChecker $service;
  private DocumentService $documentService;

  protected function setUp(): void
  {
    parent::setUp();
    $this->documentService = $this->createMock(DocumentService::class);
    $this->service = new EligibilityChecker($this->documentService);
  }

  /** @test */
  public function checkPaymentStatus_returns_valid_for_verified_payment()
  {
    $paiement = new Paiement(['statut' => StatutPaiement::VERIFIED]);
    $candidature = new Candidature();
    $candidature->setRelation('paiement', $paiement);

    $result = $this->service->checkPaymentStatus($candidature);

    $this->assertTrue($result['valid']);
    $this->assertEquals(StatutPaiement::VERIFIED->value, $result['status']);
    $this->assertEmpty($result['reason']);
  }

  /** @test */
  public function checkPaymentStatus_returns_invalid_for_pending_payment()
  {
    $paiement = new Paiement(['statut' => StatutPaiement::PENDING]);
    $candidature = new Candidature();
    $candidature->setRelation('paiement', $paiement);

    $result = $this->service->checkPaymentStatus($candidature);

    $this->assertFalse($result['valid']);
    $this->assertEquals(StatutPaiement::PENDING->value, $result['status']);
    $this->assertStringContainsString('en attente', $result['reason']);
  }

  /** @test */
  public function checkPaymentStatus_returns_invalid_for_pending_manual_review()
  {
    $paiement = new Paiement(['statut' => StatutPaiement::PENDING_MANUAL_REVIEW]);
    $candidature = new Candidature();
    $candidature->setRelation('paiement', $paiement);

    $result = $this->service->checkPaymentStatus($candidature);

    $this->assertFalse($result['valid']);
    $this->assertEquals(StatutPaiement::PENDING_MANUAL_REVIEW->value, $result['status']);
    $this->assertStringContainsString('en attente de validation manuelle', $result['reason']);
  }

  /** @test */
  public function checkPaymentStatus_returns_invalid_for_rejected_payment()
  {
    $paiement = new Paiement(['statut' => StatutPaiement::REJECTED]);
    $candidature = new Candidature();
    $candidature->setRelation('paiement', $paiement);

    $result = $this->service->checkPaymentStatus($candidature);

    $this->assertFalse($result['valid']);
    $this->assertEquals(StatutPaiement::REJECTED->value, $result['status']);
    $this->assertStringContainsString('rejeté', $result['reason']);
  }

  /** @test */
  public function checkPaymentStatus_returns_invalid_when_no_payment()
  {
    $candidature = new Candidature();
    $candidature->setRelation('paiement', null);

    $result = $this->service->checkPaymentStatus($candidature);

    $this->assertFalse($result['valid']);
    $this->assertEquals('MISSING', $result['status']);
    $this->assertStringContainsString('Aucun paiement', $result['reason']);
  }

  /** @test */
  public function checkDocumentsStatus_returns_valid_when_documents_complete()
  {
    $candidature = new Candidature();

    $this->documentService
      ->expects($this->once())
      ->method('areDocumentsComplete')
      ->with($candidature)
      ->willReturn(true);

    $result = $this->service->checkDocumentsStatus($candidature);

    $this->assertTrue($result['valid']);
    $this->assertEmpty($result['missing']);
    $this->assertEmpty($result['pending']);
    $this->assertEmpty($result['rejected']);
  }

  /** @test */
  public function checkDocumentsStatus_returns_invalid_with_missing_documents()
  {
    $candidature = new Candidature();

    $this->documentService
      ->expects($this->once())
      ->method('areDocumentsComplete')
      ->with($candidature)
      ->willReturn(false);

    $this->documentService
      ->expects($this->once())
      ->method('getRequiredDocumentsStatusForCandidature')
      ->with($candidature)
      ->willReturn([
        new \DocumentStatutDTO(
          documentRequisId: '1',
          nom: 'Carte d\'identité',
          estObligatoire: true,
          statut: StatutVerificationDocument::NON_SOUMIS,
          commentaire: null
        ),
        new \DocumentStatutDTO(
          documentRequisId: '2',
          nom: 'Diplôme',
          estObligatoire: true,
          statut: StatutVerificationDocument::EN_ATTENTE,
          commentaire: null
        ),
      ]);

    $result = $this->service->checkDocumentsStatus($candidature);

    $this->assertFalse($result['valid']);
    $this->assertCount(1, $result['missing']);
    $this->assertContains('Carte d\'identité', $result['missing']);
    $this->assertCount(1, $result['pending']);
    $this->assertContains('Diplôme', $result['pending']);
    $this->assertEmpty($result['rejected']);
  }

  /** @test */
  public function checkDocumentsStatus_returns_invalid_with_rejected_documents()
  {
    $candidature = new Candidature();

    $this->documentService
      ->expects($this->once())
      ->method('areDocumentsComplete')
      ->with($candidature)
      ->willReturn(false);

    $this->documentService
      ->expects($this->once())
      ->method('getRequiredDocumentsStatusForCandidature')
      ->with($candidature)
      ->willReturn([
        new \DocumentStatutDTO(
          documentRequisId: '1',
          nom: 'Photo',
          estObligatoire: true,
          statut: StatutVerificationDocument::REJETE,
          commentaire: 'Photo floue'
        ),
      ]);

    $result = $this->service->checkDocumentsStatus($candidature);

    $this->assertFalse($result['valid']);
    $this->assertEmpty($result['missing']);
    $this->assertEmpty($result['pending']);
    $this->assertCount(1, $result['rejected']);
    $this->assertContains('Photo', $result['rejected']);
  }

  /** @test */
  public function checkFullEligibility_returns_eligible_when_all_criteria_met()
  {
    $paiement = new Paiement(['statut' => StatutPaiement::VERIFIED]);
    $candidature = new Candidature(['statut_candidature' => StatutCandidature::VALIDE]);
    $candidature->setRelation('paiement', $paiement);

    $this->documentService
      ->expects($this->once())
      ->method('areDocumentsComplete')
      ->with($candidature)
      ->willReturn(true);

    $result = $this->service->checkFullEligibility($candidature);

    $this->assertTrue($result['eligible']);
    $this->assertEmpty($result['reasons']);
  }

  /** @test */
  public function checkFullEligibility_returns_ineligible_with_invalid_status()
  {
    $paiement = new Paiement(['statut' => StatutPaiement::VERIFIED]);
    $candidature = new Candidature(['statut_candidature' => StatutCandidature::SOUMISE]);
    $candidature->setRelation('paiement', $paiement);

    $this->documentService
      ->expects($this->once())
      ->method('areDocumentsComplete')
      ->with($candidature)
      ->willReturn(true);

    $result = $this->service->checkFullEligibility($candidature);

    $this->assertFalse($result['eligible']);
    $this->assertCount(1, $result['reasons']);
    $this->assertStringContainsString('statut VALIDE', $result['reasons'][0]);
  }

  /** @test */
  public function checkFullEligibility_returns_ineligible_with_unverified_payment()
  {
    $paiement = new Paiement(['statut' => StatutPaiement::PENDING_MANUAL_REVIEW]);
    $candidature = new Candidature(['statut_candidature' => StatutCandidature::VALIDE]);
    $candidature->setRelation('paiement', $paiement);

    $this->documentService
      ->expects($this->once())
      ->method('areDocumentsComplete')
      ->with($candidature)
      ->willReturn(true);

    $result = $this->service->checkFullEligibility($candidature);

    $this->assertFalse($result['eligible']);
    $this->assertCount(1, $result['reasons']);
    $this->assertStringContainsString('en attente de validation manuelle', $result['reasons'][0]);
  }

  /** @test */
  public function checkFullEligibility_returns_ineligible_with_incomplete_documents()
  {
    $paiement = new Paiement(['statut' => StatutPaiement::VERIFIED]);
    $candidature = new Candidature(['statut_candidature' => StatutCandidature::VALIDE]);
    $candidature->setRelation('paiement', $paiement);

    $this->documentService
      ->expects($this->once())
      ->method('areDocumentsComplete')
      ->with($candidature)
      ->willReturn(false);

    $this->documentService
      ->expects($this->once())
      ->method('getRequiredDocumentsStatusForCandidature')
      ->with($candidature)
      ->willReturn([
        new \DocumentStatutDTO(
          documentRequisId: '1',
          nom: 'Acte de naissance',
          estObligatoire: true,
          statut: StatutVerificationDocument::NON_SOUMIS,
          commentaire: null
        ),
      ]);

    $result = $this->service->checkFullEligibility($candidature);

    $this->assertFalse($result['eligible']);
    $this->assertCount(1, $result['reasons']);
    $this->assertStringContainsString('Documents manquants', $result['reasons'][0]);
    $this->assertStringContainsString('Acte de naissance', $result['reasons'][0]);
  }

  /** @test */
  public function checkFullEligibility_returns_multiple_reasons_when_multiple_criteria_fail()
  {
    $paiement = new Paiement(['statut' => StatutPaiement::PENDING]);
    $candidature = new Candidature(['statut_candidature' => StatutCandidature::SOUMISE]);
    $candidature->setRelation('paiement', $paiement);

    $this->documentService
      ->expects($this->once())
      ->method('areDocumentsComplete')
      ->with($candidature)
      ->willReturn(false);

    $this->documentService
      ->expects($this->once())
      ->method('getRequiredDocumentsStatusForCandidature')
      ->with($candidature)
      ->willReturn([
        new \DocumentStatutDTO(
          documentRequisId: '1',
          nom: 'Relevé de notes',
          estObligatoire: true,
          statut: StatutVerificationDocument::REJETE,
          commentaire: 'Illisible'
        ),
      ]);

    $result = $this->service->checkFullEligibility($candidature);

    $this->assertFalse($result['eligible']);
    $this->assertCount(3, $result['reasons']);
  }

  /** @test */
  public function canGenerateConvocation_delegates_to_checkFullEligibility()
  {
    $paiement = new Paiement(['statut' => StatutPaiement::VERIFIED]);
    $candidature = new Candidature(['statut_candidature' => StatutCandidature::VALIDE]);
    $candidature->setRelation('paiement', $paiement);

    $this->documentService
      ->expects($this->once())
      ->method('areDocumentsComplete')
      ->with($candidature)
      ->willReturn(true);

    $result = $this->service->canGenerateConvocation($candidature);

    $this->assertTrue($result['eligible']);
    $this->assertEmpty($result['reasons']);
  }

  /** @test */
  public function canGenerateFicheInscription_delegates_to_checkFullEligibility()
  {
    $paiement = new Paiement(['statut' => StatutPaiement::VERIFIED]);
    $candidature = new Candidature(['statut_candidature' => StatutCandidature::VALIDE]);
    $candidature->setRelation('paiement', $paiement);

    $this->documentService
      ->expects($this->once())
      ->method('areDocumentsComplete')
      ->with($candidature)
      ->willReturn(true);

    $result = $this->service->canGenerateFicheInscription($candidature);

    $this->assertTrue($result['eligible']);
    $this->assertEmpty($result['reasons']);
  }
}
