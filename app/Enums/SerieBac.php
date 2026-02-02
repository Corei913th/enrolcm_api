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
    case GCE_O_LEVEL = 'GCE O-Level';
    case GCE_A_LEVEL = 'GCE A-Level';

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
            self::A => 'Série A (Littéraire)',
            self::C => 'Série C (Mathématiques et Sciences Physiques)',
            self::D => 'Série D (Mathématiques et Sciences de la Vie)',
            self::E => 'Série E (Mathématiques et Techniques)',
            self::F => 'Série F (Techniques Industrielles)',
            self::G => 'Série G (Techniques Tertiaires)',
            self::TI => 'Série TI (Technologies de l\'Information)',
            self::GCE_O_LEVEL => 'GCE O-Level (Ordinary Level)',
            self::GCE_A_LEVEL => 'GCE A-Level (Advanced Level)',
        };
    }

    /**
     * Get short label
     */
    public function shortLabel(): string
    {
        return $this->value;
    }

    /**
     * Check if series is francophone
     */
    public function isFrancophone(): bool
    {
        return in_array($this, [
            self::A,
            self::C,
            self::D,
            self::E,
            self::F,
            self::G,
            self::TI,
        ]);
    }

    /**
     * Check if series is anglophone
     */
    public function isAnglophone(): bool
    {
        return in_array($this, [
            self::GCE_O_LEVEL,
            self::GCE_A_LEVEL,
        ]);
    }

    /**
     * Check if series is scientific
     */
    public function isScientific(): bool
    {
        return in_array($this, [
            self::C,
            self::D,
            self::E,
        ]);
    }

    /**
     * Check if series is literary
     */
    public function isLiterary(): bool
    {
        return in_array($this, [
            self::A,
        ]);
    }

    /**
     * Check if series is technical
     */
    public function isTechnical(): bool
    {
        return in_array($this, [
            self::E,
            self::F,
            self::G,
            self::TI,
        ]);
    }

    /**
     * Get all options for select
     */
    public static function options(): array
    {
        return [
            self::A->value => self::A->label(),
            self::C->value => self::C->label(),
            self::D->value => self::D->label(),
            self::E->value => self::E->label(),
            self::F->value => self::F->label(),
            self::G->value => self::G->label(),
            self::TI->value => self::TI->label(),
            self::GCE_O_LEVEL->value => self::GCE_O_LEVEL->label(),
            self::GCE_A_LEVEL->value => self::GCE_A_LEVEL->label(),
        ];
    }

    /**
     * Get francophone series only
     */
    public static function francophoneOptions(): array
    {
        return [
            self::A->value => self::A->label(),
            self::C->value => self::C->label(),
            self::D->value => self::D->label(),
            self::E->value => self::E->label(),
            self::F->value => self::F->label(),
            self::G->value => self::G->label(),
            self::TI->value => self::TI->label(),
        ];
    }

    /**
     * Get anglophone series only
     */
    public static function anglophoneOptions(): array
    {
        return [
            self::GCE_O_LEVEL->value => self::GCE_O_LEVEL->label(),
            self::GCE_A_LEVEL->value => self::GCE_A_LEVEL->label(),
        ];
    }

    /**
     * Get scientific series only (francophone)
     */
    public static function scientificOptions(): array
    {
        return [
            self::C->value => self::C->label(),
            self::D->value => self::D->label(),
            self::E->value => self::E->label(),
        ];
    }

    /**
     * Get technical series only (francophone)
     */
    public static function technicalOptions(): array
    {
        return [
            self::E->value => self::E->label(),
            self::F->value => self::F->label(),
            self::G->value => self::G->label(),
            self::TI->value => self::TI->label(),
        ];
    }
}
