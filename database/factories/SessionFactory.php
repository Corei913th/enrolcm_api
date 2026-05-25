<?php

namespace Database\Factories;

use App\Enums\StatutSession;
use App\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

class SessionFactory extends Factory
{
    protected $model = Session::class;

    public function definition(): array
    {
        return [
            'libelle_session' => 'Session ' . $this->faker->year(),
            'desc_session' => $this->faker->sentence(),
            'est_actif' => true,
            'statut_session' => StatutSession::OUVERT,
            'date_ouverture_inscription' => now()->subDays(30),
            'date_fermeture_inscription' => now()->addDays(30),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_actif' => false,
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut_session' => StatutSession::FERME,
        ]);
    }
}
