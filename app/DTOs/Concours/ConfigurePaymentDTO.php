<?php

namespace App\DTOs\Concours;

class ConfigurePaymentDTO
{
    public function __construct(
        public readonly float $montant,
        public readonly string $banque_nom,
        public readonly string $numero_compte,
        public readonly string $nom_beneficiaire,
        public readonly string $date_limite,
        public readonly ?string $instructions = null,
        public readonly bool $est_actif = true
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            montant: $data['montant'],
            banque_nom: $data['banque_nom'],
            numero_compte: $data['numero_compte'],
            nom_beneficiaire: $data['nom_beneficiaire'],
            date_limite: $data['date_limite'],
            instructions: $data['instructions'] ?? null,
            est_actif: $data['est_actif'] ?? true
        );
    }

    public function toArray(): array
    {
        return [
            'montant' => $this->montant,
            'banque_nom' => $this->banque_nom,
            'numero_compte' => $this->numero_compte,
            'nom_beneficiaire' => $this->nom_beneficiaire,
            'date_limite' => $this->date_limite,
            'instructions' => $this->instructions,
            'est_actif' => $this->est_actif,
        ];
    }
}
