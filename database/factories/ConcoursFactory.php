<?php

namespace Database\Factories;

use App\Models\Concours;
use App\Models\Ecole;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConcoursFactory extends Factory
{
  protected $model = Concours::class;

  public function definition(): array
  {
    return [
      'ecole_id' => Ecole::factory(),
      'libelle_concours' => 'Concours ' . $this->faker->words(3, true),
      'description' => $this->faker->paragraph(),
      'date_limite_depot' => now()->addDays(60),
      'date_examen' => now()->addDays(90),
      'nbre_max_places' => $this->faker->numberBetween(50, 200),
      'frais_inscription' => $this->faker->randomFloat(2, 5000, 50000),
      'est_actif' => true,
    ];
  }

  public function inactive(): static
  {
    return $this->state(fn(array $attributes) => [
      'est_actif' => false,
    ]);
  }

  public function closed(): static
  {
    return $this->state(fn(array $attributes) => [
      'date_limite_depot' => now()->subDays(10),
    ]);
  }
}
