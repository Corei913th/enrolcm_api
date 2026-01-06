<?php

Route::middleware(['auth:sanctum', 'role:ADMIN'])
    ->prefix('users')
    ->group(base_path('routes/api/users.php'));

Route::prefix('auth')->group(function () {
    require __DIR__ . '/api/auth.php';
});

Route::prefix('candidates')->group(function () {
    require __DIR__ . '/api/candidates.php';
});

Route::prefix('candidats')->group(function () {
    require __DIR__ . '/api/candidats.php';
});

Route::middleware('auth:sanctum', 'role:ADMIN')->group(function () {
    Route::prefix('departements')->group(function () {
        require __DIR__ . '/api/departements.php';
    });
    require __DIR__ . '/api/matieres.php';
    require __DIR__ . '/api/niveaux.php';
});

// Admin documents routes
require __DIR__ . '/api/admin-documents.php';
