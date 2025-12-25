<?php

namespace App\DTOs\Auth;

use Spatie\LaravelData\Data;
use App\Http\Requests\Auth\RegisterCandidatRequest;

class RegisterCandidatDTO extends Data
{
    public function __construct(
        public readonly string $user_name, // Numéro de reçu
        public readonly string $mot_de_passe,
        public readonly ?string $nationalite_cand = 'Camerounaise',
    ) {}

    public static function fromRequest(RegisterCandidatRequest $request): self
    {
        return new self(
            user_name: $request->validated('user_name'),
            mot_de_passe: $request->validated('mot_de_passe'),
            nationalite_cand: $request->validated('nationalite_cand') ?? 'Camerounaise',
        );
    }
}
