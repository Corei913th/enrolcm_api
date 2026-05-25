<?php

use Illuminate\Support\Facades\Route;

/**
 * Routes Admin - Référentiel
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */
Route::prefix('centres')->group(base_path('routes/api/v1/admin/referentiel/centres.php'));
Route::prefix('departements')->group(base_path('routes/api/v1/admin/referentiel/departements.php'));
Route::prefix('ecoles')->group(base_path('routes/api/v1/admin/referentiel/ecoles.php'));
Route::prefix('filieres')->group(base_path('routes/api/v1/admin/referentiel/filieres.php'));
Route::prefix('matieres')->group(base_path('routes/api/v1/admin/referentiel/matieres.php'));
Route::prefix('niveaux')->group(base_path('routes/api/v1/admin/referentiel/niveaux.php'));
