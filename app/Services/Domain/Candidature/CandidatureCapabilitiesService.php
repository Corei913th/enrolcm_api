<?php

namespace App\Services\Domain\Candidature;

use App\Models\Candidature;
use App\Services\Domain\Candidature\Checkers\EligibilityChecker;

class CandidatureCapabilitiesService
{
  public function __construct(
    private readonly EligibilityChecker $eligibilityChecker,
    private readonly DocumentService $documentService
  ) {}

  /**
   * Get all capabilities for a candidature
   * Returns only business capabilities, no UI concerns
   */
  public function getCapabilities(Candidature $candidature): array
  {
    $capabilities = [
      'can_download_convocation' => $this->canDownloadConvocation($candidature),
      'can_download_inscription_form' => $this->canDownloadInscriptionForm($candidature),
      'can_check_results' => $this->canCheckResults($candidature),
      'can_modify' => $this->canModify($candidature),
      'can_view_countdown' => $this->canViewCountdown($candidature),
      'can_view_exam_countdown' => $this->canViewExamCountdown($candidature),
      'documents_status' => $this->getDocumentsCapabilities($candidature),
      'payment_status' => $this->getPaymentCapabilities($candidature)
    ];

    // Add countdown data if countdown is visible
    if ($capabilities['can_view_countdown']) {
      $capabilities['countdown_data'] = $this->getCountdownData($candidature);
    }

    if ($capabilities['can_view_exam_countdown']) {
      $capabilities['exam_countdown_data'] = $this->getExamCountdownData($candidature);
    }

    return $capabilities;
  }

  /**
   * Check if convocation can be downloaded
   */
  public function canDownloadConvocation(Candidature $candidature): bool
  {
    $eligibility = $this->eligibilityChecker->canGenerateConvocation($candidature);
    return $eligibility['eligible'];
  }

  /**
   * Check if inscription form can be downloaded
   */
  public function canDownloadInscriptionForm(Candidature $candidature): bool
  {
    return $candidature->peutTelechargerFiche();
  }

  /**
   * Check if results can be checked
   */
  public function canCheckResults(Candidature $candidature): bool
  {
    if (!$candidature->estValidee()) {
      return false;
    }

    $resultat = $candidature->resultatFinal;
    return $resultat && $resultat->date_publication && $resultat->date_publication->isPast();
  }

  /**
   * Check if candidature can be modified
   */
  public function canModify(Candidature $candidature): bool
  {
    // 1. Check if status allows modification
    if (!$candidature->peutEtreModifiee()) {
      return false;
    }

    // 2. Check if concours deadline is passed
    if ($candidature->concours->date_limite_depot && $candidature->concours->date_limite_depot->isPast()) {
      return false;
    }

    return true;
  }

  /**
   * Check if countdown should be visible
   */
  public function canViewCountdown(Candidature $candidature): bool
  {
    if (!$candidature->estSoumise() && !$candidature->estValidee()) {
      return false;
    }

    $resultat = $candidature->resultatFinal;
    return $resultat && $resultat->date_publication && $resultat->date_publication->isFuture();
  }

  /**
   * Check if exam countdown should be visible
   */
  public function canViewExamCountdown(Candidature $candidature): bool
  {
    if (!$candidature->estSoumise() && !$candidature->estValidee()) {
      return false;
    }

    $dateExamen = $candidature->concours->date_examen;
    return $dateExamen && $dateExamen->isFuture();
  }

  /**
   * Get documents-related capabilities
   */
  private function getDocumentsCapabilities(Candidature $candidature): array
  {
    $documentsComplete = $this->documentService->areDocumentsComplete($candidature);
    $documentsStatus = $this->documentService->getRequiredDocumentsStatusForCandidature($candidature);

    $requiredDocs = array_filter($documentsStatus, fn($doc) => $doc['est_obligatoire']);

    return [
      'are_complete' => $documentsComplete || $candidature->estValidee(),
      'total_required' => count($requiredDocs),
      'submitted_count' => count(array_filter($requiredDocs, fn($doc) => $doc['statut'] !== \App\Enums\StatutVerificationDocument::NON_SOUMIS)),
      'validated_count' => count(array_filter($requiredDocs, fn($doc) => $doc['statut'] === \App\Enums\StatutVerificationDocument::VALIDE)) || ($candidature->estValidee() ? count($requiredDocs) : 0)
    ];
  }

  /**
   * Get payment-related capabilities
   */
  private function getPaymentCapabilities(Candidature $candidature): array
  {
    $paymentCheck = $this->eligibilityChecker->checkPaymentStatus($candidature);

    return [
      'is_valid' => $paymentCheck['valid'] || $candidature->estValidee(),
      'status' => $candidature->estValidee() ? \App\Enums\StatutPaiement::VERIFIED->value : ($paymentCheck['status'] ?? 'UNKNOWN'),
      'has_payment' => $candidature->paiement !== null || $candidature->estValidee()
    ];
  }

  /**
   * Get countdown data for results publication
   */
  private function getCountdownData(Candidature $candidature): ?array
  {
    if (!$candidature->estSoumise() && !$candidature->estValidee()) {
      return null;
    }

    $resultat = $candidature->resultatFinal;
    if (!$resultat || !$resultat->date_publication) {
      return null;
    }

    $publicationDate = $resultat->date_publication;
    $now = now();

    if ($publicationDate->isPast()) {
      return [
        'publication_date' => $publicationDate->toDateTimeString(),
        'seconds_remaining' => 0,
        'results_published' => true
      ];
    }

    return [
      'publication_date' => $publicationDate->toDateTimeString(),
      'seconds_remaining' => $publicationDate->diffInSeconds($now),
      'results_published' => false
    ];
  }

  /**
   * Get countdown data for exam date
   */
  private function getExamCountdownData(Candidature $candidature): ?array
  {
    $dateExamen = $candidature->concours->date_examen;
    if (!$dateExamen) {
      return null;
    }

    $now = now();

    if ($dateExamen->isPast()) {
      return [
        'exam_date' => $dateExamen->toDateTimeString(),
        'seconds_remaining' => 0,
        'is_started' => true
      ];
    }

    return [
      'exam_date' => $dateExamen->toDateTimeString(),
      'seconds_remaining' => $dateExamen->diffInSeconds($now),
      'is_started' => false
    ];
  }
}
