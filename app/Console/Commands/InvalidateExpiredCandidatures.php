<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InvalidateExpiredCandidatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'concours:invalidate-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invalide les candidatures non validées après la date limite de dépôt';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\Domain\Candidature\CandidatureService $candidatureService)
    {
        $now = \Carbon\Carbon::now();

        $candidatures = \App\Models\Candidature::query()
            ->whereIn('statut_candidature', [
                \App\Enums\StatutCandidature::BROUILLON,
                \App\Enums\StatutCandidature::SOUMISE,
                \App\Enums\StatutCandidature::DOCUMENTS_VERIFIES,
                \App\Enums\StatutCandidature::PAIEMENT_VERIFIE,
            ])
            ->whereHas('concours', function ($query) use ($now) {
                $query->where('date_limite_depot', '<', $now);
            })
            ->get();

        if ($candidatures->isEmpty()) {
            $this->info('Aucune candidature expirée trouvée.');
            return 0;
        }

        $this->info("Sélection de {$candidatures->count()} candidatures expirées...");

        $bar = $this->output->createProgressBar($candidatures->count());
        $bar->start();

        foreach ($candidatures as $candidature) {
            $candidatureService->rejeter(
                $candidature->id,
                "Délai de dépôt des dossiers dépassé (Date limite : " .
                    ($candidature->concours->date_limite_depot ? $candidature->concours->date_limite_depot->format('d/m/Y') : 'N/A') . ")"
            );
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Candidatures invalidées avec succès.');

        return 0;
    }
}
