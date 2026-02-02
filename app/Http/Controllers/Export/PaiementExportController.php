<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Exports\Paiements\JournalPaiementsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PaiementExportController extends Controller
{
  /**
   * Exporter le journal des paiements en Excel
   */
  public function exportJournalPaiements(Request $request)
  {
    $filters = $request->only([
      'concours_id',
      'statut',
      'mode_paiement',
      'date_debut',
      'date_fin'
    ]);

    $filename = 'journal_paiements_' . now()->format('Y-m-d_His') . '.xlsx';

    return Excel::download(new JournalPaiementsExport($filters), $filename);
  }
}
