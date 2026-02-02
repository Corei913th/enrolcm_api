<?php

namespace Database\Factories;

use App\Enums\StatutCandidature;
use App\Models\Candidat;
use App\Models\Concours;
use App\Models\Session;
use App\Models\Centre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidature>
 */
class CandidatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $candidat = Candidat::factory()->create();
        $session = Session::first() ?? Session::factory()->create();
        $concours = Concours::first() ?? Concours::factory()->create();
        $centre = Centre::first();

        // Ensure the concours-session relationship exists
        if (!$concours->sessions()->where('session_id', $session->id)->exists()) {
            $concours->sessions()->attach($session->id);
        }

        return [
            'candidat_id' => $candidat->id,
            'concours_id' => $concours->id,
            'session_id' => $session->id,
            'centre_examen_id' => $centre?->id,
            'centre_depot_id' => $centre?->id,
            'date_candidature' => now(),
            'statut_candidature' => StatutCandidature::SOUMISE,
            'documents_complets' => false,
            'paiement_valide' => false,
        ];
    }
}
