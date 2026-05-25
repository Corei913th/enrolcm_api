<?php

use App\Enums\StatutVerificationDocument;

class DocumentStatutDTO
{
    public function __construct(
        public string $documentRequisId,
        public string $nom,
        public bool $estObligatoire,
        public StatutVerificationDocument $statut,
        public ?string $commentaire
    ) {}
}
