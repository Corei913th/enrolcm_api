<?php

namespace App\Services\Domain\Examen\Processors;

use App\Enums\DecisionAdmission;
use App\Models\Candidature;
use App\Models\ResultatFinal;
use App\Services\Domain\Examen\Calculators\MoyenneCalculator;
use Illuminate\Support\Collection;

/**
 * Processeur de résultats individuels
 */
class ResultatProcessor
{
    public function __construct(
        private readonly MoyenneCalculator $moyenneCalculator
    ) {}

    /**
     * Traiter le résultat d'un candidat
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function traiterResultat(Candidature $candidature, bool $force = false): ResultatFinal
    {
        if ($force) {
            ResultatFinal::where('candidature_id', $candidature->id)->delete();
        }

        $notes = $this->getNotesDefinitives($candidature);

        if ($notes->isEmpty()) {
            throw new \Exception("Aucune note définitive trouvée pour la candidature {$candidature->id}");
        }

        // Vérifier élimination
        if ($this->estElimine($notes)) {
            return $this->creerResultatElimine($candidature);
        }

        // Calculer moyenne et créer résultat
        return $this->creerResultatAvecMoyenne($candidature, $notes);
    }

    /**
     * Récupérer les notes définitives
     */
    private function getNotesDefinitives(Candidature $candidature): Collection
    {
        return $candidature->notes()
            ->where('est_definitive', true)
            ->with('epreuve')
            ->get();
    }

    /**
     * Vérifier si le candidat est éliminé
     */
    private function estElimine(Collection $notes): bool
    {
        return $notes->contains(function ($note) {
            return $note->est_eliminatoire
              && $note->valeur < ($note->epreuve->note_eliminatoire ?? 5);
        });
    }

    /**
     * Créer un résultat pour un candidat éliminé
     *
     * @throws \Exception
     */
    private function creerResultatElimine(Candidature $candidature): ResultatFinal
    {
        return ResultatFinal::updateOrCreate(
            ['candidature_id' => $candidature->id],
            [
                'session_id' => $candidature->session_id,
                'moyenne_generale' => 0,
                'total_point' => 0,
                'rang' => null,
                'decision' => DecisionAdmission::REFUSEE,
                'mention' => null,
                'est_admis' => false,
                'date_publication' => null,
            ]
        );
    }

    /**
     * Créer un résultat avec calcul de moyenne
     * La décision finale sera déterminée par AdmissionProcessor
     */
    private function creerResultatAvecMoyenne(Candidature $candidature, Collection $notes): ResultatFinal
    {
        $calcul = $this->moyenneCalculator->calculer($candidature, $notes);
        $mention = $this->moyenneCalculator->calculerMention($calcul['moyenne_generale']);

        return ResultatFinal::updateOrCreate(
            ['candidature_id' => $candidature->id],
            [
                'session_id' => $candidature->session_id,
                'moyenne_generale' => $calcul['moyenne_generale'],
                'total_point' => $calcul['total_point'],
                'rang' => null,
                'decision' => null, // Sera déterminée par determinerAdmissions()
                'mention' => $mention,
                'est_admis' => false,
                'date_publication' => null,
            ]
        );
    }
}
