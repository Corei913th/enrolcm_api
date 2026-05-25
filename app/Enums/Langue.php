<?php

namespace App\Enums;

enum Langue: string
{
    case FRANCAIS = 'Français';
    case ANGLAIS = 'Anglais';
    case AUTRE = 'Autre';

    /**
     * Get all enum values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get label for display
     */
    public function label(): string
    {
        return match ($this) {
            self::FRANCAIS => 'Français',
            self::ANGLAIS => 'Anglais',
            self::AUTRE => 'Autre',
        };
    }

    /**
     * Check if language requires specification
     */
    public function requiresSpecification(): bool
    {
        return $this === self::AUTRE;
    }

    /**
     * Get all options for select
     */
    public static function options(): array
    {
        return [
            self::FRANCAIS->value => self::FRANCAIS->label(),
            self::ANGLAIS->value => self::ANGLAIS->label(),
            self::AUTRE->value => self::AUTRE->label(),
        ];
    }
}
