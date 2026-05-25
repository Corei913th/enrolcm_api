<?php

namespace Database\Factories;

use App\Models\Concours;
use App\Models\ConcoursPaiement;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConcoursPaiementFactory extends Factory
{
    protected $model = ConcoursPaiement::class;

    public function definition(): array
    {
        return [
            'concours_id' => Concours::factory(),
            'banque_nom' => $this->faker->randomElement(['BICEC', 'UBA', 'Afriland First Bank', 'SCB Cameroun']),
            'numero_compte' => $this->faker->numerify('##########'),
            'nom_beneficiaire' => $this->faker->company(),
            'montant' => $this->faker->randomElement([25000, 50000, 75000, 100000]),
            'date_limite' => now()->addDays(30),
            'instructions' => $this->faker->optional()->sentence(),
            'est_actif' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_actif' => false,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_limite' => now()->subDays(1),
        ]);
    }
}
