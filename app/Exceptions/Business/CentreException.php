<?php

namespace App\Exceptions\Business;

use Exception;

class CentreException extends Exception
{
    public function __construct(string $message = "Erreur liée au centre", int $code = 400)
    {
        parent::__construct($message, $code);
    }

    public static function notFound(string $id): self
    {
        return new self("Centre avec l'ID {$id} introuvable.", 404);
    }

    public static function hasCandidatures(string $id): self
    {
        return new self("Impossible de supprimer le centre {$id} car il possède des candidatures.", 422);
    }
}
