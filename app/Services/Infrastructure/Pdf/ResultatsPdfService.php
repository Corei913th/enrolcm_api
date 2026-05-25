<?php

namespace App\Services\Infrastructure\Pdf;

use App\Models\Concours;
use App\Services\Domain\Examen\ResultatService;
use Spatie\LaravelPdf\Facades\Pdf;

class ResultatsPdfService
{
    public function __construct(
        private readonly EcoleDocumentPdfService $ecoleDocumentService,
        private readonly ResultatService $resultatService
    ) {}

    /**
     * Génère la fiche des résultats par ordre de mérite
     *
     * @param  string  $concoursId  ID du concours
     * @param  string|null  $sessionId  ID de la session (optionnel)
     * @return \Barryvdh\DomPDF\PDF
     */
    public function genererFicheResultats(string $concoursId, ?string $sessionId = null)
    {
        $concours = Concours::with(['ecole', 'sessions'])->findOrFail($concoursId);

        if (! $concours->ecole) {
            throw new \Exception('Le concours doit être associé à une école');
        }

        // Récupérer la session
        $session = $sessionId
          ? $concours->sessions()->findOrFail($sessionId)
          : $concours->sessions()->first();

        if (! $session) {
            throw new \Exception('Aucune session trouvée pour ce concours');
        }

        // Récupérer les résultats par ordre de mérite
        $resultats = $this->resultatService->getResultatsParOrdreDeMerite($concoursId, $sessionId);

        // Générer l'en-tête officielle
        $ecoleHeader = $this->ecoleDocumentService->generateOfficialHeader($concours->ecole);

        // Préparer les données
        $data = [
            'concours' => $concours,
            'session' => $session,
            'ecole' => $concours->ecole,
            'ecoleHeader' => $ecoleHeader,
            'resultats' => $resultats,
            'dateGeneration' => now()->format('d/m/Y'),
            'heureGeneration' => now()->format('H:i'),
        ];

        // Générer le PDF
        return Pdf::view('pdf.fiche-resultats', $data)->format('a4')->landscape();
    }

    /**
     * Génère la fiche des résultats pour une filière spécifique
     *
     * @param  string  $concoursId  ID du concours
     * @param  string  $filiereId  ID de la filière
     * @param  string|null  $sessionId  ID de la session
     * @return \Barryvdh\DomPDF\PDF
     */
    public function genererFicheResultatsParFiliere(
        string $concoursId,
        string $filiereId,
        ?string $sessionId = null
    ) {
        $concours = Concours::with(['ecole', 'sessions'])->findOrFail($concoursId);

        if (! $concours->ecole) {
            throw new \Exception('Le concours doit être associé à une école');
        }

        $session = $sessionId
          ? $concours->sessions()->findOrFail($sessionId)
          : $concours->sessions()->first();

        // Récupérer les résultats filtrés par filière
        $resultats = $this->resultatService->getResultats(
            $concoursId,
            $filiereId,
            $sessionId
        );

        $ecoleHeader = $this->ecoleDocumentService->generateOfficialHeader($concours->ecole);

        $data = [
            'concours' => $concours,
            'session' => $session,
            'ecole' => $concours->ecole,
            'ecoleHeader' => $ecoleHeader,
            'resultats' => $resultats,
            'filiere' => $resultats->first()?->candidature->candidat->filiere,
            'dateGeneration' => now()->format('d/m/Y'),
            'heureGeneration' => now()->format('H:i'),
        ];

        return Pdf::view('pdf.fiche-resultats', $data)->format('a4')->landscape();
    }
}
