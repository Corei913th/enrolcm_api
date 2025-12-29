<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    require __DIR__ . '/api/auth.php';
});

Route::prefix('candidates')->group(function () {
    require __DIR__ . '/api/candidates.php';
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('candidats')->group(function () {
        require __DIR__ . '/api/candidats.php';
    });
});
