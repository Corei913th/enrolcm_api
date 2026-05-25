<?php

namespace App\Enums;

enum NiveauScolaire: string
{
    case COLLEGE = 'COLLEGE';
    case LYCEE = 'LYCEE';
    case BACCALAUREAT = 'BACCALAUREAT';
    case BTS_DUT = 'BTS_DUT';
    case LICENCE = 'LICENCE';
    case MASTER = 'MASTER';
    case DOCTORAT = 'DOCTORAT';
    case AUTRE = 'AUTRE';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::COLLEGE => 'Collège',
            self::LYCEE => 'Lycée',
            self::BACCALAUREAT => 'Baccalauréat',
            self::BTS_DUT => 'BTS / DUT',
            self::LICENCE => 'Licence',
            self::MASTER => 'Master',
            self::DOCTORAT => 'Doctorat',
            self::AUTRE => 'Autre',
        };
    }
}
