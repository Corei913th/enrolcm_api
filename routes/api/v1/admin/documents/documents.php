<?php

use App\Http\Controllers\Admin\Documents\DocumentRequisController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Admin - Documents Requis
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */
Route::prefix('requis')->group(function () {
    Route::get('/{concoursId}', [DocumentRequisController::class, 'index']);
    Route::post('/', [DocumentRequisController::class, 'store']);
    Route::get('/{concoursId}/{documentId}', [DocumentRequisController::class, 'show']);
    Route::put('/{concoursId}/{documentId}', [DocumentRequisController::class, 'update']);
    Route::delete('/{concoursId}/{documentId}', [DocumentRequisController::class, 'destroy']);
});
