<?php

namespace App\Enums;

class SerieBac
{
    public const A = 'A';
    public const C = 'C';
    public const D = 'D';
    public const E = 'E';
    public const F = 'F';
    public const G = 'G';
    public const TI = 'TI';
    public const ESF = 'ESF';
    public const AUTRE = 'AUTRE';

    public static function values(): array
    {
        return [
            self::A,
            self::C,
            self::D,
            self::E,
            self::F,
            self::G,
            self::TI,
            self::ESF,
            self::AUTRE,
        ];
    }

    public static function label(string $value): string
    {
        $labels = [
            self::A => 'Série A (Littéraire)',
            self::C => 'Série C (Mathématiques et Sciences Physiques)',
            self::D => 'Série D (Mathématiques et Sciences de la Vie)',
            self::E => 'Série E (Mathématiques et Techniques)',
            self::F => 'Série F (Électrotechnique)',
            self::G => 'Série G (Techniques Commerciales)',
            self::TI => 'Série TI (Tecnnologie de l\' Informatique)',
            self::ESF => 'Série ESF (Économie Sociale et Familiale)',
            self::AUTRE => 'Autre série',
        ];

        return $labels[$value] ?? $value;
    }
}
