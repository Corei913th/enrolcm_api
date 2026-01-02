<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\PaiementController;


Route::post('/', [PaiementController::class, 'store']);
Route::post('/verify-pru', [PaiementController::class, 'verifyPRU']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/', [PaiementController::class, 'index']);
    Route::get('/pending', [PaiementController::class, 'pending']);
    Route::get('/{id}', [PaiementController::class, 'show']);
    Route::post('/{id}/validate', [PaiementController::class, 'validate']);
    Route::post('/{id}/reject', [PaiementController::class, 'reject']);
});
