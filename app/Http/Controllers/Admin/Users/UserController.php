<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\DTOs\Users\CreateUserDTO;
use App\Http\Requests\Admin\Users\StoreUserRequest;
use App\Http\Resources\UtilisateurResource;
use App\Services\Domain\User\UserService;

use App\Http\Requests\Admin\Users\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * Liste des utilisateurs.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'type_utilisateur', 'est_actif']);
        $perPage = $request->input('per_page', 15);
        $users = $this->userService->getAll($filters, $perPage);
        return api_paginated($users, 'Liste des utilisateurs');
    }

    /**
     * Créer un membre du staff (ADMIN, CORRECTEUR, RESPONSABLE_CENTRE).
     * @param StoreUserRequest $request Requête validée contenant les informations du staff
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $dto = CreateUserDTO::fromRequest($request);
            $result = $this->userService->createStaff($dto);

            return api_created([
                'user' => new UtilisateurResource($result),
            ], 'Utilisateur créé avec succès');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    /**
     * Détails d'un utilisateur.
     */
    public function show(string $id): JsonResponse
    {
        $user = $this->userService->getById($id);
        return api_success(new UtilisateurResource($user));
    }

    /**
     * Mettre à jour un utilisateur.
     */
    public function update(string $id, UpdateUserRequest $request): JsonResponse
    {
        $user = $this->userService->update($id, $request->validated());
        return api_success(new UtilisateurResource($user), 'Utilisateur mis à jour avec succès');
    }

    /**
     * Supprimer (désactiver ou supprimer) un utilisateur.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->userService->delete($id);
        return api_deleted('Utilisateur supprimé avec succès');
    }
}
