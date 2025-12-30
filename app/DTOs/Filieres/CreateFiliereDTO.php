<?php

namespace App\DTOs\Filieres;

use Spatie\LaravelData\Data;

class CreateFiliereDTO extends Data
{
    public function __construct(
        public string $code_filiere,
        public string $libelle_filiere,
        public ?string $departement_id,
        public ?string $desc_filiere,
        public bool $est_actif = true,
    ) {}
}
