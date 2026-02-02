<?php

namespace App\Observers;

use App\Models\Utilisateur;
use App\Services\Domain\Candidature\Validators\CandidatureValidationService;
use App\Services\Domain\Notification\Generators\AlertGeneratorService;
use Illuminate\Support\Facades\Cache;
use App\Models\Notification;
use App\Enums\CanalNotification;
use App\Enums\TypeNotification;
use App\Services\Domain\Notification\NotificationService;
use Illuminate\Support\Facades\Log;
use App\Enums\TypeUtilisateur;


class UtilisateurObserver
{
  public function __construct(
    private readonly NotificationService $notificationService
  ) {}

  /**
   * Envoyer les emails de bienvenue et de vérification lors de la création d'un candidat.
   */
  public function created(Utilisateur $utilisateur): void
  {
    if ($utilisateur->type_utilisateur !== TypeUtilisateur::CANDIDAT) {
      return;
    }

    try {
      // NOTE: L'email de bienvenue est maintenant envoyé manuellement par le RegistrationService
      // pour éviter la race condition (Candidat créé après Utilisateur).
      /*
      $candidat = $utilisateur->candidat;
      if ($candidat) {
        Mail::to($utilisateur->email)->send(new \App\Mail\WelcomeMail($utilisateur, $candidat));
      }
      */
    } catch (\Exception $e) {
      Log::error('Erreur lors de l\'envoi de l\'email de bienvenue (observer)', [
        'utilisateur_id' => $utilisateur->id,
        'error' => $e->getMessage(),
      ]);
    }

    if ($utilisateur->email_verifie) {
      return;
    }

    // Éviter les doublons
    $alreadySent = Notification::where('utilisateur_id', $utilisateur->id)
      ->where('type_notification', TypeNotification::INFORMATION_GENERALE->value)
      ->where('canal', CanalNotification::APP->value)
      ->where('titre', 'Email de vérification envoyé')
      ->exists();

    if ($alreadySent) {
      return;
    }

    try {
      $this->notificationService->sendEmailVerification($utilisateur);
    } catch (\Exception $e) {
      Log::error('Erreur lors de l\'envoi de l\'email de vérification (observer)', [
        'utilisateur_id' => $utilisateur->id,
        'error' => $e->getMessage(),
      ]);
    }
  }

  /**
   * Révoquer tous les tokens lors de la désactivation
   * Invalider le cache lors de la vérification d'email
   */
  public function updating(Utilisateur $utilisateur): void
  {
    // Gérer la désactivation du compte
    if ($utilisateur->isDirty('est_actif') && !$utilisateur->est_actif) {
      $utilisateur->tokens()->delete();

      Log::info('Tokens révoqués suite à désactivation utilisateur', [
        'utilisateur_id' => $utilisateur->id,
        'email' => $utilisateur->email
      ]);
    }
  }

  /**
   * Révoquer tous les tokens avant suppression
   */
  public function deleting(Utilisateur $utilisateur): void
  {
    $utilisateur->tokens()->delete();

    Log::info('Tokens révoqués suite à suppression utilisateur', [
      'utilisateur_id' => $utilisateur->id,
      'email' => $utilisateur->email
    ]);
  }
}
