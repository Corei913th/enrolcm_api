<?php

namespace App\Services\Concours;

use App\Models\Note;
use App\Models\Candidature;
use App\Models\Epreuve;
use App\Models\Concours;
use App\Models\Session;
use App\Enums\StatutNote;
use App\Exceptions\ConcoursException;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service de gestion des notes d'examen.
 *
 * Permet de :
 * - Saisir des notes pour les épreuves
 * - Valider et modifier les notes
 * - Calculer les moyennes
 * - Gérer le workflow de saisie
 */
class NoteService
{
  /**
   * Saisir une note pour une épreuve.
   *
   * @param string $candidatureId ID de la candidature
   * @param string $epreuveId ID de l'épreuve
   * @param float $valeur Valeur de la note (0-20)
   * @param bool $estEliminatoire Si la note est éliminatoire
   *
   * @return Note Note créée
   *
   * @throws ConcoursException Si la valeur est invalide ou la note existe déjà
   */
  public function saisirNote(string $candidatureId, string $epreuveId, float $valeur, bool $estEliminatoire = false): Note
  {
    // Validation de la valeur
    if ($valeur < 0 || $valeur > 20) {
      throw ConcoursException::noteInvalide($valeur);
    }

    return DB::transaction(function () use ($candidatureId, $epreuveId, $valeur, $estEliminatoire) {
      // Vérifier si la note existe déjà
      $existingNote = Note::where('candidature_id', $candidatureId)
        ->where('epreuve_id', $epreuveId)
        ->first();

      if ($existingNote) {
        throw ConcoursException::noteDejaExiste($candidatureId, $epreuveId);
      }

      // Créer la note
      $note = Note::create([
        'candidature_id' => $candidatureId,
        'epreuve_id' => $epreuveId,
        'valeur' => $valeur,
        'est_eliminatoire' => $estEliminatoire,
        'statut' => StatutNote::SAISIE_TERMINEE,
        'date_saisie' => now(),
      ]);

      return $note->fresh();
    });
  }

  /**
   * Valider une note saisie.
   *
   * @param string $noteId ID de la note
   *
   * @return Note Note validée
   *
   * @throws ConcoursException Si la note n'existe pas ou n'est pas en statut SAISIE
   */
  public function validerNote(string $noteId): Note
  {
    $note = Note::findOrFail($noteId);

    if ($note->statut !== StatutNote::SAISIE_TERMINEE) {
      throw ConcoursException::noteNonModifiable($noteId);
    }

    $note->update([
      'statut' => StatutNote::SAISIE_TERMINEE,
      'est_definitive' => true,
    ]);

    return $note->fresh();
  }

  /**
   * Modifier une note (avant validation).
   *
   * @param string $noteId ID de la note
   * @param float $valeur Nouvelle valeur
   * @param bool $estEliminatoire Si éliminatoire
   *
   * @return Note Note modifiée
   *
   * @throws ConcoursException Si la note est déjà validée
   */
  public function modifierNote(string $noteId, float $valeur, bool $estEliminatoire = false): Note
  {
    if ($valeur < 0 || $valeur > 20) {
      throw ConcoursException::noteInvalide($valeur);
    }

    $note = Note::findOrFail($noteId);

    if ($note->statut !== StatutNote::SAISIE_TERMINEE) {
      throw ConcoursException::noteNonModifiable($noteId);
    }

    $note->update([
      'valeur' => $valeur,
      'est_eliminatoire' => $estEliminatoire,
      'date_saisie' => now(),
    ]);

    return $note->fresh();
  }

  /**
   * Annuler une note.
   *
   * @param string $noteId ID de la note
   *
   * @return bool True si annulée
   *
   * @throws ConcoursException Si la note est déjà validée
   */
  public function annulerNote(string $noteId): bool
  {
    $note = Note::findOrFail($noteId);

    if ($note->statut !== StatutNote::SAISIE_TERMINEE) {
      throw ConcoursException::noteNonModifiable($noteId);
    }

    return $note->delete();
  }

  /**
   * Obtenir toutes les notes d'un candidat pour un concours.
   *
   * @param string $candidatureId ID de la candidature
   *
   * @return Collection Notes du candidat
   */
  public function getNotesCandidat(string $candidatureId): Collection
  {
    return Note::where('candidature_id', $candidatureId)
      ->with(['epreuve'])
      ->orderBy('created_at')
      ->get();
  }

  /**
   * Calculer la moyenne générale d'un candidat.
   *
   * @param string $candidatureId ID de la candidature
   *
   * @return array ['moyenne' => float, 'total_points' => float, 'notes_validees' => int]
   */
  public function calculerMoyenneGenerale(string $candidatureId): array
  {
    $notes = Note::where('candidature_id', $candidatureId)
      ->where('statut', StatutNote::SAISIE_TERMINEE)
      ->with('epreuve')
      ->get();

    if ($notes->isEmpty()) {
      return [
        'moyenne' => 0,
        'total_points' => 0,
        'notes_validees' => 0,
      ];
    }

    $totalPoints = $notes->sum('valeur');
    $nombreNotes = $notes->count();
    $moyenne = round($totalPoints / $nombreNotes, 2);

    return [
      'moyenne' => $moyenne,
      'total_points' => $totalPoints,
      'notes_validees' => $nombreNotes,
    ];
  }
}
