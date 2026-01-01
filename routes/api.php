<?php


use Illuminate\Support\Facades\Route;

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
    });

    
// Middleware désactivé temporairement pour les tests
Route::prefix('v1')->group(function () {
    require __DIR__.'/api/filieres.php';
});
