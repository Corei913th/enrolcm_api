<?php

namespace App\Exceptions\Business;

use Exception;
use Illuminate\Http\JsonResponse;

class ResourceNotFoundException extends Exception
{
    protected $code = 404;

    public function __construct(string $resource = 'Ressource', ?string $identifier = null)
    {
        $message = $identifier 
            ? "{$resource} avec l'identifiant '{$identifier}' introuvable."
            : "{$resource} introuvable.";
        
        parent::__construct($message, $this->code);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->message,
            'error_code' => 'RESOURCE_NOT_FOUND',
        ], $this->code);
    }
}
