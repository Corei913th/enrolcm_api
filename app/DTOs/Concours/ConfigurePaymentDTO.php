<?php

namespace App\DTOs\Concours;

class ConfigurePaymentDTO
{
    public function __construct(
        // Informations bancaires de base
        public readonly string $banque_nom,
        public readonly string $numero_compte,
        public readonly string $nom_beneficiaire,

        // Informations bancaires complètes
        public readonly ?string $devise = 'XAF',
        public readonly ?string $code_banque = null,
        public readonly ?string $agence_banque = null,
        public readonly ?string $iban = null,

        // Configuration paiement
        public readonly ?string $type_paiement = 'virement',
        public readonly ?array $banques_acceptees = null,
        public readonly ?float $frais_paiement = 0,

        // Montant et date
        public readonly float $montant,
        public readonly string $date_limite,

        // Validation et sécurité
        public readonly ?string $reference_format = null,
        public readonly ?float $minimum_confiance_ocr = 85.0,
        public readonly ?bool $validation_auto = true,

        // Instructions et métadonnées
        public readonly ?string $instructions = null,
        public readonly ?string $commentaires = null,
        public readonly bool $est_actif = true
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            // Informations bancaires de base
            banque_nom: $data['banque_nom'],
            numero_compte: $data['numero_compte'],
            nom_beneficiaire: $data['nom_beneficiaire'],

            // Informations bancaires complètes
            devise: $data['devise'] ?? 'XAF',
            code_banque: $data['code_banque'] ?? null,
            agence_banque: $data['agence_banque'] ?? null,
            iban: $data['iban'] ?? null,

            // Configuration paiement
            type_paiement: $data['type_paiement'] ?? 'virement',
            banques_acceptees: $data['banques_acceptees'] ?? null,
            frais_paiement: $data['frais_paiement'] ?? 0,

            // Montant et date
            montant: $data['montant'],
            date_limite: $data['date_limite'],

            // Validation et sécurité
            reference_format: $data['reference_format'] ?? null,
            minimum_confiance_ocr: $data['minimum_confiance_ocr'] ?? 85.0,
            validation_auto: $data['validation_auto'] ?? true,

            // Instructions et métadonnées
            instructions: $data['instructions'] ?? null,
            commentaires: $data['commentaires'] ?? null,
            est_actif: $data['est_actif'] ?? true
        );
    }

    public function toArray(): array
    {
        return [
            // Informations bancaires de base
            'banque_nom' => $this->banque_nom,
            'numero_compte' => $this->numero_compte,
            'nom_beneficiaire' => $this->nom_beneficiaire,

            // Informations bancaires complètes
            'devise' => $this->devise,
            'code_banque' => $this->code_banque,
            'agence_banque' => $this->agence_banque,
            'iban' => $this->iban,

            // Configuration paiement
            'type_paiement' => $this->type_paiement,
            'banques_acceptees' => $this->banques_acceptees,
            'frais_paiement' => $this->frais_paiement,

            // Montant et date
            'montant' => $this->montant,
            'date_limite' => $this->date_limite,

            // Validation et sécurité
            'reference_format' => $this->reference_format,
            'minimum_confiance_ocr' => $this->minimum_confiance_ocr,
            'validation_auto' => $this->validation_auto,

            // Instructions et métadonnées
            'instructions' => $this->instructions,
            'commentaires' => $this->commentaires,
            'est_actif' => $this->est_actif,
        ];
    }
}
