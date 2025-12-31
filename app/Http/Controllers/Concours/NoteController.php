<?php

namespace App\Http\Controllers\Concours;

use App\Http\Controllers\Controller;
use App\Services\Concours\NoteService;
use App\Http\Requests\Concours\SaisirNoteRequest;
use App\Http\Requests\Concours\ModifierNoteRequest;
use App\Models\Note;
use App\Exceptions\ConcoursException;
use App\Http\Resources\NoteResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Controller de gestion des notes d'examen.
 */
class NoteController extends Controller
{
  public function __construct(
    private readonly NoteService $noteService
  ) {}

  /**
   * Saisir une note pour une épreuve.
   *
   * Endpoint : POST /api/concours/{concoursId}/sessions/{sessionId}/notes
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param SaisirNoteRequest $request Requête validée
   *
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
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param string $noteId ID de la note
   *
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
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param string $noteId ID de la note
   * @param ModifierNoteRequest $request Requête validée
   *
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
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param string $noteId ID de la note
   *
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
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param string $candidatureId ID de la candidature
   *
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
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param string $candidatureId ID de la candidature
   *
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
