<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Enums\RegionCameroun;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regionsCodes = [
            RegionCameroun::ADAMAOUA->value => 'AD',
            RegionCameroun::CENTRE->value => 'CE',
            RegionCameroun::EST->value => 'ES',
            RegionCameroun::EXTREME_NORD->value => 'EN',
            RegionCameroun::LITTORAL->value => 'LT',
            RegionCameroun::NORD->value => 'NO',
            RegionCameroun::NORD_OUEST->value => 'NW',
            RegionCameroun::OUEST->value => 'OU',
            RegionCameroun::SUD->value => 'SU',
            RegionCameroun::SUD_OUEST->value => 'SW',
        ];

        foreach (RegionCameroun::cases() as $regionEnum) {
            Region::firstOrCreate(
                ['libelle' => $regionEnum], // Vérifie si la région existe déjà par son libellé
                [
                    'code' => $regionsCodes[$regionEnum->value] ?? substr($regionEnum->value, 0, 2),
                    'est_actif' => true,
                ]
            );
        }
    }
}
