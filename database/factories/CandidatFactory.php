<?php

namespace Database\Factories;

use App\Models\Candidat;
use App\Models\Utilisateur;
use App\Enums\TypeUtilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

class CandidatFactory extends Factory
{
    protected $model = Candidat::class;

    public function definition(): array
    {
        $utilisateur = Utilisateur::factory()->create([
            'type_utilisateur' => TypeUtilisateur::CANDIDAT,
        ]);

        $dateNaissance = $this->faker->dateTimeBetween('-30 years', '-18 years');
        $age = (int) abs(now()->diffInYears($dateNaissance));

        return [
            'utilisateur_id' => $utilisateur->id,
            'nom_cand' => $this->faker->lastName(),
            'prenom_cand' => $this->faker->firstName(),
            'nationalite_cand' => 'Camerounaise',
            'age_cand' => $age,
            'date_naissance_cand' => $dateNaissance,
            'lieu_naissance_cand' => $this->faker->city(),
            'sexe_cand' => $this->faker->randomElement(['M', 'F']),
            'est_actif' => true,
        ];
    }
}
