<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentReceipt;
use App\Models\Candidat;
use App\Models\Utilisateur;
use App\Enums\StatutVerificationPaiement;

class PaymentReceiptSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer quelques candidats
        $candidats = Candidat::limit(5)->get();

        if ($candidats->isEmpty()) {
            $this->command->warn('Aucun candidat trouvé. Créez des candidats d\'abord.');
            return;
        }

        foreach ($candidats as $index => $candidat) {
            // Reçu vérifié
            if ($index === 0) {
                PaymentReceipt::create([
                    'candidat_id' => $candidat->utilisateur_id,
                    'numero_recu' => 'REC-2024-' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                    'banque' => 'BICEC',
                    'montant' => 5000,
                    'date_paiement' => now()->subDays(5),
                    'image_path' => 'receipts/sample_' . ($index + 1) . '.jpg',
                    'ocr_data' => [
                        'full_text' => 'BICEC Reçu N° REC-2024-000001 Montant: 5000 FCFA Date: ' . now()->subDays(5)->format('d/m/Y'),
                        'extracted' => [
                            'numero_recu' => 'REC-2024-000001',
                            'montant' => '5000',
                            'date' => now()->subDays(5)->format('d/m/Y'),
                            'banque' => 'BICEC',
                        ],
                    ],
                    'statut_verification' => StatutVerificationPaiement::VERIFIE,
                    'verified_at' => now()->subDays(4),
                    'verified_by' => Utilisateur::where('type_utilisateur', 'admin')->first()?->id,
                ]);
            }
            // Reçu en attente
            elseif ($index === 1) {
                PaymentReceipt::create([
                    'candidat_id' => $candidat->utilisateur_id,
                    'numero_recu' => 'REC-2024-' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                    'banque' => 'UBA',
                    'montant' => 5000,
                    'date_paiement' => now()->subDays(2),
                    'image_path' => 'receipts/sample_' . ($index + 1) . '.jpg',
                    'ocr_data' => [
                        'full_text' => 'UBA Reçu N° REC-2024-000002 Montant: 5000 FCFA Date: ' . now()->subDays(2)->format('d/m/Y'),
                        'extracted' => [
                            'numero_recu' => 'REC-2024-000002',
                            'montant' => '5000',
                            'date' => now()->subDays(2)->format('d/m/Y'),
                            'banque' => 'UBA',
                        ],
                    ],
                    'statut_verification' => StatutVerificationPaiement::EN_ATTENTE,
                ]);
            }
            // Reçu rejeté
            elseif ($index === 2) {
                PaymentReceipt::create([
                    'candidat_id' => $candidat->utilisateur_id,
                    'numero_recu' => 'REC-2024-' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                    'banque' => 'Orange Money',
                    'montant' => 4500,
                    'date_paiement' => now()->subDays(3),
                    'image_path' => 'receipts/sample_' . ($index + 1) . '.jpg',
                    'ocr_data' => [
                        'full_text' => 'Orange Money Reçu N° REC-2024-000003 Montant: 4500 FCFA Date: ' . now()->subDays(3)->format('d/m/Y'),
                        'extracted' => [
                            'numero_recu' => 'REC-2024-000003',
                            'montant' => '4500',
                            'date' => now()->subDays(3)->format('d/m/Y'),
                            'banque' => 'Orange Money',
                        ],
                    ],
                    'statut_verification' => StatutVerificationPaiement::REJETE,
                    'motif_rejet' => 'Montant incorrect. Le montant attendu est de 5000 FCFA.',
                    'verified_at' => now()->subDays(2),
                    'verified_by' => Utilisateur::where('type_utilisateur', 'admin')->first()?->id,
                ]);
            }
            // Autres reçus en attente
            else {
                PaymentReceipt::create([
                    'candidat_id' => $candidat->utilisateur_id,
                    'numero_recu' => 'REC-2024-' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                    'banque' => ['SGBC', 'Afriland First Bank', 'MTN Mobile Money'][rand(0, 2)],
                    'montant' => 5000,
                    'date_paiement' => now()->subDays(rand(1, 7)),
                    'image_path' => 'receipts/sample_' . ($index + 1) . '.jpg',
                    'ocr_data' => [
                        'full_text' => 'Reçu de paiement',
                        'extracted' => [],
                    ],
                    'statut_verification' => StatutVerificationPaiement::EN_ATTENTE,
                ]);
            }
        }

        $this->command->info('✓ ' . PaymentReceipt::count() . ' reçus de paiement créés');
    }
}
