<?php

namespace App\DTOs\Documents;

class ValidateDocumentDTO
{
  public function __construct(
    public readonly string $statut,
    public readonly ?string $commentaire,
  ) {}

  public static function fromRequest(array $data): self
  {
    return new self(
      statut: $data['statut'],
      commentaire: $data['commentaire'] ?? null,
    );
  }
}
