<?php

namespace App\DTOs\Payment;

use Spatie\LaravelData\Data;

class PaymentReceiptDTO extends Data
{
    public function __construct(
        public readonly ?string $numero_recu,
        public readonly ?float $montant,
        public readonly ?string $date_paiement,
        public readonly ?string $banque,
        public readonly float $ocr_confidence,
        public readonly array $raw_data,
    ) {}
}
