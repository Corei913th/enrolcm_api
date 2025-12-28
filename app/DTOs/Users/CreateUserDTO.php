<?php

namespace App\DTOs\Users;

use App\Http\Requests\Admin\StoreUserRequest;
use Illuminate\Support\Facades\Hash;
use Spatie\LaravelData\Data;


class CreateUserDTO extends Data
{
    public function __construct(
        public readonly ?string $email,
        public readonly string $user_name,
        public readonly string $mot_de_passe,
        public readonly string $type_utilisateur,
        public readonly ?string $telephone = null,
        public readonly ?string $matricule = null,
        public readonly ?string $matricule_enseignant = null,
        public readonly ?string $specialite = null,
        public readonly ?string $code_agent = null,
        public readonly ?bool $email_verifie,
        public readonly ?string $nationalite_cand = 'Camerounaise',
    ) {}

    public static function fromRequest(StoreUserRequest $request): self
    {
        return new self(
            email: $request->validated('email'),
            user_name: $request->validated('user_name'),
            mot_de_passe : Hash::make($request->validated('mot_de_passe')),
            type_utilisateur : $request->validated('type_utilisateur'),
            telephone: $request->validated('telephone'),
            email_verifie : false,
            matricule: $request->validated('matricule'),
            matricule_enseignant: $request->validated('matricule_enseignant'),
            specialite: $request->validated('specialite'),
            code_agent: $request->validated('code_agent'),
            nationalite_cand: $request->validated('nationalite_cand') ?? 'Camerounaise',
        );
    }
}