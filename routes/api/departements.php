<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartementController;

Route::prefix('departements')->group(function () {
    // Liste des départements actifs (pour les sélections)
    Route::get('actifs', [DepartementController::class, 'active']);
    
    // Récupérer par code
    Route::get('code/{code}', [DepartementController::class, 'showByCode']);
    
    // CRUD standard
    Route::get('/', [DepartementController::class, 'index']);
    Route::post('/', [DepartementController::class, 'store']);
    Route::get('{departement}', [DepartementController::class, 'show']);
    Route::put('{departement}', [DepartementController::class, 'update']);
    Route::delete('{departement}', [DepartementController::class, 'destroy']);
    
    // Toggle statut
    Route::patch('{departement}/toggle-status', [DepartementController::class, 'toggleStatus']);
});
