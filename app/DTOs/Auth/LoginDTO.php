<?php

namespace App\DTOs\Auth;

use App\Http\Requests\Auth\LoginRequest;
use Spatie\LaravelData\Data;

class LoginDTO extends Data
{
    public function __construct(
        public readonly string $email,
        public readonly string $mot_de_passe,
    ) {}

    public static function fromRequest(LoginRequest $request): self
    {
        return new self(
            email: $request->validated('email'),
            mot_de_passe: $request->validated('password'),
        );
    }
}
