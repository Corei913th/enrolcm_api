<?php

namespace App\DTOs\Candidats;

class LoginCandidatDTO
{
    public function __construct(
        public readonly string $pru,
        public readonly string $password
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            pru: $data['pru'],
            password: $data['password']
        );
    }
}
