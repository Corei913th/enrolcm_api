<?php

namespace App\Services\Infrastructure\Pdf;

use App\Models\Ecole;
use Illuminate\Support\Facades\View;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

class EcoleDocumentPdfService
{
    /**
     * Générer l'en-tête officielle d'une école
     *
     * @param  Ecole  $ecole  École concernée
     * @param  bool  $contentOnly  Si true, retourne uniquement le contenu sans balises HTML complètes
     * @return string HTML de l'en-tête rendu
     */
    public function generateOfficialHeader(Ecole $ecole, bool $contentOnly = true): string
    {
        $template = $contentOnly ? 'pdf.ecole-header-content' : 'pdf.ecole-header';

        return View::make($template, [
            'ecole' => $ecole,
            'logo_path' => $ecole->logo_full_path,
            'embleme_path' => $ecole->embleme_full_path,
            'header_frame_path' => $ecole->header_frame_full_path,
        ])->render();
    }

    /**
     * Générer un document PDF avec en-tête officielle
     *
     * @param  Ecole  $ecole  École concernée
     * @param  string  $title  Titre du document
     * @param  string  $content  Contenu HTML du document
     * @return PdfBuilder Document PDF généré
     */
    public function generateDocument(Ecole $ecole, string $title, string $content): PdfBuilder
    {
        $header = $this->generateOfficialHeader($ecole);

        return Pdf::view('pdf.document-template', [
            'ecole' => $ecole,
            'header' => $header,
            'title' => $title,
            'content' => $content,
        ])->format('a4');
    }

    /**
     * Générer une attestation PDF
     *
     * @param  Ecole  $ecole  École concernée
     * @param  array  $data  Données de l'attestation (nom, prénom, etc.)
     * @return PdfBuilder Document PDF d'attestation
     */
    public function generateAttestation(Ecole $ecole, array $data): PdfBuilder
    {
        return $this->generateDocument(
            $ecole,
            'ATTESTATION',
            View::make('pdf.attestation', $data)->render()
        );
    }

    /**
     * Générer un relevé de notes PDF
     *
     * @param  Ecole  $ecole  École concernée
     * @param  array  $data  Données du relevé (notes, matières, etc.)
     * @return PdfBuilder Document PDF de relevé de notes
     */
    public function generateReleveNotes(Ecole $ecole, array $data): PdfBuilder
    {
        return $this->generateDocument(
            $ecole,
            'RELEVÉ DE NOTES',
            View::make('pdf.releve-notes', $data)->render()
        );
    }

    /**
     * Générer un document administratif générique
     *
     * @param  Ecole  $ecole  École concernée
     * @param  string  $title  Titre du document
     * @param  array  $data  Données du document
     * @return PdfBuilder Document PDF administratif
     */
    public function generateAdministrativeDocument(Ecole $ecole, string $title, array $data): PdfBuilder
    {
        return Pdf::view('pdf.administrative-document', [
            'ecole' => $ecole,
            'title' => $title,
            'data' => $data,
            'logo_path' => $ecole->logo_full_path,
            'embleme_path' => $ecole->embleme_full_path,
        ])->format('a4');
    }
}
