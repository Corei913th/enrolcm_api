<?php

namespace App\DTOs\Ecoles;

use Spatie\LaravelData\Data;

class CreateEcoleDTO extends Data
{
    public function __construct(
        // Informations de base
        public string $code_ecole,
        public string $libelle_ecole,
        public ?string $libelle_ecole_en = null,

        // Localisation
        public ?string $region = null,
        public ?string $localisation = null,
        public ?string $adresse_complete = null,
        public ?string $ville = null,

        // Contact
        public ?string $telephone_ecole = null,
        public ?string $fax = null,
        public ?string $telephone_2 = null,
        public ?string $email_ecole = null,
        public ?string $siteweb_ecole = null,
        public ?string $bp_ecole = null,

        // Identité visuelle
        public ?string $devise = null,
        public ?string $slogan = null,

        // Direction
        public ?string $nom_directeur = null,
        public ?string $titre_directeur = null,

        // Institution tutelle
        public ?string $nom_institution_tutelle = null,
        public ?string $nom_institution_tutelle_en = null,
        public ?string $numero_agrement = null,
        public ?string $date_creation = null,
        public ?string $logo_institution_tutelle_url = null,

        // URLs externes (peuvent être fournies manuellement)
        public ?string $logo_url = null,
        public ?string $embleme_ecole = null,

        // Statut
        public bool $est_actif = true,

        // Mentions légales
        public ?string $mentions_legales = null,
    ) {}
}
