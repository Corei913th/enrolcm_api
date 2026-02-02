<?php

namespace App\Services\Infrastructure\Pdf;

use App\Models\Candidature;
use Spatie\LaravelPdf\Facades\Pdf;

class ConvocationPdfService
{
  public function __construct(
    private readonly EcoleDocumentPdfService $ecoleDocumentService
  ) {}

  /**
   * Générer une convocation individuelle pour un candidat
   * 
   * @param Candidature $candidature
   * @return \Spatie\LaravelPdf\PdfBuilder
   */
  public function genererConvocation(Candidature $candidature)
  {
    $candidature->load([
      'candidat.utilisateur',
      'concours.ecole',
      'session',
      'centreExamen.region',
      'affectationsSalles.salle',
      'plannings.epreuve'
    ]);

    // Préparer les épreuves avec leurs salles
    $epreuves = $candidature->plannings->map(function ($planning) use ($candidature) {
      $aff = $candidature->affectationsSalles->firstWhere('planning_epreuve_id', $planning->id);
      $planning->salleExamen = $aff?->salle;
      return $planning;
    })->sortBy('date_epreuve');

    // Générer le header officiel
    $ecoleHeader = $this->ecoleDocumentService->generateOfficialHeader($candidature->concours->ecole);

    $data = [
      'candidature' => $candidature,
      'candidat' => $candidature->candidat,
      'concours' => $candidature->concours,
      'session' => $candidature->session,
      'centre' => $candidature->centreExamen,
      'epreuves' => $epreuves,
      'ecole' => $candidature->concours->ecole,
      'ecoleHeader' => $ecoleHeader,
      'code_candidat' => $candidature->code_cand_def ?? $candidature->numero_inscription,
      'qrCode' => $candidature->qr_code,
      'date_generation' => now()->format('d/m/Y à H:i'),
    ];

    return Pdf::view('pdf.convocation', $data)->format('a4');
  }

  /**
   * Générer les convocations pour tous les candidats d'un concours
   * 
   * @param string $concoursId
   * @return \Spatie\LaravelPdf\PdfBuilder
   */
  public function genererConvocationsGroupees(string $concoursId)
  {
    $candidatures = Candidature::where('concours_id', $concoursId)
      ->where('statut_candidature', 'VALIDE')
      ->with([
        'candidat.utilisateur',
        'concours.ecole',
        'session',
        'centreExamen.region',
        'plannings.epreuve.matiere',
        'plannings.salleExamen'
      ])
      ->get();

    $ecole = $candidatures->first()?->concours->ecole;
    $ecoleHeader = $ecole ? $this->ecoleDocumentService->generateOfficialHeader($ecole) : null;

    $data = [
      'candidatures' => $candidatures,
      'concours' => $candidatures->first()?->concours,
      'ecole' => $ecole,
      'ecoleHeader' => $ecoleHeader,
      'date_generation' => now()->format('d/m/Y à H:i'),
    ];

    return Pdf::view('pdf.convocations-groupees', $data)->format('a4');
  }

  /**
   * Générer les convocations par centre d'examen
   * 
   * @param string $centreId
   * @param string $concoursId
   * @return \Spatie\LaravelPdf\PdfBuilder
   */
  public function genererConvocationsParCentre(string $centreId, string $concoursId)
  {
    $candidatures = Candidature::where('concours_id', $concoursId)
      ->where('centre_examen_id', $centreId)
      ->where('statut_candidature', 'VALIDE')
      ->with([
        'candidat.utilisateur',
        'concours.ecole',
        'session',
        'centreExamen.region',
        'plannings.epreuve.matiere',
        'plannings.salleExamen'
      ])
      ->orderBy('code_cand_def')
      ->get();

    $ecole = $candidatures->first()?->concours->ecole;
    $ecoleHeader = $ecole ? $this->ecoleDocumentService->generateOfficialHeader($ecole) : null;

    $data = [
      'candidatures' => $candidatures,
      'centre' => $candidatures->first()?->centreExamen,
      'concours' => $candidatures->first()?->concours,
      'ecole' => $ecole,
      'ecoleHeader' => $ecoleHeader,
      'date_generation' => now()->format('d/m/Y à H:i'),
    ];

    return Pdf::view('pdf.convocations-par-centre', $data)->format('a4');
  }
}
