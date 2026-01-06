<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Documents\DocumentRequisController;
use App\Http\Controllers\Admin\Documents\DocumentValidationController;

// Routes admin pour la gestion des documents
Route::middleware(['auth:sanctum', 'role:ADMIN'])->prefix('admin/documents')->group(function () {

  // Gestion des documents requis par concours
  Route::prefix('requis/{concoursId}')->group(function () {
    Route::get('/', [DocumentRequisController::class, 'index']);
    Route::post('/', [DocumentRequisController::class, 'store']);
    Route::get('/{documentId}', [DocumentRequisController::class, 'show']);
    Route::put('/{documentId}', [DocumentRequisController::class, 'update']);
    Route::delete('/{documentId}', [DocumentRequisController::class, 'destroy']);
  });

  // Validation des documents
  Route::prefix('validation')->group(function () {
    Route::get('/en-attente', [DocumentValidationController::class, 'index']);
    Route::post('/document/{documentId}/valider', [DocumentValidationController::class, 'validateDocument']);
    Route::get('/document/{documentId}/download', [DocumentValidationController::class, 'downloadDocument']);
  });
});
