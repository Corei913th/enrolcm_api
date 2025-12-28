<?php

namespace App\DTOs\Ecoles;

use Spatie\LaravelData\Data;

class EcoleData extends Data
{
    public function __construct(
        public string $code_ecole,
        public string $libelle_ecole,
        public ?string $region = null,
        public ?string $localisation = null,
        public ?string $logo_url = null,
        public ?string $embleme_ecole = null,
        public ?string $photo_facade = null,
        public ?string $document_agrement = null,
        public ?string $bp_ecole = null,
        public ?string $email_ecole = null,
        public ?string $siteweb_ecole = null,
        public ?string $telephone_ecole = null,
        public ?string $fax_ecole = null,
        public ?string $devise = null,
        public ?string $directeur_nom = null,
        public ?string $directeur_email = null,
        public ?string $directeur_telephone = null,
        public ?string $numero_agrement = null,
        public ?string $date_creation = null,
        public ?string $type_etablissement = null,
        public bool $est_actif = true,
        public ?string $description = null,
    ) {}
}
