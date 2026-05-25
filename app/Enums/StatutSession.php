<?php

namespace App\Enums;

enum StatutSession: string
{
    case BROUILLON = 'BROUILLON';
    case OUVERT = 'OUVERT';
    case FERME = 'FERME';
    case EN_COURS = 'EN_COURS';
    case TERMINE = 'TERMINE';

    /**
     * Vérifie si la session accepte les inscriptions
     */
    public function accepteInscriptions(): bool
    {
        return $this === self::OUVERT;
    }

    /**
     * Vérifie si la session est active
     */
    public function estActive(): bool
    {
        return in_array($this, [self::OUVERT, self::EN_COURS]);
    }

    /**
     * Vérifie si la session est terminée
     */
    public function estTerminee(): bool
    {
        return $this === self::TERMINE;
    }

    /**
     * Liste des valeurs pour les migrations
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Labels pour l'affichage
     */
    public function label(): string
    {
        return match ($this) {
            self::BROUILLON => 'Brouillon',
            self::OUVERT => 'Inscriptions ouvertes',
            self::FERME => 'Inscriptions fermées',
            self::EN_COURS => 'Session en cours',
            self::TERMINE => 'Session terminée',
        };
    }
}
