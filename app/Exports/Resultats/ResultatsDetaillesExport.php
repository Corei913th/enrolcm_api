<?php

namespace App\Exports\Resultats;

use App\Models\Candidature;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResultatsDetaillesExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $concoursId;

    protected $filters;

    public function __construct(string $concoursId, array $filters = [])
    {
        $this->concoursId = $concoursId;
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Candidature::where('concours_id', $this->concoursId)
            ->whereHas('resultatFinal', function ($q) {
                $q->whereNotNull('date_publication');
            })
            ->with([
                'candidat.utilisateur',
                'concours',
                'resultatFinal',
                'notes.epreuve.matiere',
                'filieres',
            ]);

        // Filtres
        if (isset($this->filters['filiere_id'])) {
            $query->whereHas('filieres', function ($q) {
                $q->where('filiere_id', $this->filters['filiere_id']);
            });
        }

        if (isset($this->filters['decision'])) {
            $query->whereHas('resultatFinal', function ($q) {
                $q->where('decision', $this->filters['decision']);
            });
        }

        if (isset($this->filters['est_admis'])) {
            $query->whereHas('resultatFinal', function ($q) {
                $q->where('est_admis', $this->filters['est_admis']);
            });
        }

        return $query->orderBy('code_cand_def')->get();
    }

    public function headings(): array
    {
        $headings = [
            'Code Candidat',
            'Nom',
            'Prénom',
            'Filière(s)',
        ];

        // Ajouter les colonnes de notes dynamiquement
        $firstCandidat = $this->collection()->first();
        if ($firstCandidat && $firstCandidat->notes->isNotEmpty()) {
            foreach ($firstCandidat->notes as $note) {
                $headings[] = $note->epreuve->matiere->nom_matiere;
            }
        }

        $headings = array_merge($headings, [
            'Moyenne Générale',
            'Rang',
            'Décision',
            'Admis',
            'Observations',
        ]);

        return $headings;
    }

    public function map($candidature): array
    {
        $row = [
            $candidature->code_cand_def ?? $candidature->code_cand_temp,
            $candidature->candidat->nom_cand,
            $candidature->candidat->prenom_cand,
            $candidature->filieres->pluck('nom_filiere')->join(', '),
        ];

        // Ajouter les notes
        foreach ($candidature->notes as $note) {
            $row[] = number_format($note->note, 2);
        }

        $resultat = $candidature->resultatFinal;

        $row = array_merge($row, [
            $resultat ? number_format($resultat->moyenne_generale, 2) : '',
            $resultat?->rang ?? '',
            $resultat?->decision ?? '',
            $resultat?->est_admis ? 'Oui' : 'Non',
            $resultat?->observations ?? '',
        ]);

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '059669'],
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Résultats détaillés';
    }
}
