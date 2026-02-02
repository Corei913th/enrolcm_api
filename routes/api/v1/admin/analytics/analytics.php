<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;

/**
 * Routes Admin - Dashboard
 * Prefix: /admin/dashboard
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

Route::get('global', [DashboardController::class, 'globalStats']);
Route::get('ecole/{ecoleId}', [DashboardController::class, 'ecoleStats']);
Route::get('my-ecole', [DashboardController::class, 'myEcoleStats']);
