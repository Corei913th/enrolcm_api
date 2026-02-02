<?php

namespace Tests\Unit\Services;

use App\Exceptions\Business\EligibilityException;
use App\Models\Candidature;
use App\Models\Convocation;
use App\Services\Domain\Candidature\Checkers\EligibilityChecker;
use App\Services\Domain\Candidature\ConvocationService;
use App\Services\Infrastructure\Pdf\ConvocationPdfService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ConvocationServiceTest extends TestCase
{
  use RefreshDatabase;

  private ConvocationService $service;
  private EligibilityChecker $eligibilityChecker;
  private ConvocationPdfService $pdfService;

  protected function setUp(): void
  {
    parent::setUp();

    $this->eligibilityChecker = $this->createMock(EligibilityChecker::class);
    $this->pdfService = $this->createMock(ConvocationPdfService::class);

    $this->service = new ConvocationService(
      $this->eligibilityChecker,
      $this->pdfService
    );
  }

  /**
   * Property 27: Convocation download blocked for non-validated candidature
   * Validates: Requirements 10.1
   */
  public function test_property_27_convocation_blocked_for_non_validated_candidature(): void
  {
    // Arrange: Créer une candidature avec différents statuts non-validés
    $nonValidatedStatuses = [\App\Enums\StatutCandidature::BROUILLON, \App\Enums\StatutCandidature::SOUMISE, \App\Enums\StatutCandidature::DOCUMENTS_VERIFIES, \App\Enums\StatutCandidature::REJETEE];

    foreach ($nonValidatedStatuses as $status) {
      $candidature = Candidature::factory()->create([
        'statut_candidature' => $status,
      ]);

      // Mock: L'eligibility checker retourne non éligible pour statut invalide
      $this->eligibilityChecker
        ->expects($this->once())
        ->method('canGenerateConvocation')
        ->with($candidature)
        ->willReturn([
          'eligible' => false,
          'reasons' => ['La candidature doit être validée (statut actuel: ' . $status->value . ')']
        ]);

      // Act & Assert: Vérifier que l'exception est lancée
      try {
        $this->service->downloadConvocation($candidature);
        $this->fail("Expected EligibilityException for status: {$status->value}");
      } catch (EligibilityException $e) {
        $this->assertStringContainsString('ne remplit pas tous les critères', $e->getMessage());
        $this->assertNotEmpty($e->getReasons());
      }

      // Reset mock for next iteration
      $this->setUp();
    }
  }

  /**
   * Property 28: Convocation download blocked for unverified payment
   * Validates: Requirements 10.2
   */
  public function test_property_28_convocation_blocked_for_unverified_payment(): void
  {
    // Arrange: Créer une candidature validée mais avec paiement non vérifié
    $candidature = Candidature::factory()->create([
      'statut_candidature' => \App\Enums\StatutCandidature::VALIDE,
      'paiement_valide' => false,
    ]);

    // Mock: L'eligibility checker retourne non éligible pour paiement non vérifié
    $this->eligibilityChecker
      ->expects($this->once())
      ->method('canGenerateConvocation')
      ->with($candidature)
      ->willReturn([
        'eligible' => false,
        'reasons' => ['Le paiement doit être vérifié et validé']
      ]);

    // Act & Assert: Vérifier que l'exception est lancée
    $this->expectException(EligibilityException::class);
    $this->expectExceptionMessage('ne remplit pas tous les critères');

    $this->service->downloadConvocation($candidature);
  }

  /**
   * Property 29: Convocation download blocked for unvalidated documents
   * Validates: Requirements 10.3
   */
  public function test_property_29_convocation_blocked_for_unvalidated_documents(): void
  {
    // Arrange: Créer une candidature validée avec paiement OK mais documents incomplets
    $candidature = Candidature::factory()->create([
      'statut_candidature' => \App\Enums\StatutCandidature::VALIDE,
      'paiement_valide' => true,
      'documents_complets' => false,
    ]);

    // Mock: L'eligibility checker retourne non éligible pour documents incomplets
    $this->eligibilityChecker
      ->expects($this->once())
      ->method('canGenerateConvocation')
      ->with($candidature)
      ->willReturn([
        'eligible' => false,
        'reasons' => ['Tous les documents requis doivent être validés']
      ]);

    // Act & Assert: Vérifier que l'exception est lancée
    $this->expectException(EligibilityException::class);
    $this->expectExceptionMessage('ne remplit pas tous les critères');

    $this->service->downloadConvocation($candidature);
  }

  /**
   * Property 30: Eligible candidature generates convocation PDF
   * Validates: Requirements 10.4
   */
  public function test_property_30_eligible_candidature_generates_convocation_pdf(): void
  {
    // Arrange: Créer une candidature complètement éligible
    $candidature = Candidature::factory()->create([
      'statut_candidature' => \App\Enums\StatutCandidature::VALIDE,
      'paiement_valide' => true,
      'documents_complets' => true,
    ]);

    // Mock: L'eligibility checker retourne éligible
    $this->eligibilityChecker
      ->expects($this->exactly(2)) // Called twice: once for downloadConvocation, once for generateConvocation
      ->method('canGenerateConvocation')
      ->with($candidature)
      ->willReturn([
        'eligible' => true,
        'reasons' => []
      ]);

    // Mock: Le PDF service génère un PDF
    $mockPdf = $this->createMock(\Barryvdh\DomPDF\PDF::class);
    $mockResponse = new Response('PDF Content', 200, [
      'Content-Type' => 'application/pdf',
      'Content-Disposition' => 'attachment; filename="convocation_test.pdf"'
    ]);

    $mockPdf->expects($this->once())
      ->method('download')
      ->willReturn($mockResponse);

    $this->pdfService
      ->expects($this->once())
      ->method('genererConvocation')
      ->with(
        $this->equalTo($candidature),
        $this->isInstanceOf(Convocation::class)
      )
      ->willReturn($mockPdf);

    // Act: Télécharger la convocation
    $response = $this->service->downloadConvocation($candidature);

    // Assert: Vérifier que le PDF est retourné
    $this->assertInstanceOf(Response::class, $response);
    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));

    // Assert: Vérifier qu'une convocation a été créée
    $this->assertDatabaseHas('convocations', [
      'candidature_id' => $candidature->id,
    ]);

    $convocation = $candidature->fresh()->convocation;
    $this->assertNotNull($convocation);
    $this->assertNotNull($convocation->numero_convocation);
    $this->assertStringStartsWith('CONV-', $convocation->numero_convocation);
    $this->assertNotNull($convocation->date_generation);
  }

  /**
   * Test: Génération de convocation crée un numéro unique
   */
  public function test_generate_convocation_creates_unique_numero(): void
  {
    // Arrange: Créer plusieurs candidatures éligibles
    $candidatures = Candidature::factory()->count(3)->create([
      'statut_candidature' => \App\Enums\StatutCandidature::VALIDE,
      'paiement_valide' => true,
      'documents_complets' => true,
    ]);

    // Mock: Toutes sont éligibles
    $this->eligibilityChecker
      ->method('canGenerateConvocation')
      ->willReturn([
        'eligible' => true,
        'reasons' => []
      ]);

    $numeros = [];

    // Act: Générer des convocations pour chaque candidature
    foreach ($candidatures as $candidature) {
      $convocation = $this->service->generateConvocation($candidature);
      $numeros[] = $convocation->numero_convocation;
    }

    // Assert: Tous les numéros sont uniques
    $this->assertCount(3, array_unique($numeros));
    foreach ($numeros as $numero) {
      $this->assertStringStartsWith('CONV-' . now()->year . '-', $numero);
    }
  }

  /**
   * Test: Génération de convocation ne crée pas de doublon
   */
  public function test_generate_convocation_does_not_create_duplicate(): void
  {
    // Arrange: Créer une candidature éligible
    $candidature = Candidature::factory()->create([
      'statut_candidature' => \App\Enums\StatutCandidature::VALIDE,
      'paiement_valide' => true,
      'documents_complets' => true,
    ]);

    // Mock: Éligible
    $this->eligibilityChecker
      ->method('canGenerateConvocation')
      ->willReturn([
        'eligible' => true,
        'reasons' => []
      ]);

    // Act: Générer la convocation deux fois
    $convocation1 = $this->service->generateConvocation($candidature);
    $convocation2 = $this->service->generateConvocation($candidature);

    // Assert: C'est la même convocation
    $this->assertEquals($convocation1->id, $convocation2->id);
    $this->assertEquals($convocation1->numero_convocation, $convocation2->numero_convocation);

    // Assert: Une seule convocation en base
    $this->assertDatabaseCount('convocations', 1);
  }

  /**
   * Test: Téléchargement marque la convocation comme téléchargée
   */
  public function test_download_convocation_marks_as_downloaded(): void
  {
    // Arrange: Créer une candidature éligible
    $candidature = Candidature::factory()->create([
      'statut_candidature' => \App\Enums\StatutCandidature::VALIDE,
      'paiement_valide' => true,
      'documents_complets' => true,
    ]);

    // Mock: Éligible
    $this->eligibilityChecker
      ->method('canGenerateConvocation')
      ->willReturn([
        'eligible' => true,
        'reasons' => []
      ]);

    // Mock: PDF service
    $mockPdf = $this->createMock(\Barryvdh\DomPDF\PDF::class);
    $mockPdf->method('download')->willReturn(new Response('PDF'));
    $this->pdfService->method('genererConvocation')->willReturn($mockPdf);

    // Act: Télécharger la convocation
    $this->service->downloadConvocation($candidature);

    // Assert: La convocation est marquée comme téléchargée
    $convocation = $candidature->fresh()->convocation;
    $this->assertNotNull($convocation->date_telechargement);
    $this->assertTrue($convocation->est_telechargee);
  }

  /**
   * Test: Multiple reasons are included in eligibility exception
   */
  public function test_eligibility_exception_includes_all_reasons(): void
  {
    // Arrange: Candidature avec plusieurs problèmes
    $candidature = Candidature::factory()->create([
      'statut_candidature' => \App\Enums\StatutCandidature::SOUMISE,
      'paiement_valide' => false,
      'documents_complets' => false,
    ]);

    $reasons = [
      'La candidature doit être validée',
      'Le paiement doit être vérifié',
      'Les documents doivent être validés'
    ];

    // Mock: Multiple raisons de non-éligibilité
    $this->eligibilityChecker
      ->expects($this->once())
      ->method('canGenerateConvocation')
      ->willReturn([
        'eligible' => false,
        'reasons' => $reasons
      ]);

    // Act & Assert
    try {
      $this->service->generateConvocation($candidature);
      $this->fail('Expected EligibilityException');
    } catch (EligibilityException $e) {
      $this->assertCount(3, $e->getReasons());
      foreach ($reasons as $reason) {
        $this->assertContains($reason, $e->getReasons());
      }
    }
  }
}
