<?php

namespace App\Exceptions\Business;

use Exception;

class FiliereException extends Exception
{
    protected $code;

    public function __construct(string $message = "Erreur liée aux filières", int $code = 500)
    {
        parent::__construct($message);
        $this->code = $code;
    }

    public function render()
    {
        return api_error($this->getMessage(), null, $this->code);
    }
}
