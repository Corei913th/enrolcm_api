<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DTOs\Users\CreateUserDTO;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Resources\UtilisateurResource;
use App\Services\Users\UserService;

class UserController extends Controller
{
    
  

    public function __construct(
        private readonly UserService $staffService
      )
    {
        
    }

    
    /**
     * Creation du staff (ADMIN, CORRECTEUR, RESPONSABLE_CENTRE)
     */
    public function store(StoreUserRequest $request)
    {
        try {
            /*if(!$request->user()->hasRole(TypeUtilisateur::ADMIN)){
               return api_unauthorized();
            }*/
            $dto = CreateUserDTO::fromRequest($request);
            $result = $this->staffService->createStaff($dto);

            return api_created([
                'staff' => new UtilisateurResource($result),
            ], 'Staff crée avec succès ');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 400);
        }
    }

    
}
