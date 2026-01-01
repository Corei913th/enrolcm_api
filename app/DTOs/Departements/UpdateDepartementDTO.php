<?php

namespace App\DTOs\Departements;


class UpdateDepartementDTO
{
  public function __construct(
    public readonly ?string $code_departement = null,
    public readonly ?string $libelle_departement = null,
    public readonly ?string $ecole_id = null,
    public readonly ?string $desc_departement = null,
    public readonly ?bool $est_actif = null,
  ) {}


  public static function fromArray(array $data): self
  {
    return new self(
      code_departement: $data['code_departement'] ?? null,
      libelle_departement: $data['libelle_departement'] ?? null,
      ecole_id: $data['ecole_id'] ?? null,
      desc_departement: $data['desc_departement'] ?? null,
      est_actif: $data['est_actif'] ?? null,
    );
  }


  public static function fromRequest(array $validated): self
  {
    return self::fromArray($validated);
  }


  public function toArray(): array
  {
    $data = [];

    if ($this->code_departement !== null) {
      $data['code_departement'] = $this->code_departement;
    }
    if ($this->libelle_departement !== null) {
      $data['libelle_departement'] = $this->libelle_departement;
    }
    if ($this->ecole_id !== null) {
      $data['ecole_id'] = $this->ecole_id;
    }
    if ($this->desc_departement !== null) {
      $data['desc_departement'] = $this->desc_departement;
    }
    if ($this->est_actif !== null) {
      $data['est_actif'] = $this->est_actif;
    }

    return $data;
  }


  public function hasData(): bool
  {
    return !empty($this->toArray());
  }
}
