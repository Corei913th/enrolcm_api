<?php

use App\Http\Controllers\Dashboard\DashboardController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Admin - Dashboard
 * Prefix: /admin/dashboard
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */
Route::get('global', [DashboardController::class, 'globalStats']);
Route::get('ecole/{ecoleId}', [DashboardController::class, 'ecoleStats']);
Route::get('my-ecole', [DashboardController::class, 'myEcoleStats']);
