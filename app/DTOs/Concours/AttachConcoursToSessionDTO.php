<?php

namespace App\DTOs\Concours;

class AttachConcoursToSessionDTO
{
  public function __construct(
    public readonly string $session_id,
    public readonly ?string $date_debut = null,
    public readonly ?string $date_limite_depot = null,
    public readonly ?int $nombre_places = null
  ) {}

  public static function fromRequest(array $data): self
  {
    return new self(
      session_id: $data['session_id'],
      date_debut: $data['date_debut'] ?? null,
      date_limite_depot: $data['date_limite_depot'] ?? null,
      nombre_places: $data['nombre_places'] ?? null
    );
  }

  public function toArray(): array
  {
    return array_filter([
      'date_examen' => $this->date_debut,
      'date_limite_depot' => $this->date_limite_depot,
      'nbre_max_places' => $this->nombre_places,
    ], fn($value) => $value !== null);
  }
}
