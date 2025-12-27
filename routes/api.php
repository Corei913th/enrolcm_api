<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EcoleController;

Route::middleware('auth:sanctum')->group(function () {
   Route::apiResource('ecoles', EcoleController::class);
   Route::patch('ecoles/{ecole}/toggle', [EcoleController::class, 'toggle']);
});
