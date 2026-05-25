<?php

use App\Http\Controllers\Concours\ConcoursController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Publiques - Concours
 * Informations sur les concours ouverts
 */

// Liste des concours ouverts
Route::get('ouverts', [ConcoursController::class, 'availables']);

// Détails d'un concours
Route::get('{concours}', [ConcoursController::class, 'show'])
    ->where('concours', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

// Informations de paiement
Route::get('{concours}/payment-info', [ConcoursController::class, 'paymentInfo'])
    ->where('concours', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

Route::get('{concours}/filieres', [ConcoursController::class, 'listFilieresAttachees']);
