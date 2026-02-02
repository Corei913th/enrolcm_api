<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionController extends Controller
{
  /**
   * Liste de toutes les sessions avec filtres
   */
  public function index(Request $request): JsonResponse
  {
    $query = Session::query();

    // Filtre par statut actif
    if ($request->has('est_actif')) {
      $query->where('est_actif', $request->boolean('est_actif'));
    }

    // Filtre par statut de session
    if ($request->has('statut_session')) {
      $query->where('statut_session', $request->input('statut_session'));
    }

    // Recherche
    if ($request->has('search')) {
      $search = $request->input('search');
      $query->where(function ($q) use ($search) {
        $q->where('libelle_session', 'like', "%{$search}%")
          ->orWhere('desc_session', 'like', "%{$search}%");
      });
    }

    $sessions = $query->orderBy('created_at', 'desc')
      ->paginate($request->input('per_page', 20));

    return api_paginated($sessions, 'Liste des sessions');
  }

  /**
   * Sessions disponibles (actives et ouvertes aux inscriptions)
   */
  public function disponibles(Request $request): JsonResponse
  {
    $sessions = Session::where('est_actif', true)
      ->where('statut_session', \App\Enums\StatutSession::OUVERT->value)
      ->orderBy('libelle_session', 'desc')
      ->get();

    return api_success($sessions, 'Sessions disponibles');
  }

  /**
   * Sessions actives
   */
  public function actives(Request $request): JsonResponse
  {
    $sessions = Session::where('est_actif', true)
      ->orderBy('libelle_session', 'desc')
      ->get();

    return api_success($sessions, 'Sessions actives');
  }

  /**
   * Détails d'une session
   */
  public function show(string $id): JsonResponse
  {
    $session = Session::with(['concours', 'candidatures'])
      ->withCount(['concours', 'candidatures'])
      ->findOrFail($id);

    return api_success($session);
  }

  /**
   * Créer une session
   */
  public function store(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'libelle_session' => 'required|string|max:200|unique:sessions,libelle_session',
      'desc_session' => 'nullable|string',
      'statut_session' => 'required|string',
      'date_ouverture_inscription' => 'nullable|date',
      'date_fermeture_inscription' => 'nullable|date|after:date_ouverture_inscription',
      'est_actif' => 'boolean',
    ]);

    $session = Session::create($validated);

    return api_created($session, 'Session créée avec succès');
  }

  /**
   * Mettre à jour une session
   */
  public function update(string $id, Request $request): JsonResponse
  {
    $session = Session::findOrFail($id);

    $validated = $request->validate([
      'libelle_session' => 'sometimes|string|max:200|unique:sessions,libelle_session,' . $id,
      'desc_session' => 'nullable|string',
      'statut_session' => 'sometimes|string',
      'date_ouverture_inscription' => 'nullable|date',
      'date_fermeture_inscription' => 'nullable|date|after:date_ouverture_inscription',
      'est_actif' => 'boolean',
    ]);

    $session->update($validated);

    return api_updated($session, 'Session mise à jour avec succès');
  }

  /**
   * Supprimer une session
   */
  public function destroy(string $id): JsonResponse
  {
    $session = Session::findOrFail($id);

    // Vérifier s'il y a des concours attachés
    if ($session->concours()->exists()) {
      return api_error('Impossible de supprimer cette session car elle a des concours attachés', 400);
    }

    $session->delete();

    return api_deleted('Session supprimée avec succès');
  }

  /**
   * Activer une session
   */
  public function activate(string $id): JsonResponse
  {
    $session = Session::findOrFail($id);
    $session->update(['est_actif' => true]);

    return api_success($session, 'Session activée avec succès');
  }

  /**
   * Désactiver une session
   */
  public function deactivate(string $id): JsonResponse
  {
    $session = Session::findOrFail($id);
    $session->update(['est_actif' => false]);

    return api_success($session, 'Session désactivée avec succès');
  }

  /**
   * Statistiques d'une session
   */
  public function stats(string $id): JsonResponse
  {
    $session = Session::withCount([
      'concours',
      'candidatures',
      'candidatures as candidatures_validees' => function ($query) {
        $query->where('statut_candidature', 'VALIDE');
      }
    ])->findOrFail($id);

    $stats = [
      'session' => [
        'id' => $session->id,
        'libelle' => $session->libelle_session,
        'statut' => $session->statut_session,
        'est_actif' => $session->est_actif,
      ],
      'total_concours' => $session->concours_count,
      'total_candidatures' => $session->candidatures_count,
      'candidatures_validees' => $session->candidatures_validees,
    ];

    return api_success($stats);
  }
}
