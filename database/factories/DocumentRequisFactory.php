<?php

namespace Database\Factories;

use App\Enums\TypeDocument;
use App\Models\Concours;
use App\Models\DocumentRequis;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentRequisFactory extends Factory
{
    protected $model = DocumentRequis::class;

    public function definition(): array
    {
        return [
            'concours_id' => Concours::factory(),
            'nom_document' => $this->faker->randomElement([
                'Copie CNI',
                'Acte de naissance',
                'Diplôme du Baccalauréat',
                'Relevé de notes du Bac',
                'Certificat de scolarité',
            ]),
            'description' => $this->faker->sentence(),
            'type_document' => $this->faker->randomElement(TypeDocument::values()),
            'est_obligatoire' => true,
            'format_accepte' => ['pdf', 'jpg', 'jpeg', 'png'],
            'taille_max_mb' => 5,
            'ordre_affichage' => $this->faker->numberBetween(1, 10),
            'est_actif' => true,
        ];
    }

    public function optional(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_obligatoire' => false,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_actif' => false,
        ]);
    }
}
