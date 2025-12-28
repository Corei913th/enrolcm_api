<?php

namespace App\DTOs\Payment;

use Spatie\LaravelData\Data;

class UpdatePaymentReceiptDTO extends Data
{
    public function __construct(
        public readonly ?string $candidat_id = null,
        public readonly ?string $numero_recu = null,
        public readonly ?string $banque = null,
        public readonly ?float $montant = null,
        public readonly ?string $date_paiement = null,
        public readonly ?string $image_path = null,
        public readonly ?array $ocr_data = null,
        public readonly ?string $statut_verification = null,
        public readonly ?string $motif_rejet = null,
    ) {}
}
