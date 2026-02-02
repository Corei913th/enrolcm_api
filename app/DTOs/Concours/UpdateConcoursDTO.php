<?php

namespace App\DTOs\Concours;

class UpdateConcoursDTO
{
    public function __construct(
        public readonly ?string $ecole_id = null,
        public readonly ?string $spec_concours_id = null,
        public readonly ?string $libelle_concours = null,
        public readonly ?string $description = null,
        // date_examen supprimé - utiliser planning_epreuves.date_epreuve
        public readonly ?string $date_limite_depot = null,
        public readonly ?int $nombre_places = null,
        public readonly ?string $session_id = null,
        public readonly ?bool $est_actif = null
    ) {}

    public static function fromRequest($data): self
    {
        // Support pour Request object ou array
        if (is_object($data) && method_exists($data, 'validated')) {
            $data = $data->validated();
        }

        return new self(
            ecole_id: $data['ecole_id'] ?? null,
            spec_concours_id: $data['spec_concours_id'] ?? null,
            libelle_concours: $data['libelle_concours'] ?? null,
            description: $data['description'] ?? null,
            // date_examen supprimé
            date_limite_depot: $data['date_limite_depot'] ?? null,
            nombre_places: $data['nombre_places'] ?? null,
            session_id: $data['session_id'] ?? null,
            est_actif: $data['est_actif'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'ecole_id' => $this->ecole_id,
            'spec_concours_id' => $this->spec_concours_id,
            'libelle_concours' => $this->libelle_concours,
            'description' => $this->description,
            // date_examen supprimé - utiliser planning_epreuves.date_epreuve
            'date_limite_depot' => $this->date_limite_depot,
            'nbre_max_places' => $this->nombre_places, // Mapping vers DB
            'est_actif' => $this->est_actif,
        ], fn($value) => $value !== null);
    }
}
