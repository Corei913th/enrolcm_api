<?php


use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])
    ->prefix('users')
    ->group(base_path('routes/api/users.php'));

