<?php

namespace App\DTOs\Documents;

use App\Enums\TypeDocument;

class CreateDocumentRequisDTO
{
  public function __construct(
    public readonly string $concoursId,
    public readonly string $nomDocument,
    public readonly string $description,
    public readonly TypeDocument $typeDocument,
    public readonly bool $estObligatoire,
    public readonly array $formatAccepte,
    public readonly int $tailleMaxMb,
    public readonly int $ordreAffichage,
  ) {}

  public static function fromRequest(array $data): self
  {
    return new self(
      concoursId: $data['concours_id'],
      nomDocument: $data['nom_document'],
      description: $data['description'] ?? '',
      typeDocument: TypeDocument::from($data['type_document']),
      estObligatoire: $data['est_obligatoire'] ?? true,
      formatAccepte: $data['format_accepte'] ?? ['pdf', 'jpg', 'jpeg', 'png'],
      tailleMaxMb: $data['taille_max_mb'] ?? 5,
      ordreAffichage: $data['ordre_affichage'] ?? 0,
    );
  }
}
