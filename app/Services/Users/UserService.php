<?php

namespace App\Services\Users;

use App\DTOs\Auth\RegisterCandidatDTO;
use App\Enums\TypeUtilisateur;
use App\Models\Candidat;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createCandidatAccount(RegisterCandidatDTO $dto): Utilisateur
    {
        return DB::transaction(function () use ($dto) {
            $user = Utilisateur::create([
                'user_name' => $dto->user_name,
                'mot_de_passe' => Hash::make($dto->mot_de_passe),
                'type_utilisateur' => TypeUtilisateur::CANDIDAT,
                'est_actif' => true,
                'email_verifie' => false,
            ]);
            return $user;
        });
    }

    public function createStaff(array $data, string $type): Utilisateur
    {
        return DB::transaction(function () use ($data, $type) {
            $user = Utilisateur::create([
                'user_name' => $data['user_name'],
                'mot_de_passe' => Hash::make($data['mot_de_passe']),
                'type_utilisateur' => $type,
                'est_actif' => $data['est_actif'] ?? true,
            ]);

            return $user;
        });
    }
}
