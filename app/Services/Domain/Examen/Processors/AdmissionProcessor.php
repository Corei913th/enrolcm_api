<?php

namespace App\Services\Domain\Examen\Processors;

use App\Enums\DecisionAdmission;
use App\Helpers\PlaceHelper;
use App\Models\ResultatFinal;
use Illuminate\Support\Collection;

/**
 * Processeur de détermination des admissions
 */
class AdmissionProcessor
{
    /**
     * Determine admissions for a list of results
     *
     * @param  string|null  $concoursId  For over-booking detection
     * @param  string|null  $sessionId  For over-booking detection
     */
    public function determiner(
        Collection $resultats,
        int $nombrePlaces,
        ?string $concoursId = null,
        ?string $sessionId = null
    ): array {

        if ($concoursId && $sessionId) {
            $overBooking = PlaceHelper::checkOverBooking($concoursId, $sessionId);

            if (! empty($overBooking)) {
                logServiceError('CRITICAL: Over-booking detected during admission processing', new \Exception('Over-booking'), [
                    'concours_id' => $concoursId,
                    'session_id' => $sessionId,
                    'issues' => $overBooking,
                    'total_surplus' => array_sum(array_column($overBooking, 'surplus')),
                ]);
            }
        }

        $nombreListeAttente = (int) ($nombrePlaces * 0.2);

        $rang = 1;
        $stats = [
            'admis' => 0,
            'liste_attente' => 0,
            'non_admis' => 0,
        ];

        foreach ($resultats as $resultat) {
            $resultat->rang = $rang;

            if ($stats['admis'] < $nombrePlaces) {
                $this->marquerAdmis($resultat);
                $stats['admis']++;
            } elseif ($stats['liste_attente'] < $nombreListeAttente) {
                $this->marquerListeAttente($resultat);
                $stats['liste_attente']++;
            } else {
                $this->marquerRefuse($resultat);
                $stats['non_admis']++;
            }

            $resultat->save();
            $rang++;
        }

        return $stats;
    }

    /**
     * Déterminer les admissions avec une contrainte simple de quotas région.
     *
     * Règle (simple et explicable) :
     * - On conserve l'ordre de mérite (résultats déjà triés par moyenne desc).
     * - Un candidat est ADMIS si :
     *   - il reste des places
     *   - ET sa région n'a pas dépassé son plafond (max_par_region) si défini.
     * - Si la région est au plafond, le candidat n'est pas admis même avec une bonne note.
     * - Ensuite on remplit une liste d'attente (20% des places) dans le même ordre.
     *
     * @param  Collection  $resultats  Collection<ResultatFinal>
     * @param  array  $maxParRegion  ex: ['CENTRE' => 50, 'LITTORAL' => 40]
     */
    public function determinerAvecQuotasRegion(Collection $resultats, int $nombrePlaces, array $maxParRegion): array
    {
        $nombreListeAttente = (int) ($nombrePlaces * 0.2);

        // Compteur des admis par région (uniquement pour appliquer max_par_region)
        $admisParRegion = [];

        $rang = 1;
        $stats = [
            'admis' => 0,
            'liste_attente' => 0,
            'non_admis' => 0,
        ];

        // 1) Première passe : marquer les admis sous contrainte de places + quotas.
        foreach ($resultats as $resultat) {
            $resultat->rang = $rang;

            $candidature = $resultat->candidature;

            $region = $candidature?->region_figee ?? $candidature?->candidat?->region;

            $regionKey = is_object($region) && property_exists($region, 'value')
              ? $region->value
              : (string) ($region ?? 'UNKNOWN');

            $maxRegion = $maxParRegion[$regionKey] ?? null;
            $admisDansRegion = $admisParRegion[$regionKey] ?? 0;

            $quotaOk = $maxRegion === null || $admisDansRegion < (int) $maxRegion;

            if ($stats['admis'] < $nombrePlaces && $quotaOk) {
                $this->marquerAdmis($resultat);
                $stats['admis']++;
                $admisParRegion[$regionKey] = $admisDansRegion + 1;
            }

            $rang++;
        }

        // 2) Deuxième passe : compléter liste d'attente puis refusés.
        foreach ($resultats as $resultat) {
            if ($resultat->decision === DecisionAdmission::ADMIS) {
                $resultat->save();

                continue;
            }

            if ($stats['liste_attente'] < $nombreListeAttente) {
                $this->marquerListeAttente($resultat);
                $stats['liste_attente']++;
            } else {
                $this->marquerRefuse($resultat);
                $stats['non_admis']++;
            }

            $resultat->save();
        }

        return $stats;
    }

    /**
     * Marquer un résultat comme admis
     */
    private function marquerAdmis(ResultatFinal $resultat): void
    {
        $resultat->decision = DecisionAdmission::ADMIS;
        $resultat->est_admis = true;
    }

    /**
     * Marquer un résultat en liste d'attente
     */
    private function marquerListeAttente(ResultatFinal $resultat): void
    {
        $resultat->decision = DecisionAdmission::LISTE_ATTENTE;
        $resultat->est_admis = false;
    }

    /**
     * Marquer un résultat comme refusé
     */
    private function marquerRefuse(ResultatFinal $resultat): void
    {
        $resultat->decision = DecisionAdmission::REFUSEE;
        $resultat->est_admis = false;
    }
}
