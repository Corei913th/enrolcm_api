<?php

namespace App\Exceptions;

use Exception;

class PlanningException extends Exception
{
    public static function dateInvalide(string $dateEpreuve, string $dateExamen): self
    {
        return new self("La date de l'épreuve ({$dateEpreuve}) doit correspondre à la date d'examen du concours ({$dateExamen})", 422);
    }

    public static function heuresInvalides(string $heureDebut, string $heureFin): self
    {
        return new self("L'heure de fin ({$heureFin}) doit être après l'heure de début ({$heureDebut})", 422);
    }

    public static function concoursSessionInvalide(string $concoursId, string $sessionId): self
    {
        return new self("Le concours {$concoursId} n'est pas lié à la session {$sessionId}", 404);
    }

    public static function epreuveDejaPlannifiee(string $epreuveId, string $concoursId, string $sessionId): self
    {
        return new self("L'épreuve {$epreuveId} est déjà planifiée pour ce concours et cette session", 409);
    }

    public static function planningIntrouvable(string $planningId): self
    {
        return new self("Planning avec l'ID {$planningId} introuvable", 404);
    }

    public static function conflitHoraire(string $dateEpreuve, string $heureDebut, string $heureFin): self
    {
        return new self("Conflit horaire détecté le {$dateEpreuve} entre {$heureDebut} et {$heureFin}", 409);
    }
}
