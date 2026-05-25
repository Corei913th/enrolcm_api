<?php

namespace App\Http\Controllers\Centres;

use App\Http\Controllers\Controller;
use App\Http\Requests\Centres\CreateCentreRequest;
use App\Http\Requests\Centres\UpdateCentreRequest;
use App\Http\Resources\CentreResource;
use App\Services\Domain\Referentiel\CentreService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CentreController extends Controller
{
    public function __construct(
        private readonly CentreService $centreService
    ) {}

    /**
     * Liste des centres.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'region_id', 'est_actif']);
        $perPage = $request->input('per_page', 15);
        $centres = $this->centreService->getAll($filters, $perPage);

        return api_paginated($centres, 'Liste des centres');
    }

    /**
     * Créer un centre.
     */
    public function store(CreateCentreRequest $request): JsonResponse
    {
        $centre = $this->centreService->create($request->validated());

        return api_created(new CentreResource($centre), 'Centre créé avec succès');
    }

    /**
     * Détails d'un centre.
     */
    public function show(string $id): JsonResponse
    {
        $centre = $this->centreService->getById($id);

        return api_success(new CentreResource($centre));
    }

    /**
     * Mettre à jour un centre.
     */
    public function update(string $id, UpdateCentreRequest $request): JsonResponse
    {
        $centre = $this->centreService->update($id, $request->validated());

        return api_updated(new CentreResource($centre), 'Centre mis à jour avec succès');
    }

    /**
     * Supprimer un centre.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->centreService->delete($id);

        return api_deleted('Centre supprimé avec succès');
    }

    /**
     * Liste des centres actifs (pour dropdowns).
     */
    public function active(): JsonResponse
    {
        $centres = $this->centreService->getActive();

        return api_success(CentreResource::collection($centres));
    }
}
