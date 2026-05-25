<?php

namespace App\Mail;

use App\Models\Candidat;
use App\Models\Paiement;
use App\Models\Utilisateur;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Utilisateur $utilisateur,
        public Candidat $candidat,
        public Paiement $paiement,
        public string $motif
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Paiement rejeté - ' . $this->paiement->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-rejected',
            with: [
                'utilisateur' => $this->utilisateur,
                'candidat' => $this->candidat,
                'paiement' => $this->paiement,
                'concours' => $this->paiement->concours,
                'motif' => $this->motif,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
