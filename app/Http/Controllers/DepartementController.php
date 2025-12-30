<?php

namespace App\Http\Controllers;

use App\DTOs\Departements\CreateDepartementDTO;
use App\Exceptions\Business\DepartementException;
use App\Http\Requests\Departements\StoreDepartementRequest;
use App\Http\Requests\Departements\UpdateDepartementRequest;
use App\Http\Resources\DepartementResource;
use App\Services\Departements\DepartementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartementController extends Controller
{
    protected DepartementService $departementService;

    public function __construct(DepartementService $departementService)
    {
        $this->departementService = $departementService;
    }

    /**
     * Liste des départements avec filtres et pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['est_actif', 'ecole_id', 'search', 'per_page']);
            $departements = $this->departementService->getAll($filters);

            return api_paginated(
                DepartementResource::collection($departements)->resource,
                'Liste des départements récupérée avec succès'
            );
        } catch (DepartementException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Afficher un département spécifique par ID
     */
    public function show(string $id): JsonResponse
    {
        try {
            $departement = $this->departementService->getById($id);
            
            return api_success(
                'Département récupéré avec succès',
                new DepartementResource($departement)
            );
        } catch (DepartementException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Afficher un département par son code
     */
    public function showByCode(string $code): JsonResponse
    {
        try {
            $departement = $this->departementService->getByCode($code);
            
            return api_success(
                'Département récupéré avec succès',
                new DepartementResource($departement)
            );
        } catch (DepartementException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Créer un nouveau département
     */
    public function store(StoreDepartementRequest $request): JsonResponse
    {
        try {
            $departementData = CreateDepartementDTO::from($request->validated());
            $departement = $this->departementService->create($departementData);

            return api_created(
                new DepartementResource($departement),
                'Département créé avec succès'
            );
        } catch (DepartementException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error('Erreur lors de la création du département: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour un département
     */
    public function update(UpdateDepartementRequest $request, string $id): JsonResponse
    {
        try {
            $departementData = CreateDepartementDTO::from($request->validated());
            $departement = $this->departementService->update($id, $departementData);

            return api_updated(
                new DepartementResource($departement),
                'Département mis à jour avec succès'
            );
        } catch (DepartementException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error('Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un département
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->departementService->delete($id);
            
            return api_deleted('Département supprimé avec succès');
        } catch (DepartementException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Activer/Désactiver un département
     */
    public function toggleStatus(string $id): JsonResponse
    {
        try {
            $departement = $this->departementService->toggleStatus($id);
            
            return api_updated(
                new DepartementResource($departement),
                'Statut du département modifié avec succès'
            );
        } catch (DepartementException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Liste des départements actifs (pour les sélections)
     */
    public function active(): JsonResponse
    {
        try {
            $departements = $this->departementService->getActive();
            
            return api_success(
                'Départements actifs récupérés avec succès',
                DepartementResource::collection($departements)
            );
        } catch (DepartementException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }
}
