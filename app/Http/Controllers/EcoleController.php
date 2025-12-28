<?php

namespace App\Http\Controllers;

use App\DTOs\Ecoles\EcoleData;
use App\Exceptions\Business\EcoleException;
use App\Http\Requests\Ecoles\StoreEcoleRequest;
use App\Http\Requests\Ecoles\UpdateEcoleRequest;
use App\Http\Resources\EcoleResource;
use App\Services\Ecoles\EcoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EcoleController extends Controller
{
    protected EcoleService $ecoleService;

    public function __construct(EcoleService $ecoleService)
    {
        $this->ecoleService = $ecoleService;
    }

    /**
     * Liste des écoles avec filtres et pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['est_actif', 'region', 'search', 'per_page']);
            $ecoles = $this->ecoleService->getAll($filters);

            return api_paginated(
                EcoleResource::collection($ecoles)->resource,
                'Liste des écoles récupérée avec succès'
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Afficher une école spécifique par ID
     */
    public function show(string $id): JsonResponse
    {
        try {
            $ecole = $this->ecoleService->getById($id);
            
            return api_success(
                new EcoleResource($ecole),
                'École récupérée avec succès'
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Afficher une école par son code
     */
    public function showByCode(string $code): JsonResponse
    {
        try {
            $ecole = $this->ecoleService->getByCode($code);
            
            return api_success(
                new EcoleResource($ecole),
                'École récupérée avec succès'
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Créer une nouvelle école
     */
    public function store(StoreEcoleRequest $request): JsonResponse
    {
        try {
            $ecoleData = EcoleData::from($request->validated());
            $ecole = $this->ecoleService->create($ecoleData);

            return api_created(
                new EcoleResource($ecole),
                'École créée avec succès'
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Mettre à jour une école
     */
    public function update(UpdateEcoleRequest $request, string $id): JsonResponse
    {
        try {
            $ecoleData = EcoleData::from($request->validated());
            $ecole = $this->ecoleService->update($id, $ecoleData);

            return api_updated(
                new EcoleResource($ecole),
                'École mise à jour avec succès'
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Supprimer une école
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->ecoleService->delete($id);
            
            return api_deleted('École supprimée avec succès');
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Activer/Désactiver une école
     */
    public function toggleStatus(string $id): JsonResponse
    {
        try {
            $ecole = $this->ecoleService->toggleStatus($id);
            
            return api_updated(
                new EcoleResource($ecole),
                'Statut de l\'école modifié avec succès'
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Liste des écoles actives (pour les sélections)
     */
    public function active(): JsonResponse
    {
        try {
            $ecoles = $this->ecoleService->getActive();
            
            return api_success(
                EcoleResource::collection($ecoles),
                'Écoles actives récupérées avec succès'
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }
}
