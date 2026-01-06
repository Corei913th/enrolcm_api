<?php

namespace App\DTOs\Concours;

class CreateConcoursDTO
{
    public function __construct(
        public readonly string $libelle_concours,
        public readonly ?string $description,
        public readonly ?string $date_debut, // OPTIONNEL - Sera mappé vers date_examen
        public readonly ?string $date_limite_depot, // OPTIONNEL
        public readonly ?int $nombre_places, // OPTIONNEL - Sera mappé vers nbre_max_places
        public readonly ?string $spec_concours_id, // OPTIONNEL
        public readonly ?string $session_id = null, // OPTIONNEL
        public readonly bool $est_actif = true
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            libelle_concours: $data['libelle_concours'],
            description: $data['description'] ?? null,
            date_debut: $data['date_debut'] ?? null,
            date_limite_depot: $data['date_limite_depot'] ?? null,
            nombre_places: $data['nombre_places'] ?? null,
            spec_concours_id: $data['spec_concours_id'] ?? null,
            session_id: $data['session_id'] ?? null,
            est_actif: $data['est_actif'] ?? true
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'libelle_concours' => $this->libelle_concours,
            'description' => $this->description,
            'date_examen' => $this->date_debut, // Mapping correct
            'date_limite_depot' => $this->date_limite_depot,
            'nbre_max_places' => $this->nombre_places, // Mapping correct
            'spec_concours_id' => $this->spec_concours_id,
            'est_actif' => $this->est_actif,
        ], fn($value) => $value !== null);
    }
}
