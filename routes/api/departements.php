<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartementController;

// Routes de gestion des départements (admin uniquement)
Route::middleware('auth:sanctum', 'role:ADMIN')->group(function () {
    Route::get('/departements', [DepartementController::class, 'index']);
    Route::post('/departements', [DepartementController::class, 'store']);
    Route::get('/departements/{departement}', [DepartementController::class, 'show']);
    Route::put('/departements/{departement}', [DepartementController::class, 'update']);
    Route::delete('/departements/{departement}', [DepartementController::class, 'destroy']);
    Route::post('/departements/{departement}/activate', [DepartementController::class, 'activate']);
    Route::post('/departements/{departement}/deactivate', [DepartementController::class, 'deactivate']);
    Route::get('/departements/{departement}/stats', [DepartementController::class, 'stats']);
});
