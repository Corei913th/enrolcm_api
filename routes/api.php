<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    require __DIR__ . '/api/auth.php';
});

Route::prefix('payment')->group(function () {
    require __DIR__ . '/api/payment.php';
});

Route::middleware('auth:sanctum')->group(function () {
    
});
