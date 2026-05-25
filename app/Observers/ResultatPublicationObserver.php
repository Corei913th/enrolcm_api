<?php

namespace App\Observers;

use App\Models\ResultatFinal;
use App\Models\ResultatPublication;
use App\Services\Domain\Notification\NotificationService;
use App\Services\Infrastructure\Logger\ActivityLoggerService;

class ResultatPublicationObserver
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly ActivityLoggerService $logger
    ) {}

    /**
     * Handle the ResultatPublication "created" event.
     */
    public function created(ResultatPublication $publication): void
    {
        $this->logger->logActivity('publication_created', 'resultat_publication', $publication->id, [
            'concours_id' => $publication->concours_id,
            'session_id' => $publication->session_id,
            'timer_actif' => $publication->timer_actif,
            'date_prevue' => $publication->date_publication_prevue,
        ]);
    }

    /**
     * Handle the ResultatPublication "updated" event.
     * Notifie TOUS les candidats lorsque les résultats sont publiés.
     */
    public function updated(ResultatPublication $publication): void
    {
        // Vérifier si est_publie vient de passer à true
        if (! $publication->isDirty('est_publie') || ! $publication->est_publie) {
            return;
        }

        $this->logger->logActivity('publication_published', 'resultat_publication', $publication->id, [
            'concours_id' => $publication->concours_id,
            'session_id' => $publication->session_id,
            'date_publication_effective' => $publication->date_publication_effective,
            'message_candidat' => $publication->message_candidat,
        ]);

        // Récupérer TOUS les résultats pour ce concours/session
        $resultats = ResultatFinal::whereHas('candidature', function ($q) use ($publication) {
            $q->where('concours_id', $publication->concours_id)
                ->where('session_id', $publication->session_id);
        })
            ->with(['candidature.candidat', 'candidature.concours'])
            ->get();

        // Notifier chaque candidat
        foreach ($resultats as $resultat) {
            $candidature = $resultat->candidature;
            if ($candidature && $candidature->candidat) {
                try {
                    $this->notificationService->notifyResultsPublished(
                        $candidature->candidat,
                        $candidature,
                        $resultat,
                        $publication->message_candidat
                    );
                } catch (\Exception $e) {
                    $this->logger->logActivity('notification_error', 'resultat_publication', $publication->id, [
                        'candidat_id' => $candidature->candidat->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->logger->logActivity('publication_notifications_sent', 'resultat_publication', $publication->id, [
            'nombre_notifications' => $resultats->count(),
        ]);
    }

    /**
     * Handle the ResultatPublication "deleted" event.
     */
    public function deleted(ResultatPublication $publication): void
    {
        $this->logger->logActivity('publication_deleted', 'resultat_publication', $publication->id, [
            'concours_id' => $publication->concours_id,
            'session_id' => $publication->session_id,
        ]);
    }
}
