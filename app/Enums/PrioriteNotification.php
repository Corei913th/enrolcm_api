<?php

namespace App\Enums;

class PrioriteNotification
{
    public const BASSE = 'basse';
    public const NORMALE = 'normale';
    public const HAUTE = 'haute';
    public const URGENTE = 'urgente';
    
    public static function values(): array
    {
        return [
            self::BASSE,
            self::NORMALE,
            self::HAUTE,
            self::URGENTE,
        ];
    }

    public static function label(string $value): string
    {
        $labels = [
            self::BASSE => 'Basse',
            self::NORMALE => 'Normale',
            self::HAUTE => 'Haute',
            self::URGENTE => 'Urgente',
        ];

        return $labels[$value] ?? $value;
    }
}
