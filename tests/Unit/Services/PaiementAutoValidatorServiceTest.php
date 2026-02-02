<?php

namespace Tests\Unit\Services;

use App\Enums\StatutPaiement;
use App\Models\Concours;
use App\Services\Domain\Registration\PaiementAutoValidatorService;
use Tests\TestCase;

class PaiementAutoValidatorServiceTest extends TestCase
{
  private PaiementAutoValidatorService $service;
  private Concours $concours;

  protected function setUp(): void
  {
    parent::setUp();
    $this->service = new PaiementAutoValidatorService();
    $this->concours = Concours::factory()->create(['frais_inscription' => 50000]);
  }

  /** @test */
  public function valide_paiement_avec_donnees_correctes()
  {
    $data = [
      'montant' => 50000,
      'date_paiement' => now()->format('Y-m-d'),
      'reference_paiement' => 'POLY-2026-12345',
    ];

    $result = $this->service->validate($this->concours, $data);

    $this->assertEquals(StatutPaiement::VERIFIED, $result['statut']);
    $this->assertTrue($result['auto_valide']);
    $this->assertEmpty($result['raisons_attente']);
  }

  /** @test */
  public function valide_paiement_avec_tolerance_montant_5_pourcent()
  {
    // +5% = 52500, -5% = 47500
    $data = [
      'montant' => 52500, // Exactement +5%
      'date_paiement' => now()->format('Y-m-d'),
      'reference_paiement' => 'POLY-2026-12345',
    ];

    $result = $this->service->validate($this->concours, $data);

    $this->assertEquals(StatutPaiement::VERIFIED, $result['statut']);
    $this->assertTrue($result['auto_valide']);
  }

  /** @test */
  public function rejette_paiement_montant_trop_different()
  {
    $data = [
      'montant' => 40000, // -20% (hors tolérance)
      'date_paiement' => now()->format('Y-m-d'),
      'reference_paiement' => 'POLY-2026-12345',
    ];

    $result = $this->service->validate($this->concours, $data);

    $this->assertEquals(StatutPaiement::PENDING, $result['statut']);
    $this->assertFalse($result['auto_valide']);
    $this->assertNotEmpty($result['raisons_attente']);
    $this->assertStringContainsString('Montant incorrect', $result['raisons_attente'][0]);
  }

  /** @test */
  public function rejette_paiement_date_future()
  {
    $data = [
      'montant' => 50000,
      'date_paiement' => now()->addDays(1)->format('Y-m-d'),
      'reference_paiement' => 'POLY-2026-12345',
    ];

    $result = $this->service->validate($this->concours, $data);

    $this->assertEquals(StatutPaiement::PENDING, $result['statut']);
    $this->assertFalse($result['auto_valide']);
    $this->assertStringContainsString('futur', $result['raisons_attente'][0]);
  }

  /** @test */
  public function rejette_paiement_date_trop_ancienne()
  {
    $data = [
      'montant' => 50000,
      'date_paiement' => now()->subDays(61)->format('Y-m-d'), // > 60 jours
      'reference_paiement' => 'POLY-2026-12345',
    ];

    $result = $this->service->validate($this->concours, $data);

    $this->assertEquals(StatutPaiement::PENDING, $result['statut']);
    $this->assertFalse($result['auto_valide']);
    $this->assertStringContainsString('ancienne', $result['raisons_attente'][0]);
  }

  /** @test */
  public function valide_paiement_date_limite_60_jours()
  {
    $data = [
      'montant' => 50000,
      'date_paiement' => now()->subDays(60)->format('Y-m-d'), // Exactement 60 jours
      'reference_paiement' => 'POLY-2026-12345',
    ];

    $result = $this->service->validate($this->concours, $data);

    $this->assertEquals(StatutPaiement::VERIFIED, $result['statut']);
    $this->assertTrue($result['auto_valide']);
  }

  /** @test */
  public function rejette_paiement_reference_format_invalide()
  {
    $invalidReferences = [
      'POLY202612345',      // Sans tirets
      'poly-2026-12345',    // Minuscules
      'POLY-26-12345',      // Année courte
      'POLY-2026-123',      // Numéro court
      'POLY-2026-123456',   // Numéro long
      '123-2026-12345',     // Commence par chiffre
    ];

    foreach ($invalidReferences as $reference) {
      $data = [
        'montant' => 50000,
        'date_paiement' => now()->format('Y-m-d'),
        'reference_paiement' => $reference,
      ];

      $result = $this->service->validate($this->concours, $data);

      $this->assertEquals(StatutPaiement::PENDING, $result['statut'], "Failed for reference: {$reference}");
      $this->assertFalse($result['auto_valide']);
      $this->assertStringContainsString('Format de référence invalide', $result['raisons_attente'][0]);
    }
  }

  /** @test */
  public function valide_paiement_references_format_valide()
  {
    $validReferences = [
      'POLY-2026-12345',
      'ENAM-2026-00001',
      'ENSP-2026-99999',
      'ABC-2026-12345',
    ];

    foreach ($validReferences as $reference) {
      $data = [
        'montant' => 50000,
        'date_paiement' => now()->format('Y-m-d'),
        'reference_paiement' => $reference,
      ];

      $result = $this->service->validate($this->concours, $data);

      $this->assertEquals(StatutPaiement::VERIFIED, $result['statut'], "Failed for reference: {$reference}");
      $this->assertTrue($result['auto_valide']);
    }
  }

  /** @test */
  public function rejette_paiement_reference_manquante()
  {
    $data = [
      'montant' => 50000,
      'date_paiement' => now()->format('Y-m-d'),
      'reference_paiement' => '',
    ];

    $result = $this->service->validate($this->concours, $data);

    $this->assertEquals(StatutPaiement::PENDING, $result['statut']);
    $this->assertFalse($result['auto_valide']);
    $this->assertStringContainsString('manquante', $result['raisons_attente'][0]);
  }

  /** @test */
  public function retourne_toutes_les_raisons_echec()
  {
    $data = [
      'montant' => 40000,                                      // Montant incorrect
      'date_paiement' => now()->addDays(1)->format('Y-m-d'),  // Date future
      'reference_paiement' => 'invalid',                       // Format invalide
    ];

    $result = $this->service->validate($this->concours, $data);

    $this->assertEquals(StatutPaiement::PENDING, $result['statut']);
    $this->assertFalse($result['auto_valide']);
    $this->assertCount(3, $result['raisons_attente']);
  }
}
