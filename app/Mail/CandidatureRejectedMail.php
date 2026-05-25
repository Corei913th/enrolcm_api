<?php

namespace App\Mail;

use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Utilisateur;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidatureRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Utilisateur $utilisateur,
        public Candidat $candidat,
        public Candidature $candidature,
        public string $motif
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Candidature Rejetée - ' . ($this->candidature->concours->libelle_concours ?? 'Concours'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.candidature-rejected',
            with: [
                'utilisateur' => $this->utilisateur,
                'candidat' => $this->candidat,
                'candidature' => $this->candidature,
                'concours' => $this->candidature->concours,
                'motif' => $this->motif,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
