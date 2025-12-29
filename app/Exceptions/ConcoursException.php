<?php

namespace App\Exceptions;

use Exception;

class ConcoursException extends Exception
{
    public static function notFound(int $id): self
    {
        return new self("Concours avec l'ID {$id} introuvable.", 404);
    }

    public static function alreadyActive(int $id): self
    {
        return new self("Le concours {$id} est déjà actif.", 400);
    }

    public static function alreadyInactive(int $id): self
    {
        return new self("Le concours {$id} est déjà inactif.", 400);
    }

    public static function cannotDelete(int $id, string $reason): self
    {
        return new self("Impossible de supprimer le concours {$id}: {$reason}", 400);
    }

    public static function invalidDateRange(): self
    {
        return new self("La date de fin doit être postérieure à la date de début.", 400);
    }

    public static function paiementNotConfigured(int $concoursId): self
    {
        return new self("Le paiement n'est pas configuré pour le concours {$concoursId}.", 400);
    }

    public static function invalidMontant(): self
    {
        return new self("Le montant du paiement doit être supérieur à 0.", 400);
    }

    public static function invalidDateLimite(): self
    {
        return new self("La date limite de paiement doit être antérieure à la date de fin du concours.", 400);
    }

    public static function hasActiveInscriptions(int $id): self
    {
        return new self("Impossible de supprimer le concours {$id}: des inscriptions actives existent.", 400);
    }
}
