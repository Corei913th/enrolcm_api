<?php

namespace App\Mail;

use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\ResultatFinal;
use App\Models\Utilisateur;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResultsPublishedMail extends Mailable
{
  use Queueable, SerializesModels;

  public function __construct(
    public Utilisateur $utilisateur,
    public Candidat $candidat,
    public Candidature $candidature,
    public ResultatFinal $resultat
  ) {}

  public function envelope(): Envelope
  {
    $subject = match (true) {
      $this->resultat->est_admis => 'Félicitations ! Vous êtes admis(e)',
      $this->resultat->decision === 'LISTE_ATTENTE' => 'Liste d\'attente - Résultats',
      default => 'Résultats disponibles',
    };

    return new Envelope(
      subject: $subject . ' - ' . ($this->candidature->concours->libelle_concours ?? 'Concours'),
    );
  }

  public function content(): Content
  {
    return new Content(
      view: 'emails.results-published',
      with: [
        'utilisateur' => $this->utilisateur,
        'candidat' => $this->candidat,
        'candidature' => $this->candidature,
        'concours' => $this->candidature->concours,
        'resultat' => $this->resultat,
      ]
    );
  }

  public function attachments(): array
  {
    return [];
  }
}
