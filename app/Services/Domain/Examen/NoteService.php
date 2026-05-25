<?php

namespace App\Services\Domain\Examen;

use App\Enums\StatutCandidature;
use App\Enums\StatutNote;
use App\Exceptions\ConcoursException;
use App\Helpers\NoteHelper;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\Note;
use App\Services\Domain\Concours\Checkers\ConcoursStatusChecker;
use App\Traits\HasAdvancedSearch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class NoteService
{
    use HasAdvancedSearch;

    public function __construct(
        private readonly ConcoursStatusChecker $statusChecker
    ) {}

    /**
     * Valider qu'une candidature est éligible à la saisie de notes.
     *
     * @param  string  $candidatureId  ID de la candidature
     *
     * @throws ConcoursException Si la candidature n'est pas éligible
     */
    private function validateCandidatureEligiblePourNotes(string $candidatureId): void
    {
        $candidature = Candidature::findOrFail($candidatureId);

        if ($candidature->statut_candidature !== StatutCandidature::VALIDE) {
            throw new ConcoursException(
                "Impossible de saisir des notes : la candidature n'est pas validée (statut actuel : {$candidature->statut_candidature->value})",
                403
            );
        }

        $concours = $candidature->concours;

        if (! $this->statusChecker->hasCompletePlanning($concours)) {
            throw new ConcoursException(
                "Impossible de saisir des notes : aucun planning d'épreuves n'est défini pour ce concours",
                403
            );
        }

        // $dateDebutExamen = $this->statusChecker->getExamStartDate($concours);
        // if (!$dateDebutExamen || now()->lt($dateDebutExamen)) {
        //   $dateFormatted = $dateDebutExamen ? $dateDebutExamen->format('d/m/Y') : 'non définie';
        //   throw new ConcoursException(
        //     "Impossible de saisir des notes : la première épreuve ({$dateFormatted}) n'est pas encore passée",
        //     403
        //   );
        // }
    }

    /**
     * Saisir une note pour une épreuve.
     *
     * @param  string  $candidatureId  ID de la candidature
     * @param  string  $epreuveId  ID de l'épreuve
     * @param  float  $valeur  Valeur de la note (0-20)
     * @param  bool  $estEliminatoire  Si la note est éliminatoire
     * @return Note Note créée
     *
     * @throws ConcoursException Si la valeur est invalide ou la note existe déjà
     */
    public function saisirNote(string $candidatureId, string $epreuveId, float $valeur, bool $estEliminatoire = false): Note
    {

        $this->validateCandidatureEligiblePourNotes($candidatureId);

        if ($valeur < 0 || $valeur > 20) {
            throw ConcoursException::noteInvalide($valeur);
        }

        return runTransaction(function () use ($candidatureId, $epreuveId, $valeur, $estEliminatoire) {

            $existingNote = Note::where('candidature_id', $candidatureId)
                ->where('epreuve_id', $epreuveId)
                ->first();

            if ($existingNote) {
                throw ConcoursException::noteDejaExiste($candidatureId, $epreuveId);
            }

            $note = Note::create([
                'candidature_id' => $candidatureId,
                'epreuve_id' => $epreuveId,
                'valeur' => $valeur,
                'est_eliminatoire' => $estEliminatoire,
                'statut' => StatutNote::SAISIE_TERMINEE,
                'date_saisie' => now(),
            ]);

            return $note->fresh();
        }, 'NoteService::saisirNote');
    }

    /**
     * Valider une note saisie.
     *
     * @param  string  $noteId  ID de la note
     * @return Note Note validée
     *
     * @throws ConcoursException Si la note n'existe pas ou n'est pas en statut SAISIE_TERMINEE
     */
    public function validerNote(string $noteId): Note
    {
        $note = Note::findOrFail($noteId);

        if ($note->statut !== StatutNote::SAISIE_TERMINEE) {
            throw ConcoursException::noteNonModifiable($noteId);
        }

        $note->update([
            'statut' => StatutNote::VALIDEE,
            'est_definitive' => true,
        ]);

        return $note->fresh();
    }

    /**
     * Modifier une note (avant validation définitive).
     *
     * @param  string  $noteId  ID de la note
     * @param  float  $valeur  Nouvelle valeur
     * @param  bool  $estEliminatoire  Si éliminatoire
     * @return Note Note modifiée
     *
     * @throws ConcoursException Si la note est déjà validée définitivement
     */
    public function modifierNote(string $noteId, float $valeur, bool $estEliminatoire = false): Note
    {
        if ($valeur < 0 || $valeur > 20) {
            throw ConcoursException::noteInvalide($valeur);
        }

        $note = Note::findOrFail($noteId);

        if ($note->statut === StatutNote::VALIDEE) {
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
     * @param  string  $noteId  ID de la note
     * @return bool True si annulée
     *
     * @throws ConcoursException Si la note est déjà validée définitivement
     */
    public function annulerNote(string $noteId): bool
    {
        $note = Note::findOrFail($noteId);

        if ($note->statut === StatutNote::VALIDEE) {
            throw ConcoursException::noteNonModifiable($noteId);
        }

        return $note->delete();
    }

    /**
     * Obtenir les candidats éligibles à la saisie de notes pour un concours.
     *
     * @param  string  $concoursId  ID du concours
     * @param  string  $sessionId  ID de la session
     * @param  array  $filters  Filtres disponibles : search (recherche par code_cand_def ou numero_candidature)
     * @param  int  $perPage  Nombre d'éléments par page
     * @return LengthAwarePaginator Liste paginée des candidatures éligibles
     */
    public function getCandidatsEligiblesPourNotes(string $concoursId, string $sessionId, array $filters = [], int $perPage = 100)
    {
        // $concours = Concours::with('plannings')->findOrFail($concoursId);

        // Vérifier que le planning existe et que la première épreuve est passée
        // $dateDebutExamen = $this->statusChecker->getExamStartDate($concours);
        // if (!$this->statusChecker->hasCompletePlanning($concours) || !$dateDebutExamen || now()->lt($dateDebutExamen)) {
        //   return Candidature::query()->whereRaw('1 = 0')->paginate($perPage);
        // }

        $query = Candidature::select([
            'id',
            'code_cand_def',
            'numero_candidature',
            'concours_id',
            'session_id',
            'statut_candidature',
            'created_at',
            'updated_at',
        ])
            ->where('concours_id', $concoursId)
            ->where('session_id', $sessionId)
            ->where('statut_candidature', StatutCandidature::VALIDE)
            ->with([
                'notes:valeur,candidature_id,epreuve_id,statut,est_eliminatoire',
                'notes.epreuve:id_epreuve,intitule,session',
            ]);

        // Recherche anonyme par code_cand_def ou numero_candidature uniquement
        if (! empty($filters['search'])) {
            $this->applySearch(
                $query,
                $filters['search'],
                [
                    'code_cand_def' => 'partial',
                    'numero_candidature' => 'partial',
                ]
            );
        }

        return $query->orderBy('created_at')->paginate($perPage);
    }

    /**
     * Obtenir toutes les notes d'un candidat pour un concours.
     *
     * @param  string  $candidatureId  ID de la candidature
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
     * Calculate general average of a candidate with coefficients and eliminatory check
     *
     * @param  string  $candidatureId  Candidature ID
     * @return array ['average' => float, 'total_points' => float, 'total_coefficients' => float, 'validated_count' => int, 'is_eliminated' => bool]
     */
    public function calculateGeneralAverage(string $candidatureId): array
    {
        // Use optimized helper that handles coefficients and eliminatory notes
        return NoteHelper::calculateCompleteAverage($candidatureId);
    }
}
