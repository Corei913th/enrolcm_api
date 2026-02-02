<?php

namespace App\Services\Infrastructure\OCR;

use thiagoalessio\TesseractOCR\TesseractOCR;

class TesseractOcrService
{
  /**
   * Extraire le texte brut d'une image via Tesseract OCR.
   */
  public function extractText(string $imagePath): array
  {
    try {
      $ocr = new TesseractOCR($imagePath);

      if ($tesseractPath = env('TESSERACT_PATH')) {
        $ocr->executable($tesseractPath);
      }

      $ocr->lang('fra', 'eng');
      $ocr->psm(6);

      $text = $ocr->run();
      $confidence = $this->calculateConfidence($text);

      return [
        'full_text' => $text,
        'confidence' => $confidence,
      ];
    } catch (\Exception $e) {
      throw new \Exception("Erreur lors de l'extraction OCR: " . $e->getMessage());
    }
  }

  /**
   * Extraire les données structurées d'un reçu de paiement.
   */
  public function extractReceiptData(string $imagePath): array
  {
    $ocrData = $this->extractText($imagePath);
    $text = $ocrData['full_text'];

    $patterns = [
      'numero_recu' => [
        '/(?:N°|Numéro|Ref|Reference|Référence|Reçu)[:\s]+([A-Z0-9\-]{6,})/i',
        '/(?:Référence|Reference)[:\s]*([A-Z0-9\-]{6,})/i',
        '/\bRCP(\d{8,})\b/i',
        '/\bPAY[\-\s](\d{8}[\-\s]\d{3})\b/i',
        '/\bPAY[\-\s](\d{8}[\-\s]\d{3,})/i',
        '/(?:Transaction|Trans)[:\s]*([A-Z0-9\-]{8,})/i',
        '/\b([A-Z]{2,4}[\-\/][0-9]{4,}[\-\/][0-9]{4,})\b/i',
        '/\b([A-Z0-9]{8,})\b/',
        '/\b([0-9]{10,})\b/',
      ],
      'montant' => [
        '/(?:Montant|Total|Amount|Somme)[:\s]*([\d\s,\.]+)\s*(?:FCFA|XAF|F\s*CFA)/i',
        '/(?:Débit|Debit|Crédit|Credit|Crediteur)[:\s]*([\d\s,\.]+)\s*(?:FCFA|XAF|F\s*CFA)?/i',
        '/(?:DEP|APPRO|RET)[^\d]+([\d\s,\.]{6,})/i',
        '/([\d\s,\.]{6,})\s*(?:FCFA|XAF|F\s*CFA)/i',
        '/(?:FCFA|XAF|F\s*CFA)\s*([\d\s,\.]{6,})/i',
      ],
      'date' => [
        '/(?:Date)[:\s]*(\d{2}[\/\-]\d{2}[\/\-]\d{4})/i',
        '/(\d{2}[\/\-][A-Za-z]{3}[\/\-]\d{4})/i',
        '/(\d{2}\s+[A-Za-z]{3}[\.,]\s+\d{4})/i',
        '/(\d{4}[\-\/]\d{2}[\-\/]\d{2})/i',
      ],
      'banque' => [
        '/(BICEC|UBA|SGBC|Afriland|Ecobank|SCB|Express\s*Union|Orange\s*Money|MTN\s*Mobile\s*Money)/i',
      ],
      'numero_compte' => [
        '/(?:N°?\s*compte|N\s*compte|Numéro\s*de\s*compte|Compte|N°\s*compte)[:\s]*([A-Z]{2,}[\s\-\.]*[\d]{4,}[\s\-\.]*[\d]{4,}[\s\-\.]*[\d]{0,})/i',
        '/(?:Account|Account\s*Number|BIC|IBAN)[:\s]*([A-Z]{2,}[\s\-\.]*[\d]{4,}[\s\-\.]*[\d]{4,}[\s\-\.]*[\d]{0,})/i',
        '/Numéro\s+de\s+compte[:\s]*([A-Z]{2,}[\s\-\.]*[\d]{4,}[\s\-\.]*[\d]{4,}[\s\-\.]*[\d]{0,})/i',
        '/\b([A-Z]{2,}[\s\-\.]*[\d]{4,}[\s\-\.]*[\d]{4,}[\s\-\.]*[\d]{0,})\b/i',
        '/\b([A-Z]{3}[\d]{9,})\b/i',
        '/\b(ECO[\d]{9,}|BICEC[\d]{9,}|UBA[\d]{9,}|SGBC[\d]{9,}|AFRILAND[\d]{9,})\b/i',
        '/\b(CM[\d]{2,}[\s\-\.]*[\d]{4,}[\s\-\.]*[\d]{4,}[\s\-\.]*[\d]{0,})\b/i',
        '/([A-Z]{2,}[\d]{6,})/i',
        '/(\d{10,})/',
        '/\b([A-Z0-9]{8,})\b/',
      ],
    ];

    $extracted = $this->extractPatterns($text, $patterns);

    if ($extracted['numero_recu'] && strlen($extracted['numero_recu']) < 6) {
      $extracted['numero_recu'] = null;
    }

    return [
      'numero_recu' => $extracted['numero_recu'],
      'montant' => $this->parseMontant($extracted['montant']),
      'date_paiement' => $this->parseDate($extracted['date']),
      'banque' => $extracted['banque'],
      'numero_compte' => $extracted['numero_compte'],
      'ocr_confidence' => $ocrData['confidence'],
      'raw_data' => [
        'full_text' => $text,
        'extracted' => $extracted,
      ],
    ];
  }

