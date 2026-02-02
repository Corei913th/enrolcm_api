<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Documents\DocumentValidationController;

/**
 * Routes Admin - Validation des Documents
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

// Liste des documents en attente
Route::get('en-attente', [DocumentValidationController::class, 'enAttente']);

// Get validation statistics
Route::get('stats', [DocumentValidationController::class, 'stats']);

// Validation/Rejet de documents
Route::post('{document}/valider', [DocumentValidationController::class, 'valider']);
Route::post('{document}/rejeter', [DocumentValidationController::class, 'rejeter']);
