<?php

namespace App\Http\Controllers;

use App\DTOs\Matieres\CreateMatiereDTO;
use App\Exceptions\Business\MatiereException;
use App\Http\Requests\Matieres\StoreMatiereRequest;
use App\Http\Requests\Matieres\UpdateMatiereRequest;
use App\Http\Resources\MatiereResource;
use App\Services\Domain\Referentiel\MatiereService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatiereController extends Controller
{
    public function __construct(
        private readonly MatiereService $matiereService
    ) {}

    /**
     * Liste des matières avec filtres et pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['est_actif', 'search', 'per_page']);
            $matieres = $this->matiereService->getAll($filters);

            return api_paginated(
                MatiereResource::collection($matieres)->resource,
                'Liste des matières récupérée avec succès'
            );
        } catch (MatiereException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Afficher une matière spécifique par ID
     */
    public function show(string $id): JsonResponse
    {
        try {
            $matiere = $this->matiereService->getById($id);

            return api_success(
                new MatiereResource($matiere),
                'Matière récupérée avec succès'
            );
        } catch (MatiereException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Afficher une matière par son code
     */
    public function showByCode(string $code): JsonResponse
    {
        try {
            $matiere = $this->matiereService->getByCode($code);

            return api_success(
                new MatiereResource($matiere),
                'Matière récupérée avec succès'
            );
        } catch (MatiereException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Créer une nouvelle matière
     */
    public function store(StoreMatiereRequest $request): JsonResponse
    {
        try {
            $matiereData = CreateMatiereDTO::from($request->validated());
            $matiere = $this->matiereService->create($matiereData);

            return api_created(
                new MatiereResource($matiere),
                'Matière créée avec succès'
            );
        } catch (MatiereException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error('Erreur lors de la création de la matière: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour une matière
     */
    public function update(UpdateMatiereRequest $request, string $id): JsonResponse
    {
        try {
            $matiereData = CreateMatiereDTO::from($request->validated());
            $matiere = $this->matiereService->update($id, $matiereData);

            return api_updated(
                new MatiereResource($matiere),
                'Matière mise à jour avec succès'
            );
        } catch (MatiereException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error('Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une matière
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->matiereService->delete($id);

            return api_deleted('Matière supprimée avec succès');
        } catch (MatiereException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Activer/Désactiver une matière
     */
    public function toggleStatus(string $id): JsonResponse
    {
        try {
            $matiere = $this->matiereService->toggleStatus($id);

            return api_updated(
                new MatiereResource($matiere),
                'Statut de la matière modifié avec succès'
            );
        } catch (MatiereException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Liste des matières actives (pour les sélections)
     */
    public function active(): JsonResponse
    {
        try {
            $matieres = $this->matiereService->getActive();

            return api_success(
                MatiereResource::collection($matieres),
                'Matières actives récupérées avec succès'
            );
        } catch (MatiereException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }
}
