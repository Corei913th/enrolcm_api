<?php

namespace Tests\Unit\Services;

use App\Enums\CanalNotification;
use App\Enums\PrioriteNotification;
use App\Enums\StatutCandidature;
use App\Enums\StatutPaiement;
use App\Enums\TypeNotification;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\Notification;
use App\Models\Paiement;
use App\Models\Session;
use App\Models\Utilisateur;
use App\Services\Domain\Notification\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationServicePaymentTest extends TestCase
{
  use RefreshDatabase;

  private NotificationService $notificationService;

  protected function setUp(): void
  {
    parent::setUp();
    $this->notificationService = new NotificationService();
  }

  private function createCandidatWithUtilisateur(bool $emailVerifie = true): Candidat
  {
    $utilisateur = Utilisateur::create([
      'user_name' => 'test_' . uniqid(),
      'email' => 'test_' . uniqid() . '@example.com',
      'mot_de_passe' => bcrypt('password'),
      'telephone' => '123456789',
      'est_actif' => true,
      'email_verifie' => $emailVerifie,
      'type_utilisateur' => 'CANDIDAT',
    ]);

    return Candidat::create([
      'utilisateur_id' => $utilisateur->id,
      'nom_cand' => 'Test',
      'prenom_cand' => 'User',
      'nationalite_cand' => 'Camerounaise',
      'age_cand' => 20,
      'date_naissance_cand' => now()->subYears(20),
      'lieu_naissance_cand' => 'Yaoundé',
      'sexe_cand' => 'M',
    ]);
  }

  private function createPaiement(Candidat $candidat, string $concoursId, StatutPaiement $statut = StatutPaiement::PENDING): Paiement
  {
    return Paiement::create([
      'candidat_id' => $candidat->utilisateur_id,
      'concours_id' => $concoursId,
      'reference' => 'REF_' . uniqid(),
      'montant' => 50000,
      'preuve_paiement' => 'path/to/receipt.pdf',
      'statut' => $statut,
    ]);
  }

  /** @test */
  public function it_can_check_if_candidat_can_receive_email()
  {
    // Arrange
    $candidatWithVerifiedEmail = $this->createCandidatWithUtilisateur(true);
    $candidatWithUnverifiedEmail = $this->createCandidatWithUtilisateur(false);

    // Act & Assert
    $this->assertTrue($this->notificationService->canSendEmail($candidatWithVerifiedEmail));
    $this->assertFalse($this->notificationService->canSendEmail($candidatWithUnverifiedEmail));
  }

  /** @test */
  public function it_creates_in_app_notification_for_payment_pending_review()
  {
    // Arrange
    $candidat = $this->createCandidatWithUtilisateur(true);
    $concours = Concours::first();
    if (!$concours) {
      $this->markTestSkipped('No concours available in database');
    }
    $paiement = $this->createPaiement($candidat, $concours->id, StatutPaiement::PENDING_MANUAL_REVIEW);

    // Act
    $this->notificationService->notifyPaymentPendingReview($candidat, $paiement);

    // Assert
    $this->assertDatabaseHas('notifications', [
      'utilisateur_id' => $candidat->utilisateur_id,
      'type_notification' => TypeNotification::PAIEMENT_RECU->value,
      'canal' => CanalNotification::APP->value,
      'priorite' => PrioriteNotification::NORMALE->value,
      'est_envoyee' => true,
    ]);

    $notification = Notification::where('utilisateur_id', $candidat->utilisateur_id)
      ->where('canal', CanalNotification::APP->value)
      ->first();

    $this->assertNotNull($notification);
    $this->assertStringContainsString($paiement->reference, $notification->message);
    $this->assertStringContainsString('en attente de validation', $notification->message);
    $this->assertEquals($paiement->id, $notification->metadata['paiement_id']);
  }

  /** @test */
  public function it_sends_email_notification_for_payment_pending_review_when_email_verified()
  {
    // Arrange
    $candidat = $this->createCandidatWithUtilisateur(true);
    $concours = Concours::first();
    if (!$concours) {
      $this->markTestSkipped('No concours available in database');
    }
    $paiement = $this->createPaiement($candidat, $concours->id, StatutPaiement::PENDING_MANUAL_REVIEW);

    // Act
    $this->notificationService->notifyPaymentPendingReview($candidat, $paiement);

    // Assert - Should have both APP and EMAIL notifications
    $notifications = Notification::where('utilisateur_id', $candidat->utilisateur_id)->get();
    $this->assertCount(2, $notifications);

    $emailNotification = $notifications->where('canal', CanalNotification::EMAIL->value)->first();
    $this->assertNotNull($emailNotification);
    $this->assertEquals(TypeNotification::PAIEMENT_RECU->value, $emailNotification->type_notification);
  }

  /** @test */
  public function it_does_not_send_email_notification_for_payment_pending_review_when_email_not_verified()
  {
    // Arrange
    $candidat = $this->createCandidatWithUtilisateur(false);
    $concours = Concours::first();
    if (!$concours) {
      $this->markTestSkipped('No concours available in database');
    }
    $paiement = $this->createPaiement($candidat, $concours->id, StatutPaiement::PENDING_MANUAL_REVIEW);

    // Act
    $this->notificationService->notifyPaymentPendingReview($candidat, $paiement);

    // Assert - Should only have APP notification
    $notifications = Notification::where('utilisateur_id', $candidat->utilisateur_id)->get();
    $this->assertCount(1, $notifications);

    $appNotification = $notifications->where('canal', CanalNotification::APP->value)->first();
    $this->assertNotNull($appNotification);

    $emailNotification = $notifications->where('canal', CanalNotification::EMAIL->value)->first();
    $this->assertNull($emailNotification);
  }

  /** @test */
  public function it_creates_in_app_notification_for_payment_verified()
  {
    // Arrange
    $candidat = $this->createCandidatWithUtilisateur(true);
    $concours = Concours::first();
    if (!$concours) {
      $this->markTestSkipped('No concours available in database');
    }
    $paiement = $this->createPaiement($candidat, $concours->id, StatutPaiement::VERIFIED);

    // Act
    $this->notificationService->notifyPaymentVerified($candidat, $paiement);

    // Assert
    $this->assertDatabaseHas('notifications', [
      'utilisateur_id' => $candidat->utilisateur_id,
      'type_notification' => TypeNotification::PAIEMENT_VALIDE->value,
      'canal' => CanalNotification::APP->value,
      'priorite' => PrioriteNotification::HAUTE->value,
      'est_envoyee' => true,
    ]);

    $notification = Notification::where('utilisateur_id', $candidat->utilisateur_id)
      ->where('canal', CanalNotification::APP->value)
      ->first();

    $this->assertNotNull($notification);
    $this->assertStringContainsString($paiement->reference, $notification->message);
    $this->assertStringContainsString('validé avec succès', $notification->message);
    $this->assertEquals($paiement->id, $notification->metadata['paiement_id']);
  }

  /** @test */
  public function it_sends_email_notification_for_payment_verified_when_email_verified()
  {
    // Arrange
    $candidat = $this->createCandidatWithUtilisateur(true);
    $concours = Concours::first();
    if (!$concours) {
      $this->markTestSkipped('No concours available in database');
    }
    $paiement = $this->createPaiement($candidat, $concours->id, StatutPaiement::VERIFIED);

    // Act
    $this->notificationService->notifyPaymentVerified($candidat, $paiement);

    // Assert - Should have both APP and EMAIL notifications
    $notifications = Notification::where('utilisateur_id', $candidat->utilisateur_id)->get();
    $this->assertCount(2, $notifications);

    $emailNotification = $notifications->where('canal', CanalNotification::EMAIL->value)->first();
    $this->assertNotNull($emailNotification);
    $this->assertEquals(TypeNotification::PAIEMENT_VALIDE->value, $emailNotification->type_notification);
    $this->assertEquals(PrioriteNotification::HAUTE->value, $emailNotification->priorite);
  }

  /** @test */
  public function it_does_not_send_email_notification_for_payment_verified_when_email_not_verified()
  {
    // Arrange
    $candidat = $this->createCandidatWithUtilisateur(false);
    $concours = Concours::first();
    if (!$concours) {
      $this->markTestSkipped('No concours available in database');
    }
    $paiement = $this->createPaiement($candidat, $concours->id, StatutPaiement::VERIFIED);

    // Act
    $this->notificationService->notifyPaymentVerified($candidat, $paiement);

    // Assert - Should only have APP notification
    $notifications = Notification::where('utilisateur_id', $candidat->utilisateur_id)->get();
    $this->assertCount(1, $notifications);

    $appNotification = $notifications->where('canal', CanalNotification::APP->value)->first();
    $this->assertNotNull($appNotification);

    $emailNotification = $notifications->where('canal', CanalNotification::EMAIL->value)->first();
    $this->assertNull($emailNotification);
  }

  /** @test */
  public function it_creates_in_app_notification_for_payment_rejected()
  {
    // Arrange
    $candidat = $this->createCandidatWithUtilisateur(true);
    $concours = Concours::first();
    if (!$concours) {
      $this->markTestSkipped('No concours available in database');
    }
    $paiement = $this->createPaiement($candidat, $concours->id, StatutPaiement::REJECTED);
    $motif = 'Montant incorrect';

    // Act
    $this->notificationService->notifyPaymentRejected($candidat, $paiement, $motif);

    // Assert
    $this->assertDatabaseHas('notifications', [
      'utilisateur_id' => $candidat->utilisateur_id,
      'type_notification' => TypeNotification::PAIEMENT_REJETE->value,
      'canal' => CanalNotification::APP->value,
      'priorite' => PrioriteNotification::URGENTE->value,
      'est_envoyee' => true,
    ]);

    $notification = Notification::where('utilisateur_id', $candidat->utilisateur_id)
      ->where('canal', CanalNotification::APP->value)
      ->first();

    $this->assertNotNull($notification);
    $this->assertStringContainsString($paiement->reference, $notification->message);
    $this->assertStringContainsString($motif, $notification->message);
    $this->assertStringContainsString('rejeté', $notification->message);
    $this->assertEquals($paiement->id, $notification->metadata['paiement_id']);
    $this->assertEquals($motif, $notification->metadata['motif_rejet']);
  }

  /** @test */
  public function it_sends_email_notification_for_payment_rejected_when_email_verified()
  {
    // Arrange
    $candidat = $this->createCandidatWithUtilisateur(true);
    $concours = Concours::first();
    if (!$concours) {
      $this->markTestSkipped('No concours available in database');
    }
    $paiement = $this->createPaiement($candidat, $concours->id, StatutPaiement::REJECTED);
    $motif = 'Référence invalide';

    // Act
    $this->notificationService->notifyPaymentRejected($candidat, $paiement, $motif);

    // Assert - Should have both APP and EMAIL notifications
    $notifications = Notification::where('utilisateur_id', $candidat->utilisateur_id)->get();
    $this->assertCount(2, $notifications);

    $emailNotification = $notifications->where('canal', CanalNotification::EMAIL->value)->first();
    $this->assertNotNull($emailNotification);
    $this->assertEquals(TypeNotification::PAIEMENT_REJETE->value, $emailNotification->type_notification);
    $this->assertEquals(PrioriteNotification::URGENTE->value, $emailNotification->priorite);
    $this->assertStringContainsString($motif, $emailNotification->message);
  }

  /** @test */
  public function it_does_not_send_email_notification_for_payment_rejected_when_email_not_verified()
  {
    // Arrange
    $candidat = $this->createCandidatWithUtilisateur(false);
    $concours = Concours::first();
    if (!$concours) {
      $this->markTestSkipped('No concours available in database');
    }
    $paiement = $this->createPaiement($candidat, $concours->id, StatutPaiement::REJECTED);
    $motif = 'Document illisible';

    // Act
    $this->notificationService->notifyPaymentRejected($candidat, $paiement, $motif);

    // Assert - Should only have APP notification
    $notifications = Notification::where('utilisateur_id', $candidat->utilisateur_id)->get();
    $this->assertCount(1, $notifications);

    $appNotification = $notifications->where('canal', CanalNotification::APP->value)->first();
    $this->assertNotNull($appNotification);

    $emailNotification = $notifications->where('canal', CanalNotification::EMAIL->value)->first();
    $this->assertNull($emailNotification);
  }

  /** @test */
  public function it_handles_candidat_without_utilisateur_gracefully()
  {
    // Arrange
    $candidat = new Candidat([
      'utilisateur_id' => 'non-existent-id',
      'nom_cand' => 'Test',
      'prenom_cand' => 'User',
    ]);
    $candidat->save();

    $concours = Concours::first();
    if (!$concours) {
      $this->markTestSkipped('No concours available in database');
    }
    $paiement = $this->createPaiement($candidat, $concours->id);

    // Act - Should not throw exception
    $this->notificationService->notifyPaymentPendingReview($candidat, $paiement);
    $this->notificationService->notifyPaymentVerified($candidat, $paiement);
    $this->notificationService->notifyPaymentRejected($candidat, $paiement, 'Test');

    // Assert - No notifications should be created
    $this->assertDatabaseMissing('notifications', [
      'utilisateur_id' => $candidat->utilisateur_id,
    ]);
  }
}
