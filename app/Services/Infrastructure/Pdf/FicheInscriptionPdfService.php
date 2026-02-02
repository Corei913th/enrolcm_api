<?php

namespace App\Services\Infrastructure\Pdf;

use App\Models\Candidature;
use App\Services\Domain\Candidature\DocumentService;
use Spatie\LaravelPdf\Facades\Pdf;

class FicheInscriptionPdfService
{
  public function __construct(
    private readonly EcoleDocumentPdfService $ecoleDocumentService,
    private readonly DocumentService $documentService
  ) {}

  /**
   * Generate registration form PDF for a validated candidature
   */
    public function genererFicheInscription(Candidature $candidature)
    {
        // Verify that candidature is submitted or validated
        if (!$candidature->estSoumise() && !$candidature->estValidee()) {
            throw new \Exception('Only submitted or validated candidatures can generate a registration form');
        }

    // Load necessary relations
    $candidature->load([
      'candidat.utilisateur',
      'candidat.filiere',
      'concours.ecole',
      'concours.documentsRequis',
      'session',
      'centreExamen',
      'centreDepot'
    ]);

    // Get school via concours (direct relation now)
    $school = $candidature->concours?->ecole;

    if (!$school) {
      throw new \Exception('Impossible de déterminer l\'école pour cette candidature. Vérifiez que le concours a une école associée.');
    }

    // Get official header
    $ecoleHeader = $this->ecoleDocumentService->generateOfficialHeader($school);

    // Get validated photo path
    $photoPath = $this->documentService->getValidatedPhotoPath($candidature);


    $data = [
      'candidature' => $candidature,
      'candidat' => $candidature->candidat,
      'concours' => $candidature->concours,
      'session' => $candidature->session,
      'centre' => $candidature->centreExamen,
      'ecole' => $school,
      'ecoleHeader' => $ecoleHeader,
      'qrCode' => $candidature->qr_code,
      'photoPath' => $photoPath,
      'dateGeneration' => now()->format('d/m/Y'),
      'statusChecker' => app(\App\Services\Domain\Concours\Checkers\ConcoursStatusChecker::class),
    ];

    // Generate PDF with Spatie
    return Pdf::view('pdf.fiche-inscription', $data)->format('a4');
  }
}
