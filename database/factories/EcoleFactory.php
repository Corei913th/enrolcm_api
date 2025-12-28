<?php

namespace Database\Factories;

use App\Models\Ecole;
use App\Enums\RegionCameroun;
use Illuminate\Database\Eloquent\Factories\Factory;

class EcoleFactory extends Factory
{
    protected $model = Ecole::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'code_ecole' => strtoupper($this->faker->unique()->lexify('???')),
            'libelle_ecole' => $this->faker->company() . ' - École Supérieure',
            'region' => $this->faker->randomElement(RegionCameroun::values()),
            'localisation' => $this->faker->city(),
            'email_ecole' => $this->faker->unique()->companyEmail(),
            'telephone_ecole' => $this->faker->phoneNumber(),
            'siteweb_ecole' => $this->faker->url(),
            'devise' => $this->faker->sentence(3),
            'bp_ecole' => 'BP ' . $this->faker->numberBetween(100, 9999),
            'logo_url' => null,
            'embleme_ecole' => null,
            'est_actif' => $this->faker->boolean(90),
        ];
    }

    /**
     * Indicate that the ecole is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_actif' => true,
        ]);
    }

    /**
     * Indicate that the ecole is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_actif' => false,
        ]);
    }
}
