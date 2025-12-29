<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    require __DIR__ . '/api/auth.php';
});
Route::middleware(['auth:sanctum', 'role:ADMIN'])
    ->prefix('users')
    ->group(base_path('routes/api/users.php'));


Route::prefix('candidats')->group(function () {
        require __DIR__ . '/api/candidats.php';
});
