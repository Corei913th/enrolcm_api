<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OCR\TesseractOcrService;
use App\Services\Payment\PaymentVerificationService;
use App\Services\Payment\PaymentReceiptService;
use App\Http\Requests\Payment\UploadReceiptRequest;
use App\Http\Requests\Payment\ManualReceiptEntryRequest;
use App\Http\Requests\Payment\CheckReceiptNumberRequest;
use App\Http\Requests\Payment\CancelUploadRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PreRegistrationController extends Controller
{
    public function __construct(
        private readonly TesseractOcrService $ocrService,
        private readonly PaymentVerificationService $paymentService,
        private readonly PaymentReceiptService $receiptService
    ) {}

    
    /**
     * Étape 1: Upload et extraction OCR (sans sauvegarde)
     */
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
            
            
            if ($this->receiptService->numeroRecuExists($receiptData->numero_recu)) {
                Storage::delete($path);
                return api_error(
                    'Ce numéro de reçu a déjà été utilisé pour une inscription.',
                    null,
                    400
                );
            }
            
            
            return api_success([
                'temp_path' => $path,
                'numero_recu' => $receiptData->numero_recu,
                'montant' => $receiptData->montant,
                'banque' => $receiptData->banque,
                'date_paiement' => $receiptData->date_paiement,
                'ocr_confidence' => $receiptData->ocr_confidence,
                'ocr_data' => $receiptData->raw_data,
                'message' => 'Données extraites avec succès. Veuillez vérifier et confirmer.',
            ]);
            
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Étape 2: Confirmer et sauvegarder le reçu
     */
    public function confirmReceipt(ManualReceiptEntryRequest $request)
    {
        try {
            
            if ($this->receiptService->numeroRecuExists($request->numero_recu)) {
                return api_error(
                    'Ce numéro de reçu a déjà été utilisé pour une inscription.',
                    null,
                    400
                );
            }

            
            $tempPath = $request->input('temp_path');
            if ($tempPath && Storage::exists($tempPath)) {
                
                $permanentPath = str_replace('receipts/temp/', 'receipts/', $tempPath);
                Storage::move($tempPath, $permanentPath);
                $imagePath = $permanentPath;
            } elseif ($request->hasFile('receipt_image')) {
                
                $imagePath = $request->file('receipt_image')->store('receipts');
            } else {
                return api_error('Aucune image de reçu fournie', null, 400);
            }

            
            $this->receiptService->create([
                'candidat_id' => null, // Sera lié lors de l'inscription
                'numero_recu' => $request->numero_recu,
                'banque' => $request->banque,
                'montant' => $request->montant ?? 0,
                'date_paiement' => $request->date_paiement,
                'image_path' => $imagePath,
                'ocr_data' => $request->input('ocr_data', ['manual_entry' => true]),
                'statut_verification' => 'en_attente',
            ]);

            return api_success([
                'numero_recu' => $request->numero_recu,
                'montant' => $request->montant,
                'banque' => $request->banque,
                'date_paiement' => $request->date_paiement,
                'message' => 'Reçu enregistré avec succès. Utilisez le numéro de reçu comme identifiant pour créer votre compte.',
            ]);
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Annuler l'upload (nettoyer le fichier temporaire)
     */
    public function cancelUpload(CancelUploadRequest $request)
    {
        try {
            $tempPath = $request->input('temp_path');
            
            if ($tempPath && Storage::exists($tempPath)) {
                Storage::delete($tempPath);
            }

            return api_success(null, 'Upload annulé avec succès');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Vérifier si un numéro de reçu est disponible
     */
    public function checkReceiptNumber(CheckReceiptNumberRequest $request)
    {
        $exists = $this->receiptService->numeroRecuExists($request->numero_recu);

        return api_success([
            'available' => !$exists,
            'message' => $exists 
                ? 'Ce numéro de reçu a déjà été utilisé' 
                : 'Numéro de reçu disponible',
        ]);
    }
}
