<?php

namespace App\Http\Controllers\OCR;

use App\Http\Controllers\Controller;
use App\Models\ConcoursPaiement;
use App\Services\Domain\Paiement\ConcoursPaiementService;
use App\Services\Infrastructure\OCR\TesseractOcrService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\PdfToImage\Pdf;

class OcrTestController extends Controller
{
    public function __construct(
        private readonly TesseractOcrService $ocrService,
        private readonly ConcoursPaiementService $concoursPaiementService
    ) {}

    /**
     * Tester l'OCR sur une image uploadée avec VALIDATION STRICTE (sans stockage en base).
     */
    public function testOcr(Request $request): JsonResponse
    {
        try {
            // Validation de l'upload et des paramètres
            $request->validate([
                'receipt' => 'required|file|mimes:png,jpg,jpeg' . (class_exists('Imagick') ? ',pdf' : '') . '|max:' . (class_exists('Imagick') ? '10240' : '5120'), // 10MB pour PDF, 5MB pour images si pas de PDF
                'concours_id' => 'required|string|uuid',
            ]);

            $file = $request->file('receipt');
            $concoursId = $request->input('concours_id');

            if (! $file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier invalide',
                    'errors' => ['receipt' => 'Le fichier uploadé n\'est pas valide'],
                ], 400);
            }

            $isPdf = $file->getMimeType() === 'application/pdf';
            $imagePaths = [];

