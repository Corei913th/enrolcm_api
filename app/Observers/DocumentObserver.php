<?php

namespace App\Observers;

use App\Enums\StatutVerificationDocument;
use App\Models\Document;
use App\Services\Domain\Notification\NotificationService;

class DocumentObserver
{
  public function __construct(
    private readonly NotificationService $notificationService
  ) {}

  /**
   * Handle the Document "updated" event.
   * Optimisé pour auto-validation : notification groupée pour documents validés
   */
  public function updated(Document $document): void
  {
    if (!$document->isDirty('statut_verification')) {
      return;
    }

    $nouveauStatut = $document->statut_verification;
    $candidature = $document->candidature;

    // Si pas de candidature ou pas de candidat, on ne peut pas notifier
    if (!$candidature || !$candidature->candidat) {
      return;
    }

    $candidat = $candidature->candidat;

    // Gérer les notifications selon le statut
    match ($nouveauStatut) {
      // Rejet : toujours notifier immédiatement (action requise)
      StatutVerificationDocument::REJETE =>
      $this->notificationService->notifyDocumentRejected(
        $candidat,
        $document,
        $document->commentaire_verification ?? 'Document rejeté'
      ),

      // Validation : vérifier si tous les documents sont validés
      StatutVerificationDocument::VALIDE =>
      $this->handleDocumentValidation($document, $candidature, $candidat),

      default => null, // Pas de notification pour EN_ATTENTE
    };
  }

  /**
   * Gérer la validation de document avec notification groupée
   * Notifie seulement quand TOUS les documents obligatoires sont validés
   */
  private function handleDocumentValidation(Document $document, $candidature, $candidat): void
  {
    if (!$candidature->relationLoaded('concours')) {
      $candidature->load('concours');
    }
    $documentsRequisCount = $candidature->concours->documentsRequis()
      ->where('est_obligatoire', true)
      ->count();

    // Compter les documents obligatoires validés
    $documentsValidesCount = $candidature->documents()
      ->where('statut_verification', StatutVerificationDocument::VALIDE)
      ->whereHas('documentRequis', fn($q) => $q->where('est_obligatoire', true))
      ->count();

    // Notification groupée seulement si TOUS les documents obligatoires sont validés
    if ($documentsRequisCount > 0 && $documentsRequisCount === $documentsValidesCount) {
      $this->notificationService->notifyDocumentVerified($candidat, $document);
    }
  }
}
