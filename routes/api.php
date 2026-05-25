<?php

use Illuminate\Support\Facades\Route;

/**
 * Point d'entrée principal de l'API
 *
 * Toutes les routes sont versionnées et organisées par domaine métier
 * Structure : /api/v1/{role}/{domaine}/{ressource}
 */

// API v1 - Architecture RESTful avec versioning
Route::prefix('v1')->group(base_path('routes/api/v1/routes.php'));
