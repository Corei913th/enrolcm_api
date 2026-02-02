<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Export\ExportController;
use App\Http\Controllers\Export\PaiementExportController;
use App\Http\Controllers\Export\ResultatExportController;

/**
 * Routes Admin - Exports (Excel & PDF)
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

// Excel
Route::get('candidats/excel', [ExportController::class, 'exportCandidatsExcel']);
Route::get('concours/excel', [ExportController::class, 'exportConcoursExcel']);

// PDF
Route::get('concours/pdf', [ExportController::class, 'generateConcoursPdf']);

Route::get('concours/{concours}/sessions/{session}/resultats/excel', [ExportController::class, 'exportResultatsExcel']);
Route::get('concours/{concours}/sessions/{session}/planning/excel', [ExportController::class, 'exportPlanningExcel']);

Route::get('concours/{concours}/candidats/excel', [ExportController::class, 'exportCandidatsParConcoursExcel']);
Route::get('concours/{concours}/candidats/par-region/excel', [ExportController::class, 'exportCandidatsParRegionExcel']);
Route::get('concours/{concours}/candidats/par-filiere/excel', [ExportController::class, 'exportCandidatsParFiliereExcel']);
Route::get('concours/{concours}/candidats-par-centre/excel', [ExportController::class, 'exportCandidatsParCentre']);
Route::get('concours/{concours}/fiche/excel', [ExportController::class, 'exportFicheConcoursExcel']);
Route::get('concours/{concours}/etat-documents/excel', [ExportController::class, 'exportEtatDocumentsExcel']);
Route::get('concours/{concours}/repartition-candidats/excel', [ExportController::class, 'exportRepartitionCandidatsExcel']);
Route::get('concours/{concours}/statistiques/excel', [ExportController::class, 'exportStatistiquesConcoursExcel']);
Route::get('concours/{concours}/resultats-detailles/excel', [ResultatExportController::class, 'exportResultatsExcel']);
Route::get('paiements/journal/excel', [PaiementExportController::class, 'exportJournalPaiements']);

// PDF
Route::get('candidatures/{candidature}/convocation', [ExportController::class, 'generateConvocation']);
Route::get('concours/{concours}/fiche/pdf', [ExportController::class, 'generateFicheConcoursPdf']);
Route::get('concours/{concours}/etat-documents/pdf', [ExportController::class, 'generateEtatDocumentsPdf']);
Route::get('concours/{concours}/repartition-candidats/pdf', [ExportController::class, 'generateRepartitionCandidatsPdf']);
Route::get('concours/{concours}/statistiques/pdf', [ExportController::class, 'generateStatistiquesConcoursPdf']);
Route::get('concours/{concours}/sessions/{session}/emargement', [ExportController::class, 'generateEmargement']);
Route::get('concours/{concours}/sessions/{session}/resultats/pdf', [ExportController::class, 'generateResultatsPdf']);
Route::get('concours/{concours}/sessions/{session}/planning/pdf', [ExportController::class, 'exportPlanningPdf']);
Route::get('concours/{concours}/candidats-par-centre/pdf', [ExportController::class, 'generateCandidatsParCentrePdf']);
