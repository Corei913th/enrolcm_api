<?php

namespace App\Traits;

trait HasFindByMethods
{
    /**
     * Trouver par n'importe quelle colonne
     */
    public static function findBy(string $column, mixed $value): ?static
    {
        return static::where($column, $value)->first();
    }

    /**
     * Trouver ou échouer par n'importe quelle colonne
     */
    public static function findByOrFail(string $column, mixed $value): static
    {
        return static::where($column, $value)->firstOrFail();
    }

    /**
     * Vérifier l'existence par colonne
     */
    public static function existsBy(string $column, mixed $value): bool
    {
        return static::where($column, $value)->exists();
    }

    /**
     * Trouver plusieurs par colonne
     */
    public static function findManyBy(string $column, mixed $value)
    {
        return static::where($column, $value)->get();
    }
}
