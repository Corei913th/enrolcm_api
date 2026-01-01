<?php

namespace App\DTOs\Matieres;

use Spatie\LaravelData\Data;

class CreateMatiereDTO extends Data
{
    public function __construct(
        public string $code_matiere,
        public string $libelle_matiere,
        public ?int $coefficient,
        public bool $est_actif = true,
    ) {}
}
