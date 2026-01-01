<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NiveauController;

Route::prefix('niveaux')->group(function () {
    // Liste des niveaux actifs (pour les sélections)
    Route::get('actifs', [NiveauController::class, 'active']);
    
    // Récupérer par code
    Route::get('code/{code}', [NiveauController::class, 'showByCode']);
    
    // CRUD standard
    Route::get('/', [NiveauController::class, 'index']);
    Route::post('/', [NiveauController::class, 'store']);
    Route::get('{niveau}', [NiveauController::class, 'show']);
    Route::put('{niveau}', [NiveauController::class, 'update']);
    Route::delete('{niveau}', [NiveauController::class, 'destroy']);
    
    // Toggle statut
    Route::patch('{niveau}/toggle-status', [NiveauController::class, 'toggleStatus']);
});
