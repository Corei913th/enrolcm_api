<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Concours\NoteController;

/**
 * Routes Admin - Gestion des Notes
 * Prefix: /admin/concours/{concours}/sessions/{session}
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

// Candidats éligibles pour la saisie de notes
Route::get('candidats-eligibles-notes', [NoteController::class, 'getCandidatsEligibles']);

// CRUD des notes
Route::post('notes', [NoteController::class, 'saisirNote']);
Route::put('notes/{note}', [NoteController::class, 'modifierNote']);
Route::put('notes/{note}/validate', [NoteController::class, 'validerNote']);
Route::delete('notes/{note}', [NoteController::class, 'annulerNote']);

// Notes par candidature
Route::get('candidatures/{candidature}/notes', [NoteController::class, 'getNotesCandidat']);
Route::get('candidatures/{candidature}/moyenne', [NoteController::class, 'calculerMoyenne']);
