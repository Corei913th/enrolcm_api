<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Concours\ConcoursController;
use App\Http\Controllers\Concours\NoteController;


Route::get('/ouverts', [ConcoursController::class, 'availables']);
Route::get('/{concours}', [ConcoursController::class, 'show']);
Route::get('/{concours}/payment-info', [ConcoursController::class, 'paymentInfo']);

// Protected routes (admin only)
Route::middleware('auth:sanctum',  'role:ADMIN')->group(function () {
    Route::get('/', [ConcoursController::class, 'index']);
    Route::post('/', [ConcoursController::class, 'store']);
    Route::put('/{concours}', [ConcoursController::class, 'update']);
    Route::delete('/{concours}', [ConcoursController::class, 'destroy']);
    Route::post('/{concours}/activate', [ConcoursController::class, 'activate']);
    Route::post('/{concours}/deactivate', [ConcoursController::class, 'deactivate']);
    Route::post('/{concours}/configure-payment', [ConcoursController::class, 'configurePayment']);
    Route::get('/{concours}/stats', [ConcoursController::class, 'stats']);

    // Session management
    Route::post('/{concours}/attach-session', [ConcoursController::class, 'attachToSession']);
    Route::post('/{concours}/sessions', [ConcoursController::class, 'attachSession']);
    Route::delete('/{concours}/sessions/{session}', [ConcoursController::class, 'detachSession']);
    Route::put('/{concours}/sessions/{session}/state', [ConcoursController::class, 'changeSessionState']);
    Route::get('/{concours}/sessions/{session}/state', [ConcoursController::class, 'getSessionState']);

    // Gestion des filières par concours et session
    Route::get('/{concours}/sessions/{session}/filieres', [ConcoursController::class, 'listFilieres']);
    Route::post('/{concours}/sessions/{session}/filieres', [ConcoursController::class, 'attachFiliere']);
    Route::delete('/{concours}/sessions/{session}/filieres/{filiere}', [ConcoursController::class, 'detachFiliere']);
    Route::get('/{concours}/sessions/{session}/filieres/{filiere}/stats', [ConcoursController::class, 'getFiliereStats']);
    Route::put('/{concours}/sessions/{session}/filieres/{filiere}/places', [ConcoursController::class, 'updateFilierePlaces']);

    // Gestion des notes d'examen
    Route::post('/{concours}/sessions/{session}/notes', [NoteController::class, 'saisirNote']);
    Route::put('/{concours}/sessions/{session}/notes/{note}/validate', [NoteController::class, 'validerNote']);
    Route::put('/{concours}/sessions/{session}/notes/{note}', [NoteController::class, 'modifierNote']);
    Route::delete('/{concours}/sessions/{session}/notes/{note}', [NoteController::class, 'annulerNote']);
    Route::get('/{concours}/sessions/{session}/candidatures/{candidature}/notes', [NoteController::class, 'getNotesCandidat']);
    Route::get('/{concours}/sessions/{session}/candidatures/{candidature}/moyenne', [NoteController::class, 'calculerMoyenne']);
});
