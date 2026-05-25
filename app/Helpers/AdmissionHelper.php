<?php

namespace App\Helpers;

class AdmissionHelper
{
    /**
     * Calculate age score (younger = higher score)
     */
    public static function calculateAgeScore(?int $age, float $weight): float
    {
        if ($age === null) {
            return 0;
        }

        // Score: 18 years = 100 points, 25 years = 30 points
        $score = max(0, 100 - ($age - 18) * 10);

        return ($score / 100) * $weight;
    }

    /**
     * Calculate regional quota places from percentage
     */
    public static function calculateRegionalPlaces(int $totalPlaces, array $percentages): array
    {
        if (empty($percentages)) {
            return [];
        }

        $places = [];
        foreach ($percentages as $region => $percentage) {
            $places[$region] = (int) ceil($totalPlaces * ($percentage / 100));
        }

        return $places;
    }

    /**
     * Get region from candidature
     */
    public static function getRegion($candidature): string
    {
        if (! $candidature) {
            return 'UNKNOWN';
        }

        $region = $candidature->region_figee ?? $candidature->candidat?->region;

        return is_object($region) && property_exists($region, 'value')
            ? $region->value
            : (string) ($region ?? 'UNKNOWN');
    }

    /**
     * Calculate maximum conditional admission places
     */
    public static function calculateMaxConditionalPlaces(int $totalPlaces, int $percentage): int
    {
        return (int) ceil($totalPlaces * ($percentage / 100));
    }
}
