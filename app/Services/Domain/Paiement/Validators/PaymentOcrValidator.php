<?php

namespace App\Services\Domain\Paiement\Validators;

use App\Enums\StatutPaiement;
use App\Models\ConcoursPaiement;
use App\Models\Paiement;
use App\Services\Domain\Paiement\Processors\AccountNormalizer;

class PaymentOcrValidator
{
  /**
   * Valide automatiquement un paiement basé sur les données OCR.
   *
   * @param Paiement $paiement
   * @param ConcoursPaiement $config
   * @return bool True si validé automatiquement
   */
  public function autoValidate(Paiement $paiement, ConcoursPaiement $config): bool
  {
    if ($paiement->statut === StatutPaiement::VERIFIED) {
      return true;
    }

    $validations = [
      'reference' => $this->validateReference($paiement),
      'montant' => $this->validateAmount($paiement, $config),
      'date' => $this->validateDate($paiement, $config),
      'banque' => $this->validateBank($paiement, $config),
      'numero_compte' => $this->validateAccountNumber($paiement, $config),
      'ocr_confiance' => $this->validateOcrConfidence($paiement, $config),
    ];

    // Validation logging removed after debugging

    $allValid = !in_array(false, $validations, true);

    if ($allValid) {
      $this->markAsVerified($paiement);
      return true;
    }

    // Vérifier si c'est une erreur mineure OCR
    if ($this->isMinorOcrError($paiement, $config)) {
      $this->markForManualReview($paiement, $validations);
      return true; // Considéré comme traité
    }

    return false;
  }

  /**
   * Valide la référence du paiement.
   */
  private function validateReference(Paiement $paiement): bool
  {
    return !empty($paiement->reference);
  }

  /**
   * Valide le montant du paiement.
   */
  private function validateAmount(Paiement $paiement, ConcoursPaiement $config): bool
  {
    $tolerance = 0.05; // ±5%
    $expectedAmount = $config->montant;
    $min = $expectedAmount * (1 - $tolerance);
    $max = $expectedAmount * (1 + $tolerance);
    $amount = $paiement->montant_ocr ?? $paiement->montant;

    return $amount >= $min && $amount <= $max;
  }

  /**
   * Valide la date du paiement.
   */
  private function validateDate(Paiement $paiement, ConcoursPaiement $config): bool
  {
    $date = $paiement->date_ocr ?? $paiement->created_at;
    return $date <= $config->date_limite;
  }

  /**
   * Valide la banque du paiement.
   */
  private function validateBank(Paiement $paiement, ConcoursPaiement $config): bool
  {
    $banqueOcr = $paiement->banque_ocr;

    if (!$banqueOcr) {
      return true; // Pas de validation si pas détecté
    }


    if ($config->banques_acceptees) {
      return in_array($banqueOcr, $config->banques_acceptees);
    }


    if ($config->banque_nom) {
      // Comparaison insensible à la casse et aux espaces
      $banqueOcrNormalized = strtolower(trim($banqueOcr));
      $banqueConfigNormalized = strtolower(trim($config->banque_nom));

      // Vérifier si la banque détectée est contenue dans le nom configuré ou vice versa
      return str_contains($banqueConfigNormalized, $banqueOcrNormalized) ||
        str_contains($banqueOcrNormalized, $banqueConfigNormalized);
    }

    return true; // Pas de contrainte spécifique
  }

  /**
   * Valide le numéro de compte du paiement.
   */
  private function validateAccountNumber(Paiement $paiement, ConcoursPaiement $config): bool
  {
    $numeroCompteOcr = $paiement->numero_compte_ocr;
    $numeroCompteConfig = $config->numero_compte;

    if (!$numeroCompteConfig) {
      return true;
    }

    if (!$numeroCompteOcr) {
      return false;
    }

    $normalizer = new AccountNormalizer();
    $ocrClean = $normalizer->normalize($numeroCompteOcr);
    $configClean = $normalizer->normalize($numeroCompteConfig);

    if (strcasecmp($ocrClean, $configClean) === 0) {
      return true;
    }

    return $this->isMinorOcrError($numeroCompteOcr, $numeroCompteConfig);
  }

  /**
   * Valide la confiance OCR.
   */
  private function validateOcrConfidence(Paiement $paiement, ConcoursPaiement $config): bool
  {
    $confianceOcr = $paiement->ocr_confidence;

    // Debug temporaire
    error_log("Validating OCR confidence - paiement ID: {$paiement->id}, confiance: {$confianceOcr}");

    if (!$confianceOcr) {
      error_log("OCR confidence is null or zero");
      return false;
    }


    $seuilMinimum = $config->minimum_confiance_ocr ?? 85.0;


    if (is_string($seuilMinimum)) {
      $seuilMinimum = (float) $seuilMinimum;
    }


    // La confiance détectée est déjà en décimal (0.999... = 99.99%)
    // Le seuil de la base est en pourcentage (85.00), convertir en décimal (0.85)
    if ($seuilMinimum > 1) {
      $seuilMinimum = $seuilMinimum / 100;
    }

    // Debug temporaire
    $result = $confianceOcr >= $seuilMinimum;
    error_log("OCR Confidence validation: {$confianceOcr} >= {$seuilMinimum} (seuil converti de pourcentage à décimal) = " . ($result ? 'TRUE' : 'FALSE'));

    return $result;
  }

  /**
   * Marque le paiement comme vérifié.
   */
  private function markAsVerified(Paiement $paiement): void
  {
    $paiement->update([
      'statut' => StatutPaiement::VERIFIED,
      'validated_at' => now(),
      'validated_by' => null,
    ]);
  }

  /**
   * Marque le paiement pour validation manuelle.
   */
  private function markForManualReview(Paiement $paiement, array $validations): void
  {
    $issues = [];
    foreach ($validations as $field => $valid) {
      if (!$valid) {
        $issues[] = $field;
      }
    }

    $paiement->update([
      'statut' => StatutPaiement::PENDING_MANUAL_REVIEW,
      'validation_notes' => 'Erreurs OCR mineures détectées: ' . implode(', ', $issues),
    ]);
  }

  /**
   * Détermine si l'erreur OCR peut être considérée comme mineure.
   */
  private function isMinorOcrError(string $detected, string $required): bool
  {
    $normalizer = new AccountNormalizer();
    return $normalizer->isMinorOcrError($detected, $required);
  }
}
