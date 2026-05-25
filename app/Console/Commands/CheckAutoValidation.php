<?php

namespace App\Console\Commands;

use App\Models\Candidature;
use App\Services\Domain\Candidature\Checkers\EligibilityChecker;
use App\Services\Domain\Candidature\Validators\CandidatureValidationService;
use App\Services\Domain\Notification\Generators\AlertGeneratorService;
use Illuminate\Console\Command;

class CheckAutoValidation extends Command
{
    protected $signature = 'debug:check-validation {candidature_id}';

    protected $description = 'Test the auto-validation and alert cleanup flow for a candidature';

    public function handle(AlertGeneratorService $alertGenerator, CandidatureValidationService $validationService)
    {
        $id = $this->argument('candidature_id');
        $candidature = Candidature::with(['candidat.utilisateur', 'alerts'])->findOrFail($id);

        $this->info("Checking Candidature: {$id}");
        $this->info("Current Status: {$candidature->statut_candidature->value}");

        $this->info("\n--- Alert Status ---");
        foreach ($candidature->alerts as $alert) {
            $this->line("- [{$alert->type}] {$alert->title} (Dismissed: " . ($alert->is_dismissed ? 'Yes' : 'No') . ')');
        }

        $this->info("\n--- Running Manual Clean & Check ---");

        // 1. Clean alerts
        $count = $alertGenerator->cleanObsoleteAlerts($candidature);
        $this->info("Cleaned {$count} alerts.");

        // 2. Try auto-validation
        $result = $validationService->checkAndValidateIfReady($candidature);

        if ($result) {
            $candidature->refresh();
            $this->success('Candidature VALIDATED successfully!');
            $this->info("New Status: {$candidature->statut_candidature->value}");
            $this->info("Definitive Code: {$candidature->code_cand_def}");
            $this->info("Number: {$candidature->numero_candidature}");
        } else {
            $this->warn('Candidature NOT READY for validation.');

            // Check why
            $checker = app(EligibilityChecker::class);
            $eligibility = $checker->checkFullEligibility($candidature);
            foreach ($eligibility['reasons'] as $reason) {
                $this->error('- ' . $reason);
            }
        }

        return 0;
    }

    private function success($message)
    {
        $this->output->writeln("<info>{$message}</info>");
    }
}
