<?php

namespace Tests\Unit\Services;

use App\DTOs\Payment\PaymentReceiptDTO;
use App\Services\Infrastructure\OCR\PaymentOcrService;
use App\Services\Infrastructure\OCR\TesseractOcrService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PaymentOcrServiceTest extends TestCase
{
  private PaymentOcrService $paymentOcrService;
  private TesseractOcrService $tesseractService;

  protected function setUp(): void
  {
    parent::setUp();

    $this->tesseractService = $this->createMock(TesseractOcrService::class);
    $this->paymentOcrService = new PaymentOcrService($this->tesseractService);
  }

  /**
   * Test successful OCR extraction with all required fields
   */
  public function test_extract_payment_data_with_successful_ocr(): void
  {
    // Arrange
    $file = UploadedFile::fake()->image('receipt.jpg');

    $receiptDTO = new PaymentReceiptDTO(
      numero_recu: 'PRU123456',
      montant: 50000.0,
      date_paiement: '2026-01-15',
      banque: 'UBA',
      numero_compte: 'CM21001234567890',
      ocr_confidence: 0.85,
      raw_data: ['full_text' => 'Sample receipt text']
    );

    $this->tesseractService
      ->expects($this->once())
      ->method('extractReceiptData')
      ->willReturn($receiptDTO);

    // Act
    $result = $this->paymentOcrService->extractPaymentData($file);

    // Assert
    $this->assertTrue($result['success']);
    $this->assertIsArray($result['data']);
    $this->assertEquals('PRU123456', $result['data']['reference']);
    $this->assertEquals(50000.0, $result['data']['montant']);
    $this->assertEquals('UBA', $result['data']['banque']);
    $this->assertEquals('2026-01-15', $result['data']['date_paiement']);
    $this->assertEquals('CM21001234567890', $result['data']['numero_compte']);
    $this->assertEquals(0.85, $result['confidence']);
    $this->assertEquals('Informations de paiement extraites avec succès', $result['message']);
  }

  /**
   * Test OCR extraction with low confidence
   */
  public function test_extract_payment_data_with_low_confidence(): void
  {
    // Arrange
    $file = UploadedFile::fake()->image('receipt.jpg');

    $receiptDTO = new PaymentReceiptDTO(
      numero_recu: 'PRU123456',
      montant: 50000.0,
      date_paiement: '2026-01-15',
      banque: 'UBA',
      numero_compte: null,
      ocr_confidence: 0.4, // Below threshold
      raw_data: ['full_text' => 'Blurry text']
    );

    $this->tesseractService
      ->expects($this->once())
      ->method('extractReceiptData')
      ->willReturn($receiptDTO);

    // Act
    $result = $this->paymentOcrService->extractPaymentData($file);

    // Assert
    $this->assertFalse($result['success']);
    $this->assertNull($result['data']);
    $this->assertEquals(0.4, $result['confidence']);
    $this->assertEquals('Impossible d\'extraire les informations. Veuillez les saisir manuellement', $result['message']);
  }

  /**
   * Test OCR extraction with missing reference
   */
  public function test_extract_payment_data_with_missing_reference(): void
  {
    // Arrange
    $file = UploadedFile::fake()->image('receipt.jpg');

    $receiptDTO = new PaymentReceiptDTO(
      numero_recu: null, // Missing reference
      montant: 50000.0,
      date_paiement: '2026-01-15',
      banque: 'UBA',
      numero_compte: null,
      ocr_confidence: 0.8,
      raw_data: ['full_text' => 'Receipt without reference']
    );

    $this->tesseractService
      ->expects($this->once())
      ->method('extractReceiptData')
      ->willReturn($receiptDTO);

    // Act
    $result = $this->paymentOcrService->extractPaymentData($file);

    // Assert
    $this->assertFalse($result['success']);
    $this->assertNull($result['data']);
    $this->assertEquals('Impossible d\'extraire les informations. Veuillez les saisir manuellement', $result['message']);
  }

  /**
   * Test OCR extraction with missing amount
   */
  public function test_extract_payment_data_with_missing_amount(): void
  {
    // Arrange
    $file = UploadedFile::fake()->image('receipt.jpg');

    $receiptDTO = new PaymentReceiptDTO(
      numero_recu: 'PRU123456',
      montant: null, // Missing amount
      date_paiement: '2026-01-15',
      banque: 'UBA',
      numero_compte: null,
      ocr_confidence: 0.8,
      raw_data: ['full_text' => 'Receipt without amount']
    );

    $this->tesseractService
      ->expects($this->once())
      ->method('extractReceiptData')
      ->willReturn($receiptDTO);

    // Act
    $result = $this->paymentOcrService->extractPaymentData($file);

    // Assert
    $this->assertFalse($result['success']);
    $this->assertNull($result['data']);
    $this->assertEquals('Impossible d\'extraire les informations. Veuillez les saisir manuellement', $result['message']);
  }

  /**
   * Test OCR extraction with exception handling
   */
  public function test_extract_payment_data_handles_exception(): void
  {
    // Arrange
    $file = UploadedFile::fake()->image('receipt.jpg');

    $this->tesseractService
      ->expects($this->once())
      ->method('extractReceiptData')
      ->willThrowException(new \Exception('OCR processing failed'));

    Log::shouldReceive('error')
      ->once()
      ->with('Erreur lors de l\'extraction OCR', \Mockery::type('array'));

    // Act
    $result = $this->paymentOcrService->extractPaymentData($file);

    // Assert
    $this->assertFalse($result['success']);
    $this->assertNull($result['data']);
    $this->assertEquals(0.0, $result['confidence']);
    $this->assertEquals('Erreur lors de l\'extraction. Veuillez saisir les informations manuellement', $result['message']);
  }

  /**
   * Test data mapping from DTO to registration format
   */
  public function test_data_mapping_from_dto_to_registration_format(): void
  {
    // Arrange
    $file = UploadedFile::fake()->image('receipt.jpg');

    $receiptDTO = new PaymentReceiptDTO(
      numero_recu: 'TEST-REF-789',
      montant: 75000.0,
      date_paiement: '2026-01-18',
      banque: 'BICEC',
      numero_compte: 'BICEC123456789',
      ocr_confidence: 0.92,
      raw_data: ['full_text' => 'Complete receipt']
    );

    $this->tesseractService
      ->expects($this->once())
      ->method('extractReceiptData')
      ->willReturn($receiptDTO);

    // Act
    $result = $this->paymentOcrService->extractPaymentData($file);

    // Assert - Verify field mapping
    $this->assertTrue($result['success']);
    $this->assertArrayHasKey('reference', $result['data']);
    $this->assertArrayHasKey('montant', $result['data']);
    $this->assertArrayHasKey('banque', $result['data']);
    $this->assertArrayHasKey('date_paiement', $result['data']);
    $this->assertArrayHasKey('numero_compte', $result['data']);

    // Verify values are correctly mapped
    $this->assertEquals('TEST-REF-789', $result['data']['reference']);
    $this->assertEquals(75000.0, $result['data']['montant']);
    $this->assertEquals('BICEC', $result['data']['banque']);
    $this->assertEquals('2026-01-18', $result['data']['date_paiement']);
    $this->assertEquals('BICEC123456789', $result['data']['numero_compte']);
  }

  /**
   * Test OCR with partial data extraction
   */
  public function test_extract_payment_data_with_partial_data(): void
  {
    // Arrange
    $file = UploadedFile::fake()->image('receipt.jpg');

    $receiptDTO = new PaymentReceiptDTO(
      numero_recu: 'PRU999888',
      montant: 30000.0,
      date_paiement: null, // Missing date
      banque: null, // Missing bank
      numero_compte: null, // Missing account
      ocr_confidence: 0.75,
      raw_data: ['full_text' => 'Partial receipt']
    );

    $this->tesseractService
      ->expects($this->once())
      ->method('extractReceiptData')
      ->willReturn($receiptDTO);

    // Act
    $result = $this->paymentOcrService->extractPaymentData($file);

    // Assert - Should succeed if reference and amount are present
    $this->assertTrue($result['success']);
    $this->assertEquals('PRU999888', $result['data']['reference']);
    $this->assertEquals(30000.0, $result['data']['montant']);
    $this->assertNull($result['data']['date_paiement']);
    $this->assertNull($result['data']['banque']);
    $this->assertNull($result['data']['numero_compte']);
  }
}