            // Créer un répertoire temporaire s'il n'existe pas
            $tempDir = storage_path('app/temp/ocr');
            if (! is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Générer un nom de fichier unique
            $fileName = 'ocr_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $fullTempPath = $tempDir . '/' . $fileName;

            // Copier le contenu du fichier uploadé vers le fichier temporaire
            if (! copy($file->getRealPath(), $fullTempPath)) {
                throw new \Exception('Impossible de créer le fichier temporaire: ' . $fullTempPath);
            }

            try {
                // Si c'est un PDF, essayer de le convertir en images
                if ($isPdf) {
                    // Vérifier si ImageMagick est disponible
                    if (! class_exists('Imagick')) {
                        throw new \Exception(
                            'Support PDF non disponible : ImageMagick n\'est pas installé. ' .
                              'L\'OCR ne peut traiter que les images (PNG, JPG, JPEG) pour le moment. ' .
                              'Pour activer le support PDF, installez ImageMagick sur le serveur.'
                        );
                    }

                    try {
                        $pdf = new Pdf($fullTempPath);
                        $imagePaths = [];

                        // Convertir la première page seulement
                        $tempImagePath = $tempDir . '/' . pathinfo($fileName, PATHINFO_FILENAME) . '_page_1.png';
                        $pdf->setPage(1)->saveImage($tempImagePath);
                        $imagePaths[] = $tempImagePath;

                        // Utiliser la première image pour l'OCR
                        $ocrPath = $tempImagePath;
                    } catch (\Exception $e) {
                        throw new \Exception('Erreur lors de la conversion PDF : ' . $e->getMessage());
                    }
                } else {
                    $ocrPath = $fullTempPath;
                    $imagePaths = [$fullTempPath];
                }

                // Extraire le texte OCR
                $ocrResult = $this->ocrService->extractText($ocrPath);

                // Extraire les données structurées
                $receiptData = $this->ocrService->extractReceiptData($ocrPath);

                // Récupérer la configuration du concours pour validation stricte
                $config = $this->concoursPaiementService->getConfiguration($concoursId);

                // Appliquer les mêmes validations strictes que pour les paiements réels
                $validationResults = $this->validateReceiptAgainstConfig($receiptData, $config);

                // Supprimer tous les fichiers temporaires
                foreach ($imagePaths as $path) {
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
                if ($isPdf && file_exists($fullTempPath)) {
                    unlink($fullTempPath);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'OCR traité avec succès - TEST UNIQUEMENT (pas de stockage)',
                    'warning' => 'Ceci est un test OCR. Aucun paiement n\'a été créé en base de données.',
                    'data' => [
                        'ocr_text' => [
                            'full_text' => $ocrResult['full_text'],
                            'confidence' => $ocrResult['confidence'],
                        ],
                        'extracted_data' => [
                            'numero_recu' => $receiptData->numero_recu,
                            'numero_compte' => $receiptData->numero_compte,
                            'montant' => $receiptData->montant,
                            'date_paiement' => $receiptData->date_paiement,
                            'banque' => $receiptData->banque,
                            'ocr_confidence' => $receiptData->ocr_confidence,
                        ],
                        'validation_stricte' => [
                            'concours_config' => $config ? [
                                'banque_nom' => $config->banque_nom,
                                'numero_compte' => $config->numero_compte,
                                'nom_beneficiaire' => $config->nom_beneficiaire,
                                'montant' => $config->montant,
                                'date_limite' => $config->date_limite?->format('Y-m-d'),
                                'banques_acceptees' => $config->banques_acceptees,
                                'minimum_confiance_ocr' => $config->minimum_confiance_ocr,
                            ] : null,
                            'resultats' => $validationResults,
                            'validation_complete' => $this->isValidationComplete($validationResults),
                        ],
                        'raw_data' => $receiptData->raw_data,
                        'file_info' => [
                            'original_name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'size' => $file->getSize(),
                            'size_human' => $this->formatBytes($file->getSize()),
                            'is_pdf' => $isPdf,
                            'converted_pages' => $isPdf ? count($imagePaths) : 0,
                            'pdf_support_available' => class_exists('Imagick'),
                        ],
                    ],
                ]);
            } catch (\Exception $e) {
                // Supprimer tous les fichiers temporaires en cas d'erreur
                if (isset($imagePaths)) {
                    foreach ($imagePaths as $path) {
                        if (file_exists($path)) {
                            unlink($path);
                        }
                    }
                }
                if (isset($fullTempPath) && file_exists($fullTempPath)) {
                    unlink($fullTempPath);
                }

                Log::error('Erreur OCR: ' . $e->getMessage(), [
                    'file' => $file->getClientOriginalName(),
                    'error' => $e->getMessage(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors du traitement OCR',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur générale OCR test: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur interne du serveur',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Vérifier la disponibilité du support PDF.
     */
    public static function checkPdfSupport(): array
    {
        $available = class_exists('Imagick');

        return [
            'pdf_support_available' => $available,
            'supported_formats' => $available ? ['png', 'jpg', 'jpeg', 'pdf'] : ['png', 'jpg', 'jpeg'],
            'max_file_size' => $available ? '10MB' : '5MB',
            'message' => $available
              ? 'Support PDF activé - ImageMagick détecté'
              : 'Support PDF désactivé - ImageMagick non installé. Installez ImageMagick pour activer le support PDF.',
        ];
    }

    /**
     * Formater la taille en bytes en format humain lisible.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Appliquer les mêmes validations strictes que pour les paiements réels.
     *
     * @param  mixed  $receiptData
     * @param  ConcoursPaiement|null  $config
     */
    private function validateReceiptAgainstConfig($receiptData, $config): array
    {
        if (! $config) {
            return [
                'config_trouvee' => false,
                'message' => 'Configuration de concours non trouvée',
                'validations' => [],
            ];
        }

        $validations = [];

        // 1. Validation du montant (exactitude stricte)
        $montantExact = $this->validateStrictAmount($receiptData->montant, $config->montant);
        $validations['montant'] = [
            'requis' => $config->montant,
            'detecte' => $receiptData->montant,
            'valide' => $montantExact,
            'message' => $montantExact ? 'Montant exact détecté' : 'Montant ne correspond pas exactement',
        ];

        // 2. Validation de la date (avant date limite)
        $dateValide = $this->validateDate($receiptData->date_paiement, $config->date_limite);
        $validations['date'] = [
            'date_limite' => $config->date_limite?->format('Y-m-d'),
            'date_detectee' => $receiptData->date_paiement,
            'valide' => $dateValide,
            'message' => $dateValide ? 'Date valide (avant limite)' : 'Date invalide ou après limite',
        ];

        // 3. Validation de la banque
        $banqueValide = $this->validateBank($receiptData->banque, $config);
        $validations['banque'] = [
            'banques_acceptees' => $config->banques_acceptees,
            'banque_detectee' => $receiptData->banque,
            'valide' => $banqueValide,
            'message' => $banqueValide ? 'Banque acceptée' : 'Banque non acceptée ou non détectée',
        ];

        // 4. Validation du numéro de compte (stricte)
        $compteValide = $this->validateAccountNumber($receiptData->numero_compte, $config->numero_compte);
        $validations['numero_compte'] = [
            'requis' => $config->numero_compte,
            'detecte' => $receiptData->numero_compte,
            'valide' => $compteValide,
            'message' => $compteValide ?
              (($receiptData->numero_compte === preg_replace('/[\s\-\.]/', '', trim(strtoupper($config->numero_compte))) ||
                $receiptData->numero_compte === strtoupper($config->numero_compte)) ?
                'Numéro de compte exact' : 'Numéro de compte validé (tolérance OCR)') : ($receiptData->numero_compte === null ?
                'Numéro de compte non détecté par OCR' :
                'Numéro de compte ne correspond pas'),
        ];

        // 5. Validation de la confiance OCR
        $seuilConfiance = $config->minimum_confiance_ocr ?? 0.85;
        if (is_string($seuilConfiance)) {
            $seuilConfiance = (float) $seuilConfiance;
        }
        // Le seuil en base est en pourcentage (85.00), convertir en décimal (0.85)
        if ($seuilConfiance > 1) {
            $seuilConfiance = $seuilConfiance / 100;
        }

        $confianceValide = $this->validateOcrConfidence($receiptData->ocr_confidence, $seuilConfiance);
        $validations['ocr_confiance'] = [
            'minimum_requis' => $seuilConfiance,
            'confiance_detectee' => $receiptData->ocr_confidence,
            'valide' => $confianceValide,
            'message' => $confianceValide ? 'Confiance OCR suffisante' : 'Confiance OCR insuffisante',
        ];

        return [
            'config_trouvee' => true,
            'concours_id' => $config->concours_id,
            'validations' => $validations,
        ];
    }

    /**
     * Vérifier si toutes les validations sont passées.
     */
    private function isValidationComplete(array $validationResults): bool
    {
        if (! $validationResults['config_trouvee']) {
            return false;
        }

        foreach ($validationResults['validations'] as $validation) {
            if (! $validation['valide']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validation stricte du montant (exactitude parfaite).
     */
    private function validateStrictAmount(?float $detectedAmount, float $requiredAmount): bool
    {
        if ($detectedAmount === null) {
            return false;
        }

        return abs($detectedAmount - $requiredAmount) < 0.01;
    }

    /**
     * Validation de la date (doit être avant la date limite).
     */
    private function validateDate(?string $detectedDate, ?\DateTime $deadline): bool
    {
        if (! $detectedDate || ! $deadline) {
            return false;
        }

        try {
            $detected = new \DateTime($detectedDate);

            return $detected <= $deadline;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Validation de la banque (doit être dans la liste acceptée).
     */
    private function validateBank(?string $detectedBank, $config): bool
    {
        if (! $detectedBank) {
            return true; // Si non détectée, on considère valide (validation manuelle possible)
        }

        if (! $config->banques_acceptees) {
            return true; // Si aucune restriction, toutes acceptées
        }

        return in_array($detectedBank, $config->banques_acceptees);
    }

    /**
     * Validation stricte du numéro de compte (multi-banques).
     */
    private function validateAccountNumber(?string $detectedAccount, ?string $requiredAccount): bool
    {
        if (! $requiredAccount) {
            return true; // Si pas configuré, valide
        }

        if (! $detectedAccount) {
            return false; // Si non détecté, validation échoue - doit être détecté par OCR
        }

        // Nettoyage pour comparaison (supprimer espaces, tirets, et normaliser)
        $detected = preg_replace('/[\s\-\.]/', '', trim(strtoupper($detectedAccount)));
        $required = preg_replace('/[\s\-\.]/', '', trim(strtoupper($requiredAccount)));

        // Comparaison exacte d'abord
        if ($detected === $required) {
            return true;
        }

        // Si pas exact, vérifier similarité (tolérance OCR)
        return $this->areSimilarAccountNumbers($detected, $required);
    }

    /**
     * Vérifie si deux numéros de compte sont similaires (tolérance OCR).
     * Gère les confusions classiques : 8↔0, 1↔7, 6↔5, etc.
     */
    private function areSimilarAccountNumbers(string $detected, string $required): bool
    {
        // Même longueur ?
        if (strlen($detected) !== strlen($required)) {
            return false;
        }

        // Même préfixe bancaire ?
        $detectedPrefix = $this->extractBankPrefix($detected);
        $requiredPrefix = $this->extractBankPrefix($required);

        if ($detectedPrefix !== $requiredPrefix) {
            return false;
        }

        // Comparer chiffre par chiffre avec tolérance
        $maxDifferences = 1; // Maximum 1 différence acceptée
        $differences = 0;

        $detectedNumbers = substr($detected, strlen($detectedPrefix));
        $requiredNumbers = substr($required, strlen($requiredPrefix));

        for ($i = 0; $i < strlen($detectedNumbers); $i++) {
            if ($detectedNumbers[$i] !== $requiredNumbers[$i]) {
                $differences++;

                // Vérifier si c'est une confusion classique
                if (! $this->isCommonOcrConfusion($detectedNumbers[$i], $requiredNumbers[$i])) {
                    $differences++; // Pénalité pour confusion non-standard
                }

                if ($differences > $maxDifferences) {
                    return false;
                }
            }
        }

        return $differences <= $maxDifferences;
    }

    /**
     * Extrait le préfixe bancaire (lettres au début).
     */
    private function extractBankPrefix(string $account): string
    {
        preg_match('/^[A-Z]+/', $account, $matches);

        return $matches[0] ?? '';
    }

    /**
     * Vérifie si deux chiffres sont sujets à confusion OCR courante.
     */
    private function isCommonOcrConfusion(string $digit1, string $digit2): bool
    {
        $confusions = [
            '0' => ['8', '6', '9'],
            '1' => ['7', '9'],
            '2' => ['7'],
            '3' => ['8'],
            '5' => ['6', '8'],
            '6' => ['5', '0', '8'],
            '7' => ['1', '2'],
            '8' => ['0', '3', '5', '6', '9'],
            '9' => ['8', '1'],
        ];

        return isset($confusions[$digit1]) && in_array($digit2, $confusions[$digit1]);
    }

    /**
     * Validation de la confiance OCR.
     */
    private function validateOcrConfidence(float $detectedConfidence, float $minimumRequired): bool
    {
        return $detectedConfidence >= $minimumRequired;
    }
}
