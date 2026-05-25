<?php

namespace App\Http\Controllers\Concours;

use App\Exceptions\ConcoursException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Concours\ModifierNoteRequest;
use App\Http\Requests\Concours\SaisirNoteRequest;
use App\Http\Resources\NoteResource;
use App\Services\Domain\Examen\NoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function __construct(
        private readonly NoteService $noteService
    ) {}

    /**
     * Obtenir les candidats éligibles à la saisie de notes.
     *
     * Endpoint : GET /admin/concours/{concoursId}/sessions/{sessionId}/candidats-eligibles-notes
     *
     * @param  string  $concoursId  ID du concours
     * @param  string  $sessionId  ID de la session
     * @return JsonResponse Réponse avec les candidats éligibles
     */
    public function getCandidatsEligibles(string $concoursId, string $sessionId, Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search']);
            $perPage = $request->input('per_page', 100); // Increased from 20 to 100

            $candidats = $this->noteService->getCandidatsEligiblesPourNotes($concoursId, $sessionId, $filters, $perPage);

            return api_paginated($candidats, 'Candidats éligibles à la saisie de notes');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Saisir une note pour une épreuve.
     *
     * Endpoint : POST /api/concours/{concoursId}/sessions/{sessionId}/notes
     *
     * @param  string  $concoursId  ID du concours
     * @param  string  $sessionId  ID de la session
     * @param  SaisirNoteRequest  $request  Requête validée
     * @return JsonResponse Réponse avec la note créée
     */
    public function saisirNote(string $concoursId, string $sessionId, SaisirNoteRequest $request): JsonResponse
    {
        try {
            $note = $this->noteService->saisirNote(
                $request->candidature_id,
                $request->epreuve_id,
                $request->valeur,
                $request->est_eliminatoire ?? false
            );

            return api_created(new NoteResource($note->load(['epreuve', 'candidature'])), 'Note saisie avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Valider une note saisie.
     *
     * Endpoint : PUT /api/concours/{concoursId}/sessions/{sessionId}/notes/{noteId}/validate
     *
     * @param  string  $concoursId  ID du concours
     * @param  string  $sessionId  ID de la session
     * @param  string  $noteId  ID de la note
     * @return JsonResponse Réponse avec la note validée
     */
    public function validerNote(string $concoursId, string $sessionId, string $noteId): JsonResponse
    {
        try {
            $note = $this->noteService->validerNote($noteId);

            return api_success(new NoteResource($note->load(['epreuve', 'candidature'])), 'Note validée avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Modifier une note (avant validation).
     *
     * Endpoint : PUT /api/concours/{concoursId}/sessions/{sessionId}/notes/{noteId}
     *
     * @param  string  $concoursId  ID du concours
     * @param  string  $sessionId  ID de la session
     * @param  string  $noteId  ID de la note
     * @param  ModifierNoteRequest  $request  Requête validée
     * @return JsonResponse Réponse avec la note modifiée
     */
    public function modifierNote(string $concoursId, string $sessionId, string $noteId, ModifierNoteRequest $request): JsonResponse
    {
        try {
            $note = $this->noteService->modifierNote(
                $noteId,
                $request->valeur,
                $request->est_eliminatoire ?? false
            );

            return api_success(new NoteResource($note->load(['epreuve', 'candidature'])), 'Note modifiée avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Annuler une note saisie.
     *
     * Endpoint : DELETE /api/concours/{concoursId}/sessions/{sessionId}/notes/{noteId}
     *
     * @param  string  $concoursId  ID du concours
     * @param  string  $sessionId  ID de la session
     * @param  string  $noteId  ID de la note
     * @return JsonResponse Réponse de succès
     */
    public function annulerNote(string $concoursId, string $sessionId, string $noteId): JsonResponse
    {
        try {
            $this->noteService->annulerNote($noteId);

            return api_success(null, 'Note annulée avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Obtenir les notes d'un candidat.
     *
     * Endpoint : GET /api/concours/{concoursId}/sessions/{sessionId}/candidatures/{candidatureId}/notes
     *
     * @param  string  $concoursId  ID du concours
     * @param  string  $sessionId  ID de la session
     * @param  string  $candidatureId  ID de la candidature
     * @return JsonResponse Réponse avec les notes
     */
    public function getNotesCandidat(string $concoursId, string $sessionId, string $candidatureId): JsonResponse
    {
        try {
            $notes = $this->noteService->getNotesCandidat($candidatureId);

            return api_success(NoteResource::collection($notes), 'Notes récupérées avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Calculer la moyenne générale d'un candidat.
     *
     * Endpoint : GET /api/concours/{concoursId}/sessions/{sessionId}/candidatures/{candidatureId}/moyenne
     *
     * @param  string  $concoursId  ID du concours
     * @param  string  $sessionId  ID de la session
     * @param  string  $candidatureId  ID de la candidature
     * @return JsonResponse Réponse avec la moyenne calculée
     */
    public function calculerMoyenne(string $concoursId, string $sessionId, string $candidatureId): JsonResponse
    {
        try {
            $resultat = $this->noteService->calculerMoyenneGenerale($candidatureId);

            return api_success($resultat, 'Moyenne calculée avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }
}
