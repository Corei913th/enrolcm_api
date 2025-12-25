<?php

use Illuminate\Support\Facades\Route;

Route::prefix('concours')->group(function () {
    require __DIR__ . '/api/concours.php';
});


Route::prefix('auth')->group(function () {
    require __DIR__ . '/api/auth.php';
});

Route::middleware('auth:sanctum')->group(function () {
    
});

Route::middleware(['auth:sanctum', 'role:ADMIN'])
    ->prefix('users')
    ->group(base_path('routes/api/users.php'));

