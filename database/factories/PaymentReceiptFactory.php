<?php

namespace Database\Factories;

use App\Models\PaymentReceipt;
use App\Models\Candidat;
use App\Models\Utilisateur;
use App\Enums\StatutVerificationPaiement;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentReceiptFactory extends Factory
{
    protected $model = PaymentReceipt::class;

    public function definition(): array
    {
        $banques = [
            'BICEC',
            'UBA',
            'SGBC',
            'Afriland First Bank',
            'Ecobank',
            'SCB Cameroun',
            'Orange Money',
            'MTN Mobile Money',
        ];

        $numeroRecu = 'REC-' . date('Y') . '-' . str_pad($this->faker->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT);

        return [
            'candidat_id' => Candidat::factory(),
            'numero_recu' => $numeroRecu,
            'banque' => $this->faker->randomElement($banques),
            'montant' => 5000,
            'date_paiement' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'image_path' => 'receipts/' . $this->faker->uuid() . '.jpg',
            'ocr_data' => [
                'full_text' => "Reçu de paiement\nNuméro: {$numeroRecu}\nMontant: 5000 FCFA",
                'extracted' => [
                    'numero_recu' => $numeroRecu,
                    'montant' => '5000',
                    'banque' => $this->faker->randomElement($banques),
                ],
            ],
            'statut_verification' => StatutVerificationPaiement::EN_ATTENTE,
        ];
    }

    public function verifie(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut_verification' => StatutVerificationPaiement::VERIFIE,
            'verified_at' => now(),
            'verified_by' => Utilisateur::factory(),
        ]);
    }

    public function rejete(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut_verification' => StatutVerificationPaiement::REJETE,
            'motif_rejet' => $this->faker->sentence(),
            'verified_at' => now(),
            'verified_by' => Utilisateur::factory(),
        ]);
    }

    public function enAttente(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut_verification' => StatutVerificationPaiement::EN_ATTENTE,
            'verified_at' => null,
            'verified_by' => null,
            'motif_rejet' => null,
        ]);
    }
}
