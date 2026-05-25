<?php

namespace App\Exports\Candidats;

use App\Models\Candidature;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CandidatsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $concoursId;

    protected $filters;

    public function __construct(?string $concoursId = null, array $filters = [])
    {
        $this->concoursId = $concoursId;
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Candidature::with([
            'candidat.utilisateur',
            'candidat.filiere',
            'concours',
            'centreExamen',
        ]);

        if ($this->concoursId) {
            $query->where('concours_id', $this->concoursId);
        }

        // Filtres
        if (isset($this->filters['statut'])) {
            $query->where('statut_candidature', $this->filters['statut']);
        }

        if (isset($this->filters['filiere_id'])) {
            $query->whereHas('candidat', function ($q) {
                $q->where('filiere_id', $this->filters['filiere_id']);
            });
        }

        if (isset($this->filters['centre_id'])) {
            $query->where('centre_examen_id', $this->filters['centre_id']);
        }

        return $query->orderBy('code_cand_def')->get();
    }

    public function headings(): array
    {
        return [
            'Code Candidat',
            'Nom',
            'Prénom',
            'Email',
            'Téléphone',
            'Date de naissance',
            'Lieu de naissance',
            'Concours',
            'Filière(s)',
            'Centre d\'examen',
            'Statut',
            'Paiement validé',
            'Documents complets',
            'Date d\'inscription',
        ];
    }

    public function map($candidature): array
    {
        return [
            $candidature->code_cand_def ?? $candidature->code_cand_temp,
            $candidature->candidat->nom_cand,
            $candidature->candidat->prenom_cand,
            $candidature->candidat->utilisateur->email ?? '',
            $candidature->candidat->utilisateur->telephone ?? '',
            $candidature->candidat->date_naissance_cand ? Carbon::parse($candidature->candidat->date_naissance_cand)->format('d/m/Y') : '',
            $candidature->candidat->lieu_naissance_cand ?? '',
            $candidature->concours->libelle_concours ?? '',
            $candidature->candidat->filiere?->libelle_filiere ?? '',
            $candidature->centreExamen->nom_centre ?? '',
            $candidature->statut_candidature?->value ?? '',
            $candidature->paiement_valide ? 'Oui' : 'Non',
            $candidature->documents_complets ? 'Oui' : 'Non',
            $candidature->created_at->format('d/m/Y H:i'),
        ];
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
        return 'Liste des candidats';
    }
}
