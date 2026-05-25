<?php

use Illuminate\Support\Facades\Route;

/**
 * Routes Admin - Examen
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

// Affectations
Route::prefix('affectations')->group(function () {
    require __DIR__ . '/affectations.php';
});

// Planning
Route::prefix('planning')->group(function () {
    require __DIR__ . '/planning.php';
});

// Résultats
Route::prefix('resultats')->group(function () {
    require __DIR__ . '/resultats.php';
});
