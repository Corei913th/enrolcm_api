<?php

namespace App\Console\Commands;

use App\Enums\StatutPaiement;
use App\Models\Alert;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\Session;
use App\Services\Domain\Registration\RegistrationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TestRegistrationWorkflow extends Command
{
    protected $signature = 'test:registration-workflow';

    protected $description = 'Verify registration workflow fixes (Email, Status, Alerts)';

    public function handle()
    {
        $this->info('Starting Registration Workflow Verification...');

        // 1. Setup Data
        $concours = Concours::first();
        if (! $concours) {
            $this->error('No concours found!');

            return;
        }
        $session = Session::where('est_actif', true)->first();
        if (! $session) {
            $session = Session::first();
        }

        $email = 'test_' . time() . '@example.com';
        $token = Str::random(32);

        // Mock Cache Data
        $cacheData = [
            'step' => 4,
            'concours_id' => $concours->id,
            'session_id' => $session->id,
            'eligibility_data' => [
                'date_naissance' => '2000-01-01',
                'nationalite' => 'Camerounaise',
                'nom' => 'Test',
                'prenom' => 'User',
                'sexe' => 'M',
            ],
            'payment_data' => [
                'reference' => 'TEST_' . time() . '_' . rand(100, 999),
                'montant' => 10000,
                'date_paiement' => '2024-01-01',
                'statut' => StatutPaiement::PENDING_MANUAL_REVIEW->value,
                'validation_notes' => 'Test',
            ],
            'preuve_paiement_path' => 'test/path.jpg',
        ];

        Cache::put("registration_{$token}", $cacheData, 3600);

        // 2. Execute Registration
        try {
            /** @var RegistrationService $service */
            $service = app(RegistrationService::class);

            $result = $service->completeRegistration($token, [
                'email' => $email,
                'password' => 'password123',
                'telephone' => '699999999',
            ]);

            $this->info('Registration completed.');

            // 3. Verify Candidature
            $candidature = Candidature::find($result['candidature']['id']);

            $this->info('Statut: ' . $candidature->statut_candidature->value);
            $this->info('Docs Complets: ' . ($candidature->documents_complets ? 'YES' : 'NO'));
            $this->info('Paiement Valide: ' . ($candidature->paiement_valide ? 'YES' : 'NO'));

            if ($candidature->statut_candidature->value !== 'VALIDE') {
                $this->error('FAIL: Status should be VALIDE');
            } else {
                $this->info('PASS: Status is VALIDE');
            }

            // 4. Check Database Alerts
            $alerts = Alert::where('candidature_id', $candidature->id)->get();
            $this->info('Alerts found in DB: ' . $alerts->count());

            $hasDocAlert = false;
            $hasPaymentAlert = false;

            foreach ($alerts as $alert) {
                $this->line(" - DB Alert: {$alert->type} ({$alert->title})");
                if ($alert->type === 'missing_documents') {
                    $hasDocAlert = true;
                }
                if ($alert->type === 'payment_pending') {
                    $hasPaymentAlert = true;
                }
            }

            if ($hasDocAlert) {
                $this->info('PASS: Missing Documents Alert exists in DB');
            } else {
                $this->error('FAIL: Missing Documents Alert MISSING from DB');
            }

            if ($hasPaymentAlert) {
                $this->info('PASS: Payment Pending Alert exists in DB');
            } else {
                $this->error('FAIL: Payment Pending Alert MISSING from DB');
            }
        } catch (\Throwable $e) {
            $this->error('Registration failed: ' . $e->getMessage());
            $this->line($e->getTraceAsString());
            file_put_contents('test_error.log', $e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }
}
