<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegionController;

/**
 * Routes Publiques - Régions
 * Données géographiques accessibles sans authentification
 */

Route::get('/', [RegionController::class, 'index']);
Route::get('actifs', [RegionController::class, 'actifs']);
