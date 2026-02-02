<?php

namespace App\Exceptions;

use Exception;

class EpreuveException extends Exception
{
  public static function notFound(string $epreuveId): self
  {
    return new self("Épreuve avec l'ID {$epreuveId} introuvable", 404);
  }

  public static function cannotDelete(string $epreuveId, string $reason): self
  {
    return new self("Impossible de supprimer l'épreuve {$epreuveId}: {$reason}", 400);
  }

  public static function invalidData(string $message): self
  {
    return new self($message, 422);
  }
}
