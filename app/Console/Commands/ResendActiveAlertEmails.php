<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\Candidature;
use App\Services\Domain\Notification\Notifiers\AlertEmailService;
use Illuminate\Console\Command;

class ResendActiveAlertEmails extends Command
{
    protected $signature = 'alerts:resend-latest';

    protected $description = 'Resend emails for active alerts of the latest candidate.';

    public function handle()
    {
        $this->info('Finding latest candidate...');

        $candidature = Candidature::with(['candidat.utilisateur'])->latest()->first();

        if (! $candidature || ! $candidature->candidat) {
            $this->error('No candidate found.');

            return;
        }

        $candidat = $candidature->candidat;
        $this->info("Candidate found: {$candidat->nom_cand} {$candidat->prenom_cand} ({$candidat->utilisateur->email})");

        // Get all active alerts
        $alerts = Alert::where(function ($q) use ($candidature, $candidat) {
            $q->where('candidature_id', $candidature->id)
                ->orWhereHas('candidature', fn ($sq) => $sq->where('candidat_id', $candidat->utilisateur_id));
        })
            ->where('is_dismissed', false)
            ->get();

        $this->info("Found {$alerts->count()} active alerts.");

        /** @var AlertEmailService $emailService */
        $emailService = app(AlertEmailService::class);

        foreach ($alerts as $alert) {
            $this->line("Processing alert: [{$alert->type}] {$alert->title}...");

            $sent = $emailService->sendAlertEmail($alert, $candidat);

            if ($sent) {
                $this->info(' -> Email QUEUED (Success).');
            } else {
                $this->error(' -> Email NOT QUEUED (Failed).');
            }
        }
    }
}
