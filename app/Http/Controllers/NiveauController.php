<?php

namespace App\Http\Controllers;

use App\DTOs\Niveaux\CreateNiveauDTO;
use App\Exceptions\Business\NiveauException;
use App\Http\Requests\Niveaux\StoreNiveauRequest;
use App\Http\Requests\Niveaux\UpdateNiveauRequest;
use App\Http\Resources\NiveauResource;
use App\Services\Niveaux\NiveauService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NiveauController extends Controller
{
    protected NiveauService $niveauService;

    public function __construct(NiveauService $niveauService)
    {
        $this->niveauService = $niveauService;
    }

    /**
     * Liste des niveaux avec filtres et pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['est_actif', 'filiere_id', 'search', 'per_page']);
            $niveaux = $this->niveauService->getAll($filters);

            return api_paginated(
                NiveauResource::collection($niveaux)->resource,
                'Liste des niveaux récupérée avec succès'
            );
        } catch (NiveauException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Afficher un niveau spécifique par ID
     */
    public function show(string $id): JsonResponse
    {
        try {
            $niveau = $this->niveauService->getById($id);
            
            return api_success(
                'Niveau récupéré avec succès',
                new NiveauResource($niveau)
            );
        } catch (NiveauException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Afficher un niveau par son code
     */
    public function showByCode(string $code): JsonResponse
    {
        try {
            $niveau = $this->niveauService->getByCode($code);
            
            return api_success(
                'Niveau récupéré avec succès',
                new NiveauResource($niveau)
            );
        } catch (NiveauException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Créer un nouveau niveau
     */
    public function store(StoreNiveauRequest $request): JsonResponse
    {
        try {
            $niveauData = CreateNiveauDTO::from($request->validated());
            $niveau = $this->niveauService->create($niveauData);

            return api_created(
                new NiveauResource($niveau),
                'Niveau créé avec succès'
            );
        } catch (NiveauException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error('Erreur lors de la création du niveau: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour un niveau
     */
    public function update(UpdateNiveauRequest $request, string $id): JsonResponse
    {
        try {
            $niveauData = CreateNiveauDTO::from($request->validated());
            $niveau = $this->niveauService->update($id, $niveauData);

            return api_updated(
                new NiveauResource($niveau),
                'Niveau mis à jour avec succès'
            );
        } catch (NiveauException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error('Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un niveau
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->niveauService->delete($id);
            
            return api_deleted('Niveau supprimé avec succès');
        } catch (NiveauException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Activer/Désactiver un niveau
     */
    public function toggleStatus(string $id): JsonResponse
    {
        try {
            $niveau = $this->niveauService->toggleStatus($id);
            
            return api_updated(
                new NiveauResource($niveau),
                'Statut du niveau modifié avec succès'
            );
        } catch (NiveauException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Liste des niveaux actifs (pour les sélections)
     */
    public function active(): JsonResponse
    {
        try {
            $niveaux = $this->niveauService->getActive();
            
            return api_success(
                'Niveaux actifs récupérés avec succès',
                NiveauResource::collection($niveaux)
            );
        } catch (NiveauException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }
}
