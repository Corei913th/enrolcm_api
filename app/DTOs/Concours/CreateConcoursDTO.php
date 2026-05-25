<?php

namespace App\DTOs\Concours;

class CreateConcoursDTO
{
    public function __construct(
        public readonly string $libelle_concours,
        public readonly string $ecole_id,
        public readonly ?string $description,
        // date_examen supprimé - utiliser planning_epreuves.date_epreuve
        public readonly ?string $date_limite_depot, // OPTIONNEL
        public readonly ?int $nombre_places, // OPTIONNEL - Sera mappé vers nbre_max_places
        public readonly ?string $spec_concours_id, // OPTIONNEL
        public readonly ?string $session_id = null, // OPTIONNEL
        public readonly bool $est_actif = true
    ) {}

    public static function fromRequest($data): self
    {
        // Support pour Request object ou array
        if (is_object($data) && method_exists($data, 'validated')) {
            $data = $data->validated();
        }

        return new self(
            libelle_concours: $data['libelle_concours'],
            ecole_id: $data['ecole_id'],
            description: $data['description'] ?? null,
            // date_examen supprimé
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
            'ecole_id' => $this->ecole_id,
            'description' => $this->description,
            // date_examen supprimé - utiliser planning_epreuves.date_epreuve
            'date_limite_depot' => $this->date_limite_depot,
            'nbre_max_places' => $this->nombre_places, // Mapping correct
            'spec_concours_id' => $this->spec_concours_id,
            'est_actif' => $this->est_actif,
        ], fn ($value) => $value !== null);
    }
}
