<?php

use Illuminate\Support\Facades\Route;

/**
 * ============================================
 * SESSION CONTEXT ROUTES
 * ============================================
 *
 * Routes within: /admin/concours/{concours}/sessions/{session}
 *
 * Sub-resources:
 * - Planning (exam scheduling)
 * - Notes (grade entry)
 * - Resultats (results publication)
 */

// --- Planning des Épreuves ---
Route::prefix('plannings')->name('plannings.')->group(function () {
    require __DIR__ . '/planning.php';
});

// --- Saisie des Notes ---
Route::prefix('notes')->name('notes.')->group(function () {
    require __DIR__ . '/notes.php';
});

// --- Gestion des Résultats ---
Route::prefix('resultats')->name('resultats.')->group(function () {
    require __DIR__ . '/resultats.php';
});

// --- Affectation des Salles ---
Route::prefix('affectations')->name('affectations.')->group(function () {
    require __DIR__ . '/affectations.php';
});
