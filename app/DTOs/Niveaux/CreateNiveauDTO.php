<?php

namespace App\DTOs\Niveaux;

use Spatie\LaravelData\Data;

class CreateNiveauDTO extends Data
{
    public function __construct(
        public string $code_niveau,
        public string $libelle_niveau,
        public ?string $filiere_id,
        public ?int $ordre,
        public ?string $desc_niveau,
        public bool $est_actif = true,
    ) {}
}
