<?php

namespace App\DTOs\Payment;

use Spatie\LaravelData\Data;

class CreatePaymentReceiptDTO extends Data
{
    public function __construct(
        public readonly ?string $candidat_id,
        public readonly string $numero_recu,
        public readonly ?string $banque,
        public readonly float $montant,
        public readonly ?string $date_paiement,
        public readonly string $image_path,
        public readonly ?array $ocr_data = null,
        public readonly string $statut_verification = 'en_attente',
    ) {}
}
