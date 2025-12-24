<?php

namespace App\Enums;

class Genre
{
  public const M = 'M';
  public const F = 'F';

  public static function values(): array
  {
    return [
      self::M,
      self::F
    ];
  }

  public static function label(string $value): string
  {
    $labels = [
      self::M => 'Masculin',
      self::F => 'Féminin'
    ];

    return $labels[$value] ?? $value;
  }
}
