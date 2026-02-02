<?php

namespace App\Http\Controllers;

use App\DTOs\Filieres\CreateFiliereDTO;
use App\Exceptions\Business\FiliereException;
use App\Http\Requests\Filieres\StoreFiliereRequest;
use App\Http\Requests\Filieres\UpdateFiliereRequest;
use App\Http\Resources\FiliereResource;
use App\Services\Domain\Referentiel\FiliereService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FiliereController extends Controller
{
    protected FiliereService $filiereService;

    public function __construct(FiliereService $filiereService)
    {
        $this->filiereService = $filiereService;
    }

    /**
     * Liste des filières avec filtres et pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['est_actif', 'departement_id', 'search', 'per_page']);
            $filieres = $this->filiereService->getAll($filters);

            return api_paginated(
                FiliereResource::collection($filieres)->resource,
                'Liste des filières récupérée avec succès'
            );
        } catch (FiliereException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Afficher une filière spécifique par ID
     */
    public function show(string $id): JsonResponse
    {
        try {
            $filiere = $this->filiereService->getById($id);

            return api_success(
                new FiliereResource($filiere),
                'Filière récupérée avec succès',
            );
        } catch (FiliereException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Afficher une filière par son code
     */
    public function showByCode(string $code): JsonResponse
    {
        try {
            $filiere = $this->filiereService->getByCode($code);

            return api_success(
                new FiliereResource($filiere),
                'Filière récupérée avec succès'
            );
        } catch (FiliereException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Créer une nouvelle filière
     */
    public function store(StoreFiliereRequest $request): JsonResponse
    {
        try {
            $filiereData = CreateFiliereDTO::from($request->validated());
            $filiere = $this->filiereService->create($filiereData);

            return api_created(
                new FiliereResource($filiere),
                'Filière créée avec succès'
            );
        } catch (FiliereException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error('Erreur lors de la création de la filière: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour une filière
     */
    public function update(UpdateFiliereRequest $request, string $id): JsonResponse
    {
        try {
            $filiereData = CreateFiliereDTO::from($request->validated());
            $filiere = $this->filiereService->update($id, $filiereData);

            return api_updated(
                new FiliereResource($filiere),
                'Filière mise à jour avec succès'
            );
        } catch (FiliereException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error('Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une filière
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->filiereService->delete($id);

            return api_deleted('Filière supprimée avec succès');
        } catch (FiliereException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Activer/Désactiver une filière
     */
    public function toggleStatus(string $id): JsonResponse
    {
        try {
            $filiere = $this->filiereService->toggleStatus($id);

            return api_updated(
                new FiliereResource($filiere),
                'Statut de la filière modifié avec succès'
            );
        } catch (FiliereException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Liste des filières actives (pour les sélections)
     */
    public function active(): JsonResponse
    {
        try {
            $filieres = $this->filiereService->getActive();

            return api_success(
                FiliereResource::collection($filieres),
                'Filières actives récupérées avec succès'
            );
        } catch (FiliereException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }
}
