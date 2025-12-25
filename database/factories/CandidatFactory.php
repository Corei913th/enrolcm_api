<?php

namespace Database\Factories;

use App\Models\Candidat;
use App\Models\Utilisateur;
use App\Enums\TypeUtilisateur;
use App\Enums\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;

class CandidatFactory extends Factory
{
    protected $model = Candidat::class;

    public function definition(): array
    {
        $utilisateur = Utilisateur::factory()->create([
            'type_utilisateur' => TypeUtilisateur::CANDIDAT,
        ]);

        return [
            'utilisateur_id' => $utilisateur->id,
            'nom_cand' => $this->faker->lastName(),
            'prenom_cand' => $this->faker->firstName(),
            'nationalite_cand' => 'Camerounaise',
            'age_cand' => $this->faker->numberBetween(18, 30),
            'date_naissance_cand' => $this->faker->dateTimeBetween('-30 years', '-18 years'),
            'sexe_cand' => $this->faker->randomElement([Genre::MASCULIN->value, Genre::FEMININ->value]),
            'telephone_candidat' => $this->faker->phoneNumber(),
            'numero_recu' => 'TEMP-' . $this->faker->unique()->numberBetween(1000, 9999),
            'est_actif' => true,
        ];
    }
}
