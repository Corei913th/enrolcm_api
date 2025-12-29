<?php

use Illuminate\Support\Facades\Route;

Route::prefix('concours')->group(function () {
    require __DIR__ . '/api/concours.php';
});

Route::middleware('auth:sanctum')->group(function () {
    
});


