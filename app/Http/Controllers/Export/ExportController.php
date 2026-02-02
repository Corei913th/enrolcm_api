<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Enums\StatutCandidature;
use App\Models\Candidature;
use App\Models\Session;
use App\Services\Domain\Concours\CentreService;
use App\Services\Domain\Concours\ConcoursFiliereService;
use App\Services\Infrastructure\Export\ExcelExportService;
use App\Services\Infrastructure\Export\PdfExportService;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use App\Services\Infrastructure\Pdf\ConvocationPdfService;
use App\Services\Domain\Candidat\CandidatService;
use App\Services\Domain\Candidature\CandidatureService;
use App\Services\Domain\Concours\ConcoursService;
use App\Services\Domain\Examen\ResultatService;
use App\Services\Domain\Examen\PlanningService;
use App\Services\Domain\Paiement\PaiementService;
use App\Services\Domain\Session\SessionService;
use Illuminate\Http\Request;

class ExportController extends Controller
{
  public function __construct(
    private readonly ExcelExportService $excelService,
    private readonly PdfExportService $pdfService,
    private readonly ActivityLoggerService $logger,
    private readonly CandidatService $candidatService,
    private readonly CandidatureService $candidatureService,
    private readonly ConcoursService $concoursService,
    private readonly ConcoursFiliereService $concoursFiliereService,
    private readonly CentreService $centreService,
    private readonly PaiementService $paiementService,
    private readonly ResultatService $resultatService,
    private readonly PlanningService $planningService,
    private readonly SessionService $sessionService
  ) {}

