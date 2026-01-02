<?php

namespace App\Services\Payment;

use App\Models\Paiement;
use App\Enums\StatutPaiement;
use App\Models\ConcoursPaiement;
use App\Services\OCR\TesseractOcrService;
use App\Services\Payment\ConcoursPaiementService;
use App\Services\Payment\Validators\PaymentOcrValidator;
use App\Services\Payment\Processors\OcrDataProcessor;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class PaiementService
{
    public function __construct(
        private readonly TesseractOcrService $ocrService,
        private readonly ConcoursPaiementService $concoursPaiementService
    ) {}

    /**
     * Crée un paiement avec preuve uploadée et données OCR.
     * @param string $concoursId UUID du concours
     * @param UploadedFile $preuve Fichier de preuve de paiement
     *
     * @return Paiement Paiement créé
     *
     * @throws \Exception Si la référence est déjà utilisée ou configuration inactive
     */
    /**
     * Crée un paiement avec OCR automatique (RECOMMANDÉ)
     * L'OCR extrait automatiquement référence et montant du reçu
     */
    public function createPaymentWithOcr(
        string $concoursId,
        UploadedFile $preuve
    ): Paiement {
        return DB::transaction(function () use ($concoursId, $preuve) {
            $config = $this->concoursPaiementService->getConfiguration($concoursId);
            if (!$config || !$config->est_actif) {
                throw new \Exception('Configuration de paiement non disponible pour ce concours');
            }

            $path = $preuve->store('paiements', 'public');
            $processor = new OcrDataProcessor();

            try {
                $fullPath = Storage::disk('public')->path($path);
                $ocrData = $this->ocrService->extractReceiptData($fullPath);
            } catch (\Exception $e) {
                Log::warning("OCR failed for payment: {$e->getMessage()}");
                return $processor->createFailedOcrPayment($concoursId, $path, $e);
            }

            // Traiter les données OCR avec le processeur modulaire
            [$paiement, $errors, $warnings] = $processor->processOcrData($concoursId, $path, $ocrData, $config);

            if (!empty($errors)) {
                throw new \Exception(
                    'Le reçu n\'a pas pu être analysé correctement. ' .
                        'Informations essentielles manquantes: ' . implode(', ', $errors) .
                        '. Ces informations sont obligatoires pour valider le paiement.'
                );
            }

            // Tentative de validation automatique seulement si pas de warnings
            if (empty($warnings)) {
                $validator = new PaymentOcrValidator();
                $validator->autoValidate($paiement, $config);
            }

            return $paiement;
        });
    }

    /**
     * Crée un paiement avec saisie manuelle (FALLBACK)
     * À utiliser seulement si l'OCR échoue
     *
     * @deprecated Utiliser createPaymentWithOcr de préférence
     */
    public function createPayment(
        string $concoursId,
        string $reference,
        float $montant,
        UploadedFile $preuve
    ): Paiement {
        return DB::transaction(function () use ($concoursId, $reference, $montant, $preuve) {
            $existant = Paiement::where('reference', $reference)
                ->where('concours_id', $concoursId)
                ->first();

            if ($existant) {
                throw new \Exception('Cette référence de paiement est déjà utilisée');
            }

            $config = $this->concoursPaiementService->getConfiguration($concoursId);
            if (!$config || !$config->est_actif) {
                throw new \Exception('Configuration de paiement non disponible pour ce concours');
            }

            $path = $preuve->store('paiements', 'public');

            $ocrData = null;
            $statut = StatutPaiement::PENDING;

            try {
                $fullPath = Storage::disk('public')->path($path);
                $ocrData = $this->ocrService->extractReceiptData($fullPath);
            } catch (\Exception $e) {
                Log::warning("OCR failed for payment: {$e->getMessage()}");
            }

            $paiement = Paiement::create([
                'candidat_id' => null,
                'concours_id' => $concoursId,
                'reference' => $reference,
                'montant' => $montant,
                'preuve_paiement' => $path,
                'montant_ocr' => $ocrData->montant ?? null,
                'date_ocr' => $ocrData->date_paiement ?? null,
                'banque_ocr' => $ocrData->banque ?? null,
                'reference_ocr' => $ocrData->numero_recu ?? null,
                'ocr_confidence' => $ocrData->ocr_confidence ?? null,
                'ocr_raw_data' => $ocrData->raw_data ?? null,
                'statut' => $statut,
            ]);

            if ($ocrData) {
                $this->autoValidate($paiement);
            }

            return $paiement->fresh();
        });
    }

    /**
     * Auto-validation d'un paiement via OCR.
     * et diffère du numéro de reçu bancaire extrait par OCR.
     * Si OK → statut VERIFIED.
     *
     * @param Paiement $paiement Paiement à valider
     *
     * @return bool True si validé, False sinon
     */
    public function autoValidate(Paiement $paiement): bool
    {
        if ($paiement->statut === StatutPaiement::VERIFIED) {
            return true;
        }

        $config = $paiement->concours->configurationPaiement;
        if (!$config) {
            return false;
        }

        // Utiliser le validator modulaire pour la validation OCR
        $validator = new \App\Services\Payment\Validators\PaymentOcrValidator();
        return $validator->autoValidate($paiement, $config);
    }
}
