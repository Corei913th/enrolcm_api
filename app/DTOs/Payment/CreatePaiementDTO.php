<?php

namespace App\DTOs\Payment;

use Spatie\LaravelData\Data;
use Illuminate\Http\UploadedFile;

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
