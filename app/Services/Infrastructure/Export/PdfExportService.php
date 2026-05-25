<?php

namespace App\Services\Infrastructure\Export;

use App\Services\Infrastructure\Pdf\EcoleDocumentPdfService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelPdf\Facades\Pdf;

class PdfExportService
{
    public function __construct(
        private readonly EcoleDocumentPdfService $ecoleDocumentService
    ) {}

    /**
     * Generate PDF from view
     */
    public function generateFromView(string $view, array $data, string $filename = 'document.pdf', string $orientation = 'portrait')
    {
        $pdf = Pdf::view($view, $data)->format('a4');

        if ($orientation === 'landscape') {
            $pdf = $pdf->landscape();
        }

        return $pdf->download($filename);
    }

    /**
     * Generate convocation PDF
     */
    public function generateConvocation(object $candidature, string $filename = 'convocation.pdf')
    {
        $ecole = $candidature->concours->ecole;
        $ecoleHeader = $this->ecoleDocumentService->generateOfficialHeader($ecole);

        $data = [
            'candidature' => $candidature,
            'candidat' => $candidature->candidat,
            'concours' => $candidature->concours,
            'centre' => $candidature->centre,
            'salle' => $candidature->salle,
            'ecole' => $ecole,
            'ecoleHeader' => $ecoleHeader,
            'date_generation' => now()->format('d/m/Y H:i'),
        ];

        return $this->generateFromView('pdf.convocation', $data, $filename);
    }

    /**
     * Generate attestation PDF
     */
    public function generateAttestation(object $candidat, string $type, string $filename = 'attestation.pdf')
    {

        $ecole = $candidat->candidatures->first()?->concours->ecole;
        $ecoleHeader = $ecole ? $this->ecoleDocumentService->generateOfficialHeader($ecole) : null;

        $data = [
            'candidat' => $candidat,
            'type' => $type,
            'ecole' => $ecole,
            'ecoleHeader' => $ecoleHeader,
            'date_generation' => now()->format('d/m/Y'),
            'numero_attestation' => 'ATT-' . now()->format('Ymd') . '-' . $candidat->id,
        ];

        return $this->generateFromView('pdf.attestation', $data, $filename);
    }

    /**
     * Generate results PDF
     */
    public function generateResultats(Collection $resultats, object $concours, string $filename = 'resultats.pdf')
    {
        $ecole = $concours->ecole;
        $ecoleHeader = $this->ecoleDocumentService->generateOfficialHeader($ecole);

        $data = [
            'resultats' => $resultats,
            'concours' => $concours,
            'ecole' => $ecole,
            'ecoleHeader' => $ecoleHeader,
            'date_generation' => now()->format('d/m/Y H:i'),
            'total_candidats' => $resultats->count(),
        ];

        return $this->generateFromView('pdf.resultats', $data, $filename, 'landscape');
    }

    /**
     * Generate liste émargement PDF
     */
    public function generateListeEmargement(Collection $candidatures, object $epreuve, string $filename = 'emargement.pdf')
    {

        $ecole = $candidatures->first()?->concours->ecole;
        $ecoleHeader = $ecole ? $this->ecoleDocumentService->generateOfficialHeader($ecole) : null;

        $data = [
            'candidatures' => $candidatures,
            'epreuve' => $epreuve,
            'ecole' => $ecole,
            'ecoleHeader' => $ecoleHeader,
            'date' => now()->format('d/m/Y'),
            'total' => $candidatures->count(),
        ];

        return $this->generateFromView('pdf.emargement', $data, $filename);
    }

    /**
     * Generate reçu paiement PDF
     */
    public function generateRecuPaiement(object $paiement, string $filename = 'recu.pdf')
    {
        $ecole = $paiement->candidature->concours->ecole;
        $ecoleHeader = $this->ecoleDocumentService->generateOfficialHeader($ecole);

        $data = [
            'paiement' => $paiement,
            'candidature' => $paiement->candidature,
            'candidat' => $paiement->candidature->candidat,
            'ecole' => $ecole,
            'ecoleHeader' => $ecoleHeader,
            'date_generation' => now()->format('d/m/Y H:i'),
        ];

        return $this->generateFromView('pdf.recu-paiement', $data, $filename);
    }

    /**
     * Generate candidats par centre PDF
     */
    public function generateCandidatsParCentre(Collection $candidatures, object $concours, ?object $session = null, string $filename = 'candidats_par_centre.pdf')
    {
        $ecole = $concours->ecole;
        $ecoleHeader = $this->ecoleDocumentService->generateOfficialHeader($ecole);

        $candidaturesParCentre = $candidatures->groupBy(function ($candidature) {
            return $candidature->centreExamen?->id ?? 'sans_centre';
        })->map(function ($candidaturesGroupe, $centreId) {
            return [
                'centre' => $candidaturesGroupe->first()->centreExamen,
                'candidatures' => $candidaturesGroupe->sortBy(function ($c) {
                    return $c->candidat->nom_cand . ' ' . $c->candidat->prenom_cand;
                }),
            ];
        });

        $dateExamen = null;
        if ($concours->plannings && $concours->plannings->isNotEmpty()) {
            $firstPlanning = $concours->plannings->sortBy('date_epreuve')->first();
            $dateExamen = $firstPlanning->date_epreuve ? Carbon::parse($firstPlanning->date_epreuve)->format('d/m/Y') : null;
        }

        $data = [
            'candidaturesParCentre' => $candidaturesParCentre,
            'concours' => $concours,
            'session' => $session,
            'ecole' => $ecole,
            'ecoleHeader' => $ecoleHeader,
            'dateExamen' => $dateExamen,
            'dateGeneration' => now()->format('d/m/Y H:i'),
        ];

        return $this->generateFromView('pdf.candidats-par-centre', $data, $filename, 'landscape');
    }

