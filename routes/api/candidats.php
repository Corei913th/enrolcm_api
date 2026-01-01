<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Candidats\CandidatController;

// Routes pour le candidat connecté
Route::get('me', [CandidatController::class, 'me']);
Route::put('me', [CandidatController::class, 'update']);


Route::middleware('role:ADMIN')->group(function () {
    Route::get('/', [CandidatController::class, 'index']);
    Route::get('stats', [CandidatController::class, 'stats']);
    Route::post('search', [CandidatController::class, 'search']);
    Route::get('numero-recu/{numero}', [CandidatController::class, 'getByNumeroRecu']);
    Route::get('check-numero-recu/{numero}', [CandidatController::class, 'checkNumeroRecu']);
    Route::get('{id}', [CandidatController::class, 'show']);
    Route::delete('{id}', [CandidatController::class, 'destroy']);
    Route::post('{id}/activate', [CandidatController::class, 'activate']);
});
