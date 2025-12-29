<?php

namespace App\DTOs\Concours;

class UpdateConcoursDTO
{
    public function __construct(
        public readonly ?string $libelle_concours = null,
        public readonly ?string $description = null,
        public readonly ?string $date_debut = null,
        public readonly ?string $date_limite_depot = null,
        public readonly ?int $nombre_places = null,
        public readonly ?bool $est_actif = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            libelle_concours: $data['libelle_concours'] ?? null,
            description: $data['description'] ?? null,
            date_debut: $data['date_debut'] ?? null,
            date_limite_depot: $data['date_limite_depot'] ?? null,
            nombre_places: $data['nombre_places'] ?? null,
            est_actif: $data['est_actif'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'libelle_concours' => $this->libelle_concours,
            'description' => $this->description,
            'date_debut' => $this->date_debut,
            'date_limite_depot' => $this->date_limite_depot,
            'nombre_places' => $this->nombre_places,
            'est_actif' => $this->est_actif,
        ], fn($value) => $value !== null);
    }
}
