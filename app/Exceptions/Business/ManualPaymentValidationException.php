<?php

namespace App\Exceptions\Business;

use Exception;

class ManualPaymentValidationException extends Exception
{
    protected $code;

    public function __construct(
        public readonly array $errors,
        string $message = 'Données de paiement invalides',
        int $code = 422
    ) {
        parent::__construct($message);
        $this->code = $code;
    }

    public function render()
    {
        return api_error(
            $this->getMessage(),
            [
                'errors' => $this->errors,
                'valid' => false,
            ],
            $this->code
        );
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
