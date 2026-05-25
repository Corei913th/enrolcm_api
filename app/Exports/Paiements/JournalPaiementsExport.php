<?php

namespace App\Exports\Paiements;

use App\Models\Paiement;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JournalPaiementsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Paiement::with([
            'candidat.utilisateur',
            'concours',
            'candidature',
        ]);

        // Filtres
        if (isset($this->filters['concours_id'])) {
            $query->where('concours_id', $this->filters['concours_id']);
        }

        if (isset($this->filters['statut'])) {
            $query->where('statut', $this->filters['statut']);
        }

        if (isset($this->filters['mode_paiement'])) {
            $query->where('mode_paiement', $this->filters['mode_paiement']);
        }

        if (isset($this->filters['date_debut'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_debut']);
        }

        if (isset($this->filters['date_fin'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_fin']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Date',
            'Candidat',
            'Email',
            'Téléphone',
            'Concours',
            'Montant (FCFA)',
            'Mode de paiement',
            'Statut',
            'Date de vérification',
            'Vérifié par',
            'Motif de rejet',
            'Observations',
        ];
    }

    public function map($paiement): array
    {
        return [
            $paiement->reference,
            $paiement->created_at->format('d/m/Y H:i'),
            $paiement->candidat ? ($paiement->candidat->nom_cand . ' ' . $paiement->candidat->prenom_cand) : '',
            $paiement->candidat?->utilisateur->email ?? '',
            $paiement->candidat?->utilisateur->telephone ?? '',
            $paiement->concours->libelle_concours ?? '',
            number_format($paiement->montant, 0, ',', ' '),
            $paiement->mode_paiement ?? '',
            $paiement->statut?->value ?? '',
            $paiement->date_verification ? Carbon::parse($paiement->date_verification)->format('d/m/Y H:i') : '',
            $paiement->verifie_par ?? '',
            $paiement->motif_rejet ?? '',
            $paiement->observations ?? '',
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
        return 'Journal des paiements';
    }
}
