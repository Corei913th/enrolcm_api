<?php


use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:ADMIN'])
    ->prefix('users')
    ->group(base_path('routes/api/users.php'));

Route::prefix('auth')->group(function () {
    require __DIR__ . '/api/auth.php';
});