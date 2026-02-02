<?php

namespace App\Enums;

enum TypeDiplome: string
{
  case BACCALAUREAT = 'BACCALAUREAT';
  case GCE_AL = 'GCE_AL';
  case LICENCE = 'LICENCE';
  case BTS = 'BTS';
  case DUT = 'DUT';
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
      self::BACCALAUREAT => 'Baccalauréat',
      self::GCE_AL => 'GCE A-Level',
      self::LICENCE => 'Licence',
      self::BTS => 'BTS',
      self::DUT => 'DUT',
      self::MASTER => 'Master',
      self::DOCTORAT => 'Doctorat',
      self::AUTRE => 'Autre',
    };
  }
}
