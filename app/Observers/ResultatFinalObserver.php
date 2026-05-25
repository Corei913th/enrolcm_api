<?php

namespace App\Observers;

use App\Models\ResultatFinal;
use App\Services\Domain\Notification\NotificationService;
use App\Services\Infrastructure\Logger\ActivityLoggerService;

class ResultatFinalObserver
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly ActivityLoggerService $logger
    ) {}

    /**
     * Handle the ResultatFinal "created" event
     */
    public function created(ResultatFinal $resultat): void
    {
        $this->logger->logActivity('resultat_created', 'resultat_final', $resultat->id, [
            'candidature_id' => $resultat->candidature_id,
            'est_admis' => $resultat->est_admis,
            'moyenne' => $resultat->moyenne_generale,
            'rang' => $resultat->rang,
        ]);
    }

    /**
     * Handle the ResultatFinal "updated" event
     * Notifie le candidat uniquement lorsque les résultats sont publiés.
     */
    public function updated(ResultatFinal $resultat): void
    {

        if (! $resultat->isDirty('date_publication') || $resultat->date_publication === null) {
            return;
        }

        $this->logger->logActivity('resultat_published', 'resultat_final', $resultat->id, [
            'candidature_id' => $resultat->candidature_id,
            'est_admis' => $resultat->est_admis,
            'decision' => $resultat->decision,
            'moyenne' => $resultat->moyenne_generale,
            'rang' => $resultat->rang,
            'date_publication' => $resultat->date_publication,
        ]);

        // Charger les relations nécessaires
        $resultat->load(['candidature.candidat', 'candidature.concours']);

        $candidature = $resultat->candidature;
        if ($candidature && $candidature->candidat) {
            $this->notificationService->notifyResultsPublished(
                $candidature->candidat,
                $candidature,
                $resultat
            );
        }
    }
}
