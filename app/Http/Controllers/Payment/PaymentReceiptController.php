<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentVerificationService;
use App\Services\Payment\PaymentReceiptService;
use App\Http\Requests\Payment\UploadReceiptRequest;
use App\Http\Requests\Payment\VerifyReceiptRequest;
use App\Http\Requests\Payment\UpdatePaymentReceiptRequest;
use App\Http\Requests\Payment\RejectPaymentReceiptRequest;
use App\Http\Requests\Payment\FilterPaymentReceiptsRequest;
use App\Http\Resources\PaymentReceiptResource;
use App\Models\PaymentReceipt;
use Illuminate\Http\Request;

class PaymentReceiptController extends Controller
{
    public function __construct(
        private PaymentVerificationService $paymentVerificationService,
        private PaymentReceiptService $receiptService
    ) {}

    /**
     * Upload et vérifier un reçu de paiement (Candidat)
     */
    public function upload(UploadReceiptRequest $request)
    {
        try {
            $candidat = $request->user()->candidat;
            
            if (!$candidat) {
                return api_error('Vous devez être un candidat pour uploader un reçu', null, 403);
            }
            
            // Vérifier si le candidat n'a pas déjà un reçu vérifié
            if ($this->paymentVerificationService->hasVerifiedPayment($candidat)) {
                return api_error('Vous avez déjà un reçu de paiement vérifié', null, 400);
            }
            
            $receipt = $this->paymentVerificationService->verifyReceipt(
                $request->file('receipt_image'),
                $candidat
            );
            
            return api_created(
                new PaymentReceiptResource($receipt),
                'Reçu enregistré avec succès. Il sera vérifié sous 24-48h.'
            );
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Obtenir le reçu du candidat connecté
     */
    public function myReceipt(Request $request)
    {
        $candidat = $request->user()->isCandidat();
        
        if (!$candidat) {
            return api_error('Vous devez être un candidat', null, 403);
        }
        
        $receipts = $this->receiptService->getByCandidatId($candidat->utilisateur_id);
        
        if ($receipts->isEmpty()) {
            return api_not_found('Aucun reçu trouvé');
        }
        
        return api_success(PaymentReceiptResource::collection($receipts));
    }

    /**
     * Liste des reçus en attente de vérification (Admin)
     */
    public function pending(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $receipts = $this->receiptService->getEnAttente($perPage);
        
        return api_paginated(
            PaymentReceiptResource::collection($receipts),
            'Liste des reçus en attente'
        );
    }

    /**
     * Détails d'un reçu (Admin)
     */
    public function show(string $id)
    {
        $receipt = $this->receiptService->getById($id);
        
        if (!$receipt) {
            return api_not_found('Reçu non trouvé');
        }
        
        return api_success(new PaymentReceiptResource($receipt));
    }

    /**
     * Vérifier un reçu (Admin)
     */
    public function verify(string $id, Request $request)
    {
        try {
            $receipt = $this->receiptService->verifier($id, $request->user()->id);
            
            return api_success(
                new PaymentReceiptResource($receipt),
                'Reçu vérifié avec succès'
            );
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Rejeter un reçu (Admin)
     */
    public function reject(string $id, RejectPaymentReceiptRequest $request)
    {
        try {
            $receipt = $this->receiptService->rejeter(
                $id,
                $request->motif_rejet,
                $request->user()->id
            );
            
            return api_success(
                new PaymentReceiptResource($receipt),
                'Reçu rejeté'
            );
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Réinitialiser un reçu (Admin)
     */
    public function reset(string $id)
    {
        try {
            $receipt = $this->receiptService->reinitialiser($id);
            
            return api_success(
                new PaymentReceiptResource($receipt),
                'Reçu réinitialisé avec succès'
            );
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Liste de tous les reçus avec filtres (Admin)
     */
    public function index(FilterPaymentReceiptsRequest $request)
    {
        $perPage = $request->input('per_page', 20);
        $receipts = $this->receiptService->getAll($request->validated(), $perPage);
        
        return api_paginated(
            PaymentReceiptResource::collection($receipts),
            'Liste des reçus de paiement'
        );
    }

    /**
     * Mettre à jour un reçu (Admin)
     */
    public function update(string $id, UpdatePaymentReceiptRequest $request)
    {
        try {
            $receipt = $this->receiptService->update($id, $request->validated());
            
            return api_success(
                new PaymentReceiptResource($receipt),
                'Reçu mis à jour avec succès'
            );
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Supprimer un reçu (Admin)
     */
    public function destroy(string $id)
    {
        try {
            $this->receiptService->delete($id);
            
            return api_success(null, 'Reçu supprimé avec succès');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Obtenir les statistiques des reçus (Admin)
     */
    public function statistics()
    {
        $stats = $this->receiptService->getStats();
        
        return api_success($stats, 'Statistiques des reçus de paiement');
    }

    /**
     * Télécharger l'image d'un reçu (Admin)
     */
    public function downloadImage(string $id)
    {
        try {
            $receipt = $this->receiptService->getById($id);
            
            if (!$receipt) {
                return api_not_found('Reçu non trouvé');
            }
            
            $content = $this->receiptService->getImageContent($receipt);
            
            return response($content)
                ->header('Content-Type', 'image/jpeg')
                ->header('Content-Disposition', 'attachment; filename="receipt-' . $receipt->numero_recu . '.jpg"');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }
}
