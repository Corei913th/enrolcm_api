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
        private readonly PaiementService $paiementService,
    ) {}

    /**
     * Upload preuve de paiement 
     * @param CreatePaiementRequest $request Requête validée contenant concours_id, reference, montant et preuve
     * @return JsonResponse Réponse JSON avec paiement créé ou erreur
     */
    public function store(CreatePaiementRequest $request): JsonResponse
    {
        try {
            $result = $this->paiementService->createPaymentWithOcr(
                concoursId: $request->concours_id,
                preuve: $request->file('preuve')
            );

            return api_success([
                'success' => true,
                'message' => $result['validation_info']['message'],
                'data' => $result,
                'alert_type' => $this->getAlertTypeFromCode($result['validation_info']['code'])
            ]);
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Vérifier si un PRU est valide
     * @param VerifyPRURequest $request Requête validée contenant pru et concours_id
     * @return JsonResponse Réponse JSON indiquant validité du PRU
     */
    public function verifyPRU(VerifyPRURequest $request): JsonResponse
    {
        try {
            $result = $this->paiementService->isPRUValid(
                pru: $request->pru,
                concoursId: $request->concours_id
            );

            if ($result['valid']) {
                return api_success($result, $result['message']);
            }

            return api_error($result['message'], $result, 400);
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Liste des paiements (Admin).

     * @param FilterPaiementsRequest $request Requête validée avec filtres et pagination
     *
     * @return JsonResponse Réponse JSON paginée des paiements
     */
    public function index(FilterPaiementsRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $perPage = $request->input('per_page', 20);

        $paiements = $this->paiementService->getPayments($filters, $perPage);

        return api_paginated($paiements, 'Liste des paiements');
    }

    /**
     * Paiements en attente (Admin).
     * @param Request $request Requête avec pagination
     *
     * @return JsonResponse Réponse JSON paginée des paiements en attente
     */
    public function pending(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 20);
        $paiements = $this->paiementService->getPendingPayments($perPage);

        return api_paginated($paiements, 'Paiements en attente de validation');
    }

    /**
     * Détails d'un paiement.
     * @param string $id ID du paiement
     *
     * @return JsonResponse Réponse JSON avec détails du paiement ou erreur 404
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
     * Validation manuelle d'un paiement (Admin).
     *
     * @return JsonResponse Réponse JSON avec paiement validé ou erreur
     */
    public function validate(string $id, ValiderPaiementRequest $request): JsonResponse
    {
        try {
            $paiement = $this->paiementService->manualValidate($id, $request->user()->id);
            return api_success($paiement, 'Paiement validé avec succès');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Rejet manuel d'un paiement (Admin).
     * @param string $id ID du paiement
     * @param RejeterPaiementRequest $request Requête validée contenant motif et utilisateur
     *
     * @return JsonResponse Réponse JSON avec paiement rejeté ou erreur
     */
    public function reject(string $id, RejeterPaiementRequest $request): JsonResponse
    {
        try {
            $paiement = $this->paiementService->reject(
                paiementId: $id,
                motif: $request->motif,
                userId: $request->user()->id
            );
            return api_success($paiement, 'Paiement rejeté');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Détermine le type d'alerte UI selon le code de validation string.
     */
    private function getAlertTypeFromCode(string $code): string
    {
        return match ($code) {
            'VALIDATION_COMPLETE' => 'success',
            'VALIDATION_PARTIELLE' => 'warning',
            'VALIDATION_MANUELLE_REQUISE' => 'info',
            'VALIDATION_MANUELLE_AVEC_DONNEES_MANQUANTES' => 'warning',
            default => 'info'
        };
    }
}
