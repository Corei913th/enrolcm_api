<?php

namespace App\DTOs\Payment;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;

class CreatePaiementDTO extends Data
{
    public function __construct(
        public readonly string $candidat_id,
        public readonly string $concours_id,
        public readonly string $reference,
        public readonly float $montant,
        public readonly UploadedFile $preuve_paiement,
    ) {}
}
