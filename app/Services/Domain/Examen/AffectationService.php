<?php

namespace App\Services\Domain\Examen;

use App\Enums\StatutCandidature;
use App\Exceptions\ConcoursException;
use App\Models\Candidature;
use App\Models\CandidatureSalle;
use App\Models\PlanningEpreuve;
use App\Models\SalleExamen;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AffectationService
{
    /**
     * Affecter automatiquement tous les candidats aux salles pour une épreuve.
     *
     * @param  string  $planningEpreuveId  ID du planning d'épreuve
     * @param  string  $ordreAffectation  Ordre ('alphabetique' ou 'moyenne')
     * @return array Statistiques de l'affectation
     */
    public function affecterCandidatsSalle(string $planningEpreuveId, string $ordreAffectation = 'alphabetique'): array
    {
        $planning = $this->getPlanningEpreuveAvecRelations($planningEpreuveId);

        $stats = $this->initialiserStatsAffectation();

        DB::transaction(function () use ($planning, $ordreAffectation, &$stats) {
            $candidatures = $this->getCandidaturesPourPlanning($planning, $ordreAffectation);
            $salles = $this->getSallesDisponiblesPourCentre($planning->centre_id);

            $this->affecterCandidaturesAuxSalles($candidatures, $salles, $planning, $stats);
        });

        return $stats;
    }

    /**
     * Récupère le planning d'épreuve avec ses relations.
     */
    private function getPlanningEpreuveAvecRelations(string $planningEpreuveId): PlanningEpreuve
    {
        return PlanningEpreuve::with(['epreuve', 'concours', 'session', 'centre'])
            ->findOrFail($planningEpreuveId);
    }

    /**
     * Initialise les statistiques d'affectation.
     */
    private function initialiserStatsAffectation(): array
    {
        return [
            'total_candidatures' => 0,
            'affectes' => 0,
            'salles_utilisees' => 0,
            'places_restantes' => 0,
            'non_affectes' => 0,
            'erreurs' => [],
        ];
    }

    /**
     * Récupère les candidatures pour ce planning selon l'ordre demandé.
     */
    private function getCandidaturesPourPlanning(PlanningEpreuve $planning, string $ordreAffectation): Collection
    {
        $query = Candidature::where('concours_id', $planning->concours_id)
            ->where('session_id', $planning->session_id)
            ->where('centre_id', $planning->centre_id)
            ->where('statut_candidature', StatutCandidature::VALIDE)
            ->with(['candidat', 'resultatFinal']);

        // Appliquer l'ordre d'affectation
        if ($ordreAffectation === 'moyenne') {
            $query->leftJoin('resultats_finaux', 'candidatures.id', '=', 'resultats_finaux.candidature_id')
                ->orderBy('resultats_finaux.moyenne_generale', 'desc')
                ->orderBy('candidatures.created_at');
        } else {
            // Ordre alphabétique par défaut
            $query->join('candidats', 'candidatures.candidat_id', '=', 'candidats.utilisateur_id')
                ->orderBy('candidats.nom_cand')
                ->orderBy('candidats.prenoms_cand');
        }

        return $query->get();
    }

    /**
     * Récupère les salles disponibles pour un centre.
     */
    private function getSallesDisponiblesPourCentre(string $centreId): Collection
    {
        return SalleExamen::where('centre_id', $centreId)
            ->where('est_actif', true)
            ->orderBy('numero_salle')
            ->get();
    }

    /**
     * Affecte les candidatures aux salles disponibles.
     */
    private function affecterCandidaturesAuxSalles(Collection $candidatures, Collection $salles, PlanningEpreuve $planning, array &$stats): void
    {
        $stats['total_candidatures'] = $candidatures->count();

        $salleIndex = 0;
        $placeDansSalle = 1;
        $sallesUtilisees = collect();

        foreach ($candidatures as $candidature) {
            $affectationReussie = $this->affecterCandidatureASalle(
                $candidature,
                $salles,
                $planning,
                $salleIndex,
                $placeDansSalle,
                $sallesUtilisees
            );

            if ($affectationReussie) {
                $stats['affectes']++;
            } else {
                $stats['non_affectes']++;
                $stats['erreurs'][] = [
                    'candidature_id' => $candidature->id,
                    'raison' => 'Plus de places disponibles',
                ];
            }
        }

        $stats['salles_utilisees'] = $sallesUtilisees->unique()->count();
        $stats['places_restantes'] = $this->calculerPlacesRestantes($salles);
    }

    /**
     * Affecte une candidature à une salle.
     */
    private function affecterCandidatureASalle(
        Candidature $candidature,
        Collection $salles,
        PlanningEpreuve $planning,
        int &$salleIndex,
        int &$placeDansSalle,
        Collection &$sallesUtilisees
    ): bool {
        // Vérifier si on a encore des salles disponibles
        if ($salleIndex >= $salles->count()) {
            return false;
        }

        $salle = $salles[$salleIndex];

        CandidatureSalle::create([
            'candidature_id' => $candidature->id,
            'salle_id' => $salle->id,
            'planning_epreuve_id' => $planning->id,
            'numero_place' => $placeDansSalle,
            'est_present' => false,
        ]);

        $sallesUtilisees->push($salle->id);

        // Passer à la place suivante
        $placeDansSalle++;

        // Si la salle est pleine, passer à la salle suivante
        if ($placeDansSalle > $salle->capacite) {
            $salleIndex++;
            $placeDansSalle = 1;
        }

        return true;
    }

    /**
     * Calcule le nombre total de places restantes dans toutes les salles.
     */
    private function calculerPlacesRestantes(Collection $salles): int
    {
        $placesRestantes = 0;

        foreach ($salles as $salle) {
            $placesOccupees = CandidatureSalle::where('salle_id', $salle->id)->count();
            $placesRestantes += max(0, $salle->capacite - $placesOccupees);
        }

        return $placesRestantes;
    }

    /**
     * Réaffecter un candidat à une autre salle.
     *
     * @param  string  $candidatureSalleId  ID de l'affectation actuelle
     * @param  string  $nouvelleSalleId  ID de la nouvelle salle
     * @param  int  $nouveauNumeroPlace  Nouveau numéro de place
     * @return CandidatureSalle Affectation mise à jour
     */
    public function reaffecterCandidat(string $candidatureSalleId, string $nouvelleSalleId, int $nouveauNumeroPlace): CandidatureSalle
    {
        $affectation = CandidatureSalle::findOrFail($candidatureSalleId);

        // Vérifier que la nouvelle salle appartient au même centre
        $nouvelleSalle = SalleExamen::findOrFail($nouvelleSalleId);
        $ancienneSalle = $affectation->salle;

        if ($nouvelleSalle->centre_id !== $ancienneSalle->centre_id) {
            throw ConcoursException::reaffectationImpossible('Centres différents');
        }

        // Vérifier que la place est disponible
        $placeOccupee = CandidatureSalle::where('salle_id', $nouvelleSalleId)
            ->where('numero_place', $nouveauNumeroPlace)
            ->where('planning_epreuve_id', $affectation->planning_epreuve_id)
            ->exists();

        if ($placeOccupee) {
            throw ConcoursException::reaffectationImpossible('Place déjà occupée');
        }

        $affectation->update([
            'salle_id' => $nouvelleSalleId,
            'numero_place' => $nouveauNumeroPlace,
        ]);

        return $affectation->fresh();
    }

    /**
     * Marquer un candidat comme présent à l'examen.
     *
     * @param  string  $candidatureSalleId  ID de l'affectation
     * @param  string  $heureArrivee  Heure d'arrivée (optionnel)
     * @param  string  $observations  Observations (optionnel)
     * @return CandidatureSalle Affectation mise à jour
     */
    public function marquerPresent(string $candidatureSalleId, ?string $heureArrivee = null, ?string $observations = null): CandidatureSalle
    {
        $affectation = CandidatureSalle::findOrFail($candidatureSalleId);

        $affectation->update([
            'est_present' => true,
            'heure_arrivee' => $heureArrivee ? now()->createFromFormat('H:i', $heureArrivee) : now(),
            'observations' => $observations,
        ]);

        return $affectation->fresh();
    }

    /**
     * Obtenir le plan de salle pour une épreuve.
     *
     * @param  string  $planningEpreuveId  ID du planning d'épreuve
     * @return Collection Liste des affectations avec détails
     */
    public function getPlanSalle(string $planningEpreuveId): Collection
    {
        return CandidatureSalle::where('planning_epreuve_id', $planningEpreuveId)
            ->with([
                'candidature.candidat',
                'salle',
                'planningEpreuve.epreuve',
            ])
            ->orderBy('salle.numero_salle')
            ->orderBy('numero_place')
            ->get();
    }

    /**
     * Obtenir les statistiques d'affectation pour une épreuve.
     *
     * @param  string  $planningEpreuveId  ID du planning d'épreuve
     * @return array Statistiques détaillées
     */
    public function getStatistiquesAffectation(string $planningEpreuveId): array
    {
        $planning = PlanningEpreuve::with(['centre.salles'])->findOrFail($planningEpreuveId);

        $totalCandidatures = CandidatureSalle::where('planning_epreuve_id', $planningEpreuveId)->count();
        $presents = CandidatureSalle::where('planning_epreuve_id', $planningEpreuveId)
            ->where('est_present', true)
            ->count();

        $affectationsParSalle = CandidatureSalle::where('planning_epreuve_id', $planningEpreuveId)
            ->selectRaw('salle_id, COUNT(*) as effectif')
            ->groupBy('salle_id')
            ->with('salle')
            ->get();

        $capaciteTotale = $planning->centre->salles->sum('capacite');

        return [
            'total_candidatures' => $totalCandidatures,
            'presents' => $presents,
            'absents' => $totalCandidatures - $presents,
            'capacite_totale' => $capaciteTotale,
            'taux_occupation' => $capaciteTotale > 0 ? round(($totalCandidatures / $capaciteTotale) * 100, 2) : 0,
            'affectations_par_salle' => $affectationsParSalle,
        ];
    }
}
