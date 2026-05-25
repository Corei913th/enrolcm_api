<?php

namespace App\DTOs\Documents;

use App\Enums\TypeDocument;
use Illuminate\Http\UploadedFile;

class SubmitDocumentDTO
{
    public function __construct(
        public readonly string $candidatureId,
        public readonly string $documentRequisId,
        public readonly UploadedFile $fichier,
        public readonly TypeDocument $typeDocument,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            candidatureId: $data['candidature_id'],
            documentRequisId: $data['document_requis_id'],
            fichier: $data['fichier'],
            typeDocument: TypeDocument::from($data['type_document']),
        );
    }
}