  /**
   * Extraire les données selon les patterns définis.
   */
  private function extractPatterns(string $text, array $patterns): array
  {
    $extracted = [];

    foreach ($patterns as $key => $patternList) {
      $extracted[$key] = null;
      foreach ($patternList as $pattern) {
        if (preg_match($pattern, $text, $matches)) {
          $extracted[$key] = trim($matches[1]);
          break;
        }
      }
    }

    return $extracted;
  }

  /**
   * Parser et normaliser le montant extrait.
   */
  private function parseMontant(?string $montant): ?float
  {
    if (!$montant) {
      return null;
    }

    $montant = str_replace([' ', ','], ['', '.'], $montant);
    $montant = preg_replace('/[^\d\.]/', '', $montant);

    return $montant ? (float) $montant : null;
  }

  /**
   * Parser et normaliser la date extraite.
   */
  private function parseDate(?string $date): ?string
  {
    if (!$date) {
      return null;
    }

    try {
      $date = trim($date);

      $monthMap = [
        'jan' => '01',
        'fev' => '02',
        'feb' => '02',
        'mar' => '03',
        'avr' => '04',
        'apr' => '04',
        'mai' => '05',
        'may' => '05',
        'jun' => '06',
        'jui' => '07',
        'jul' => '07',
        'aou' => '08',
        'aug' => '08',
        'sep' => '09',
        'oct' => '10',
        'nov' => '11',
        'dec' => '12',
      ];

      foreach ($monthMap as $abbr => $num) {
        $date = preg_replace('/\b' . $abbr . '\b/i', $num, $date);
      }

      $date = preg_replace('/[\s\.]+/', '-', $date);

      $formats = ['d-m-Y', 'm-d-Y', 'Y-m-d', 'd/m/Y', 'm/d/Y', 'Y/m/d'];

      foreach ($formats as $format) {
        $parsed = \DateTime::createFromFormat($format, $date);
        if ($parsed) {
          return $parsed->format('Y-m-d');
        }
      }

      return null;
    } catch (\Exception) {
      return null;
    }
  }

  /**
   * Calculer un score de confiance basique pour le texte OCR.
   */
  private function calculateConfidence(string $text): float
  {
    $keywords = ['reçu', 'montant', 'date', 'fcfa', 'banque', 'paiement'];
    $score = 0.5;

    foreach ($keywords as $keyword) {
      if (stripos($text, $keyword) !== false) {
        $score += 0.1;
      }
    }

    if (strlen($text) > 50) {
      $score += 0.1;
    }

    return min($score, 1.0);
  }
}
