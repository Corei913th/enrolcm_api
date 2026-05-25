<?php

namespace App\DTOs\Auth;

use App\Enums\TypeUtilisateur;
use App\Http\Requests\Candidats\CreateCandidatAccountRequest;
use Illuminate\Support\Facades\Hash;
use Spatie\LaravelData\Data;


class CreateCandidatAccountDTO extends Data
{
    public function __construct(
        public readonly string $user_name,
        public readonly string $mot_de_passe,
        public readonly ?string $type_utilisateur,
        public readonly ?bool $email_verifie,
        public readonly ?string $nationalite_cand = 'Camerounaise',
    ) {}

    public static function fromRequest(CreateCandidatAccountRequest $request): self
    {
        return new self(
            user_name: $request->validated('user_name'),
            mot_de_passe : Hash::make($request->validated('mot_de_passe')),
            type_utilisateur : TypeUtilisateur::CANDIDAT,
            email_verifie : false,
            nationalite_cand: $request->validated('nationalite_cand') ?? 'Camerounaise',
        );
    }
}