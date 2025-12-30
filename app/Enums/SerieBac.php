<?php

namespace App\Enums;

enum SerieBac: string
{
    case A = 'A';
    case C = 'C';
    case D = 'D';
    case E = 'E';
    case F = 'F';
    case G = 'G';
    case TI = 'TI';
    case ESF = 'ESF';
    case AUTRE = 'AUTRE';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::A => 'Série A (Littéraire)',
            self::C => 'Série C (Mathématiques et Sciences Physiques)',
            self::D => 'Série D (Mathématiques et Sciences de la Vie)',
            self::E => 'Série E (Mathématiques et Techniques)',
            self::F => 'Série F (Électrotechnique)',
            self::G => 'Série G (Techniques Commerciales)',
            self::TI => 'Série TI (Tecnnologie de l\' Informatique)',
            self::ESF => 'Série ESF (Économie Sociale et Familiale)',
            self::AUTRE => 'Autre série',
        };
    }
}
