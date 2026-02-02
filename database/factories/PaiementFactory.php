<?php

namespace Database\Factories;

use App\Enums\StatutPaiement;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\Paiement;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaiementFactory extends Factory
{
  protected $model = Paiement::class;

  public function definition(): array
  {
    return [
      'candidat_id' => Candidat::factory(),
      'concours_id' => Concours::factory(),
      'candidature_id' => Candidature::factory(),
      'reference' => 'REF-' . strtoupper($this->faker->bothify('????####')),
      'montant' => $this->faker->randomFloat(2, 5000, 50000),
      'preuve_paiement' => 'receipts/' . $this->faker->uuid() . '.pdf',
      'statut' => StatutPaiement::PENDING,
      'validation_notes' => null,
    ];
  }

  public function verified(): static
  {
    return $this->state(fn(array $attributes) => [
      'statut' => StatutPaiement::VERIFIED,
      'validated_at' => now(),
    ]);
  }

  public function rejected(): static
  {
    return $this->state(fn(array $attributes) => [
      'statut' => StatutPaiement::REJECTED,
      'motif_rejet' => $this->faker->sentence(),
      'validated_at' => now(),
    ]);
  }

  public function pendingManualReview(): static
  {
    return $this->state(fn(array $attributes) => [
      'statut' => StatutPaiement::PENDING_MANUAL_REVIEW,
    ]);
  }

  public function ocrVerified(): static
  {
    return $this->state(fn(array $attributes) => [
      'statut' => StatutPaiement::OCR_VERIFIE,
      'montant_ocr' => $attributes['montant'],
      'reference_ocr' => $attributes['reference'],
      'ocr_confidence' => $this->faker->randomFloat(2, 0.7, 0.99),
    ]);
  }

  public function withoutCandidature(): static
  {
    return $this->state(fn(array $attributes) => [
      'candidature_id' => null,
    ]);
  }
}
