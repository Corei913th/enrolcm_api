<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\Payment\PaymentVerificationService;
use App\Services\OCR\TesseractOcrService;
use App\Models\PaymentReceipt;
use App\Models\Candidat;
use App\Models\Utilisateur;
use App\Enums\TypeUtilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PaymentVerificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentVerificationService $service;
    protected Candidat $candidat;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('private');
        
        // Créer un candidat de test
        $utilisateur = Utilisateur::factory()->create([
            'type_utilisateur' => TypeUtilisateur::CANDIDAT,
        ]);
        
        $this->candidat = Candidat::create([
            'utilisateur_id' => $utilisateur->id,
            'nom_cand' => 'Test',
            'prenom_cand' => 'Candidat',
            'nationalite_cand' => 'Camerounaise',
            'numero_recu' => 'TEST-001',
        ]);

        $ocrService = $this->createMock(TesseractOcrService::class);
        $this->service = new PaymentVerificationService($ocrService);
    }

    /** @test */
    public function it_validates_file_type()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Format d\'image non supporté');

        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('validateFile');
        $method->setAccessible(true);
        $method->invoke($this->service, $file);
    }

    /** @test */
    public function it_validates_file_size()
    {
        $file = UploadedFile::fake()->image('receipt.jpg')->size(6000); // 6MB

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('trop volumineuse');

        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('validateFile');
        $method->setAccessible(true);
        $method->invoke($this->service, $file);
    }

    /** @test */
    public function it_checks_if_candidat_has_verified_payment()
    {
        // Pas de paiement
        $this->assertFalse($this->service->hasVerifiedPayment($this->candidat));

        // Créer un paiement vérifié
        PaymentReceipt::create([
            'candidat_id' => $this->candidat->utilisateur_id,
            'numero_recu' => 'REC-001',
            'montant' => 5000,
            'image_path' => 'receipts/test.jpg',
            'statut_verification' => 'verifie',
        ]);

        $this->assertTrue($this->service->hasVerifiedPayment($this->candidat));
    }

    /** @test */
    public function it_gets_verified_receipt()
    {
        // Créer un paiement vérifié
        $receipt = PaymentReceipt::create([
            'candidat_id' => $this->candidat->utilisateur_id,
            'numero_recu' => 'REC-001',
            'montant' => 5000,
            'image_path' => 'receipts/test.jpg',
            'statut_verification' => 'verifie',
        ]);

        $result = $this->service->getVerifiedReceipt($this->candidat);

        $this->assertNotNull($result);
        $this->assertEquals($receipt->id, $result->id);
    }

    /** @test */
    public function it_returns_null_when_no_verified_receipt()
    {
        $result = $this->service->getVerifiedReceipt($this->candidat);
        $this->assertNull($result);
    }
}
