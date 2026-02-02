<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\PaiementController;

/**
 * Routes Admin - Gestion des Paiements
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

// Liste et recherche
Route::get('/', [PaiementController::class, 'index']);
Route::get('pending', [PaiementController::class, 'pending']);
Route::get('{id}', [PaiementController::class, 'show']);

// Validation/Rejet
Route::post('{id}/validate', [PaiementController::class, 'validate']);
Route::post('{id}/reject', [PaiementController::class, 'reject']);
