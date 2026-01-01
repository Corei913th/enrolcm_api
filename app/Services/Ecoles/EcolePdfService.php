<?php

namespace App\Services\Ecoles;

use App\Models\Ecole;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class EcolePdfService
{
    /**
     * Générer une entête officielle d'école
     */
    public function generateOfficialHeader(Ecole $ecole): string
    {
        return View::make('pdf.ecole-header', [
            'ecole' => $ecole,
            'logo_path' => $ecole->logo_full_path,
            'embleme_path' => $ecole->embleme_full_path,
            'header_frame_path' => $ecole->header_frame_full_path,
        ])->render();
    }

    /**
     * Générer un document PDF avec entête officielle
     */
    public function generateDocument(Ecole $ecole, string $title, string $content): \Barryvdh\DomPDF\PDF
    {
        $header = $this->generateOfficialHeader($ecole);
        
        $pdf = Pdf::loadView('pdf.document-template', [
            'ecole' => $ecole,
            'header' => $header,
            'title' => $title,
            'content' => $content,
        ]);

        $pdf->setPaper('A4', 'portrait');
        
        return $pdf;
    }

    /**
     * Générer une attestation
     */
    public function generateAttestation(Ecole $ecole, array $data): \Barryvdh\DomPDF\PDF
    {
        return $this->generateDocument(
            $ecole,
            'ATTESTATION',
            View::make('pdf.attestation', $data)->render()
        );
    }

    /**
     * Générer un relevé de notes
     */
    public function generateReleveNotes(Ecole $ecole, array $data): \Barryvdh\DomPDF\PDF
    {
        return $this->generateDocument(
            $ecole,
            'RELEVÉ DE NOTES',
            View::make('pdf.releve-notes', $data)->render()
        );
    }

    /**
     * Générer un document administratif générique
     */
    public function generateAdministrativeDocument(Ecole $ecole, string $title, array $data): \Barryvdh\DomPDF\PDF
    {
        $pdf = Pdf::loadView('pdf.administrative-document', [
            'ecole' => $ecole,
            'title' => $title,
            'data' => $data,
            'logo_path' => $ecole->logo_full_path,
            'embleme_path' => $ecole->embleme_full_path,
        ]);

        $pdf->setPaper('A4', 'portrait');
        
        return $pdf;
    }
}
