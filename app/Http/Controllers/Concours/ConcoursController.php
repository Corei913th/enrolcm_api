<?php

namespace App\Http\Controllers\Concours;

use App\Http\Controllers\Controller;
use App\Services\Concours\ConcoursService;
use App\Services\Concours\ConcoursPaiementService;
use App\Http\Requests\Concours\CreateConcoursRequest;
use App\Http\Requests\Concours\UpdateConcoursRequest;
use App\Http\Requests\Concours\ConfigurerPaiementRequest;
use App\DTOs\Concours\CreateConcoursDTO;
use App\DTOs\Concours\UpdateConcoursDTO;
use App\DTOs\Concours\ConfigurePaymentDTO;
use App\Exceptions\ConcoursException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ConcoursController extends Controller
{
    public function __construct(
        private readonly ConcoursService $concoursService,
        private readonly ConcoursPaiementService $paymentService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['est_actif', 'spec_concours_id', 'search', 'session_id']);
        $perPage = $request->input('per_page', 20);
        
        $concours = $this->concoursService->getAll($filters, $perPage);
        
        return api_paginated($concours, 'Liste des concours');
    }

    public function availables(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 20);
        $concours = $this->concoursService->getAvailableConcours($perPage);
        
        return api_paginated($concours, 'Concours ouverts');
    }

    public function show(string $id): JsonResponse
    {
        try {
            $concours = $this->concoursService->getById($id);
            return api_success($concours);
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    public function store(CreateConcoursRequest $request): JsonResponse
    {
        try {
            $dto = CreateConcoursDTO::fromRequest($request->validated());
            $concours = $this->concoursService->create($dto);
            return api_created($concours, 'Concours créé avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    public function update(string $id, UpdateConcoursRequest $request): JsonResponse
    {
        try {
            $dto = UpdateConcoursDTO::fromRequest($request->validated());
            $concours = $this->concoursService->update($id, $dto);
            return api_success($concours, 'Concours mis à jour avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->concoursService->delete($id);
            return api_success(null, 'Concours supprimé avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    public function activate(string $id): JsonResponse
    {
        try {
            $concours = $this->concoursService->activate($id);
            return api_success($concours, 'Concours activé avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    public function deactivate(string $id): JsonResponse
    {
        try {
            $concours = $this->concoursService->deactivate($id);
            return api_success($concours, 'Concours désactivé avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    public function configurePayment(string $id, ConfigurerPaiementRequest $request): JsonResponse
    {
        try {
            $dto = ConfigurePaymentDTO::fromRequest($request->validated());
            $config = $this->paymentService->configurePayment($id, $dto);
            return api_success($config, 'Configuration de paiement enregistrée avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    public function paymentInfo(string $id): JsonResponse
    {
        try {
            $info = $this->paymentService->getPaymentInfo($id);
            return api_success($info);
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    public function stats(string $id): JsonResponse
    {
        try {
            $stats = $this->concoursService->getStats($id);
            return api_success($stats);
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    public function attachSession(string $id, Request $request): JsonResponse
    {
        try {
            $request->validate(['session_id' => 'required|exists:sessions,id']);
            $this->concoursService->attachSession($id, $request->session_id);
            return api_success(null, 'Session attachée au concours avec succès (état: OUVERTE)');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    public function detachSession(string $id, string $sessionId): JsonResponse
    {
        try {
            $this->concoursService->detachSession($id, $sessionId);
            return api_success(null, 'Session détachée du concours avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    public function changeSessionState(string $id, string $sessionId, Request $request): JsonResponse
    {
        try {
            $request->validate(['etat' => 'required|in:OUVERTE,FERMEE']);
            $this->concoursService->changeSessionState($id, $sessionId, $request->etat);
            return api_success(null, 'État de la session modifié avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    public function getSessionState(string $id, string $sessionId): JsonResponse
    {
        try {
            $etat = $this->concoursService->getCurrentSessionState($id, $sessionId);
            return api_success(['etat' => $etat]);
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }
}
