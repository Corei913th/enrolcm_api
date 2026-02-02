<?php

namespace App\DTOs\Payment;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;

class ManualPaymentDTO extends Data
{
  public function __construct(
    public readonly string $concoursId,
    public readonly string $reference,
    public readonly float $montant,
    public readonly string $banque,
    public readonly Carbon $datePaiement,
    public readonly UploadedFile $preuve,
  ) {}
}
