<?php

namespace App\Services\Domain\Registration;

use App\Enums\StatutCandidature;
use App\Enums\StatutPaiement;
use App\Models\Concours;
use App\Services\Domain\Candidat\CandidatService;
use App\Services\Domain\Candidature\CandidatureService;
use App\Services\Domain\Candidature\Checkers\CapacityChecker;
use App\Services\Domain\Candidature\Checkers\EligibilityChecker;
use App\Services\Domain\Notification\NotificationService;
use App\Services\Domain\Paiement\PaiementService;
use App\Services\Domain\User\UserService;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use App\Services\Infrastructure\OCR\PaymentOcrService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class RegistrationService
{
  public function __construct(
    private readonly EligibilityChecker $eligibilityChecker,
    private readonly CapacityChecker $capacityChecker,
    private readonly PaiementAutoValidatorService $paiementValidator,
    private readonly PaymentOcrService $ocrService,
    private readonly UserService $userService,
    private readonly CandidatService $candidatService,
    private readonly CandidatureService $candidatureService,
    private readonly PaiementService $paiementService,
    private readonly NotificationService $notificationService,
    private readonly \App\Services\Domain\Notification\Generators\AlertGeneratorService $alertGenerator,
    private readonly ActivityLoggerService $logger
  ) {}

  /**
   * Étape 1: Vérifier l'éligibilité
   */
  public function checkEligibility(Concours $concours, string $sessionId, array $data): array
  {
    // Récupérer la spec du concours via la relation
    $spec = $concours->specConcours;

    if (!$spec) {
      throw new \DomainException('Spécifications du concours non trouvées');
    }

    // Vérifier l'éligibilité académique (âge, série bac, nationalité)
    $eligibilityResult = $this->eligibilityChecker->checkPreRegistrationEligibility($data, $spec);

    // Vérifier la capacité d'accueil
    $capacityReport = $this->capacityChecker->getCapacityReport($concours, $sessionId, $data['filiere_id'] ?? null);

    if (!$capacityReport['can_accept']) {
      $eligibilityResult['eligible'] = false;
      $eligibilityResult['reasons'][] = 'Le concours a atteint sa capacité maximale pour cette session';
    }

    $this->logger->logActivity('check_eligibility', 'registration', null, [
      'concours_id' => $concours->id,
      'session_id' => $sessionId,
      'eligible' => $eligibilityResult['eligible']
    ]);

    return [
      'eligible' => $eligibilityResult['eligible'],
      'raisons_ineligibilite' => $eligibilityResult['reasons'],
      'capacite' => $capacityReport
    ];
  }

  /**
   * Étape 2: Upload et validation du paiement avec OCR
   */
  public function uploadPayment(Concours $concours, array $data): array
  {
    // 1. Tenter l'extraction OCR si le fichier est déjà uploadé
    $ocrData = null;
    $ocrSuccess = false;

    if (isset($data['preuve_paiement_path'])) {
      $fullPath = Storage::disk('public')->path($data['preuve_paiement_path']);
      $ocrResult = $this->ocrService->extract($fullPath);

      if ($ocrResult['success'] && $ocrResult['data']) {
        $ocrData = $ocrResult['data'];
        $ocrSuccess = $this->ocrService->isConfidenceAcceptable($ocrData['confidence_score']);

        // Si OCR réussi avec confiance suffisante, utiliser les données extraites
        if ($ocrSuccess && $this->ocrService->hasMinimumData($ocrData)) {
          // Fusionner les données OCR avec les données fournies (priorité aux données fournies)
          $data['reference_paiement'] = $data['reference_paiement'] ?? $ocrData['reference'];
          $data['montant'] = $data['montant'] ?? $ocrData['montant'];
          $data['date_paiement'] = $data['date_paiement'] ?? $ocrData['date_paiement'];
          $data['banque'] = $data['banque'] ?? $ocrData['banque'];
          $data['numero_compte'] = $data['numero_compte'] ?? $ocrData['numero_compte'];
        }
      }
    }

    // 2. Valider automatiquement le paiement (sans créer en DB)
    $validation = $this->paiementValidator->validate($concours, $data);

    // 3. Générer upload_id pour validation manuelle
    $uploadId = \Illuminate\Support\Str::uuid()->toString();

    // 4. Stocker TOUTES les données en cache (pas de création en DB pour l'instant)
    Cache::put("registration_upload_{$uploadId}", [
      'concours_id' => $concours->id,
      'session_id' => $data['session_id'],
      'eligibility_data' => $data['eligibility_data'],
      'preuve_paiement_path' => $data['preuve_paiement_path'],
      'payment_data' => [
        'reference' => $data['reference_paiement'] ?? null,
        'montant' => $data['montant'] ?? null,
        'date_paiement' => $data['date_paiement'] ?? null,
        'banque' => $data['banque'] ?? null,
        'numero_compte' => $data['numero_compte'] ?? null,
      ],
      'ocr_data' => $ocrData,
      'ocr_success' => $ocrSuccess,
      'validation_statut' => $validation['statut']->value,
      'validation_auto' => $validation['auto_valide'],
      'validation_raisons' => $validation['raisons_attente'] ?? [],
    ], now()->addHours(2)); // 2 heures au lieu de 30 minutes

    $this->logger->logActivity('upload_payment', 'registration', null, [
      'concours_id' => $concours->id,
      'upload_id' => $uploadId,
      'ocr_success' => $ocrSuccess
    ]);

    return [
      'upload_id' => $uploadId,
      'statut' => $validation['statut'],
      'auto_valide' => $validation['auto_valide'],
      'ocr_success' => $ocrSuccess,
      'ocr_data' => $ocrData,
      'validation_raisons' => $validation['raisons_attente'] ?? [],
      'message' => $validation['auto_valide']
        ? 'Données extraites et validées avec succès.'
        : ($ocrSuccess ? 'Données extraites mais des incohérences ont été détectées.' : 'Désolé, nous n\'avons pas pu extraire toutes les données. Veuillez les saisir manuellement.')
    ];
  }

  /**
   * Étape 2b: Validation manuelle du paiement (si OCR échoue)
   */
  public function validatePayment(Concours $concours, array $data): array
  {
    // 1. Récupérer les données temporaires via upload_id
    $tempData = Cache::get("registration_upload_{$data['upload_id']}");

    if (!$tempData) {
      $this->logger->logError(new \Exception('Upload session expired'), 'registration_upload_expired', [
        'upload_id' => $data['upload_id'],
        'concours_id' => $concours->id,
      ]);

      throw new \DomainException(
        'Votre session d\'upload a expiré (durée maximale: 2 heures). ' .
          'Veuillez recommencer l\'upload de votre preuve de paiement.'
      );
    }

    // 2. Normaliser les noms de champs (accepter 'reference' ou 'reference_paiement')
    // Utiliser les données fournies manuellement, ou celles stockées en cache (OCR)
    $reference = $data['reference'] ?? $data['reference_paiement'] ?? $tempData['payment_data']['reference'] ?? null;
    $montant = $data['montant'] ?? $tempData['payment_data']['montant'] ?? null;
    $datePaiement = $data['date_paiement'] ?? $tempData['payment_data']['date_paiement'] ?? null;

    // 3. Valider les données
    $validation = $this->paiementValidator->validate($concours, [
      'reference_paiement' => $reference,
      'montant' => $montant,
      'date_paiement' => $datePaiement,
    ]);

    // 4. Générer token temporaire pour l'étape finale
    $token = $this->generateTemporaryToken();

    // 5. Stocker TOUTES les données en cache (pas de création en DB)
    Cache::put("registration_{$token}", [
      'concours_id' => $concours->id,
      'session_id' => $tempData['session_id'],
      'eligibility_data' => $tempData['eligibility_data'],
      'preuve_paiement_path' => $tempData['preuve_paiement_path'],
      'payment_data' => [
        'reference' => $reference,
        'montant' => $montant,
        'date_paiement' => $datePaiement,
        'statut' => $validation['statut']->value,
        'auto_valide' => $validation['auto_valide'],
        'validation_notes' => $validation['auto_valide']
          ? 'Validation manuelle réussie'
          : json_encode($validation['raisons_attente'])
      ],
    ], now()->addHours(2)); // 2 heures au lieu de 30 minutes

    // 6. Supprimer l'ancien cache d'upload
    Cache::forget("registration_upload_{$data['upload_id']}");

    $this->logger->logActivity('validate_payment_manual', 'registration', null, [
      'concours_id' => $concours->id,
      'statut' => $validation['statut']->value,
      'auto_valide' => $validation['auto_valide'],
    ]);

    return [
      'statut' => $validation['statut'],
      'token_temporaire' => $token,
      'validation_raisons' => $validation['raisons_attente'] ?? [],
      'message' => $validation['auto_valide']
        ? 'Paiement validé avec succès'
        : 'Paiement en attente de validation administrative'
    ];
  }

  /**
   * Étape 3: Compléter l'inscription (créer compte + candidature + paiement)
   */
  public function completeRegistration(string $token, array $data): array
  {
    return runTransaction(function () use ($token, $data) {
      $tempData = Cache::get("registration_{$token}");

      if (!$tempData) {
        throw new \DomainException('Token d\'inscription invalide ou expiré');
      }

      // Vérifier que les données de paiement sont présentes
      if (!isset($tempData['payment_data'])) {
        throw new \DomainException('Données de paiement manquantes');
      }

      $paymentData = $tempData['payment_data'];
      $eligibilityData = $tempData['eligibility_data'];


      $concours = Concours::findOrFail($eligibilityData['concours_id']);
      app(\App\Services\Domain\Concours\Checkers\ConcoursReadinessChecker::class)->ensureReady($concours);

      // 1. Créer l'utilisateur (l'observer enverra automatiquement l'email de vérification)
      $user = $this->userService->createCandidatUserForRegistration(
        $data['email'],
        $data['password'],
        $data['telephone']
      );

      // 2. Créer le candidat
      $eligibilityData = $tempData['eligibility_data'];
      $candidat = $this->candidatService->createCandidatForRegistration(
        $user->id,
        [
          'date_naissance' => $eligibilityData['date_naissance'],
          'serie_bac' => $eligibilityData['serie_bac'] ?? null,
          'nationalite' => $eligibilityData['nationalite'] ?? 'Camerounaise',
          'sexe' => $eligibilityData['sexe'] ?? null,
        ],
        $eligibilityData['filiere_id'] ?? null
      );

      $candidat->update([
        'nom_cand' => $eligibilityData['nom'] ?? '',
        'prenom_cand' => $eligibilityData['prenom'] ?? '',
        'annee_obtention_bac' => $eligibilityData['annee_bac'] ?? null,
      ]);

      // 3. MAINTENANT créer le paiement en DB
      $paiement = $this->paiementService->createManualPayment([
        'concours_id' => $tempData['concours_id'],
        'candidat_id' => $candidat->utilisateur_id,
        'reference' => $paymentData['reference'],
        'montant' => $paymentData['montant'],
        'date_ocr' => $paymentData['date_paiement'],
        'preuve_paiement' => $tempData['preuve_paiement_path'],
        'statut' => StatutPaiement::from($paymentData['statut']),
        'validation_notes' => $paymentData['validation_notes'] ?? null
      ]);

      // 4. Créer la candidature
      $candidature = $this->candidatureService->create([
        'candidat_id' => $candidat->utilisateur_id,
        'concours_id' => $tempData['concours_id'],
        'session_id' => $tempData['session_id'],
        'statut_candidature' => StatutCandidature::SOUMISE,
        'code_cand_temp' => $this->candidatureService->generateTempCode($candidat->utilisateur_id),
      ]);

      $candidature->update([
        'date_inscription' => now(),
        'date_candidature' => now(),
        'documents_complets' => false,
        'paiement_valide' => $paiement->statut === StatutPaiement::VERIFIED
      ]);

      $this->alertGenerator->generateCandidateAlerts($candidat);

      // 5. Envoyer l'email de bienvenue
      $this->notificationService->sendWelcomeEmail($user, $candidat, $concours);

      // 6. Nettoyer le cache
      Cache::forget("registration_{$token}");

      $this->logger->logActivity('complete_registration', 'candidature', $candidature->id, [
        'candidat_id' => $candidat->utilisateur_id,
        'concours_id' => $tempData['concours_id'],
        'session_id' => $tempData['session_id'],
        'paiement_id' => $paiement->id
      ]);

      $authToken = $user->createToken('auth')->plainTextToken;

      return [
        'user' => $user->only(['id', 'email', 'telephone']),
        'candidat' => $candidat->only(['utilisateur_id', 'nom_cand', 'prenom_cand']),
        'candidature' => $candidature->only(['id', 'code_cand_temp', 'statut_candidature']),
        'auth_token' => $authToken,
        'message' => 'Inscription réussie ! Veuillez compléter votre dossier.'
      ];
    }, "RegistrationService::completeRegistration");
  }

  /**
   * Générer un token temporaire unique
   */
  private function generateTemporaryToken(): string
  {
    return 'reg_' . bin2hex(random_bytes(32));
  }
}
