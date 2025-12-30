<?php

namespace App\Enums;

enum PrioriteNotification: string
{
    case BASSE = 'basse';
    case NORMALE = 'normale';
    case HAUTE = 'haute';
    case URGENTE = 'urgente';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::BASSE => 'Basse',
            self::NORMALE => 'Normale',
            self::HAUTE => 'Haute',
            self::URGENTE => 'Urgente',
        };
    }
}
