<?php

namespace Tests\Unit\Resources;

use App\Enums\StatutCandidature;
use App\Enums\StatutPaiement;
use App\Http\Resources\AlertResource;
use App\Http\Resources\CandidatureResource;
use App\Http\Resources\EligibilityResultResource;
use App\Http\Resources\PaiementResource;
use App\Models\Alert;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Paiement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiResourcesTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function alert_resource_formats_alert_correctly()
  {
    // Factory creates candidat automatically
    $candidature = Candidature::factory()->create();

    $alert = Alert::create([
      'candidature_id' => $candidature->id,
      'type' => 'payment_pending',
      'severity' => 'warning',
      'title' => 'Paiement en attente',
      'message' => 'Votre paiement est en attente de validation',
      'is_dismissed' => false,
    ]);

    $resource = new AlertResource($alert);
    $array = $resource->toArray(request());

    $this->assertEquals($alert->id, $array['id']);
    $this->assertEquals($alert->candidature_id, $array['candidature_id']);
    $this->assertEquals('payment_pending', $array['type']);
    $this->assertEquals('warning', $array['severity']);
    $this->assertEquals('Paiement en attente', $array['title']);
    $this->assertEquals('Votre paiement est en attente de validation', $array['message']);
    $this->assertFalse($array['is_dismissed']);
    $this->assertNull($array['dismissed_at']);
  }

  /** @test */
  public function eligibility_result_resource_formats_eligibility_data_correctly()
  {
    $eligibilityData = [
      'eligible' => false,
      'reasons' => ['Le paiement est en attente', 'Documents manquants'],
      'payment_status' => [
        'valid' => false,
        'status' => 'PENDING',
        'reason' => 'Le paiement est en attente'
      ],
      'documents_status' => [
        'valid' => false,
        'missing' => ['Carte d\'identité'],
        'pending' => [],
        'rejected' => []
      ],
      'academic_criteria' => [
        'eligible' => true,
        'reasons' => []
      ]
    ];

    $resource = new EligibilityResultResource($eligibilityData);
    $array = $resource->toArray(request());

    $this->assertFalse($array['eligible']);
    $this->assertCount(2, $array['reasons']);
    $this->assertFalse($array['payment_status']['valid']);
    $this->assertEquals('PENDING', $array['payment_status']['status']);
    $this->assertFalse($array['documents_status']['valid']);
    $this->assertContains('Carte d\'identité', $array['documents_status']['missing']);
    $this->assertTrue($array['academic_criteria']['eligible']);
  }

  /** @test */
  public function paiement_resource_includes_validation_notes()
  {
    // Create candidature first (which creates candidat automatically)
    $candidature = Candidature::factory()->create();

    $paiement = Paiement::factory()->create([
      'candidat_id' => $candidature->candidat_id,
      'concours_id' => $candidature->concours_id,
      'candidature_id' => $candidature->id,
      'statut' => StatutPaiement::VERIFIED,
      'validation_notes' => 'Paiement validé manuellement après vérification',
    ]);

    $resource = new PaiementResource($paiement);
    $array = $resource->toArray(request());

    $this->assertArrayHasKey('validation_notes', $array);
    $this->assertEquals('Paiement validé manuellement après vérification', $array['validation_notes']);
  }

  /** @test */
  public function candidature_resource_includes_eligibility_details()
  {
    $candidature = Candidature::factory()->create([
      'statut_candidature' => StatutCandidature::VALIDE,
      'paiement_valide' => true,
      'documents_complets' => true,
    ]);

    $resource = new CandidatureResource($candidature);
    $array = $resource->toArray(request());

    $this->assertArrayHasKey('paiement_valide', $array);
    $this->assertArrayHasKey('documents_complets', $array);
    $this->assertArrayHasKey('statut_candidature', $array);
    $this->assertTrue($array['paiement_valide']);
    $this->assertTrue($array['documents_complets']);
  }

  /** @test */
  public function candidature_resource_includes_alerts_when_loaded()
  {
    $candidature = Candidature::factory()->create();

    Alert::create([
      'candidature_id' => $candidature->id,
      'type' => 'payment_rejected',
      'severity' => 'critical',
      'title' => 'Paiement rejeté',
      'message' => 'Votre paiement a été rejeté',
      'is_dismissed' => false,
    ]);

    $candidature->load('alerts');

    $resource = new CandidatureResource($candidature);
    $array = $resource->toArray(request());

    $this->assertArrayHasKey('alerts', $array);
    $this->assertNotEmpty($array['alerts']);
  }
}
