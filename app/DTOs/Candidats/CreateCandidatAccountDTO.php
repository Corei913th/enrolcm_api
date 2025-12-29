<?php

namespace App\DTOs\Candidats;

use App\Http\Requests\Candidats\CreateCandidatAccountRequest;
use Spatie\LaravelData\Data;


class CreateCandidatAccountDTO extends Data
{
    public function __construct(
        public readonly string $user_name,
        public readonly string $mot_de_passe,
        public readonly ?string $nationalite_cand = 'Camerounaise',
    ) {}

    public static function fromRequest(CreateCandidatAccountRequest $request): self
    {
        return new self(
            user_name: $request->validated('user_name'),
            mot_de_passe: $request->validated('mot_de_passe'),
            nationalite_cand: $request->validated('nationalite_cand') ?? 'Camerounaise',
        );
    }
}