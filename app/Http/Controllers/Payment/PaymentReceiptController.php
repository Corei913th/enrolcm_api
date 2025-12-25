<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentVerificationService;
use App\Http\Requests\Payment\UploadReceiptRequest;
use App\Http\Requests\Payment\VerifyReceiptRequest;
use App\Http\Resources\PaymentReceiptResource;
use App\Models\PaymentReceipt;
use Illuminate\Http\Request;

class PaymentReceiptController extends Controller
{
    public function __construct(
        private PaymentVerificationService $paymentService
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
            if ($this->paymentService->hasVerifiedPayment($candidat)) {
                return api_error('Vous avez déjà un reçu de paiement vérifié', null, 400);
            }
            
            $receipt = $this->paymentService->verifyReceipt(
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
        $candidat = $request->user()->candidat;
        
        if (!$candidat) {
            return api_error('Vous devez être un candidat', null, 403);
        }
        
        $receipt = PaymentReceipt::where('candidat_id', $candidat->utilisateur_id)
            ->latest()
            ->first();
        
        if (!$receipt) {
            return api_not_found('Aucun reçu trouvé');
        }
        
        return api_success(new PaymentReceiptResource($receipt));
    }

    /**
     * Liste des reçus en attente de vérification (Admin)
     */
    public function pending(Request $request)
    {
        $receipts = PaymentReceipt::with(['candidat', 'verifiedBy'])
            ->enAttente()
            ->latest()
            ->paginate(20);
        
        return api_paginated(
            PaymentReceiptResource::collection($receipts),
            'Liste des reçus en attente'
        );
    }

    /**
     * Détails d'un reçu (Admin)
     */
    public function show(PaymentReceipt $receipt)
    {
        $receipt->load(['candidat', 'verifiedBy']);
        return api_success(new PaymentReceiptResource($receipt));
    }

    /**
     * Vérifier ou rejeter un reçu (Admin)
     */
    public function verify(VerifyReceiptRequest $request, PaymentReceipt $receipt)
    {
        try {
            if ($request->statut === 'verifie') {
                $receipt->verify($request->user());
                $message = 'Reçu vérifié avec succès';
            } else {
                $receipt->reject($request->motif_rejet, $request->user());
                $message = 'Reçu rejeté';
            }
            
            return api_updated(
                new PaymentReceiptResource($receipt->fresh(['candidat', 'verifiedBy'])),
                $message
            );
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Liste de tous les reçus avec filtres (Admin)
     */
    public function index(Request $request)
    {
        $query = PaymentReceipt::with(['candidat', 'verifiedBy']);
        
        // Filtrer par statut
        if ($request->has('statut')) {
            $query->where('statut_verification', $request->statut);
        }
        
        // Filtrer par candidat
        if ($request->has('candidat_id')) {
            $query->where('candidat_id', $request->candidat_id);
        }
        
        // Recherche par numéro de reçu
        if ($request->has('search')) {
            $query->where('numero_recu', 'like', '%' . $request->search . '%');
        }
        
        $receipts = $query->latest()->paginate(20);
        
        return api_paginated(
            PaymentReceiptResource::collection($receipts),
            'Liste des reçus de paiement'
        );
    }
}
