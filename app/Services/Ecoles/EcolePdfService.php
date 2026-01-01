<?php

namespace App\Services\Ecoles;

use App\Models\Ecole;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;


class EcolePdfService
{
    /**
     * Generate an official school header
     *
     * @param Ecole $ecole School model
     * @return string Rendered HTML for the header
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
     * Generate a PDF document with official header
     *
     * @param Ecole $ecole School model
     * @param string $title Document title
     * @param string $content Document content
     * @return \Barryvdh\DomPDF\PDF PDF document
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
     * Generate an attestation PDF document
     *
     * @param Ecole $ecole School model
     * @param array $data Data for the attestation
     * @return \Barryvdh\DomPDF\PDF PDF document
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
     * Generate a notes PDF document
     *
     * @param Ecole $ecole School model
     * @param array $data Data for the notes
     * @return \Barryvdh\DomPDF\PDF PDF document
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
     * Generate a generic administrative document
     *
     * @param Ecole $ecole School model
     * @param string $title Document title
     * @param array $data Data for the document
     * @return \Barryvdh\DomPDF\PDF PDF document
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
