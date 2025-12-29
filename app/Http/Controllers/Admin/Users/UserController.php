<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DTOs\Users\CreateUserDTO;
use App\Http\Requests\Admin\Users\StoreUserRequest as UsersStoreUserRequest;
use App\Http\Resources\UtilisateurResource;
use App\Services\Users\UserService;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * Créer un membre du staff (ADMIN, CORRECTEUR, RESPONSABLE_CENTRE).
     *
     * Endpoint : POST /api/admin/staff
     *
     * @param UsersStoreUserRequest $request Requête validée contenant les informations du staff
     *
     * @return \Illuminate\Http\JsonResponse Réponse JSON avec le staff créé ou une erreur
     *
     * @throws \Exception Si la création échoue
     */
    public function store(UsersStoreUserRequest $request)
    {
        try {
            // Vérification des droits d'accès (commentée ici, à activer si nécessaire)
            // if(!$request->user()->hasRole(TypeUtilisateur::ADMIN)){
            //     return api_unauthorized();
            // }

            $dto = CreateUserDTO::fromRequest($request);
            $result = $this->userService->createStaff($dto);

            return api_created([
                'user' => new UtilisateurResource($result),
            ], 'Utilisateur créé avec succès');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }
}