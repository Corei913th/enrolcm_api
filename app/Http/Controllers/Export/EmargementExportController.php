<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Services\Infrastructure\Pdf\EmargementPdfService;
use Illuminate\Http\Request;

class EmargementExportController extends Controller
{
  public function __construct(
    private readonly EmargementPdfService $emargementService
  ) {}

  /**
   * Générer une liste d'émargement pour une salle et une épreuve
   */
  public function exportListeEmargement(string $salleId, string $planningEpreuveId)
  {
    $pdf = $this->emargementService->genererListeEmargement($salleId, $planningEpreuveId);

    $filename = 'emargement_salle_' . $salleId . '_' . now()->format('Y-m-d') . '.pdf';

    return $pdf->download($filename);
  }

  /**
   * Générer toutes les listes d'émargement pour un centre
   */
  public function exportListesEmargementCentre(string $centreId, string $concoursId)
  {
    $pdf = $this->emargementService->genererListesEmargementCentre($centreId, $concoursId);

    $filename = 'emargements_centre_' . $centreId . '_' . now()->format('Y-m-d') . '.pdf';

    return $pdf->download($filename);
  }

  /**
   * Générer une feuille d'émargement vierge
   */
  public function exportFeuilleEmargementVierge(string $salleId, string $planningEpreuveId, Request $request)
  {
    $nombreLignes = $request->query('lignes', 50);

    $pdf = $this->emargementService->genererFeuilleEmargementVierge($salleId, $planningEpreuveId, $nombreLignes);

    $filename = 'emargement_vierge_salle_' . $salleId . '_' . now()->format('Y-m-d') . '.pdf';

    return $pdf->download($filename);
  }
}
