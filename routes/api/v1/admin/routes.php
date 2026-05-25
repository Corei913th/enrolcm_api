<?php

use Illuminate\Support\Facades\Route;

/**
 * Routes Admin API v1
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

// Analytics (Dashboard + Stats + Exports) - GARDER LES URLS ORIGINALES
Route::prefix('dashboard')->group(__DIR__ . '/analytics/analytics.php');
Route::prefix('stats')->group(__DIR__ . '/analytics/stats.php');
Route::prefix('exports')->group(__DIR__ . '/analytics/exports.php');

// Sessions
Route::prefix('sessions')->group(__DIR__ . '/sessions.php');

// Référentiel
Route::prefix('referentiel')->group(__DIR__ . '/referentiel/referentiel.php');

// Concours
Route::prefix('concours')->group(__DIR__ . '/concours/concours.php');

// Examen
Route::prefix('examen')->group(__DIR__ . '/examen/examen.php');

// Épreuves (définitions indépendantes)
Route::prefix('epreuves')->group(__DIR__ . '/epreuves.php');

// Candidats
Route::prefix('candidats')->group(function () {
    require __DIR__ . '/candidats/candidats.php';
    Route::prefix('documents')->group(__DIR__ . '/candidats/documents.php');
});

// Paiements
Route::prefix('paiements')->group(function () {
    require __DIR__ . '/paiements/paiements.php';
    Route::prefix('recus')->group(__DIR__ . '/paiements/recus.php');
    Route::prefix('validation')->group(__DIR__ . '/paiements/validation.php');
});

// Documents
Route::prefix('documents')->group(function () {
    require __DIR__ . '/documents/documents.php';
    Route::prefix('validation')->group(__DIR__ . '/documents/validation.php');
});

// Users
Route::prefix('users')->group(__DIR__ . '/users/users.php');

// Alertes (placeholder)
Route::prefix('alertes')->group(function () {
    Route::get('stats', fn () => response()->json([
        'success' => true,
        'data' => ['total' => 0, 'critiques' => 0, 'importantes' => 0, 'informatives' => 0],
    ]));
});
