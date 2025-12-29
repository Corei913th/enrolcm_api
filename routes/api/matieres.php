<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MatiereController;

Route::prefix('matieres')->group(function () {
    // Liste des matières actives (pour les sélections)
    Route::get('actives', [MatiereController::class, 'active']);
    
    // Récupérer par code
    Route::get('code/{code}', [MatiereController::class, 'showByCode']);
    
    // CRUD standard
    Route::get('/', [MatiereController::class, 'index']);
    Route::post('/', [MatiereController::class, 'store']);
    Route::get('{matiere}', [MatiereController::class, 'show']);
    Route::put('{matiere}', [MatiereController::class, 'update']);
    Route::delete('{matiere}', [MatiereController::class, 'destroy']);
    
    // Toggle statut
    Route::patch('{matiere}/toggle-status', [MatiereController::class, 'toggleStatus']);
});
