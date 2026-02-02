<?php

namespace App\Enums;

enum StatutMatrimonial: string
{
    case CELIBATAIRE = 'Célibataire';
    case CELIBATAIRE_MAJ = 'CÉLIBATAIRE';
    case MARIE = 'Marié(e)';
    case MARIE_MAJ = 'MARIÉ(E)';
    case DIVORCE = 'Divorcé(e)';
    case DIVORCE_MAJ = 'DIVORCÉ(E)';
    case VEUF = 'Veuf(ve)';
    case VEUF_MAJ = 'VEUF(VE)';
    case CONCUBIN = 'Concubin(age)';
    case CONCUBIN_MAJ = 'CONCUBIN(AGE)';
    case PACSE = 'Pacsé(e)';
    case PACSE_MAJ = 'PACSÉ(E)';

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
            self::CELIBATAIRE, self::CELIBATAIRE_MAJ => 'Célibataire',
            self::MARIE, self::MARIE_MAJ => 'Marié(e)',
            self::DIVORCE, self::DIVORCE_MAJ => 'Divorcé(e)',
            self::VEUF, self::VEUF_MAJ => 'Veuf(ve)',
            self::CONCUBIN, self::CONCUBIN_MAJ => 'Concubin(age)',
            self::PACSE, self::PACSE_MAJ => 'Pacsé(e)',
        };
    }

    /**
     * Get all options for select
     */
    public static function options(): array
    {
        return [
            self::CELIBATAIRE->value => self::CELIBATAIRE->label(),
            self::MARIE->value => self::MARIE->label(),
            self::DIVORCE->value => self::DIVORCE->label(),
            self::VEUF->value => self::VEUF->label(),
            self::CONCUBIN->value => self::CONCUBIN->label(),
            self::PACSE->value => self::PACSE->label(),
        ];
    }
}
