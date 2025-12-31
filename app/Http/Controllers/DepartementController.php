<?php

namespace App\Http\Controllers;

use App\Services\Departements\DepartementService;
use App\Http\Requests\Departements\StoreDepartementRequest;
use App\Http\Requests\Departements\UpdateDepartementRequest;
use App\DTOs\Departements\CreateDepartementDTO;
use App\DTOs\Departements\UpdateDepartementDTO;
use App\Exceptions\Business\DepartementException;
use App\Http\Resources\DepartementResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class DepartementController extends Controller
{
  public function __construct(
    private readonly DepartementService $departementService
  ) {}

  /**
   * Lister tous les départements.
   *
   * @param Request $request Paramètres de filtrage et pagination
   *
   * @return JsonResponse Liste des départements
   */
  public function index(Request $request): JsonResponse
  {
    try {
      $filters = $request->only(['est_actif', 'ecole_id', 'search']);
      $departements = $this->departementService->getAll($filters);

      return api_success(DepartementResource::collection($departements), 'Départements récupérés avec succès');
    } catch (\Exception $e) {
      return api_error('Erreur lors de la récupération des départements: ' . $e->getMessage(), null, 500);
    }
  }

  /**
   * Créer un département.
   * @param StoreDepartementRequest $request Requête validée
   *
   * @return JsonResponse Département créé
   */
  public function store(StoreDepartementRequest $request): JsonResponse
  {
    try {
      $dto = CreateDepartementDTO::fromRequest($request->validated());
      $departement = $this->departementService->create($dto);

      return api_created(new DepartementResource($departement), 'Département créé avec succès');
    } catch (DepartementException $e) {
      return api_error($e->getMessage(), null, $e->getCode());
    }
  }

  /**
   * Afficher un département.
   *
   * @param string $id ID du département
   *
   * @return JsonResponse Détails du département
   */
  public function show(string $id): JsonResponse
  {
    try {
      $departement = $this->departementService->findById($id);
      return api_success(new DepartementResource($departement->load(['ecole', 'filieres'])), 'Département récupéré avec succès');
    } catch (DepartementException $e) {
      return api_error($e->getMessage(), null, $e->getCode());
    }
  }

  /**
   * Mettre à jour un département.
   * @param string $id ID du département
   * @param UpdateDepartementRequest $request Requête validée
   *
   * @return JsonResponse Département mis à jour
   */
  public function update(string $id, UpdateDepartementRequest $request): JsonResponse
  {
    try {
      $dto = UpdateDepartementDTO::fromRequest($request->validated());

      if (!$dto->hasData()) {
        return api_error('Aucune donnée à mettre à jour.', null, 400);
      }

      $departement = $this->departementService->update($id, $dto);

      return api_success(new DepartementResource($departement), 'Département mis à jour avec succès');
    } catch (DepartementException $e) {
      return api_error($e->getMessage(), null, $e->getCode());
    }
  }

  /**
   * Supprimer un département.
   * @param string $id ID du département
   *
   * @return JsonResponse Confirmation de suppression
   */
  public function destroy(string $id): JsonResponse
  {
    try {
      $this->departementService->delete($id);
      return api_success(null, 'Département supprimé avec succès');
    } catch (DepartementException $e) {
      return api_error($e->getMessage(), null, $e->getCode());
    }
  }

  /**
   * Activer un département.
   * @param string $id ID du département
   *
   * @return JsonResponse Département activé
   */
  public function activate(string $id): JsonResponse
  {
    try {
      $departement = $this->departementService->activate($id);
      return api_success(new DepartementResource($departement), 'Département activé avec succès');
    } catch (DepartementException $e) {
      return api_error($e->getMessage(), null, $e->getCode());
    }
  }

  /**
   * Désactiver un département.
   * @param string $id ID du département
   *
   * @return JsonResponse Département désactivé
   */
  public function deactivate(string $id): JsonResponse
  {
    try {
      $departement = $this->departementService->deactivate($id);
      return api_success(new DepartementResource($departement), 'Département désactivé avec succès');
    } catch (DepartementException $e) {
      return api_error($e->getMessage(), null, $e->getCode());
    }
  }

  /**
   * Obtenir les statistiques d'un département.
   *
   * @param string $id ID du département
   *
   * @return JsonResponse Statistiques du département
   */
  public function stats(string $id): JsonResponse
  {
    try {
      $stats = $this->departementService->getStats($id);
      return api_success($stats, 'Statistiques récupérées avec succès');
    } catch (DepartementException $e) {
      return api_error($e->getMessage(), null, $e->getCode());
    }
  }
}
