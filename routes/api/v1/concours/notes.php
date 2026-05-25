<?php

use App\Http\Controllers\Concours\NoteController;
use Illuminate\Support\Facades\Route;

/**
 * ============================================
 * NOTES ROUTES
 * ============================================
 *
 * Context: /admin/concours/{concours}/sessions/{session}/notes
 *
 * Manages grade entry for exams
 */

// List épreuves for this session
Route::get('/epreuves', [NoteController::class, 'listEpreuves'])
    ->name('epreuves.index');

// Notes for specific épreuve
Route::prefix('epreuves/{epreuve}')->name('epreuves.')->group(function () {
    Route::get('/notes', [NoteController::class, 'listNotes'])
        ->name('notes.index');

    Route::post('/notes', [NoteController::class, 'saisirNote'])
        ->name('notes.store');

    Route::put('/notes/{note}', [NoteController::class, 'modifierNote'])
        ->name('notes.update');

    Route::delete('/notes/{note}', [NoteController::class, 'supprimerNote'])
        ->name('notes.destroy');
});
