<?php

namespace App\Services\Infrastructure\Pdf;

use App\Models\Candidature;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

class RelevePdfService
{
    public function __construct() {}

    /**
     * Générer le relevé de notes d'un candidat
     *
     * @return PdfBuilder|null
     */
    public function genererReleveNotes(Candidature $candidature)
    {
        $candidature->load([
            'candidat.utilisateur',
            'concours.ecole',
            'resultatFinal',
            'notes.epreuve.matiere',
        ]);

        // Vérifier que les résultats sont publiés
        if (! $candidature->resultatFinal || ! $candidature->resultatFinal->date_publication) {
            return;
        }

        $notes = $candidature->notes->map(function ($note) {
            return [
                'matiere' => $note->epreuve->matiere->nom_matiere,
                'coefficient' => $note->epreuve->coefficient,
                'note' => $note->note,
                'note_ponderee' => $note->note * $note->epreuve->coefficient,
            ];
        });

        $totalCoefficients = $notes->sum('coefficient');
        $totalNotesPonderees = $notes->sum('note_ponderee');
        $moyenne = $totalCoefficients > 0 ? $totalNotesPonderees / $totalCoefficients : 0;

        $data = [
            'candidature' => $candidature,
            'candidat' => $candidature->candidat,
            'concours' => $candidature->concours,
            'resultat' => $candidature->resultatFinal,
            'notes' => $notes,
            'moyenne' => $moyenne,
            'total_coefficients' => $totalCoefficients,
            'ecole' => $candidature->concours->ecole,
            'code_candidat' => $candidature->code_cand_def ?? $candidature->code_cand_temp,
            'date_generation' => now()->format('d/m/Y à H:i'),
        ];

        return Pdf::view('pdf.releve-notes', $data)->format('a4');
    }

    /**
     * Générer les relevés de notes pour tous les candidats admis d'un concours
     *
     * @return PdfBuilder
     */
    public function genererRelevesGroupes(string $concoursId)
    {
        $candidatures = Candidature::where('concours_id', $concoursId)
            ->whereHas('resultatFinal', function ($query) {
                $query->whereNotNull('date_publication');
            })
            ->with([
                'candidat.utilisateur',
                'concours.ecole',
                'resultatFinal',
                'notes.epreuve.matiere',
            ])
            ->get();

        $relevesData = $candidatures->map(function ($candidature) {
            $notes = $candidature->notes->map(function ($note) {
                return [
                    'matiere' => $note->epreuve->matiere->nom_matiere,
                    'coefficient' => $note->epreuve->coefficient,
                    'note' => $note->note,
                    'note_ponderee' => $note->note * $note->epreuve->coefficient,
                ];
            });

            $totalCoefficients = $notes->sum('coefficient');
            $totalNotesPonderees = $notes->sum('note_ponderee');
            $moyenne = $totalCoefficients > 0 ? $totalNotesPonderees / $totalCoefficients : 0;

            return [
                'candidature' => $candidature,
                'candidat' => $candidature->candidat,
                'resultat' => $candidature->resultatFinal,
                'notes' => $notes,
                'moyenne' => $moyenne,
                'total_coefficients' => $totalCoefficients,
                'code_candidat' => $candidature->code_cand_def ?? $candidature->code_cand_temp,
            ];
        });

        $data = [
            'releves' => $relevesData,
            'concours' => $candidatures->first()?->concours,
            'ecole' => $candidatures->first()?->concours->ecole,
            'date_generation' => now()->format('d/m/Y à H:i'),
        ];

        return Pdf::view('pdf.releves-groupes', $data)->format('a4');
    }
}
