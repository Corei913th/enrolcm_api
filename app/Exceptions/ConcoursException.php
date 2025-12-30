<?php

namespace App\Exceptions;

use Exception;

class ConcoursException extends Exception
{
    public static function notFound(string $id): self
    {
        return new self("Concours avec l'ID {$id} introuvable.", 404);
    }

    public static function alreadyActive(string $id): self
    {
        return new self("Le concours {$id} est déjà actif.", 400);
    }

    public static function alreadyInactive(string $id): self
    {
        return new self("Le concours {$id} est déjà inactif.", 400);
    }

    public static function cannotDelete(string $id, string $reason): self
    {
        return new self("Impossible de supprimer le concours {$id}: {$reason}", 400);
    }

    public static function invalidDateRange(): self
    {
        return new self("La date de fin doit être postérieure à la date de début.", 400);
    }

    public static function paiementNotConfigured(string $concoursId): self
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

    public static function banqueNotAccepted(string $banque): self
    {
        return new self("La banque '{$banque}' n'est pas acceptée pour ce paiement.", 400);
    }

    public static function ocrConfidenceTooLow(float $confiance, float $minimum): self
    {
        return new self("La confiance OCR ({$confiance}%) est inférieure au minimum requis ({$minimum}%).", 400);
    }

    public static function invalidPaymentType(string $type): self
    {
        return new self("Le type de paiement '{$type}' n'est pas valide.", 400);
    }

    public static function invalidCurrency(string $devise): self
    {
        return new self("La devise '{$devise}' n'est pas supportée. Utilisez XAF, USD ou EUR.", 400);
    }

    public static function paymentExpired(): self
    {
        return new self("La période de paiement est expirée.", 400);
    }

    public static function hasActiveInscriptions(string $id): self
    {
        return new self("Impossible de supprimer le concours {$id}: des inscriptions actives existent.", 400);
    }
}
