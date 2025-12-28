<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EcoleController;

Route::middleware('auth:sanctum')->prefix('ecoles')->group(function () {
    // Liste des écoles actives (pour les sélections)
    Route::get('actives', [EcoleController::class, 'active']);
    
    // Récupérer par code
    Route::get('code/{code}', [EcoleController::class, 'showByCode']);
    
    // Toggle statut
    Route::patch('{ecole}/toggle-status', [EcoleController::class, 'toggleStatus']);
    
    // CRUD standard
    Route::get('/', [EcoleController::class, 'index']);
    Route::post('/', [EcoleController::class, 'store']);
    Route::get('{ecole}', [EcoleController::class, 'show']);
    Route::put('{ecole}', [EcoleController::class, 'update']);
    Route::delete('{ecole}', [EcoleController::class, 'destroy']);
});
