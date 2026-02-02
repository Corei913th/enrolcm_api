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

class PaymentVerifiedMail extends Mailable
{
  use Queueable, SerializesModels;

  public function __construct(
    public Utilisateur $utilisateur,
    public Candidat $candidat,
    public Paiement $paiement
  ) {}

  public function envelope(): Envelope
  {
    return new Envelope(
      subject: 'Paiement validé - ' . $this->paiement->reference,
    );
  }

  public function content(): Content
  {
    return new Content(
      view: 'emails.payment-verified',
      with: [
        'utilisateur' => $this->utilisateur,
        'candidat' => $this->candidat,
        'paiement' => $this->paiement,
        'concours' => $this->paiement->concours,
      ]
    );
  }

  public function attachments(): array
  {
    return [];
  }
}
