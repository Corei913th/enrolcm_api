<?php

namespace App\DTOs\Departements;


class CreateDepartementDTO
{
  public function __construct(
    public readonly string $code_departement,
    public readonly string $libelle_departement,
    public readonly ?string $ecole_id = null,
    public readonly ?string $desc_departement = null,
    public readonly ?bool $est_actif = true,
  ) {}


  public static function fromArray(array $data): self
  {
    return new self(
      code_departement: $data['code_departement'],
      libelle_departement: $data['libelle_departement'],
      ecole_id: $data['ecole_id'] ?? null,
      desc_departement: $data['desc_departement'] ?? null,
      est_actif: $data['est_actif'] ?? true,
    );
  }


  public static function fromRequest(array $validated): self
  {
    return self::fromArray($validated);
  }


  public function toArray(): array
  {
    return [
      'code_departement' => $this->code_departement,
      'libelle_departement' => $this->libelle_departement,
      'ecole_id' => $this->ecole_id,
      'desc_departement' => $this->desc_departement,
      'est_actif' => $this->est_actif,
    ];
  }
}
