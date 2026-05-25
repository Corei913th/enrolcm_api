<?php

namespace App\Exceptions\Business;

use Exception;

class EligibilityException extends Exception
{
    protected $code;

    public function __construct(
        public readonly array $reasons,
        string $message = 'Candidature non éligible',
        int $code = 403
    ) {
        parent::__construct($message);
        $this->code = $code;
    }

    public function render()
    {
        return api_error(
            $this->getMessage(),
            [
                'reasons' => $this->reasons,
                'eligible' => false,
            ],
            $this->code
        );
    }

    public function getReasons(): array
    {
        return $this->reasons;
    }
}
