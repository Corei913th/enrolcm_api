<?php

namespace App\Http\Controllers\Concours;

use App\Http\Controllers\Controller;
use App\Services\Domain\Concours\SpecService;
use App\Http\Requests\Concours\CreateSpecConcoursRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SpecConcoursController extends Controller
{
    public function __construct(
        private readonly SpecService $specService
    ) {}

    /**
     * Liste des spécialités.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['est_actif', 'search']);
        $perPage = $request->input('per_page', 20);
        $specs = $this->specService->getAll($filters, $perPage);

        return api_paginated($specs, 'Liste des spécialités de concours', \App\Http\Resources\SpecConcoursResource::class);
    }

    /**
     * Détails d'une spécialité.
     */
    public function show(string $id): JsonResponse
    {
        $spec = $this->specService->getById($id);
        return api_success($spec);
    }

    /**
     * Créer une spécialité.
     */
    public function store(CreateSpecConcoursRequest $request): JsonResponse
    {
        $spec = $this->specService->create($request->validated());
        return api_created($spec, 'Spécialité de concours créée avec succès');
    }

    /**
     * Mettre à jour une spécialité.
     */
    public function update(string $id, Request $request): JsonResponse
    {

        $spec = $this->specService->update($id, $request->all());
        return api_success($spec, 'Spécialité de concours mise à jour avec succès');
    }

    /**
     * Supprimer une spécialité.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->specService->delete($id);
        return api_deleted('Spécialité de concours supprimée avec succès');
    }

    /**
     * Activer/Désactiver une spécialité.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        $spec = $this->specService->toggleStatus($id);
        $message = $spec->est_actif ? 'Spécialité activée' : 'Spécialité désactivée';
        return api_success($spec, $message);
    }
    /**
     * Récupérer les données de formulaire (Enums, etc.).
     */
    public function formData(): JsonResponse
    {
        return api_success([
            'series_bac' => \App\Enums\SerieBac::cases(),
            'types_document' => \App\Enums\TypeDocument::cases(),
        ]);
    }
}