    /**
     * Generate planning PDF
     */
    public function generatePlanning(Collection $plannings, object $concours, ?object $session = null, string $filename = 'planning.pdf')
    {
        $ecole = $concours->ecole;
        $ecoleHeader = $this->ecoleDocumentService->generateOfficialHeader($ecole);

        $data = [
            'plannings' => $plannings,
            'concours' => $concours,
            'session' => $session,
            'ecole' => $ecole,
            'ecoleHeader' => $ecoleHeader,
            'dateGeneration' => now()->format('d/m/Y H:i'),
        ];

        return $this->generateFromView('pdf.planning', $data, $filename, 'landscape');
    }

    public function generateFicheConcours(
        object $concours,
        ?object $session,
        Collection $plannings,
        array $stats = [],
        ?array $paymentConfig = null,
        ?Collection $centres = null,
        ?Collection $filieres = null,
        string $filename = 'fiche_concours.pdf'
    ) {
        $ecole = $concours->ecole;
        $ecoleHeader = $ecole ? $this->ecoleDocumentService->generateOfficialHeader($ecole) : null;

        $data = [
            'concours' => $concours,
            'session' => $session,
            'plannings' => $plannings,
            'stats' => $stats,
            'paymentConfig' => $paymentConfig,
            'centres' => $centres,
            'filieres' => $filieres,
            'ecole' => $ecole,
            'ecoleHeader' => $ecoleHeader,
            'dateGeneration' => now()->format('d/m/Y H:i'),
        ];

        return $this->generateFromView('pdf.fiche-concours', $data, $filename);
    }

    public function generateEtatDocuments(Collection $candidatures, object $concours, ?object $session, string $filename = 'etat_documents.pdf')
    {
        $ecole = $concours->ecole;
        $ecoleHeader = $ecole ? $this->ecoleDocumentService->generateOfficialHeader($ecole) : null;

        $data = [
            'candidatures' => $candidatures,
            'concours' => $concours,
            'session' => $session,
            'documentsRequis' => $concours->documentsRequis ?? collect(),
            'ecole' => $ecole,
            'ecoleHeader' => $ecoleHeader,
            'dateGeneration' => now()->format('d/m/Y H:i'),
        ];

        return $this->generateFromView('pdf.etat-documents', $data, $filename);
    }

    public function generateRepartitionCandidats(Collection $filieres, array $candidaturesParCentre, object $concours, ?object $session, string $filename = 'repartition_candidats.pdf')
    {
        $ecole = $concours->ecole;
        $ecoleHeader = $ecole ? $this->ecoleDocumentService->generateOfficialHeader($ecole) : null;

        $data = [
            'filieres' => $filieres,
            'candidaturesParCentre' => $candidaturesParCentre,
            'concours' => $concours,
            'session' => $session,
            'ecole' => $ecole,
            'ecoleHeader' => $ecoleHeader,
            'dateGeneration' => now()->format('d/m/Y H:i'),
        ];

        return $this->generateFromView('pdf.repartition-candidats', $data, $filename);
    }

    public function generateStatistiquesConcours(
        object $concours,
        ?object $session,
        array $stats,
        Collection $filieres,
        Collection $centres,
        ?array $paymentConfig,
        string $filename = 'statistiques_concours.pdf'
    ) {
        $ecole = $concours->ecole;
        $ecoleHeader = $ecole ? $this->ecoleDocumentService->generateOfficialHeader($ecole) : null;

        $data = [
            'concours' => $concours,
            'session' => $session,
            'stats' => $stats,
            'filieres' => $filieres,
            'centres' => $centres,
            'paymentConfig' => $paymentConfig,
            'ecole' => $ecole,
            'ecoleHeader' => $ecoleHeader,
            'dateGeneration' => now()->format('d/m/Y H:i'),
        ];

        return $this->generateFromView('pdf.statistiques-concours', $data, $filename);
    }

    /**
     * Generate liste concours PDF
     */
    public function exportConcours(Collection $concours, string $filename = 'liste_concours.pdf')
    {
        // Load relations for each concours item
        $concours->each(function ($c) {
            if (! $c->relationLoaded('ecole')) {
                $c->load('ecole');
            }
            if (! $c->relationLoaded('sessions')) {
                $c->load(['sessions' => function ($query) {
                    $query->where('est_active', true)->orderBy('date_debut_depot', 'desc')->limit(1);
                }]);
            }
        });

        $data = [
            'concours' => $concours,
            'dateGeneration' => now()->format('d/m/Y H:i'),
        ];

        return $this->generateFromView('pdf.liste-concours', $data, $filename, 'landscape');
    }
}