  /**
   * Export candidats to Excel
   */
  public function exportCandidatsExcel(Request $request)
  {
    try {
      $filters = $request->only(['concours_id', 'session_id', 'statut']);

      $candidats = $this->candidatService->getAllForExport($filters, 10000);

      $filename = 'candidats_' . now()->format('Y-m-d_His') . '.xlsx';

      $this->logger->logActivity('export', 'candidats_excel', null, $filters);

      return $this->excelService->exportCandidats($candidats, $filename);
    } catch (\Exception $e) {
      $this->logger->logError($e, 'export_candidats_excel_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  public function generateFicheConcoursPdf(string $concoursId, Request $request)
  {
    try {
      $sessionId = $request->query('session_id');

      $concours = $this->concoursService->getById($concoursId, true);
      $concours->loadMissing(['ecole', 'specConcours', 'documentsRequis']);

      $planningsQuery = $concours->plannings()
        ->with(['epreuve', 'centre', 'session'])
        ->orderBy('date_epreuve')
        ->orderBy('heure_debut');

      if ($sessionId) {
        $planningsQuery->where('session_id', $sessionId);
      }

      $plannings = $planningsQuery->get();

      $paymentConfig = $this->paiementService->getConcoursPaymentConfig($concoursId);
      $centres = $this->centreService->listCentres($concoursId);

      $session = null;
      if ($sessionId) {
        $session = Session::find($sessionId);
      }
      if (!$session) {
        $session = $this->sessionService->getActiveSession($concoursId);
      }

      $filieres = collect();
      if ($session) {
        $filieres = $this->concoursFiliereService->listFilieres($concoursId, $session->id);
      } else {
        $filieres = $concours->filieres()->get()->map(function ($filiere) {
          return [
            'id' => $filiere->id,
            'code_filiere' => $filiere->code_filiere,
            'libelle_filiere' => $filiere->libelle_filiere,
            'desc_filiere' => $filiere->desc_filiere,
            'nombre_places' => $filiere->pivot?->nombre_places,
            'candidatures_validees' => null,
            'places_restantes' => null,
            'taux_remplissage' => null,
            'peut_accepter_candidatures' => null,
          ];
        });
      }

      $totalCandidatures = Candidature::query()
        ->where('concours_id', $concoursId)
        ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
        ->count();

      $totalValidees = Candidature::query()
        ->where('concours_id', $concoursId)
        ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
        ->where('statut_candidature', StatutCandidature::VALIDE->value)
        ->count();

      $stats = [
        'total_candidatures' => $totalCandidatures,
        'total_validees' => $totalValidees,
      ];

      $filename = 'fiche_concours_' . ($concours->libelle_concours ?? 'concours') . '_' . now()->format('Y-m-d_His') . '.pdf';

      $this->logger->logActivity('pdf_generation', 'fiche_concours', null, compact('concoursId', 'sessionId'));

      return $this->pdfService->generateFicheConcours(
        $concours,
        $session,
        $plannings,
        $stats,
        $paymentConfig,
        $centres,
        $filieres,
        $filename
      );
    } catch (\Exception $e) {
      $this->logger->logError($e, 'generate_fiche_concours_pdf_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  public function exportFicheConcoursExcel(string $concoursId, Request $request)
  {
    try {
      $sessionId = $request->query('session_id');

      $concours = $this->concoursService->getById($concoursId, true);
      $concours->loadMissing(['ecole', 'specConcours', 'documentsRequis']);

      $planningsQuery = $concours->plannings()
        ->with(['epreuve', 'centre', 'session'])
        ->orderBy('date_epreuve')
        ->orderBy('heure_debut');

      if ($sessionId) {
        $planningsQuery->where('session_id', $sessionId);
      }

      $plannings = $planningsQuery->get();

      $paymentConfig = $this->paiementService->getConcoursPaymentConfig($concoursId);
      $centres = $this->centreService->listCentres($concoursId);

      $session = null;
      if ($sessionId) {
        $session = Session::find($sessionId);
      }
      if (!$session) {
        $session = $this->sessionService->getActiveSession($concoursId);
      }

      $filieres = collect();
      if ($session) {
        $filieres = $this->concoursFiliereService->listFilieres($concoursId, $session->id);
      } else {
        $filieres = $concours->filieres()->get()->map(function ($filiere) {
          return [
            'id' => $filiere->id,
            'code_filiere' => $filiere->code_filiere,
            'libelle_filiere' => $filiere->libelle_filiere,
            'desc_filiere' => $filiere->desc_filiere,
            'nombre_places' => $filiere->pivot?->nombre_places,
            'candidatures_validees' => null,
            'places_restantes' => null,
            'taux_remplissage' => null,
            'peut_accepter_candidatures' => null,
          ];
        });
      }

      $totalCandidatures = Candidature::query()
        ->where('concours_id', $concoursId)
        ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
        ->count();

      $totalValidees = Candidature::query()
        ->where('concours_id', $concoursId)
        ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
        ->where('statut_candidature', StatutCandidature::VALIDE->value)
        ->count();

      $stats = [
        'total_candidatures' => $totalCandidatures,
        'total_validees' => $totalValidees,
      ];

      $filename = 'fiche_concours_' . ($concours->libelle_concours ?? 'concours') . '_' . now()->format('Y-m-d_His') . '.xlsx';

      $this->logger->logActivity('export', 'fiche_concours_excel', null, compact('concoursId', 'sessionId'));

      return $this->excelService->exportFicheConcours(
        $concours,
        $session,
        $plannings,
        $stats,
        $paymentConfig,
        $centres,
        $filieres,
        $filename
      );
    } catch (\Exception $e) {
      $this->logger->logError($e, 'export_fiche_concours_excel_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  public function exportConcoursExcel(Request $request)
  {
    try {
      $filters = $request->only(['ecole_id', 'spec_concours_id', 'est_actif', 'search', 'sort_by', 'sort_order']);

      $concours = $this->concoursService->getAll($filters, 10000)->items();
      $concours = collect($concours);

      $filename = 'concours_' . now()->format('Y-m-d_His') . '.xlsx';

      $this->logger->logActivity('export', 'concours_excel', null, $filters);

      return $this->excelService->exportConcours($concours, $filename);
    } catch (\Exception $e) {
      $this->logger->logError($e, 'export_concours_excel_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  public function generateConcoursPdf(Request $request)
  {
    try {
      $filters = $request->only(['ecole_id', 'spec_concours_id', 'est_actif', 'search', 'sort_by', 'sort_order']);

      $concours = $this->concoursService->getAll($filters, 10000)->items();
      $concours = collect($concours);

      $filename = 'liste_concours_' . now()->format('Y-m-d_His') . '.pdf';

      $this->logger->logActivity('pdf_generation', 'concours', null, $filters);

      return $this->pdfService->exportConcours($concours, $filename);
    } catch (\Exception $e) {
      $this->logger->logError($e, 'generate_concours_pdf_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  public function exportCandidatsParConcoursExcel(string $concoursId, Request $request)
  {
    try {
      $sessionId = $request->input('session_id');
      $statut = $request->input('statut_candidature') ?? StatutCandidature::VALIDE->value;

      $candidatures = $this->candidatureService->getCandidatsForConcours(
        $concoursId,
        array_filter(['session_id' => $sessionId, 'statut_candidature' => $statut]),
        10000
      )->items();

      $candidatures = \Illuminate\Database\Eloquent\Collection::make($candidatures)->load([
        'candidat',
        'candidat.utilisateur',
        'candidat.filiere',
        'centreExamen',
      ]);

      $concours = $this->concoursService->getById($concoursId);
      $filename = 'candidats_' . ($concours->libelle_concours ?? 'concours') . '_' . now()->format('Y-m-d_His') . '.xlsx';

      $this->logger->logActivity('export', 'candidats_par_concours_excel', null, compact('concoursId', 'sessionId', 'statut'));

      return $this->excelService->exportCandidatsParConcours($candidatures, $filename);
    } catch (\Exception $e) {
      $this->logger->logError($e, 'export_candidats_par_concours_excel_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  public function exportCandidatsParRegionExcel(string $concoursId, Request $request)
  {
    try {
      $sessionId = $request->input('session_id');
      $statut = $request->input('statut_candidature') ?? StatutCandidature::VALIDE->value;

      $candidatures = $this->candidatureService->getCandidatsForConcours(
        $concoursId,
        array_filter(['session_id' => $sessionId, 'statut_candidature' => $statut]),
        10000
      )->items();

      $candidatures = \Illuminate\Database\Eloquent\Collection::make($candidatures)->load([
        'candidat',
        'candidat.filiere',
        'centreExamen',
      ]);

      $concours = $this->concoursService->getById($concoursId);
      $filename = 'candidats_par_region_' . ($concours->libelle_concours ?? 'concours') . '_' . now()->format('Y-m-d_His') . '.xlsx';

      $this->logger->logActivity('export', 'candidats_par_region_excel', null, compact('concoursId', 'sessionId', 'statut'));

      return $this->excelService->exportCandidatsParRegion($candidatures, $filename);
    } catch (\Exception $e) {
      $this->logger->logError($e, 'export_candidats_par_region_excel_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  public function exportCandidatsParFiliereExcel(string $concoursId, Request $request)
  {
    try {
      $sessionId = $request->input('session_id');
      $statut = $request->input('statut_candidature') ?? StatutCandidature::VALIDE->value;

      $candidatures = $this->candidatureService->getCandidatsForConcours(
        $concoursId,
        array_filter(['session_id' => $sessionId, 'statut_candidature' => $statut]),
        10000
      )->items();

      $candidatures = \Illuminate\Database\Eloquent\Collection::make($candidatures)->load([
        'candidat',
        'candidat.filiere',
        'centreExamen',
      ]);

      $concours = $this->concoursService->getById($concoursId);
      $filename = 'candidats_par_filiere_' . ($concours->libelle_concours ?? 'concours') . '_' . now()->format('Y-m-d_His') . '.xlsx';

      $this->logger->logActivity('export', 'candidats_par_filiere_excel', null, compact('concoursId', 'sessionId', 'statut'));

      return $this->excelService->exportCandidatsParFiliere($candidatures, $filename);
    } catch (\Exception $e) {
      $this->logger->logError($e, 'export_candidats_par_filiere_excel_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  public function exportEtatDocumentsExcel(string $concoursId, Request $request)
  {
    try {
      $sessionId = $request->query('session_id');

      $candidatures = $this->candidatureService->getCandidatsForConcours(
        $concoursId,
        array_filter(['session_id' => $sessionId, 'statut_candidature' => StatutCandidature::VALIDE->value]),
        10000
      )->items();

      $candidatures = \Illuminate\Database\Eloquent\Collection::make($candidatures)->load([
        'candidat.utilisateur',
        'documents.documentRequis',
      ]);

      $concours = $this->concoursService->getById($concoursId, true);
      $concours->loadMissing('documentsRequis');

      $filename = 'etat_documents_' . ($concours->libelle_concours ?? 'concours') . '_' . now()->format('Y-m-d_His') . '.xlsx';

      $this->logger->logActivity('export', 'etat_documents_excel', null, compact('concoursId', 'sessionId'));

      return $this->excelService->exportEtatDocuments($candidatures, $concours, $filename);
    } catch (\Exception $e) {
      $this->logger->logError($e, 'export_etat_documents_excel_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  public function exportRepartitionCandidatsExcel(string $concoursId, Request $request)
  {
    try {
      $sessionId = $request->query('session_id');

      $concours = $this->concoursService->getById($concoursId, true);
      $session = null;
      if ($sessionId) {
        $session = Session::find($sessionId);
      }
      if (!$session) {
        $session = $this->sessionService->getActiveSession($concoursId);
      }

      $filieres = collect();
      if ($session) {
        $filieres = $this->concoursFiliereService->listFilieres($concoursId, $session->id);
      }

      $centres = $this->centreService->listCentres($concoursId);

      $candidaturesParCentre = [];
      foreach ($centres as $centre) {
        $count = Candidature::where('concours_id', $concoursId)
          ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
          ->where('centre_examen_id', $centre->id)
          ->where('statut_candidature', StatutCandidature::VALIDE->value)
          ->count();
        $candidaturesParCentre[] = [
          'centre' => $centre->libelle_centre,
          'ville' => $centre->ville_centre,
          'count' => $count,
        ];
      }

      $filename = 'repartition_candidats_' . ($concours->libelle_concours ?? 'concours') . '_' . now()->format('Y-m-d_His') . '.xlsx';

      $this->logger->logActivity('export', 'repartition_candidats_excel', null, compact('concoursId', 'sessionId'));

      return $this->excelService->exportRepartitionCandidats($filieres, $candidaturesParCentre, $concours, $filename);
    } catch (\Exception $e) {
      $this->logger->logError($e, 'export_repartition_candidats_excel_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  public function exportStatistiquesConcoursExcel(string $concoursId, Request $request)
  {
    try {
      $sessionId = $request->query('session_id');

      $concours = $this->concoursService->getById($concoursId, true);
      $session = null;
      if ($sessionId) {
        $session = Session::find($sessionId);
      }
      if (!$session) {
        $session = $this->sessionService->getActiveSession($concoursId);
      }

      $stats = [
        'total_candidatures' => Candidature::where('concours_id', $concoursId)
          ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
          ->count(),
        'validees' => Candidature::where('concours_id', $concoursId)
          ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
          ->where('statut_candidature', StatutCandidature::VALIDE->value)
          ->count(),
        'soumises' => Candidature::where('concours_id', $concoursId)
          ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
          ->where('statut_candidature', StatutCandidature::SOUMISE->value)
          ->count(),
        'rejetees' => Candidature::where('concours_id', $concoursId)
          ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
          ->where('statut_candidature', StatutCandidature::REJETEE->value)
          ->count(),
        'brouillon' => Candidature::where('concours_id', $concoursId)
          ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
          ->where('statut_candidature', StatutCandidature::BROUILLON->value)
          ->count(),
      ];

      $filieres = collect();
      if ($session) {
        $filieres = $this->concoursFiliereService->listFilieres($concoursId, $session->id);
      }

      $centres = $this->centreService->listCentres($concoursId);
      $paymentConfig = $this->paiementService->getConcoursPaymentConfig($concoursId);

      $filename = 'statistiques_' . ($concours->libelle_concours ?? 'concours') . '_' . now()->format('Y-m-d_His') . '.xlsx';

      $this->logger->logActivity('export', 'statistiques_concours_excel', null, compact('concoursId', 'sessionId'));

      return $this->excelService->exportStatistiquesConcours($concours, $session, $stats, $filieres, $centres, $paymentConfig, $filename);
    } catch (\Exception $e) {
      $this->logger->logError($e, 'export_statistiques_concours_excel_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  public function generateEtatDocumentsPdf(string $concoursId, Request $request)
  {
    try {
      $sessionId = $request->query('session_id');

      $candidatures = $this->candidatureService->getCandidatsForConcours(
        $concoursId,
        array_filter(['session_id' => $sessionId, 'statut_candidature' => StatutCandidature::VALIDE->value]),
        10000
      )->items();

      $candidatures = \Illuminate\Database\Eloquent\Collection::make($candidatures)->load([
        'candidat.utilisateur',
        'documents.documentRequis',
      ]);

      $concours = $this->concoursService->getById($concoursId, true);
      $concours->loadMissing(['ecole', 'documentsRequis']);

      $session = null;
      if ($sessionId) {
        $session = Session::find($sessionId);
      }
      if (!$session) {
        $session = $this->sessionService->getActiveSession($concoursId);
      }

      $filename = 'etat_documents_' . ($concours->libelle_concours ?? 'concours') . '_' . now()->format('Y-m-d_His') . '.pdf';

      $this->logger->logActivity('pdf_generation', 'etat_documents', null, compact('concoursId', 'sessionId'));

      return $this->pdfService->generateEtatDocuments($candidatures, $concours, $session, $filename);
    } catch (\Exception $e) {
      $this->logger->logError($e, 'generate_etat_documents_pdf_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  public function generateRepartitionCandidatsPdf(string $concoursId, Request $request)
  {
    try {
      $sessionId = $request->query('session_id');

      $concours = $this->concoursService->getById($concoursId, true);
      $concours->loadMissing('ecole');

      $session = null;
      if ($sessionId) {
        $session = Session::find($sessionId);
      }
      if (!$session) {
        $session = $this->sessionService->getActiveSession($concoursId);
      }

      $filieres = collect();
      if ($session) {
        $filieres = $this->concoursFiliereService->listFilieres($concoursId, $session->id);
      }

      $centres = $this->centreService->listCentres($concoursId);

      $candidaturesParCentre = [];
      foreach ($centres as $centre) {
        $count = Candidature::where('concours_id', $concoursId)
          ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
          ->where('centre_examen_id', $centre->id)
          ->where('statut_candidature', StatutCandidature::VALIDE->value)
          ->count();
        $candidaturesParCentre[] = [
          'centre' => $centre->libelle_centre,
          'ville' => $centre->ville_centre,
          'count' => $count,
        ];
      }

      $filename = 'repartition_candidats_' . ($concours->libelle_concours ?? 'concours') . '_' . now()->format('Y-m-d_His') . '.pdf';

      $this->logger->logActivity('pdf_generation', 'repartition_candidats', null, compact('concoursId', 'sessionId'));

      return $this->pdfService->generateRepartitionCandidats($filieres, $candidaturesParCentre, $concours, $session, $filename);
    } catch (\Exception $e) {
      $this->logger->logError($e, 'generate_repartition_candidats_pdf_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  public function generateStatistiquesConcoursPdf(string $concoursId, Request $request)
  {
    try {
      $sessionId = $request->query('session_id');

      $concours = $this->concoursService->getById($concoursId, true);
      $concours->loadMissing('ecole');

      $session = null;
      if ($sessionId) {
        $session = Session::find($sessionId);
      }
      if (!$session) {
        $session = $this->sessionService->getActiveSession($concoursId);
      }

      $stats = [
        'total_candidatures' => Candidature::where('concours_id', $concoursId)
          ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
          ->count(),
        'validees' => Candidature::where('concours_id', $concoursId)
          ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
          ->where('statut_candidature', StatutCandidature::VALIDE->value)
          ->count(),
        'soumises' => Candidature::where('concours_id', $concoursId)
          ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
          ->where('statut_candidature', StatutCandidature::SOUMISE->value)
          ->count(),
        'rejetees' => Candidature::where('concours_id', $concoursId)
          ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
          ->where('statut_candidature', StatutCandidature::REJETEE->value)
          ->count(),
        'brouillon' => Candidature::where('concours_id', $concoursId)
          ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
          ->where('statut_candidature', StatutCandidature::BROUILLON->value)
          ->count(),
      ];

      $filieres = collect();
      if ($session) {
        $filieres = $this->concoursFiliereService->listFilieres($concoursId, $session->id);
      }

      $centres = $this->centreService->listCentres($concoursId);
      $paymentConfig = $this->paiementService->getConcoursPaymentConfig($concoursId);

      $filename = 'statistiques_' . ($concours->libelle_concours ?? 'concours') . '_' . now()->format('Y-m-d_His') . '.pdf';

      $this->logger->logActivity('pdf_generation', 'statistiques_concours', null, compact('concoursId', 'sessionId'));

      return $this->pdfService->generateStatistiquesConcours($concours, $session, $stats, $filieres, $centres, $paymentConfig, $filename);
    } catch (\Exception $e) {
      $this->logger->logError($e, 'generate_statistiques_concours_pdf_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Export resultats to Excel
   */
  public function exportResultatsExcel(string $concoursId, string $sessionId)
  {
    try {
      $resultats = $this->resultatService->getResultatsParOrdreDeMerite($concoursId, $sessionId);
      $filename = 'resultats_' . now()->format('Y-m-d_His') . '.xlsx';

      $this->logger->logActivity('export', 'resultats_excel', null, compact('concoursId', 'sessionId'));

      return $this->excelService->exportResultats($resultats, $filename);
    } catch (\Exception $e) {
      $this->logger->logError($e, 'export_resultats_excel_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Generate convocation PDF
   */
  public function generateConvocation(string $candidatureId)
  {
    try {
      $candidature = Candidature::findOrFail($candidatureId);

      $this->logger->logActivity('pdf_generation', 'convocation', $candidatureId);

      $pdf = app(ConvocationPdfService::class)->genererConvocation($candidature);
      $filename = 'convocation_' . ($candidature->code_cand_def ?? $candidature->code_cand_temp ?? $candidature->numero_candidature ?? $candidature->id) . '.pdf';

      return $pdf->download($filename);
    } catch (\Exception $e) {
      $this->logger->logError($e, 'generate_convocation_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Generate emargement PDF
   */
  public function generateEmargement(string $concoursId, string $sessionId, Request $request)
  {
    try {
      $centreId = $request->query('centre_id');
      $salleId = $request->query('salle_id');

      $query = Candidature::query()
        ->where('concours_id', $concoursId)
        ->where('session_id', $sessionId)
        ->where('statut_candidature', StatutCandidature::VALIDE->value);

      if ($centreId) {
        $query->where('centre_examen_id', $centreId);
      }

      if ($salleId) {
        $query->whereHas('affectationsSalles', function ($q) use ($salleId) {
          $q->where('salle_id', $salleId);
        });
      }

      $candidatures = $query
        ->with([
          'candidat.utilisateur',
          'concours.ecole',
          'centreExamen',
          'session',
        ])
        ->orderBy('code_cand_def')
        ->get();

      $epreuve = (object)['intitule' => 'Liste d\'émargement'];
      $filename = 'emargement_' . now()->format('Y-m-d_His') . '.pdf';

      $this->logger->logActivity('pdf_generation', 'emargement', null, compact('concoursId', 'sessionId', 'centreId', 'salleId'));

      return $this->pdfService->generateListeEmargement($candidatures, $epreuve, $filename);
    } catch (\Exception $e) {
      $this->logger->logError($e, 'generate_emargement_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Generate resultats PDF
   */
  public function generateResultatsPdf(string $concoursId, string $sessionId)
  {
    try {

      $concours = $this->concoursService->getById($concoursId, true);

      // Charger la relation ecole si elle n'est pas déjà chargée
      if (!$concours->relationLoaded('ecole')) {
        $concours->load('ecole');
      }

      // Récupérer les résultats par ordre de mérite
      $resultats = $this->resultatService->getResultatsParOrdreDeMerite($concoursId, $sessionId);

      $this->logger->logActivity('pdf_generation', 'resultats', null, compact('concoursId', 'sessionId'));

      return $this->pdfService->generateResultats($resultats, $concours);
    } catch (\Exception $e) {
      $this->logger->logError($e, 'generate_resultats_pdf_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Export candidats by centre (for exam composition)
   */
  public function exportCandidatsParCentre(string $concoursId, Request $request)
  {
    try {
      $sessionId = $request->input('session_id');

      // Get candidatures with relations
      $candidatures = $this->candidatureService->getCandidatsForConcours(
        $concoursId,
        ['session_id' => $sessionId, 'statut_candidature' => StatutCandidature::VALIDE->value],
        10000
      )->items();

      // Load necessary relations
      $candidatures = \Illuminate\Database\Eloquent\Collection::make($candidatures)->load([
        'candidat',
        'centreExamen',
      ]);

      $concours = $this->concoursService->getById($concoursId);
      $filename = 'candidats_par_centre_' . ($concours->libelle_concours ?? 'concours') . '_' . now()->format('Y-m-d_His') . '.xlsx';

      $this->logger->logActivity('export', 'candidats_par_centre', null, compact('concoursId', 'sessionId'));

      return $this->excelService->exportCandidatsParCentre($candidatures, $filename);
    } catch (\Exception $e) {
      $this->logger->logError($e, 'export_candidats_par_centre_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Generate candidats par centre PDF
   */
  public function generateCandidatsParCentrePdf(string $concoursId, Request $request)
  {
    try {
      $sessionId = $request->input('session_id');

      // Get candidatures with relations
      $candidatures = $this->candidatureService->getCandidatsForConcours(
        $concoursId,
        ['session_id' => $sessionId, 'statut_candidature' => StatutCandidature::VALIDE->value],
        10000
      )->items();

      // Load necessary relations
      $candidatures = \Illuminate\Database\Eloquent\Collection::make($candidatures)->load([
        'candidat',
        'centreExamen',
      ]);

      // Get concours with ecole and planning
      $concours = $this->concoursService->getById($concoursId, true);
      if (!$concours->relationLoaded('ecole')) {
        $concours->load('ecole');
      }
      $concours->load('plannings');

      // Get session
      $session = $sessionId ? Session::find($sessionId) : null;
      if (!$session) {
        $session = $this->sessionService->getActiveSession($concoursId);
      }

      $filename = 'candidats_par_centre_' . ($concours->libelle_concours ?? 'concours') . '_' . now()->format('Y-m-d_His') . '.pdf';

      $this->logger->logActivity('pdf_generation', 'candidats_par_centre', null, compact('concoursId', 'sessionId'));

      return $this->pdfService->generateCandidatsParCentre($candidatures, $concours, $session, $filename);
    } catch (\Exception $e) {
      $this->logger->logError($e, 'generate_candidats_par_centre_pdf_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Export planning to Excel
   */
  public function exportPlanningExcel(string $concoursId, string $sessionId)
  {
    try {
      // Get planning using service
      $plannings = $this->planningService->getSchedulesByConcoursSession($concoursId, $sessionId);

      // Load additional relations needed for export
      $plannings->load(['concours', 'session', 'centre']);

      $concours = $this->concoursService->getById($concoursId);
      $filename = 'planning_' . ($concours->libelle_concours ?? 'concours') . '_' . now()->format('Y-m-d_His') . '.xlsx';

      $this->logger->logActivity('export', 'planning_excel', null, compact('concoursId', 'sessionId'));

      return $this->excelService->exportPlanning($plannings, $filename);
    } catch (\Exception $e) {
      $this->logger->logError($e, 'export_planning_excel_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Export planning to PDF
   */
  public function exportPlanningPdf(string $concoursId, string $sessionId)
  {
    try {
      // Get planning using service
      $plannings = $this->planningService->getSchedulesByConcoursSession($concoursId, $sessionId);

      // Load additional relations needed for PDF
      $plannings->load(['concours', 'session', 'centre']);

      // Get concours with ecole
      $concours = $this->concoursService->getById($concoursId, true);
      if (!$concours->relationLoaded('ecole')) {
        $concours->load('ecole');
      }

      // Get session using service
      $session = $this->sessionService->getActiveSession($concoursId);

      $filename = 'planning_' . ($concours->libelle_concours ?? 'concours') . '_' . now()->format('Y-m-d_His') . '.pdf';

      $this->logger->logActivity('pdf_generation', 'planning', null, compact('concoursId', 'sessionId'));

      return $this->pdfService->generatePlanning($plannings, $concours, $session, $filename);
    } catch (\Exception $e) {
      $this->logger->logError($e, 'export_planning_pdf_failed');
      return api_error($e->getMessage(), null, 500);
    }
  }
}
