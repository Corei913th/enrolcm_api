<?php

namespace App\DTOs\Concours;

use Spatie\LaravelData\Data;

class ConfigurerPaiementDTO extends Data
{
    public function __construct(
        public readonly string $banque_nom,
        public readonly string $numero_compte,
        public readonly string $nom_beneficiaire,
        public readonly float $montant,
        public readonly string $date_limite,
        public readonly ?string $instructions = null,
        public readonly bool $est_actif = true,
    ) {}
}
