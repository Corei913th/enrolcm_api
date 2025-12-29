<?php

namespace App\Services\Candidats;

use App\DTOs\Auth\CreateCandidatAccountDTO;
use App\Models\Candidat;
use App\Models\Utilisateur;
use App\Services\Roles\RoleService;
use App\Services\Users\UserService;
use Exception;
use Illuminate\Support\Facades\DB;

class  CandidatService
{

    public function __construct(
        private readonly UserService $users,
        private readonly RoleService $roles,
    ) {}


    public function createPartialCandidat(
        CreateCandidatAccountDTO $dto,
        ?Utilisateur $user = null
    ): Utilisateur {
        return DB::transaction(function () use ($dto, $user) {
            
            $existingCandidat = Candidat::where('numero_recu', $dto->user_name)->first();
            if ($existingCandidat) {
                throw new Exception(
                    'Un candidat avec ce numéro de reçu existe déjà.'
                );
            }
            
            $user ??= $this->users->createCandidatAccount($dto);
           
            $candidat = Candidat::create([
                'utilisateur_id'     => $user->id,
                'numero_recu'        => $dto->user_name,
                'nationalite_cand'   => $dto->nationalite_cand,
            ]);
            
            $this->roles->assignDefault($user, 'CANDIDAT');

            return $user->setRelation('candidat', $candidat);
        });
    }
}
