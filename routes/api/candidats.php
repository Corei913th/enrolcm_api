<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Candidats\CandidatController;


Route::post('/verify-pru', [CandidatController::class, 'verifyPRU']);
Route::post('/register', [CandidatController::class, 'register']);
Route::post('/login', [CandidatController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [CandidatController::class, 'me']);
    Route::put('/me', [CandidatController::class, 'updateProfile']);
});

// Admin routes
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/', [CandidatController::class, 'index']);
    Route::get('/stats', [CandidatController::class, 'stats']);
    Route::get('/pru/{pru}', [CandidatController::class, 'getByPRU']);
    Route::get('/{id}', [CandidatController::class, 'show']);
    Route::post('/{id}/activate', [CandidatController::class, 'activate']);
    Route::post('/{id}/deactivate', [CandidatController::class, 'deactivate']);
});