<?php

namespace App\DTOs\Candidats;

class VerifyPRUDTO
{
    public function __construct(
        public readonly string $pru,
        public readonly string $concoursId
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            pru: $data['pru'],
            concoursId: $data['concours_id']
        );
    }
}
