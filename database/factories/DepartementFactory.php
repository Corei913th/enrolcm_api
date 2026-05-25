<?php

namespace Database\Factories;

use App\Models\Departement;
use App\Models\Ecole;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartementFactory extends Factory
{
    protected $model = Departement::class;

    public function definition(): array
    {
        return [
            'code_departement' => strtoupper($this->faker->unique()->lexify('DEP???')),
            'libelle_departement' => $this->faker->words(2, true),
            'ecole_id' => Ecole::factory(),
            'desc_departement' => $this->faker->sentence(),
            'est_actif' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_actif' => false,
        ]);
    }
}
