<?php

namespace App\DTOs\Departements;

use Spatie\LaravelData\Data;

class CreateDepartementDTO extends Data
{
    public function __construct(
        public string $code_departement,
        public string $libelle_departement,
        public ?string $ecole_id,
        public ?string $desc_departement,
        public bool $est_actif = true,
    ) {}
}
