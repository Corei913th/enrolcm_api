<?php

namespace App\Services\Domain\Examen\Validators;

use App\Exceptions\Business\ResultatException;
use App\Models\Concours;
use App\Models\Filiere;
use App\Models\ResultatFinal;
use App\Models\Session;
use Illuminate\Support\Facades\DB;

class ResultatValidator
{
    /**
     * Valider les prérequis pour le calcul des résultats
     *
     * @throws ResultatException
     */
    public function validateCalculPrerequis(string $concoursId, string $sessionId, bool $force): void
    {
        $concours = Concours::find($concoursId);
        $session = Session::find($sessionId);

        if (! $concours || ! $session) {
            throw ResultatException::concoursSessionIntrouvable($concoursId, $sessionId);
        }

        if (! $force && $this->resultatsExistent($concoursId, $sessionId)) {
            throw ResultatException::resultatsDejaCalcules($concoursId, $sessionId);
        }
    }

    /**
     * Valider les prérequis pour la détermination des admissions
     *
     * @throws ResultatException
     */
    public function validateAdmissionPrerequis(
        string $concoursId,
        string $sessionId,
        string $filiereId,
        bool $force = false
    ): array {
        $concours = Concours::find($concoursId);
        $session = Session::find($sessionId);
        $filiere = Filiere::find($filiereId);

        if (! $concours || ! $session || ! $filiere) {
            throw ResultatException::concoursSessionIntrouvable($concoursId, $sessionId);
        }

        if (! $this->resultatsExistent($concoursId, $sessionId, $filiereId)) {
            throw ResultatException::resultatsNonCalcules($concoursId, $sessionId);
        }

        if (! $force && $this->admissionsExistent($concoursId, $filiereId)) {
            throw ResultatException::admissionsDejaDeterminees($concoursId, $sessionId, $filiereId);
        }

        $concoursFiliere = DB::table('concours_filiere')
            ->where('concours_id', $concoursId)
            ->where('filiere_id', $filiereId)
            ->where('session_id', $sessionId)
            ->first();

        if (! $concoursFiliere || ! $concoursFiliere->nombre_places) {
            throw ResultatException::placesNonDefinies($filiereId);
        }

        return [
            'filiere' => $filiere,
            'session_id' => $sessionId,
            'nombre_places' => $concoursFiliere->nombre_places,
        ];
    }

    /**
     * Valider les prérequis pour la publication
     */
    public function validatePublicationPrerequis(string $concoursId, string $sessionId): void
    {
        $concours = Concours::find($concoursId);
        $session = Session::find($sessionId);

        if (! $concours || ! $session) {
            throw ResultatException::concoursSessionIntrouvable($concoursId, $sessionId);
        }

        if (! $this->resultatsExistent($concoursId, $sessionId)) {
            throw ResultatException::publicationResultatsNonCalcules();
        }

        if (! $this->admissionsDeterminees($concoursId)) {
            throw ResultatException::publicationAdmissionsNonDeterminees();
        }

        if ($this->resultatsPublies($concoursId, $sessionId)) {
            throw ResultatException::resultatsDejaPublies($concoursId, $sessionId);
        }
    }

    /**
     * Vérifier si des résultats existent
     */
    private function resultatsExistent(string $concoursId, string $sessionId, ?string $filiereId = null): bool
    {
        $query = ResultatFinal::whereHas('candidature', function ($q) use ($concoursId, $sessionId) {
            $q->where('concours_id', $concoursId)
                ->where('session_id', $sessionId);
        });

        if ($filiereId) {
            $query->whereHas('candidature.candidat', function ($q) use ($filiereId) {
                $q->where('filiere_id', $filiereId);
            });
        }

        return $query->exists();
    }

    /**
     * Vérifier si des admissions ont été déterminées
     */
    private function admissionsExistent(string $concoursId, string $filiereId): bool
    {
        return ResultatFinal::whereHas('candidature.candidat', function ($q) use ($concoursId, $filiereId) {
            $q->where('concours_id', $concoursId)
                ->where('filiere_id', $filiereId);
        })->where('est_admis', true)->exists();
    }

    /**
     * Vérifier si les admissions ont été déterminées
     */
    private function admissionsDeterminees(string $concoursId): bool
    {
        return ResultatFinal::whereHas('candidature', function ($q) use ($concoursId) {
            $q->where('concours_id', $concoursId);
        })->whereNotNull('rang')->exists();
    }

    /**
     * Vérifier si les résultats sont publiés
     */
    private function resultatsPublies(string $concoursId, string $sessionId): bool
    {
        return ResultatFinal::whereHas('candidature', function ($q) use ($concoursId, $sessionId) {
            $q->where('concours_id', $concoursId)
                ->where('session_id', $sessionId);
        })->whereNotNull('date_publication')->exists();
    }
}
