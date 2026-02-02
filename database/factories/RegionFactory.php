<?php

namespace Database\Factories;

use App\Models\Region;
use App\Enums\RegionCameroun;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegionFactory extends Factory
{
  protected $model = Region::class;

  public function definition(): array
  {
    return [
      'code' => strtoupper($this->faker->unique()->lexify('REG??')),
      'libelle' => $this->faker->randomElement(RegionCameroun::values()),
      'est_actif' => true,
    ];
  }
}
