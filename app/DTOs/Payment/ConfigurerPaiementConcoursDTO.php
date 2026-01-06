<?php

namespace App\DTOs\Payment;

use Spatie\LaravelData\Data;

class ConfigurerPaiementConcoursDTO extends Data
{
    public function __construct(
        public readonly string $concours_id,
        public readonly string $banque_nom,
        public readonly string $numero_compte,
        public readonly string $nom_beneficiaire,
        public readonly float $montant,
        public readonly string $date_limite,
        public readonly ?string $instructions = null,
        public readonly bool $est_actif = true,
    ) {}
}
