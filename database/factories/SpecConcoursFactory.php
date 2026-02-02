<?php

namespace Database\Factories;

use App\Models\SpecConcours;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecConcoursFactory extends Factory
{
  protected $model = SpecConcours::class;

  public function definition(): array
  {
    return [
      'nom_spec' => $this->faker->words(3, true),
      'desc_infos_concours' => $this->faker->paragraph(),
      'documents_requis' => [],
      'montant_frais_depot' => $this->faker->numberBetween(5000, 50000),
      'age_minimum' => null,
      'age_maximum' => null,
      'series_bac_acceptees' => null,
      'nationalites_acceptees' => null,
      'est_actif' => true,
    ];
  }

  public function withAgeRestriction(int $min = 18, int $max = 30): self
  {
    return $this->state(fn(array $attributes) => [
      'age_minimum' => $min,
      'age_maximum' => $max,
    ]);
  }

  public function withSeriesRestriction(array $series = ['C', 'D']): self
  {
    return $this->state(fn(array $attributes) => [
      'series_bac_acceptees' => $series,
    ]);
  }

  public function withNationalityRestriction(array $nationalities = ['Camerounaise']): self
  {
    return $this->state(fn(array $attributes) => [
      'nationalites_acceptees' => $nationalities,
    ]);
  }
}
