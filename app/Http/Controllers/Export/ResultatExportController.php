<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Exports\Resultats\ResultatsDetaillesExport;
use App\Services\Infrastructure\Pdf\RelevePdfService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ResultatExportController extends Controller
{
  public function __construct(
    private readonly RelevePdfService $releveService
  ) {}

  /**
   * Exporter les résultats détaillés en Excel
   */
  public function exportResultatsExcel(string $concoursId, Request $request)
  {
    $filters = $request->only(['filiere_id', 'decision', 'est_admis']);

    $filename = 'resultats_concours_' . $concoursId . '_' . now()->format('Y-m-d_His') . '.xlsx';

    return Excel::download(new ResultatsDetaillesExport($concoursId, $filters), $filename);
  }

  /**
   * Générer le relevé de notes PDF pour un candidat
   */
  public function exportRelevePdf(string $candidatureId)
  {
    $candidature = \App\Models\Candidature::findOrFail($candidatureId);

    $pdf = $this->releveService->genererReleveNotes($candidature);

    if (!$pdf) {
      return response()->json([
        'success' => false,
        'message' => 'Les résultats ne sont pas encore publiés'
      ], 400);
    }

    $filename = 'releve_notes_' . ($candidature->code_cand_def ?? $candidature->code_cand_temp) . '.pdf';

    return $pdf->download($filename);
  }

  /**
   * Générer les relevés de notes groupés
   */
  public function exportRelevesGroupes(string $concoursId)
  {
    $pdf = $this->releveService->genererRelevesGroupes($concoursId);

    $filename = 'releves_notes_concours_' . $concoursId . '_' . now()->format('Y-m-d') . '.pdf';

    return $pdf->download($filename);
  }
}
