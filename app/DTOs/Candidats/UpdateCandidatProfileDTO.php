<?php

namespace App\DTOs\Candidats;

class UpdateCandidatProfileDTO
{
    public function __construct(
        public readonly array $data
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(data: $data);
    }

    public function toArray(): array
    {
        return array_filter($this->data, fn($value) => $value !== null);
    }
}
