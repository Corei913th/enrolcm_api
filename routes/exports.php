<?php

use App\Http\Controllers\Export\CandidatExportController;
use App\Http\Controllers\Export\EmargementExportController;
use App\Http\Controllers\Export\PaiementExportController;
use App\Http\Controllers\Export\ResultatExportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes d'Export
|--------------------------------------------------------------------------
|
| Routes pour tous les exports PDF et Excel de la plateforme
|
*/

Route::middleware(['auth:sanctum'])->prefix('exports')->group(function () {

  // ==================== CANDIDATS ====================
  Route::prefix('candidats')->group(function () {
    // Excel
    Route::get('/excel', [CandidatExportController::class, 'exportCandidatsExcel'])
      ->name('exports.candidats.excel');

    // PDF Convocations
    Route::get('/{candidatureId}/convocation', [CandidatExportController::class, 'exportConvocationPdf'])
      ->name('exports.candidat.convocation');

    Route::get('/concours/{concoursId}/convocations', [CandidatExportController::class, 'exportConvocationsGroupees'])
      ->name('exports.convocations.groupees');

    Route::get('/centre/{centreId}/concours/{concoursId}/convocations', [CandidatExportController::class, 'exportConvocationsParCentre'])
      ->name('exports.convocations.centre');
  });

  // ==================== RÉSULTATS ====================
  Route::prefix('resultats')->group(function () {
    // Excel
    Route::get('/concours/{concoursId}/excel', [ResultatExportController::class, 'exportResultatsExcel'])
      ->name('exports.resultats.excel');

    // PDF Relevés de notes
    Route::get('/{candidatureId}/releve', [ResultatExportController::class, 'exportRelevePdf'])
      ->name('exports.resultat.releve');

    Route::get('/concours/{concoursId}/releves', [ResultatExportController::class, 'exportRelevesGroupes'])
      ->name('exports.releves.groupes');
  });

  // ==================== PAIEMENTS ====================
  Route::prefix('paiements')->group(function () {
    // Excel
    Route::get('/journal', [PaiementExportController::class, 'exportJournalPaiements'])
      ->name('exports.paiements.journal');
  });

  // ==================== ÉMARGEMENT ====================
  Route::prefix('emargement')->group(function () {
    // PDF Listes d'émargement
    Route::get('/salle/{salleId}/planning/{planningEpreuveId}', [EmargementExportController::class, 'exportListeEmargement'])
      ->name('exports.emargement.salle');

    Route::get('/centre/{centreId}/concours/{concoursId}', [EmargementExportController::class, 'exportListesEmargementCentre'])
      ->name('exports.emargement.centre');

    Route::get('/salle/{salleId}/planning/{planningEpreuveId}/vierge', [EmargementExportController::class, 'exportFeuilleEmargementVierge'])
      ->name('exports.emargement.vierge');
  });
});
