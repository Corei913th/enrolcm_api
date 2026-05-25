<?php

use App\Http\Controllers\Candidat\Documents\DocumentController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Candidat - Documents
 * Middleware appliqué : auth:sanctum + role:CANDIDAT
 */

// Documents requis pour un concours
Route::get('requis/{concoursId}', [DocumentController::class, 'documentsRequis']);

// Soumission de documents
Route::post('submit', [DocumentController::class, 'submitDocument']);

// Statut des documents pour une candidature
Route::get('status/{candidatureId}', [DocumentController::class, 'documentStatus']);

// Téléchargement d'un document
Route::get('download/{documentId}', [DocumentController::class, 'downloadDocument']);
