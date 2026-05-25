<?php

namespace Database\Factories;

use App\Enums\TypeUtilisateur;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UtilisateurFactory extends Factory
{
    protected $model = Utilisateur::class;

    public function definition(): array
    {
        return [
            'user_name' => $this->faker->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'mot_de_passe' => Hash::make('password'),
            'telephone' => $this->faker->phoneNumber(),
            'est_actif' => true,
            'email_verifie' => false,
            'type_utilisateur' => TypeUtilisateur::CANDIDAT,
        ];
    }

    public function emailVerified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verifie' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_actif' => false,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_utilisateur' => TypeUtilisateur::ADMIN,
        ]);
    }

    public function responsableCentre(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_utilisateur' => TypeUtilisateur::RESPONSABLE_CENTRE,
        ]);
    }
}
