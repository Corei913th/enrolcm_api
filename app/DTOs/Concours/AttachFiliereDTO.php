<?php

namespace App\DTOs\Concours;

class AttachFiliereDTO
{
    public function __construct(
        public readonly string $filiere_id,
        public readonly int $nombre_places
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            filiere_id: $data['filiere_id'],
            nombre_places: $data['nombre_places']
        );
    }
}
