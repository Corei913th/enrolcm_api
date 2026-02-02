<?php

namespace Tests\Unit\Services;

use App\Enums\StatutCandidature;
use App\Models\Alert;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\Session;
use App\Services\Domain\Notification\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationServiceAlertTest extends TestCase
{
  use RefreshDatabase;

  private NotificationService $notificationService;

  protected function setUp(): void
  {
    parent::setUp();
    $this->notificationService = new NotificationService();
  }

  private function createCandidature(): Candidature
  {
    $candidat = Candidat::factory()->create();

    // Get or create a concours and session
    $concours = Concours::first();
    if (!$concours) {
      $this->markTestSkipped('No concours available in database');
    }

    $session = Session::first();
    if (!$session) {
      $this->markTestSkipped('No session available in database');
    }

    return Candidature::create([
      'candidat_id' => $candidat->utilisateur_id,
      'concours_id' => $concours->id,
      'session_id' => $session->id,
      'date_candidature' => now(),
      'statut_candidature' => StatutCandidature::SOUMISE,
      'documents_complets' => false,
      'paiement_valide' => false,
    ]);
  }

  /** @test */
  public function it_creates_payment_pending_alert()
  {
    // Arrange
    $candidature = $this->createCandidature();

    // Act
    $alert = $this->notificationService->createPaymentPendingAlert($candidature);

    // Assert
    $this->assertInstanceOf(Alert::class, $alert);
    $this->assertEquals($candidature->id, $alert->candidature_id);
    $this->assertEquals('payment_pending', $alert->type);
    $this->assertEquals('warning', $alert->severity);
    $this->assertFalse($alert->is_dismissed);
    $this->assertDatabaseHas('alerts', [
      'candidature_id' => $candidature->id,
      'type' => 'payment_pending',
      'severity' => 'warning',
    ]);
  }

  /** @test */
  public function it_does_not_create_duplicate_payment_pending_alert()
  {
    // Arrange
    $candidature = $this->createCandidature();
    $firstAlert = $this->notificationService->createPaymentPendingAlert($candidature);

    // Act
    $secondAlert = $this->notificationService->createPaymentPendingAlert($candidature);

    // Assert
    $this->assertEquals($firstAlert->id, $secondAlert->id);
    $this->assertEquals(1, Alert::where('candidature_id', $candidature->id)
      ->where('type', 'payment_pending')
      ->where('is_dismissed', false)
      ->count());
  }

  /** @test */
  public function it_creates_payment_rejected_alert()
  {
    // Arrange
    $candidature = $this->createCandidature();
    $motif = 'Montant incorrect';

    // Act
    $alert = $this->notificationService->createPaymentRejectedAlert($candidature, $motif);

    // Assert
    $this->assertInstanceOf(Alert::class, $alert);
    $this->assertEquals($candidature->id, $alert->candidature_id);
    $this->assertEquals('payment_rejected', $alert->type);
    $this->assertEquals('critical', $alert->severity);
    $this->assertStringContainsString($motif, $alert->message);
    $this->assertFalse($alert->is_dismissed);
  }

  /** @test */
  public function it_dismisses_pending_alerts_when_creating_rejected_alert()
  {
    // Arrange
    $candidature = $this->createCandidature();
    $pendingAlert = $this->notificationService->createPaymentPendingAlert($candidature);
    $this->assertFalse($pendingAlert->is_dismissed);

    // Act
    $rejectedAlert = $this->notificationService->createPaymentRejectedAlert($candidature, 'Test motif');

    // Assert
    $pendingAlert->refresh();
    $this->assertTrue($pendingAlert->is_dismissed);
    $this->assertNotNull($pendingAlert->dismissed_at);
    $this->assertFalse($rejectedAlert->is_dismissed);
  }

  /** @test */
  public function it_gets_active_alerts_for_candidat()
  {
    // Arrange
    $candidat = Candidat::factory()->create();
    $candidature1 = $this->createCandidature();
    $candidature2 = $this->createCandidature();

    // Update candidatures to belong to the same candidat
    $candidature1->update(['candidat_id' => $candidat->utilisateur_id]);
    $candidature2->update(['candidat_id' => $candidat->utilisateur_id]);

    $alert1 = $this->notificationService->createPaymentPendingAlert($candidature1);
    $alert2 = $this->notificationService->createPaymentRejectedAlert($candidature2, 'Test');

    // Create a dismissed alert
    $dismissedAlert = Alert::create([
      'candidature_id' => $candidature1->id,
      'type' => 'test',
      'severity' => 'info',
      'title' => 'Test',
      'message' => 'Test',
      'is_dismissed' => true,
      'dismissed_at' => now(),
    ]);

    // Act
    $activeAlerts = $this->notificationService->getActiveAlerts($candidat);

    // Assert
    $this->assertCount(2, $activeAlerts);
    $this->assertTrue($activeAlerts->contains($alert1));
    $this->assertTrue($activeAlerts->contains($alert2));
    $this->assertFalse($activeAlerts->contains($dismissedAlert));
  }

  /** @test */
  public function it_orders_alerts_by_severity_and_date()
  {
    // Arrange
    $candidat = Candidat::factory()->create();
    $candidature = $this->createCandidature();
    $candidature->update(['candidat_id' => $candidat->utilisateur_id]);

    // Create alerts in different order
    $warningAlert = $this->notificationService->createPaymentPendingAlert($candidature);

    // Manually dismiss the warning alert to create a new rejected one
    Alert::where('candidature_id', $candidature->id)
      ->where('type', 'payment_pending')
      ->update(['is_dismissed' => true, 'dismissed_at' => now()]);

    $criticalAlert = $this->notificationService->createPaymentRejectedAlert($candidature, 'Test');

    // Act
    $activeAlerts = $this->notificationService->getActiveAlerts($candidat);

    // Assert
    $this->assertEquals(1, $activeAlerts->count());
    $this->assertEquals($criticalAlert->id, $activeAlerts->first()->id);
  }
}
