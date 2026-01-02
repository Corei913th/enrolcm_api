<?php

namespace App\Services\Payment\Processors;

class AccountNormalizer
{
  /**
   * Normalise un numéro de compte pour la comparaison.
   */
  public function normalize(?string $accountNumber): string
  {
    if (!$accountNumber) {
      return '';
    }

    // Supprimer tous les espaces, tirets, points
    $normalized = preg_replace('/[\s\-\.]/', '', trim(strtoupper($accountNumber)));

    return $normalized;
  }

  /**
   * Détermine si l'erreur OCR sur le numéro de compte peut être considérée comme mineure.
   */
  public function isMinorOcrError(string $detected, string $required): bool
  {
    $detected = $this->normalize($detected);
    $required = $this->normalize($required);

    // Même préfixe bancaire ?
    $detectedPrefix = $this->extractBankPrefix($detected);
    $requiredPrefix = $this->extractBankPrefix($required);

    if ($detectedPrefix !== $requiredPrefix) {
      return false; // Erreur majeure - mauvaise banque
    }

    // Calculer le nombre d'erreurs de chiffres
    $detectedNumbers = substr($detected, strlen($detectedPrefix));
    $requiredNumbers = substr($required, strlen($requiredPrefix));

    $errors = 0;
    $totalDigits = strlen($requiredNumbers);

    for ($i = 0; $i < min(strlen($detectedNumbers), $totalDigits); $i++) {
      if (isset($detectedNumbers[$i]) && $detectedNumbers[$i] !== $requiredNumbers[$i]) {
        $errors++;
      }
    }

    // Erreur mineure si ≤ 2 erreurs sur les chiffres (ex: 8→0, 1→7)
    return $errors <= 2;
  }

  /**
   * Extrait le préfixe bancaire d'un numéro de compte.
   */
  private function extractBankPrefix(string $accountNumber): string
  {
    // Patterns pour identifier les préfixes bancaires camerounais
    $patterns = [
      '/^(ECO)/i',      // Ecobank
      '/^(BICEC)/i',    // BICEC
      '/^(UBA)/i',      // UBA
      '/^(SGBC)/i',     // SGBC
      '/^(AFRILAND)/i', // Afriland
      '/^(SCB)/i',      // SCB
    ];

    foreach ($patterns as $pattern) {
      if (preg_match($pattern, $accountNumber, $matches)) {
        return strtoupper($matches[1]);
      }
    }

    // Si aucun préfixe connu, retourner les 3-4 premiers caractères
    return substr($accountNumber, 0, 4);
  }
}
