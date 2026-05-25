<?php

namespace App\Services\Infrastructure\OCR;

use Illuminate\Support\Facades\Log;

class PaymentOcrService
{
    public function __construct(
        private readonly TesseractOcrService $tesseractService
    ) {}

    /**
     * Extrait les données d'un reçu de paiement
     *
     * @param  string  $filePath  Chemin du fichier image/PDF
     * @return array{
     *   success: bool,
     *   data: array{
     *     reference: string|null,
     *     montant: float|null,
     *     date_paiement: string|null,
     *     banque: string|null,
     *     numero_compte: string|null,
     *     confidence_score: float
     *   }|null,
     *   error: string|null
     * }
     */
    public function extract(string $filePath): array
    {
        try {
            // Vérifier que le fichier existe
            if (! file_exists($filePath)) {
                return [
                    'success' => false,
                    'data' => null,
                    'error' => 'Fichier introuvable',
                ];
            }

            // Utiliser TesseractOcrService pour extraire les données structurées
            $extractedData = $this->tesseractService->extractReceiptData($filePath);

            // Mapper les données extraites au format attendu
            $data = [
                'reference' => $extractedData['numero_recu'],
                'montant' => $extractedData['montant'],
                'date_paiement' => $extractedData['date_paiement'],
                'banque' => $extractedData['banque'],
                'numero_compte' => $extractedData['numero_compte'],
                'confidence_score' => $extractedData['ocr_confidence'] * 100, // Convertir en pourcentage
            ];

            Log::info('OCR: Extraction réussie', [
                'file' => $filePath,
                'confidence' => $data['confidence_score'],
                'has_reference' => ! empty($data['reference']),
                'has_montant' => ! empty($data['montant']),
                'has_date' => ! empty($data['date_paiement']),
            ]);

            return [
                'success' => true,
                'data' => $data,
                'error' => null,
            ];
        } catch (\Exception $e) {
            Log::error('Erreur OCR extraction', [
                'file' => $filePath,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Vérifie si les données extraites sont suffisantes
     */
    public function hasMinimumData(array $data): bool
    {
        // TOUTES les données obligatoires doivent être présentes
        return ! empty($data['reference'])
          && ! empty($data['montant'])
          && $data['montant'] > 0
          && ! empty($data['date_paiement'])
          && ! empty($data['numero_compte']);
    }

    /**
     * Vérifie si le score de confiance est suffisant
     */
    public function isConfidenceAcceptable(float $score): bool
    {
        // Seuil de confiance: 60%
        return $score >= 60.0;
    }
}
