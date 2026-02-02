<?php

namespace App\Exceptions\Business;

use Exception;

class SpecConcoursException extends Exception
{
    public function __construct(string $message = "Erreur liée à la spécialité du concours", int $code = 400)
    {
        parent::__construct($message, $code);
    }

    public static function notFound(string $id): self
    {
        return new self("Spécialité avec l'ID {$id} introuvable.", 404);
    }

    public static function hasActiveConcours(string $id): self
    {
        return new self("Impossible de supprimer la spécialité {$id} car elle est utilisée par des concours.", 422);
    }
}
