<?php

use App\Http\Controllers\RegionController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Publiques - Régions
 * Données géographiques accessibles sans authentification
 */
Route::get('/', [RegionController::class, 'index']);
Route::get('actifs', [RegionController::class, 'actifs']);
