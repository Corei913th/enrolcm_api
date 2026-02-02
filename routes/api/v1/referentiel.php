<?php

use Illuminate\Support\Facades\Route;

/**
 * ============================================
 * RÉFÉRENTIEL ROUTES - API V1
 * ============================================
 * 
 * Manages reference data (écoles, départements, filières, etc.)
 */

Route::prefix('admin')
  ->middleware(['auth:sanctum', 'role:ADMIN'])
  ->name('admin.')
  ->group(function () {

    // Écoles
    require __DIR__ . '/referentiel/ecoles.php';

    // Départements
    require __DIR__ . '/referentiel/departements.php';

    // Filières
    require __DIR__ . '/referentiel/filieres.php';

    // Matières
    require __DIR__ . '/referentiel/matieres.php';

    // Niveaux
    require __DIR__ . '/referentiel/niveaux.php';

    // Centres
    require __DIR__ . '/referentiel/centres.php';
  });
