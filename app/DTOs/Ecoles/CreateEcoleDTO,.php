<?php

namespace App\DTOs\Ecoles;

use Spatie\LaravelData\Data;

class EcoleData extends Data
{
    public function __construct(
        public string $code_ecole,
        public string $libelle_ecole,
        public ?string $region,
        public ?string $localisation,
        public ?string $email_ecole,
        public ?string $telephone_ecole,
        public ?string $siteweb_ecole,
        public ?string $devise,
        public ?string $bp_ecole,
        public ?string $logo_url,
        public ?string $embleme_ecole,
        public bool $est_actif = true,
    ) {}
}