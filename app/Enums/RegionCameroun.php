<?php

namespace App\Enums;

enum RegionCameroun: string
{
    case ADAMAOUA = 'ADAMAOUA';
    case CENTRE = 'CENTRE';
    case EST = 'EST';
    case EXTREME_NORD = 'EXTREME_NORD';
    case LITTORAL = 'LITTORAL';
    case NORD = 'NORD';
    case NORD_OUEST = 'NORD_OUEST';
    case OUEST = 'OUEST';
    case SUD = 'SUD';
    case SUD_OUEST = 'SUD_OUEST';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
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
        };
    }
}
