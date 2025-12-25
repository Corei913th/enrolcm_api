<?php

namespace App\DTOs\Auth;

use Spatie\LaravelData\Data;
use App\Http\Requests\Auth\LoginRequest;

class LoginDTO extends Data
{
    public function __construct(
        public readonly string $user_name,
        public readonly string $mot_de_passe,
    ) {}

    public static function fromRequest(LoginRequest $request): self
    {
        return new self(
            user_name: $request->validated('user_name'),
            mot_de_passe: $request->validated('mot_de_passe'),
        );
    }
}
