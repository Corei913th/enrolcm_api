<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaiementService;
use App\Http\Requests\Payment\CreatePaiementRequest;
use App\Http\Requests\Payment\ValiderPaiementRequest;
use App\Http\Requests\Payment\RejeterPaiementRequest;
use App\Http\Requests\Payment\FilterPaiementsRequest;
use App\Http\Requests\Payment\VerifyPRURequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function __construct(
        private readonly PaiementService $paiementService
    ) {}

    /**
     * Upload preuve de paiement (AVANT création compte)
     * POST /api/payments
     */
    public function store(CreatePaiementRequest $request): JsonResponse
    {
        try {
            $paiement = $this->paiementService->createPayment(
                concoursId: $request->concours_id,
                reference: $request->reference,
                montant: $request->montant,
                preuve: $request->file('preuve')
            );

            return api_created($paiement, 'Preuve de paiement uploadée avec succès');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Vérifier si un PRU est valide (AVANT création compte)
     * POST /api/payments/verify-pru
     */
    public function verifyPRU(VerifyPRURequest $request): JsonResponse
    {
        try {
            $isValid = $this->paiementService->verifyPRU(
                reference: $request->pru,
                concoursId: $request->concours_id
            );

            if ($isValid) {
                return api_success(['valid' => true], 'PRU valide et disponible');
            }

            return api_error('PRU invalide ou déjà utilisé', ['valid' => false], 400);
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Liste des paiements (Admin)
     * GET /api/payments
     */
    public function index(FilterPaiementsRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $perPage = $request->input('per_page', 20);

        $paiements = $this->paiementService->getPayments($filters, $perPage);

        return api_paginated($paiements, 'Liste des paiements');
    }

    /**
     * Paiements en attente (Admin)
     * GET /api/payments/pending
     */
    public function pending(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 20);
        $paiements = $this->paiementService->getPendingPayments($perPage);

        return api_paginated($paiements, 'Paiements en attente de validation');
    }

    /**
     * Détails d'un paiement
     * GET /api/payments/{id}
     */
    public function show(string $id): JsonResponse
    {
        try {
            $paiement = \App\Models\Paiement::with(['candidat', 'concours'])->findOrFail($id);
            return api_success($paiement);
        } catch (\Exception $e) {
            return api_error('Paiement introuvable', null, 404);
        }
    }

    /**
     * Validation manuelle (Admin)
     * POST /api/payments/{id}/validate
     */
    public function validate(string $id, ValiderPaiementRequest $request): JsonResponse
    {
        try {
            $paiement = $this->paiementService->manualValidate($id, auth()->id());
            return api_success($paiement, 'Paiement validé avec succès');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Rejet manuel (Admin)
     * POST /api/payments/{id}/reject
     */
    public function reject(string $id, RejeterPaiementRequest $request): JsonResponse
    {
        try {
            $paiement = $this->paiementService->reject(
                paiementId: $id,
                motif: $request->motif,
                userId: auth()->id()
            );
            return api_success($paiement, 'Paiement rejeté');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }
}
