<?php

namespace App\Console\Commands;

use App\Models\Candidature;
use App\Services\Domain\Notification\Generators\AlertGeneratorService;
use Illuminate\Console\Command;

class RegenerateAlerts extends Command
{
    protected $signature = 'alerts:regenerate';

    protected $description = 'Regenerate alerts for latest candidate.';

    public function handle()
    {
        $candidature = Candidature::with(['candidat.utilisateur'])->latest()->first();
        if (! $candidature) {
            $this->error('No candidate.');

            return;
        }

        $candidat = $candidature->candidat;
        $this->info("Regenerating for {$candidat->nom_cand}...");

        /** @var AlertGeneratorService $generator */
        $generator = app(AlertGeneratorService::class);

        $alerts = $generator->generateCandidateAlerts($candidat);

        $this->info('Generated ' . count($alerts) . ' alerts.');
        foreach ($alerts as $alert) {
            if ($alert) {
                $this->line(" - {$alert->type}");
            }
        }
    }
}
