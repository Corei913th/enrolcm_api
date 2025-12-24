<?php

namespace App\Enums;

class RegionCameroun
{
    public const ADAMAOUA = 'ADAMAOUA';
    public const CENTRE = 'CENTRE';
    public const EST = 'EST';
    public const EXTREME_NORD = 'EXTREME_NORD';
    public const LITTORAL = 'LITTORAL';
    public const NORD = 'NORD';
    public const NORD_OUEST = 'NORD_OUEST';
    public const OUEST = 'OUEST';
    public const SUD = 'SUD';
    public const SUD_OUEST = 'SUD_OUEST';

    public static function values(): array
    {
        return [
            self::ADAMAOUA,
            self::CENTRE,
            self::EST,
            self::EXTREME_NORD,
            self::LITTORAL,
            self::NORD,
            self::NORD_OUEST,
            self::OUEST,
            self::SUD,
            self::SUD_OUEST,
        ];
    }

    public static function label(string $value): string
    {
        $labels = [
            self::ADAMAOUA => 'Adamaoua',
            self::CENTRE => 'Centre',
            self::EST => 'Est',
            self::EXTREME_NORD => 'Extrême-Nord',
            self::LITTORAL => 'Littoral',
            self::NORD => 'Nord',
            self::NORD_OUEST => 'Nord-Ouest',
            self::OUEST => 'Ouest',
            self::SUD => 'Sud',
            self::SUD_OUEST => 'Sud-Ouest',
        ];

        return $labels[$value] ?? $value;
    }
}
