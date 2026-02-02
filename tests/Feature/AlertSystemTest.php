<?php

namespace Tests\Feature;

use App\Enums\StatutCandidature;
use App\Models\Alert;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\Session;
use App\Services\Domain\Notification\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertSystemTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function alert_system_integration_test()
  {
    // Seed database
    $this->artisan('db:seed', ['--class' => 'EcoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'SessionSeeder']);

    $notificationService = new NotificationService();

    // Create candidat
    $candidat = Candidat::factory()->create();

    // Get concours and session
    $concours = Concours::first();
    $session = Session::first();

    if (!$concours || !$session) {
      $this->markTestSkipped('No concours or session available');
    }

    // Create candidature
    $candidature = Candidature::create([
      'candidat_id' => $candidat->utilisateur_id,
      'concours_id' => $concours->id,
      'session_id' => $session->id,
      'date_candidature' => now(),
      'statut_candidature' => StatutCandidature::SOUMISE,
      'documents_complets' => false,
      'paiement_valide' => false,
    ]);

    // Test 1: Create payment pending alert
    $pendingAlert = $notificationService->createPaymentPendingAlert($candidature);
    $this->assertNotNull($pendingAlert);
    $this->assertEquals('payment_pending', $pendingAlert->type);
    $this->assertEquals('warning', $pendingAlert->severity);

    // Test 2: Get active alerts
    $activeAlerts = $notificationService->getActiveAlerts($candidat);
    $this->assertCount(1, $activeAlerts);

    // Test 3: Create payment rejected alert (should dismiss pending alert)
    $rejectedAlert = $notificationService->createPaymentRejectedAlert($candidature, 'Test motif');
    $this->assertNotNull($rejectedAlert);
    $this->assertEquals('payment_rejected', $rejectedAlert->type);
    $this->assertEquals('critical', $rejectedAlert->severity);

    // Test 4: Verify pending alert was dismissed
    $pendingAlert->refresh();
    $this->assertTrue($pendingAlert->is_dismissed);

    // Test 5: Get active alerts (should only have rejected alert)
    $activeAlerts = $notificationService->getActiveAlerts($candidat);
    $this->assertCount(1, $activeAlerts);
    $this->assertEquals($rejectedAlert->id, $activeAlerts->first()->id);

    $this->assertTrue(true, 'Alert system integration test passed');
  }
}
