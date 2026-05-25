<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\EpreuveException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Epreuves\CreateEpreuveRequest;
use App\Http\Requests\Epreuves\UpdateEpreuveRequest;
use App\Http\Resources\EpreuveResource;
use App\Services\Domain\Examen\EpreuveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EpreuveController extends Controller
{
    public function __construct(
        private readonly EpreuveService $epreuveService
    ) {}

    /**
     * Liste toutes les épreuves.
     *
     * GET /api/admin/epreuves
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'type_epreuve', 'est_actif']);
            $perPage = $request->input('per_page', 15);
            $epreuves = $this->epreuveService->getAll($filters, $perPage);

            return api_paginated($epreuves, 'Liste des épreuves');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Créer une nouvelle épreuve.
     *
     * POST /api/admin/epreuves
     */
    public function store(CreateEpreuveRequest $request): JsonResponse
    {
        try {
            $epreuve = $this->epreuveService->create($request->validated());

            return api_created(new EpreuveResource($epreuve), 'Épreuve créée avec succès');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Afficher une épreuve.
     *
     * GET /api/admin/epreuves/{id}
     */
    public function show(string $id): JsonResponse
    {
        try {
            $epreuve = $this->epreuveService->getEpreuveById($id);

            return api_success(new EpreuveResource($epreuve), 'Détails de l\'épreuve');
        } catch (EpreuveException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Mettre à jour une épreuve.
     *
     * PUT /api/admin/epreuves/{id}
     */
    public function update(UpdateEpreuveRequest $request, string $id): JsonResponse
    {
        try {
            $epreuve = $this->epreuveService->update($id, $request->validated());

            return api_success(new EpreuveResource($epreuve), 'Épreuve mise à jour avec succès');
        } catch (EpreuveException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Supprimer une épreuve.
     *
     * DELETE /api/admin/epreuves/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->epreuveService->delete($id);

            return api_success(null, 'Épreuve supprimée avec succès');
        } catch (EpreuveException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }
}
