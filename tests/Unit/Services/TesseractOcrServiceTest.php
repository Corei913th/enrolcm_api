<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\OCR\TesseractOcrService;
use Illuminate\Support\Facades\Storage;

class TesseractOcrServiceTest extends TestCase
{
    protected TesseractOcrService $ocrService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ocrService = new TesseractOcrService();
    }

    /** @test */
    public function it_can_parse_montant_with_spaces()
    {
        $reflection = new \ReflectionClass($this->ocrService);
        $method = $reflection->getMethod('parseMontant');
        $method->setAccessible(true);

        $result = $method->invoke($this->ocrService, '5 000');
        $this->assertEquals(5000.0, $result);

        $result = $method->invoke($this->ocrService, '10 500,50');
        $this->assertEquals(10500.50, $result);
    }

    /** @test */
    public function it_can_parse_date_in_different_formats()
    {
        $reflection = new \ReflectionClass($this->ocrService);
        $method = $reflection->getMethod('parseDate');
        $method->setAccessible(true);

        $result = $method->invoke($this->ocrService, '25/12/2024');
        $this->assertEquals('2024-12-25', $result);

        $result = $method->invoke($this->ocrService, '25-12-2024');
        $this->assertEquals('2024-12-25', $result);
    }

    /** @test */
    public function it_returns_null_for_invalid_montant()
    {
        $reflection = new \ReflectionClass($this->ocrService);
        $method = $reflection->getMethod('parseMontant');
        $method->setAccessible(true);

        $result = $method->invoke($this->ocrService, null);
        $this->assertNull($result);

        $result = $method->invoke($this->ocrService, 'invalid');
        $this->assertNull($result);
    }

    /** @test */
    public function it_returns_null_for_invalid_date()
    {
        $reflection = new \ReflectionClass($this->ocrService);
        $method = $reflection->getMethod('parseDate');
        $method->setAccessible(true);

        $result = $method->invoke($this->ocrService, null);
        $this->assertNull($result);

        $result = $method->invoke($this->ocrService, 'invalid-date');
        $this->assertNull($result);
    }

    /** @test */
    public function it_calculates_confidence_score()
    {
        $reflection = new \ReflectionClass($this->ocrService);
        $method = $reflection->getMethod('calculateConfidence');
        $method->setAccessible(true);

        // Texte avec plusieurs mots-clés
        $text = 'Reçu de paiement Montant: 5000 FCFA Date: 25/12/2024 Banque: BICEC';
        $result = $method->invoke($this->ocrService, $text);
        $this->assertGreaterThan(0.7, $result);

        // Texte court sans mots-clés
        $text = 'Hello world';
        $result = $method->invoke($this->ocrService, $text);
        $this->assertLessThan(0.7, $result);
    }

    /** @test */
    public function it_extracts_patterns_correctly()
    {
        $reflection = new \ReflectionClass($this->ocrService);
        $method = $reflection->getMethod('extractPatterns');
        $method->setAccessible(true);

        $text = 'Reçu N° REC-2024-001 Montant: 5000 FCFA Date: 25/12/2024 Banque: BICEC';
        $patterns = [
            'numero_recu' => ['/(?:N°|Numéro|Ref)[:\s]*([A-Z0-9\-\/]+)/i'],
            'montant' => ['/(?:Montant)[:\s]*([\d\s,\.]+)\s*(?:FCFA)/i'],
            'banque' => ['/(BICEC|UBA|SGBC)/i'],
        ];

        $result = $method->invoke($this->ocrService, $text, $patterns);

        $this->assertEquals('REC-2024-001', $result['numero_recu']);
        $this->assertEquals('5000', $result['montant']);
        $this->assertEquals('BICEC', $result['banque']);
    }
}
