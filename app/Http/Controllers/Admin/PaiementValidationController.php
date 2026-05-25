<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaiementResource;
use App\Services\Domain\Paiement\PaiementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller pour la validation des paiements par les admins
 */
class PaiementValidationController extends Controller
{
    public function __construct(
        private readonly PaiementService $paiementService
    ) {}

    /**
     * Liste tous les paiements avec priorité pour les paiements manuels
     * Supporte le filtrage par statut
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 20);
            $concoursId = $request->input('concours_id');

            $paiements = $this->paiementService->getPendingPayments($perPage, $concoursId);

            return api_paginated($paiements, 'Liste des paiements', PaiementResource::class);
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Liste des paiements en attente de validation
     */
    public function enAttente(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 100);
            $concoursId = $request->input('concours_id');

            $paiements = $this->paiementService->getAllForValidation($perPage, $concoursId);

            return api_paginated($paiements, 'Paiements en attente', PaiementResource::class);
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Get payment validation statistics
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $concoursId = $request->input('concours_id');
            $stats = $this->paiementService->getValidationStats($concoursId);

            return api_success($stats, 'Statistiques des paiements');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Valider un paiement
     */
    public function valider(Request $request, string $paiementId): JsonResponse
    {
        $request->validate([
            'validation_notes' => 'nullable|string|max:500',
        ]);

        try {
            $paiement = $this->paiementService->manualValidate($paiementId, $request->user()->id);

            return api_success(new PaiementResource($paiement), 'Paiement validé avec succès');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Rejeter un paiement
     */
    public function rejeter(Request $request, string $paiementId): JsonResponse
    {
        $request->validate([
            'motif_rejet' => 'required|string|max:500',
        ]);

        try {
            $paiement = $this->paiementService->reject($paiementId, $request->motif_rejet, $request->user()->id);

            return api_success(new PaiementResource($paiement), 'Paiement rejeté');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }
}
