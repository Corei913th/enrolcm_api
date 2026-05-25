<?php

use App\Http\Controllers\Export\ExportController;
use Illuminate\Support\Facades\Route;

/**
 * ============================================
 * EXPORTS ROUTES - API V1
 * ============================================
 *
 * Manages data exports (Excel, PDF)
 */
Route::prefix('exports')
    ->middleware(['auth:sanctum', 'role:ADMIN'])
    ->name('exports.')
    ->group(function () {

        // --- Excel Exports ---
        Route::prefix('excel')->name('excel.')->group(function () {
            Route::get('/candidats', [ExportController::class, 'exportCandidatsExcel'])
                ->name('candidats');

            Route::get(
                '/concours/{concours}/sessions/{session}/resultats',
                [ExportController::class, 'exportResultatsExcel']
            )
                ->name('resultats');

            Route::get(
                '/concours/{concours}/candidats-par-centre',
                [ExportController::class, 'exportCandidatsParCentre']
            )
                ->name('candidats_par_centre');
        });

        // --- PDF Exports ---
        Route::prefix('pdf')->name('pdf.')->group(function () {
            Route::get(
                '/candidatures/{candidature}/convocation',
                [ExportController::class, 'generateConvocation']
            )
                ->name('convocation');

            Route::get(
                '/concours/{concours}/sessions/{session}/emargement',
                [ExportController::class, 'generateEmargement']
            )
                ->name('emargement');

            Route::get(
                '/concours/{concours}/sessions/{session}/resultats',
                [ExportController::class, 'generateResultatsPdf']
            )
                ->name('resultats');

            Route::get(
                '/concours/{concours}/candidats-par-centre',
                [ExportController::class, 'generateCandidatsParCentrePdf']
            )
                ->name('candidats_par_centre');
        });
    });
