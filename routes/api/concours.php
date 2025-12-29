<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Concours\ConcoursController;


Route::get('/ouverts', [ConcoursController::class, 'availables']);
Route::get('/{concours}', [ConcoursController::class, 'show']);
Route::get('/{concours}/payment-info', [ConcoursController::class, 'paymentInfo']);

// Protected routes (admin only)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/', [ConcoursController::class, 'index']);
    Route::post('/', [ConcoursController::class, 'store']);
    Route::put('/{concours}', [ConcoursController::class, 'update']);
    Route::delete('/{concours}', [ConcoursController::class, 'destroy']);
    Route::post('/{concours}/activate', [ConcoursController::class, 'activate']);
    Route::post('/{concours}/deactivate', [ConcoursController::class, 'deactivate']);
    Route::post('/{concours}/configure-payment', [ConcoursController::class, 'configurePayment']);
    Route::get('/{concours}/stats', [ConcoursController::class, 'stats']);
    
    // Session management
    Route::post('/{concours}/sessions', [ConcoursController::class, 'attachSession']);
    Route::delete('/{concours}/sessions/{session}', [ConcoursController::class, 'detachSession']);
    Route::put('/{concours}/sessions/{session}/state', [ConcoursController::class, 'changeSessionState']);
    Route::get('/{concours}/sessions/{session}/state', [ConcoursController::class, 'getSessionState']);
});
