<?php

namespace Database\Factories;

use App\Enums\TypeUtilisateur;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'user_name' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verifie' => true,
            'mot_de_passe' => static::$password ??= Hash::make('password'),
            'type_utilisateur' => TypeUtilisateur::CANDIDAT,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verifie' => false,
        ]);
    }
}
