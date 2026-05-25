<?php

namespace App\Services\Infrastructure\Pdf;

use App\Models\Centre;
use App\Models\Concours;
use App\Models\PlanningEpreuve;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

class EmargementPdfService
{
    public function __construct() {}

    /**
     * Générer une liste d'émargement pour une salle et une épreuve
     *
     * @return PdfBuilder
     */
    public function genererListeEmargement(string $salleId, string $planningEpreuveId)
    {
        $planning = PlanningEpreuve::with([
            'epreuve.matiere',
            'epreuve.concours.ecole',
            'salleExamen.centre.region',
        ])->findOrFail($planningEpreuveId);

        $candidatures = $planning->candidatures()
            ->where('salle_examen_id', $salleId)
            ->where('statut_candidature', 'VALIDE')
            ->with(['candidat.utilisateur'])
            ->orderBy('code_cand_def')
            ->get();

        $data = [
            'planning' => $planning,
            'epreuve' => $planning->epreuve,
            'matiere' => $planning->epreuve->matiere,
            'salle' => $planning->salleExamen,
            'centre' => $planning->salleExamen->centre,
            'concours' => $planning->epreuve->concours,
            'ecole' => $planning->epreuve->concours->ecole,
            'candidatures' => $candidatures,
            'date_epreuve' => $planning->date_epreuve,
            'heure_debut' => $planning->heure_debut,
            'heure_fin' => $planning->heure_fin,
            'date_generation' => now()->format('d/m/Y à H:i'),
        ];

        return Pdf::view('pdf.liste-emargement', $data)->format('a4');
    }

    /**
     * Générer toutes les listes d'émargement pour un centre
     *
     * @return PdfBuilder
     */
    public function genererListesEmargementCentre(string $centreId, string $concoursId)
    {
        $centre = Centre::with(['region', 'sallesExamen'])->findOrFail($centreId);
        $concours = Concours::with('ecole')->findOrFail($concoursId);

        $plannings = PlanningEpreuve::whereHas('epreuve', function ($query) use ($concoursId) {
            $query->where('concours_id', $concoursId);
        })
            ->with([
                'epreuve.matiere',
                'salleExamen',
                'candidatures' => function ($query) use ($centreId) {
                    $query->where('centre_examen_id', $centreId)
                        ->where('statut_candidature', 'VALIDE')
                        ->with('candidat.utilisateur')
                        ->orderBy('code_cand_def');
                },
            ])
            ->whereHas('salleExamen', function ($query) use ($centreId) {
                $query->where('centre_id', $centreId);
            })
            ->orderBy('date_epreuve')
            ->orderBy('heure_debut')
            ->get();

        $data = [
            'centre' => $centre,
            'concours' => $concours,
            'ecole' => $concours->ecole,
            'plannings' => $plannings,
            'date_generation' => now()->format('d/m/Y à H:i'),
        ];

        return Pdf::view('pdf.listes-emargement-centre', $data)->format('a4');
    }

    /**
     * Générer une feuille d'émargement vierge (sans noms)
     *
     * @return PdfBuilder
     */
    public function genererFeuilleEmargementVierge(string $salleId, string $planningEpreuveId, int $nombreLignes = 50)
    {
        $planning = PlanningEpreuve::with([
            'epreuve.matiere',
            'epreuve.concours.ecole',
            'salleExamen.centre.region',
        ])->findOrFail($planningEpreuveId);

        $data = [
            'planning' => $planning,
            'epreuve' => $planning->epreuve,
            'matiere' => $planning->epreuve->matiere,
            'salle' => $planning->salleExamen,
            'centre' => $planning->salleExamen->centre,
            'concours' => $planning->epreuve->concours,
            'ecole' => $planning->epreuve->concours->ecole,
            'nombre_lignes' => $nombreLignes,
            'date_epreuve' => $planning->date_epreuve,
            'heure_debut' => $planning->heure_debut,
            'heure_fin' => $planning->heure_fin,
            'date_generation' => now()->format('d/m/Y à H:i'),
        ];

        return Pdf::view('pdf.feuille-emargement-vierge', $data)->format('a4');
    }
}
