<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Concours\ResultatController;
use App\Http\Controllers\Admin\AdmissionRuleController;

/**
 * Routes Admin - Résultats
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

// Gestion des résultats
Route::get('concours/{concours}/sessions/{session}', [ResultatController::class, 'getResultats']);
Route::post('concours/{concours}/sessions/{session}/calculer', [ResultatController::class, 'calculerResultats']);
Route::post('concours/{concours}/sessions/{session}/admissions', [ResultatController::class, 'determinerAdmissions']);
Route::post('concours/{concours}/sessions/{session}/admissions-globales', [ResultatController::class, 'determinerToutesAdmissions']);
Route::post('concours/{concours}/sessions/{session}/traitement-global', [ResultatController::class, 'traiterGlobalement']);

// Publication des résultats
Route::post('concours/{concours}/sessions/{session}/publier', [ResultatController::class, 'publierResultats']);
Route::post('concours/{concours}/sessions/{session}/depublier', [ResultatController::class, 'depublierResultats']);
Route::get('concours/{concours}/sessions/{session}/publication', [ResultatController::class, 'getDatePublication']);

// Résultats par candidat/filière
Route::get('candidatures/{candidature}', [ResultatController::class, 'getResultatCandidat']);
Route::get('filieres/{filiere}/classement', [ResultatController::class, 'getClassementFiliere']);

// Export PDF des résultats
Route::get('concours/{concours}/sessions/{session}/pdf', [ResultatController::class, 'telechargerFicheResultats']);
Route::get('concours/{concours}/sessions/{session}/filieres/{filiere}/pdf', [ResultatController::class, 'telechargerFicheResultatsParFiliere']);

// Admission rules
Route::get('concours/{concours}/sessions/{session}/admission-rules', [AdmissionRuleController::class, 'show']);
Route::post('concours/{concours}/sessions/{session}/admission-rules', [AdmissionRuleController::class, 'upsert']);
Route::delete('concours/{concours}/sessions/{session}/admission-rules', [AdmissionRuleController::class, 'destroy']);
