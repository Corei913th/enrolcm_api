<?php

namespace Database\Factories;

use App\Models\Departement;
use App\Models\Filiere;
use Illuminate\Database\Eloquent\Factories\Factory;

class FiliereFactory extends Factory
{
    protected $model = Filiere::class;

    public function definition(): array
    {
        return [
            'code_filiere' => strtoupper($this->faker->unique()->lexify('FIL???')),
            'libelle_filiere' => $this->faker->words(3, true),
            'departement_id' => Departement::factory(),
            'desc_filiere' => $this->faker->sentence(),
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
