<?php

namespace App\Services\Payment\Processors;

use App\DTOs\Payment\PaymentReceiptDTO;
use App\Enums\StatutPaiement;
use App\Models\ConcoursPaiement;
use App\Models\Paiement;
use Exception;

class OcrDataProcessor
{
  /**
   * Traite les données OCR et crée un paiement si possible.
   *
   * @param string $concoursId
   * @param string $filePath
   * @param PaymentReceiptDTO $ocrData
   * @param ConcoursPaiement $config
   * @return array [Paiement|null, errors[]]
   */
  public function processOcrData(string $concoursId, string $filePath, PaymentReceiptDTO $ocrData, ConcoursPaiement $config): array
  {
    $errors = [];
    $warnings = [];

    // Validation des données essentielles
    if (!$ocrData->banque) {
      $errors[] = 'banque';
    }

    if (!$ocrData->montant) {
      $errors[] = 'montant';
    }

    if (!empty($errors)) {
      return [null, $errors, $warnings];
    }

    // Vérifications avec warnings
    if (!$ocrData->numero_recu) {
      $warnings[] = 'numéro de reçu';
    }

    if (!$ocrData->numero_compte) {
      $warnings[] = 'numéro de compte';
    }

    // Générer référence
    $reference = $ocrData->numero_recu ?: ('PARTIAL_' . time());

    // Vérifier unicité
    $statut = StatutPaiement::PENDING;
    $validationNotes = null;

    if ($ocrData->numero_recu) {
      $existant = Paiement::where('reference', $ocrData->numero_recu)
        ->where('concours_id', $concoursId)
        ->first();

      if ($existant) {
        $statut = StatutPaiement::PENDING_MANUAL_REVIEW;
        $validationNotes = 'Référence potentiellement déjà utilisée';
      }
    }

    // Ajouter warnings aux notes
    if (!empty($warnings)) {
      $warningText = 'Données OCR manquantes: ' . implode(', ', $warnings);
      $validationNotes = $validationNotes
        ? $validationNotes . '; ' . $warningText
        : $warningText;

      if ($statut === StatutPaiement::PENDING) {
        $statut = StatutPaiement::PENDING_MANUAL_REVIEW;
      }
    }

    // Créer le paiement
    $paiement = Paiement::create([
      'concours_id' => $concoursId,
      'reference' => $reference,
      'montant' => $ocrData->montant ?: 0,
      'preuve_paiement' => $filePath,
      'statut' => $statut,
      'montant_ocr' => $ocrData->montant,
      'banque_ocr' => $ocrData->banque,
      'numero_compte_ocr' => $ocrData->numero_compte,
      'reference_ocr' => $ocrData->numero_recu,
      'date_ocr' => $ocrData->date_paiement,
      'ocr_confidence' => $ocrData->ocr_confidence,
      'ocr_raw_data' => $ocrData->raw_data,
      'validation_notes' => $validationNotes,
    ]);

    return [$paiement, [], $warnings];
  }

  /**
   * Crée un paiement avec échec OCR complet.
   */
  public function createFailedOcrPayment(string $concoursId, string $filePath, Exception $error): Paiement
  {
    return Paiement::create([
      'concours_id' => $concoursId,
      'reference' => 'OCR_FAILED_' . time(),
      'montant' => 0,
      'preuve_paiement' => $filePath,
      'statut' => StatutPaiement::PENDING_MANUAL_REVIEW,
      'validation_notes' => 'Échec OCR complet: ' . $error->getMessage(),
      'ocr_raw_data' => ['error' => $error->getMessage()],
    ]);
  }
}
