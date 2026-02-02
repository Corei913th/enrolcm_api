<?php

namespace App\Mail;

use App\Models\Candidat;
use App\Models\Document;
use App\Models\Utilisateur;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentRejectedMail extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * Create a new message instance.
   */
  public function __construct(
    public Utilisateur $utilisateur,
    public Candidat $candidat,
    public Document $document,
    public string $motif
  ) {}

  /**
   * Get the message envelope.
   */
  public function envelope(): Envelope
  {
    $documentRequis = $this->document->documentRequis;
    $nomDocument = $documentRequis ? $documentRequis->nom_document : 'Document';

    return new Envelope(
      subject: "Document rejeté - {$nomDocument}",
    );
  }

  /**
   * Get the message content definition.
   */
  public function content(): Content
  {
    return new Content(
      view: 'emails.document-rejected',
    );
  }

  /**
   * Get the attachments for the message.
   *
   * @return array<int, \Illuminate\Mail\Mailables\Attachment>
   */
  public function attachments(): array
  {
    return [];
  }
}
