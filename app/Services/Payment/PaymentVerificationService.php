<?php

namespace App\Services\Payment;

use App\Models\PaymentReceipt;
use App\Models\Candidat;
use App\Services\OCR\TesseractOcrService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PaymentVerificationService
{
    public function __construct(
        private TesseractOcrService $ocrService
    ) {}

    /**
     * Vérifier et enregistrer un reçu de paiement
     */
    public function verifyReceipt(UploadedFile $file, Candidat $candidat): PaymentReceipt
    {
        
        $this->validateFile($file);
        
        
        $path = $file->store('receipts', 'private');
        $fullPath = storage_path('app/private/' . $path);
        
        try {
            
            $receiptData = $this->ocrService->extractReceiptData($fullPath);
            
            
            $this->validateReceiptData($receiptData);
            
            
            if ($receiptData->numero_recu && 
                PaymentReceipt::where('numero_recu', $receiptData->numero_recu)->exists()) {
                throw new \Exception('Ce numéro de reçu a déjà été utilisé');
            }
            
            
            return PaymentReceipt::create([
                'candidat_id' => $candidat->utilisateur_id,
                'numero_recu' => $receiptData->numero_recu ?? 'TEMP-' . uniqid(),
                'banque' => $receiptData->banque,
                'montant' => $receiptData->montant ?? 0,
                'date_paiement' => $receiptData->date_paiement,
                'image_path' => $path,
                'ocr_data' => $receiptData->raw_data,
                'statut_verification' => 'en_attente',
            ]);
        } catch (\Exception $e) {
            
            Storage::disk('private')->delete($path);
            throw $e;
        }
    }

    /**
     * Valider le fichier uploadé
     */
    private function validateFile(UploadedFile $file): void
    {
        // Vérifier le type MIME
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \Exception('Format d\'image non supporté. Utilisez JPG ou PNG.');
        }
        
        // Vérifier la taille (max 5MB)
        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new \Exception('L\'image est trop volumineuse. Maximum 5MB.');
        }
    }

    /**
     * Valider les données extraites
     */
    private function validateReceiptData($data): void
    {
        
        if ($data->ocr_confidence < 0.4) {
            throw new \Exception(
                'Image de mauvaise qualité. Veuillez prendre une photo plus nette et réessayer.'
            );
        }
        
        
        if ($data->montant) {
            $montantAttendu = config('concours.frais_inscription', 5000);
            
            if ($data->montant < ($montantAttendu * 0.9) || 
                $data->montant > ($montantAttendu * 1.1)) {
                throw new \Exception(
                    "Le montant détecté ({$data->montant} FCFA) ne correspond pas aux frais d'inscription attendus ({$montantAttendu} FCFA). Vérification manuelle requise."
                );
            }
        }
    }

    /**
     * Obtenir l'URL de l'image du reçu
     */
    public function getReceiptImageUrl(PaymentReceipt $receipt): string
    {
        return Storage::disk('private')->url($receipt->image_path);
    }

    /**
     * Vérifier si un candidat a un paiement vérifié
     */
    public function hasVerifiedPayment(Candidat $candidat): bool
    {
        return PaymentReceipt::where('candidat_id', $candidat->utilisateur_id)
            ->verifie()
            ->exists();
    }

    /**
     * Obtenir le reçu vérifié d'un candidat
     */
    public function getVerifiedReceipt(Candidat $candidat): ?PaymentReceipt
    {
        return PaymentReceipt::where('candidat_id', $candidat->utilisateur_id)
            ->verifie()
            ->first();
    }
}
