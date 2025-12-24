<?php

namespace App\Enums;

class TypeCentre
{
  public const DEPOT = 'DEPOT';
  public const EXAMEN = 'EXAMEN';

  public static function values(): array
  {
    return [
      self::DEPOT,
      self::EXAMEN
    ];
  }

  public static function label(string $value): string
  {
    $labels = [
      self::DEPOT => 'Dépôt',
      self::EXAMEN => 'Examen'
    ];

    return $labels[$value] ?? $value;
  }
}
