<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OCR\TesseractOcrService;
use App\Services\Payment\PaymentVerificationService;
use App\Http\Requests\Payment\UploadReceiptRequest;
use App\Http\Requests\Payment\ManualReceiptEntryRequest;
use App\Http\Requests\Payment\CheckReceiptNumberRequest;
use App\Models\PaymentReceipt;
use Illuminate\Support\Facades\Storage;

class PreRegistrationController extends Controller
{
    public function __construct(
        private TesseractOcrService $ocrService,
        private PaymentVerificationService $paymentService
    ) {}

    
    public function uploadReceipt(UploadReceiptRequest $request)
    {
        try {
            
            $path = $request->file('receipt_image')->store('receipts/temp');
            $fullPath = storage_path('app/private/' . $path);
            
            
            $receiptData = $this->ocrService->extractReceiptData($fullPath);
            
            
            if ($receiptData->ocr_confidence < 0.4) {
                Storage::delete($path);
                return api_error(
                    'Image de mauvaise qualité. Veuillez prendre une photo plus nette ou utiliser la saisie manuelle.',
                    ['suggest_manual_entry' => true],
                    400
                );
            }
            
            
            if (!$receiptData->numero_recu || strlen($receiptData->numero_recu) < 6) {
                Storage::delete($path);
                return api_error(
                    'Impossible de détecter le numéro de reçu. Veuillez utiliser la saisie manuelle.',
                    [
                        'suggest_manual_entry' => true,
                        'detected_data' => [
                            'banque' => $receiptData->banque,
                            'montant' => $receiptData->montant,
                            'date_paiement' => $receiptData->date_paiement,
                        ]
                    ],
                    400
                );
            }
            
            
            if (PaymentReceipt::where('numero_recu', $receiptData->numero_recu)->exists()) {
                Storage::delete($path);
                return api_error(
                    'Ce numéro de reçu a déjà été utilisé pour une inscription.',
                    null,
                    400
                );
            }
            
            
            $permanentPath = str_replace('receipts/temp/', 'receipts/', $path);
            Storage::move($path, $permanentPath);
            
            
            PaymentReceipt::create([
                'candidat_id' => null, // Sera lié lors de l'inscription
                'numero_recu' => $receiptData->numero_recu,
                'banque' => $receiptData->banque,
                'montant' => $receiptData->montant ?? 0,
                'date_paiement' => $receiptData->date_paiement,
                'image_path' => $permanentPath,
                'ocr_data' => $receiptData->raw_data,
                'statut_verification' => 'en_attente',
            ]);
            
            
            return api_success([
                'numero_recu' => $receiptData->numero_recu,
                'montant' => $receiptData->montant,
                'banque' => $receiptData->banque,
                'date_paiement' => $receiptData->date_paiement,
                'ocr_confidence' => $receiptData->ocr_confidence,
                'message' => 'Reçu validé et enregistré. Utilisez le numéro de reçu comme identifiant pour créer votre compte.',
            ]);
            
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Vérifier si un numéro de reçu est disponible
     */
    public function checkReceiptNumber(CheckReceiptNumberRequest $request)
    {
        $exists = PaymentReceipt::where('numero_recu', $request->numero_recu)->exists();

        return api_success([
            'available' => !$exists,
            'message' => $exists 
                ? 'Ce numéro de reçu a déjà été utilisé' 
                : 'Numéro de reçu disponible',
        ]);
    }

    /**
     * Saisie manuelle du numéro de reçu (si OCR échoue)
     */
    public function manualReceiptEntry(ManualReceiptEntryRequest $request)
    {
        try {
            $path = $request->file('receipt_image')->store('receipts');

            // Créer l'enregistrement du reçu dans la base de données
            PaymentReceipt::create([
                'candidat_id' => null, // Sera lié lors de l'inscription
                'numero_recu' => $request->numero_recu,
                'banque' => $request->banque,
                'montant' => $request->montant ?? 0,
                'date_paiement' => $request->date_paiement,
                'image_path' => $path,
                'ocr_data' => ['manual_entry' => true],
                'statut_verification' => 'en_attente',
            ]);

            return api_success([
                'numero_recu' => $request->numero_recu,
                'montant' => $request->montant,
                'banque' => $request->banque,
                'date_paiement' => $request->date_paiement,
                'message' => 'Reçu enregistré. Utilisez le numéro de reçu comme identifiant pour créer votre compte.',
            ]);
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }
}
