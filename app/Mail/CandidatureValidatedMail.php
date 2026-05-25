<?php

namespace App\Mail;

use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Utilisateur;
use App\Services\Infrastructure\Pdf\FicheInscriptionPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidatureValidatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Utilisateur $utilisateur,
        public Candidat $candidat,
        public Candidature $candidature,
        private readonly FicheInscriptionPdfService $ficheService
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Candidature Validée - ' . ($this->candidature->concours->libelle_concours ?? 'Concours'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.candidature-validated',
            with: [
                'utilisateur' => $this->utilisateur,
                'candidat' => $this->candidat,
                'candidature' => $this->candidature,
                'concours' => $this->candidature->concours,
                'code_candidat' => $this->candidature->code_cand_def,
                'numero_inscription' => $this->candidature->numero_inscription,
            ]
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        // Attacher la fiche d'inscription si disponible
        try {
            $pdf = $this->ficheService->genererFicheInscription($this->candidature);

            if ($pdf) {
                $attachments[] = Attachment::fromData(
                    fn () => $pdf->output(),
                    'fiche-inscription-' . $this->candidature->code_cand_def . '.pdf'
                )->withMime('application/pdf');
            }
        } catch (\Exception $e) {
            \Log::error('Failed to attach fiche inscription', [
                'candidature_id' => $this->candidature->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $attachments;
    }
}
