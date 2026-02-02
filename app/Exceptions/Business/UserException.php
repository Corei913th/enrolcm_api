<?php

namespace App\Exceptions\Business;

use App\Enums\TypeUtilisateur;

class UserException extends \Exception
{
    protected $severity;

    public function __construct(string $message, int $code = 400, string $severity = 'error')
    {
        parent::__construct($message, $code);
        $this->severity = $severity;
    }

    public function getSeverity(): string
    {
        return $this->severity;
    }

    public function getUserMessage(): string
    {
        return $this->message;
    }
    
    public function toArray(): array
    {
        return [
            'type' => 'UserException',
            'severity' => $this->severity,
            'message' => $this->message,
            'code' => $this->code
        ];
    }
    
    public static function notFound(string $id): self
    {
        return new self("Utilisateur introuvable (ID: {$id})", 404, 'error');
    }
}
