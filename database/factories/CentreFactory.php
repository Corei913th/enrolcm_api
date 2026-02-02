<?php

namespace Database\Factories;

use App\Models\Centre;
use App\Models\Region;
use App\Enums\TypeCentre;
use Illuminate\Database\Eloquent\Factories\Factory;

class CentreFactory extends Factory
{
  protected $model = Centre::class;

  public function definition(): array
  {
    return [
      'region_id' => Region::factory(),
      'libelle_centre' => $this->faker->company() . ' - Centre d\'Examen',
      'type_centre' => $this->faker->randomElement(TypeCentre::values()),
      'ville_centre' => $this->faker->city(),
      'capacite' => $this->faker->numberBetween(100, 500),
      'est_actif' => true,
      'responsable_id' => null,
    ];
  }

  public function inactive(): static
  {
    return $this->state(fn(array $attributes) => [
      'est_actif' => false,
    ]);
  }
}
