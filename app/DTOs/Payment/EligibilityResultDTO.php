<?php

namespace App\DTOs\Payment;

use Spatie\LaravelData\Data;

class EligibilityResultDTO extends Data
{
    public function __construct(
        public readonly bool $eligible,
        public readonly bool $paymentValid,
        public readonly bool $documentsValid,
        public readonly bool $academicCriteriaValid,
        public readonly array $reasons,
    ) {}
}
