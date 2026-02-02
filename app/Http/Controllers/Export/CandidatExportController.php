<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Exports\Candidats\CandidatsExport;
use App\Services\Infrastructure\Pdf\ConvocationPdfService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CandidatExportController extends Controller
{
  public function __construct(
    private readonly ConvocationPdfService $convocationService
  ) {}

  /**
   * Exporter la liste des candidats en Excel
   */
  public function exportCandidatsExcel(Request $request)
  {
    $concoursId = $request->query('concours_id');
    $filters = $request->only(['statut', 'filiere_id', 'centre_id']);

    $filename = 'candidats_' . now()->format('Y-m-d_His') . '.xlsx';

    return Excel::download(new CandidatsExport($concoursId, $filters), $filename);
  }

  /**
   * Générer une convocation PDF pour un candidat
   */
  public function exportConvocationPdf(string $candidatureId)
  {
    $candidature = \App\Models\Candidature::findOrFail($candidatureId);

    $pdf = $this->convocationService->genererConvocation($candidature);

    $filename = 'convocation_' . ($candidature->code_cand_def ?? $candidature->code_cand_temp) . '.pdf';

    return $pdf->download($filename);
  }

  /**
   * Générer les convocations groupées pour un concours
   */
  public function exportConvocationsGroupees(string $concoursId)
  {
    $pdf = $this->convocationService->genererConvocationsGroupees($concoursId);

    $filename = 'convocations_concours_' . $concoursId . '_' . now()->format('Y-m-d') . '.pdf';

    return $pdf->download($filename);
  }

  /**
   * Générer les convocations par centre
   */
  public function exportConvocationsParCentre(string $centreId, string $concoursId)
  {
    $pdf = $this->convocationService->genererConvocationsParCentre($centreId, $concoursId);

    $filename = 'convocations_centre_' . $centreId . '_' . now()->format('Y-m-d') . '.pdf';

    return $pdf->download($filename);
  }
}
