<?php

namespace App\Services\OCR;

use thiagoalessio\TesseractOCR\TesseractOCR;
use App\DTOs\Payment\PaymentReceiptDTO;

class TesseractOcrService
{
    /**
     * Extraire le texte d'une image
     */
    public function extractText(string $imagePath): array
    {
        try {
            $ocr = new TesseractOCR($imagePath);
            
            // Configurer le chemin de Tesseract si défini dans .env
            if ($tesseractPath = env('TESSERACT_PATH')) {
                $ocr->executable($tesseractPath);
            }
            
            $ocr->lang('fra', 'eng'); // Français et Anglais
            $ocr->psm(6); // Assume a single uniform block of text
            
            $text = $ocr->run();
            
            // Calculer un score de confiance basique
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
     * Vérifier si Tesseract est installé
     */
    private function checkTesseractInstalled(): void
    {
        $command = PHP_OS_FAMILY === 'Windows' ? 'where tesseract' : 'which tesseract';
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception(
                "Tesseract OCR n'est pas installé sur ce système. " .
                "Veuillez consulter docs/TESSERACT_INSTALLATION.md pour les instructions d'installation. " .
                "En attendant, vous pouvez utiliser l'endpoint /api/auth/pre-register/manual-receipt pour saisir manuellement les données du reçu."
            );
        }
    }

    /**
     * Extraire les données structurées d'un reçu
     */
    public function extractReceiptData(string $imagePath): PaymentReceiptDTO
    {
        $ocrData = $this->extractText($imagePath);
        $text = $ocrData['full_text'];
        
        // Patterns améliorés pour détecter les informations
        $patterns = [
            'numero_recu' => [
                // Patterns pour numéros de reçu/référence
                '/(?:N°|Numéro|Ref|Reference|Reçu)[:\s]*([A-Z0-9]{6,}[\-\/]?[A-Z0-9]*)/i',
                '/(?:Transaction|Trans)[:\s]*([A-Z0-9]{8,})/i',
                '/\b([A-Z]{2,4}[\-\/][0-9]{4,}[\-\/][0-9]{4,})\b/i', // Format: ABC-2024-001234
                '/\b([0-9]{10,})\b/', // Numéro long (10+ chiffres)
            ],
            'montant' => [
                // Patterns pour montants avec FCFA
                '/(?:Montant|Total|Amount|Somme)[:\s]*([\d\s,\.]+)\s*(?:FCFA|XAF|F\s*CFA)/i',
                '/(?:Débit|Debit|Crédit|Credit|Crediteur)[:\s]*([\d\s,\.]+)\s*(?:FCFA|XAF|F\s*CFA)?/i',
                '/(?:DEP|APPRO|RET)[^\d]+([\d\s,\.]{6,})/i', // Montants dans les transactions
                '/([\d\s,\.]{6,})\s*(?:FCFA|XAF|F\s*CFA)/i', // Montant avant FCFA (min 6 chiffres)
                '/(?:FCFA|XAF|F\s*CFA)\s*([\d\s,\.]{6,})/i', // Montant après FCFA
            ],
            'date' => [
                '/(?:Date)[:\s]*(\d{2}[\/\-]\d{2}[\/\-]\d{4})/i',
                '/(\d{2}[\/\-][A-Za-z]{3}[\/\-]\d{4})/i', // Format: 30-Oct-2024
                '/(\d{2}\s+[A-Za-z]{3}[\.,]\s+\d{4})/i', // Format: 30 Oct. 2024
                '/(\d{4}[\-\/]\d{2}[\-\/]\d{2})/i', // Format: 2024-10-30
            ],
            'banque' => [
                '/(BICEC|UBA|SGBC|Afriland|Ecobank|SCB|Express\s*Union|Orange\s*Money|MTN\s*Mobile\s*Money)/i',
            ],
        ];
        
        $extracted = $this->extractPatterns($text, $patterns);
        
        // Validation du numéro de reçu
        if ($extracted['numero_recu'] && strlen($extracted['numero_recu']) < 6) {
            $extracted['numero_recu'] = null; // Trop court, probablement une erreur
        }
        
        return new PaymentReceiptDTO(
            numero_recu: $extracted['numero_recu'],
            montant: $this->parseMontant($extracted['montant']),
            date_paiement: $this->parseDate($extracted['date']),
            banque: $extracted['banque'],
            ocr_confidence: $ocrData['confidence'],
            raw_data: [
                'full_text' => $text,
                'extracted' => $extracted,
            ]
        );
    }

    /**
     * Extraire les données selon les patterns
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
     * Parser le montant
     */
    private function parseMontant(?string $montant): ?float
    {
        if (!$montant) {
            return null;
        }
        
        // Nettoyer le montant
        $montant = str_replace([' ', ','], ['', '.'], $montant);
        $montant = preg_replace('/[^\d\.]/', '', $montant);
        
        return $montant ? (float) $montant : null;
    }

    /**
     * Parser la date
     */
    private function parseDate(?string $date): ?string
    {
        if (!$date) {
            return null;
        }
        
        try {
            // Nettoyer la date
            $date = trim($date);
            
            // Remplacer les abréviations de mois en français/anglais
            $monthMap = [
                'jan' => '01', 'fev' => '02', 'feb' => '02', 'mar' => '03', 'avr' => '04', 'apr' => '04',
                'mai' => '05', 'may' => '05', 'jun' => '06', 'jui' => '07', 'jul' => '07', 'aou' => '08',
                'aug' => '08', 'sep' => '09', 'oct' => '10', 'nov' => '11', 'dec' => '12',
            ];
            
            foreach ($monthMap as $abbr => $num) {
                $date = preg_replace('/\b' . $abbr . '\b/i', $num, $date);
            }
            
            // Nettoyer les espaces et points
            $date = preg_replace('/[\s\.]+/', '-', $date);
            
            // Essayer différents formats
            $formats = ['d-m-Y', 'm-d-Y', 'Y-m-d', 'd/m/Y', 'm/d/Y', 'Y/m/d'];
            
            foreach ($formats as $format) {
                $parsed = \DateTime::createFromFormat($format, $date);
                if ($parsed) {
                    return $parsed->format('Y-m-d');
                }
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Calculer un score de confiance basique
     */
    private function calculateConfidence(string $text): float
    {
        // Score basique basé sur la longueur et la présence de mots-clés
        $keywords = ['reçu', 'montant', 'date', 'fcfa', 'banque', 'paiement'];
        $score = 0.5; // Score de base
        
        foreach ($keywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                $score += 0.1;
            }
        }
        
        // Bonus si le texte est suffisamment long
        if (strlen($text) > 50) {
            $score += 0.1;
        }
        
        return min($score, 1.0);
    }
}
