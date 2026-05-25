<?php

namespace App\Services\Infrastructure\Export;

use App\Exports\GenericExport;
use App\Services\Domain\Concours\Checkers\ConcoursStatusChecker;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExcelExportService
{
    public function __construct(
        private readonly ConcoursStatusChecker $statusChecker
    ) {}

    /**
     * Export data to Excel
     */
    public function export(Collection $data, array $headers, string $filename = 'export.xlsx'): BinaryFileResponse
    {
        return Excel::download(new GenericExport($data, $headers), $filename);
    }

    /**
     * Export candidats list
     */
    public function exportCandidats(Collection $candidats, string $filename = 'candidats.xlsx')
    {
        $headers = [
            'ID',
            'Code',
            'PRU',
            'Nom',
            'Prénom',
            'Email',
            'Téléphone',
            'Sexe',
            'Date de naissance',
            'Lieu de naissance',
            'Nationalité',
            'CNI',
            'Région',
            'Niveau scolaire',
            'Diplôme',
            'Mention',
            'Statut',
            'Date inscription',
        ];

        $data = $candidats->map(fn ($c) => [
            $c->utilisateur_id ?? '',
            $c->candidatures->first()?->numero_candidature ?? $c->candidatures->first()?->code_cand_def ?? '',
            $c->candidatures->first()?->paiement?->reference ?? '',
            $c->nom_cand ?? '',
            $c->prenom_cand ?? '',
            $c->utilisateur->email ?? '',
            $c->telephone_candidat ?? $c->utilisateur->telephone ?? '',
            $c->sexe_cand ?? '',
            $c->date_naissance_cand?->format('Y-m-d') ?? '',
            $c->lieu_naissance_cand ?? '',
            $c->nationalite_cand ?? '',
            $c->numero_cni ?? '',
            $c->region?->value ?? '',
            $c->niveau_scolaire ?? '',
            $c->diplome_admission ?? '',
            $c->mention?->value ?? '',
            $c->est_actif ? 'Actif' : 'Inactif',
            $c->created_at?->format('Y-m-d H:i') ?? '',
        ]);

        return $this->export($data, $headers, $filename);
    }

    /**
     * Export candidatures list
     */
    public function exportCandidatures(Collection $candidatures, string $filename = 'candidatures.xlsx')
    {
        $headers = [
            'ID',
            'Numero inscription',
            'Code temporaire',
            'Code définitif',
            'Candidat',
            'Email',
            'Téléphone',
            'Concours',
            'Session',
            'Centre',
            'Statut',
            'Documents complets',
            'Paiement validé',
            'Date candidature',
            'Date validation',
            'Date inscription',
            'Date dépôt physique',
            'Motif rejet',
        ];

        $data = $candidatures->map(fn ($c) => [
            $c->id ?? '',
            $c->numero_candidature ?? '',
            $c->code_cand_temp ?? '',
            $c->code_cand_def ?? '',
            ($c->candidat->nom_cand ?? '') . ' ' . ($c->candidat->prenom_cand ?? ''),
            $c->candidat->utilisateur->email ?? '',
            $c->candidat->telephone_candidat ?? $c->candidat->utilisateur->telephone ?? '',
            $c->concours->libelle_concours ?? '',
            $c->session->libelle_session ?? '',
            $c->centre->libelle_centre ?? '',
            $c->statut_candidature?->value ?? '',
            $c->documents_complets ? 'Oui' : 'Non',
            $c->paiement_valide ? 'Oui' : 'Non',
            $c->date_candidature?->format('Y-m-d H:i') ?? '',
            $c->date_validation?->format('Y-m-d H:i') ?? '',
            $c->date_inscription?->format('Y-m-d') ?? '',
            $c->date_depot_physique?->format('Y-m-d') ?? '',
            $c->motif_rejet ?? '',
        ]);

        return $this->export($data, $headers, $filename);
    }

    /**
     * Export notes list
     */
    public function exportNotes(Collection $notes, string $filename = 'notes.xlsx')
    {
        $headers = [
            'ID Note',
            'Candidat',
            'Code candidature',
            'Épreuve',
            'Code épreuve',
            'Note',
            'Coefficient',
            'Note pondérée',
            'Correcteur',
            'Date saisie',
            'Dernière modification',
        ];

        $data = $notes->map(fn ($n) => [
            $n->id ?? '',
            ($n->candidature->candidat->nom_cand ?? '') . ' ' . ($n->candidature->candidat->prenom_cand ?? ''),
            $n->candidature->code_cand_def ?? $n->candidature->code_cand_temp ?? '',
            $n->epreuve->intitule ?? '',
            $n->epreuve->code ?? '',
            $n->valeur ?? '',
            $n->epreuve->coefficient ?? 1,
            ($n->valeur ?? 0) * ($n->epreuve->coefficient ?? 1),
            $n->correcteur->utilisateur->user_name ?? '',
            $n->created_at?->format('Y-m-d H:i') ?? '',
            $n->updated_at?->format('Y-m-d H:i') ?? '',
        ]);

        return $this->export($data, $headers, $filename);
    }

    /**
     * Export results with rankings
     */
    public function exportResultats(Collection $resultats, string $filename = 'resultats.xlsx')
    {
        $headers = [
            'Rang',
            'Code candidature',
            'Candidat',
            'Email',
            'Concours',
            'Session',
            'Total points',
            'Moyenne',
            'Est admis',
            'Date résultat',
        ];

        $data = $resultats->map(fn ($r) => [
            $r->rang ?? '',
            $r->candidature->code_cand_def ?? $r->candidature->code_cand_temp ?? '',
            ($r->candidature->candidat->nom_cand ?? '') . ' ' . ($r->candidature->candidat->prenom_cand ?? ''),
            $r->candidature->candidat->utilisateur->email ?? '',
            $r->candidature->concours->libelle_concours ?? '',
            $r->candidature->session->libelle_session ?? '',
            $r->total_points ?? '',
            $r->moyenne_generale ?? '',
            $r->est_admis ? 'Oui' : 'Non',
            $r->created_at?->format('Y-m-d H:i') ?? '',
        ]);

        return $this->export($data, $headers, $filename);
    }

    /**
     * Export planning
     */
    public function exportPlanning(Collection $plannings, string $filename = 'planning.xlsx')
    {
        $headers = [
            'ID',
            'Date',
            'Heure début',
            'Heure fin',
            'Durée (min)',
            'Épreuve',
            'Type épreuve',
            'Coefficient',
            'Concours',
            'Session',
            'Centre',
            'Instructions',
            'Actif',
        ];

        $data = $plannings->map(fn ($p) => [
            $p->id ?? '',
            $p->date_epreuve?->format('Y-m-d') ?? '',
            $p->getHeureDebutFormatee() ?? '',
            $p->getHeureFinFormatee() ?? '',
            $p->getDureeEnMinutes() ?? '',
            $p->epreuve->intitule ?? '',
            $p->epreuve->type_epreuve ?? '',
            $p->epreuve->coefficient ?? '',
            $p->concours->libelle_concours ?? '',
            $p->session->libelle_session ?? '',
            $p->centre->libelle_centre ?? '',
            $p->instructions ?? '',
            $p->est_actif ? 'Oui' : 'Non',
        ]);

        return $this->export($data, $headers, $filename);
    }

    /**
     * Export concours list
     */
    public function exportConcours(Collection $concours, string $filename = 'concours.xlsx')
    {
        $headers = [
            'ID',
            'Libellé',
            'Date limite dépôt',
            'Période examens',
            'Frais inscription',
            'Nombre max places',
            'Est actif',
            'Date création',
        ];

        $data = $concours->map(fn ($c) => [
            $c->id ?? '',
            $c->libelle_concours ?? '',
            $c->date_limite_depot?->format('Y-m-d') ?? '',
            $this->statusChecker->getExamPeriod($c) ?? 'Planning non défini',
            $c->frais_inscription ?? '',
            $c->nbre_max_places ?? '',
            $c->est_actif ? 'Oui' : 'Non',
            $c->created_at?->format('Y-m-d H:i') ?? '',
        ]);

        return $this->export($data, $headers, $filename);
    }

    /**
     * Export centres list
     */
    public function exportCentres(Collection $centres, string $filename = 'centres.xlsx')
    {
        $headers = [
            'ID',
            'Libellé',
            'Type',
            'Ville',
            'Région',
            'Département',
            'Arrondissement',
            'Capacité',
            'Nombre salles',
            'Responsable',
            'Est actif',
        ];

        $data = $centres->map(fn ($c) => [
            $c->id ?? '',
            $c->libelle_centre ?? '',
            $c->type_centre ?? '',
            $c->ville_centre ?? '',
            $c->region->libelle?->label() ?? '',
            $c->departement ?? '',
            $c->arrondissement ?? '',
            $c->capacite ?? '',
            $c->salles->count() ?? 0,
            $c->responsable->utilisateur->user_name ?? '',
            $c->est_actif ? 'Oui' : 'Non',
        ]);

        return $this->export($data, $headers, $filename);
    }

    /**
     * Export epreuves list
     */
    public function exportEpreuves(Collection $epreuves, string $filename = 'epreuves.xlsx')
    {
        $headers = [
            'ID',
            'Code',
            'Intitulé',
            'Type',
            'Coefficient',
            'Durée (min)',
            'Session',
            'Description',
            'Est actif',
            'Date création',
        ];

        $data = $epreuves->map(fn ($e) => [
            $e->id_epreuve ?? '',
            $e->code ?? '',
            $e->intitule ?? '',
            $e->type_epreuve ?? '',
            $e->coefficient ?? '',
            $e->duree_minutes ?? '',
            $e->session ?? '',
            $e->description ?? '',
            $e->est_actif ? 'Oui' : 'Non',
            $e->created_at?->format('Y-m-d H:i') ?? '',
        ]);

        return $this->export($data, $headers, $filename);
    }

    /**
     * Export paiements list
     */
    public function exportPaiements(Collection $paiements, string $filename = 'paiements.xlsx')
    {
        $headers = [
            'ID',
            'Référence',
            'Candidat',
            'Code candidature',
            'Montant',
            'Montant OCR',
            'Statut',
            'Banque OCR',
            'Référence OCR',
            'Confiance OCR',
            'Date validation',
            'Validé par',
            'Motif rejet',
        ];

        $data = $paiements->map(fn ($p) => [
            $p->id ?? '',
            $p->reference ?? '',
            ($p->candidature->candidat->nom_cand ?? '') . ' ' . ($p->candidature->candidat->prenom_cand ?? ''),
            $p->candidature->code_cand_def ?? $p->candidature->code_cand_temp ?? '',
            $p->montant ?? '',
            $p->montant_ocr ?? '',
            $p->statut?->value ?? '',
            $p->banque_ocr ?? '',
            $p->reference_ocr ?? '',
            $p->ocrConfidencePercent() ? $p->ocrConfidencePercent() . '%' : '',
            $p->validated_at?->format('Y-m-d H:i') ?? '',
            $p->validatedBy->user_name ?? '',
            $p->motif_rejet ?? '',
        ]);

        return $this->export($data, $headers, $filename);
    }

    /**
     * Export salles list
     */
    public function exportSalles(Collection $salles, string $filename = 'salles.xlsx')
    {
        $headers = [
            'ID',
            'Numéro salle',
            'Centre',
            'Capacité',
            'Est actif',
        ];

        $data = $salles->map(fn ($s) => [
            $s->id ?? '',
            $s->numero_salle ?? '',
            $s->centre->libelle_centre ?? '',
            $s->capacite ?? '',
            $s->est_actif ? 'Oui' : 'Non',
        ]);

        return $this->export($data, $headers, $filename);
    }

    /**
     * Export candidats by centre (for exam composition)
     */
    public function exportCandidatsParCentre(Collection $candidatures, string $filename = 'candidats_par_centre.xlsx')
    {
        $headers = [
            'Centre d\'examen',
            'Ville',
            'N°',
            'Code candidat',
            'Nom',
            'Prénom',
            'Date de naissance',
            'Lieu de naissance',
            'Sexe',
            'Première langue',
        ];

        // Group by centre
        $grouped = $candidatures->groupBy(function ($candidature) {
            return $candidature->centreExamen?->id ?? 'sans_centre';
        });

        $data = collect();
        $numero = 1;

        foreach ($grouped as $centreId => $candidaturesParCentre) {
            $centre = $candidaturesParCentre->first()->centreExamen;
            $centreName = $centre ? $centre->nom_centre : 'Sans centre';
            $centreVille = $centre ? $centre->ville_centre : '';

            // Add centre header row
            $data->push([
                $centreName,
                $centreVille,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ]);

            // Add candidats for this centre
            foreach ($candidaturesParCentre as $candidature) {
                $candidat = $candidature->candidat;
                $data->push([
                    '', // Centre (empty for detail rows)
                    '', // Ville (empty for detail rows)
                    $numero++,
                    $candidature->code_cand_def ?? $candidature->code_cand_temp ?? '',
                    strtoupper($candidat->nom_cand ?? ''),
                    ucwords(strtolower($candidat->prenom_cand ?? '')),
                    $candidat->date_naissance_cand ? Carbon::parse($candidat->date_naissance_cand)->format('d/m/Y') : '',
                    $candidat->lieu_naissance_cand ?? '',
                    $candidat->sexe_cand === 'M' ? 'M' : ($candidat->sexe_cand === 'F' ? 'F' : ''),
                    $candidat->premiere_langue?->label() ?? '',
                ]);
            }

            // Add empty row between centres
            $data->push(['', '', '', '', '', '', '', '', '', '']);
        }

        return $this->export($data, $headers, $filename);
    }

    public function exportCandidatsParConcours(Collection $candidatures, string $filename = 'candidats_par_concours.xlsx')
    {
        $headers = [
            'N°',
            'Code candidat',
            'Nom',
            'Prénom',
            'Email',
            'Téléphone',
            'Région',
            'Filière',
            'Centre d\'examen',
            'Statut',
        ];

        $numero = 1;
        $data = $candidatures->map(function ($candidature) use (&$numero) {
            $candidat = $candidature->candidat;

            return [
                $numero++,
                $candidature->code_cand_def ?? $candidature->code_cand_temp ?? '',
                strtoupper($candidat->nom_cand ?? ''),
                ucwords(strtolower($candidat->prenom_cand ?? '')),
                $candidat->utilisateur->email ?? '',
                $candidat->telephone ?? $candidat->utilisateur->telephone ?? '',
                $candidat->region?->label() ?? ($candidat->region?->value ?? ''),
                $candidat->filiere?->libelle_filiere ?? '',
                $candidature->centreExamen?->nom_centre ?? '',
                $candidature->statut_candidature?->value ?? '',
            ];
        });

        return $this->export($data, $headers, $filename);
    }

    public function exportCandidatsParRegion(Collection $candidatures, string $filename = 'candidats_par_region.xlsx')
    {
        $headers = [
            'Région',
            'N°',
            'Code candidat',
            'Nom',
            'Prénom',
            'Filière',
            'Centre d\'examen',
            'Statut',
        ];

        $grouped = $candidatures->groupBy(function ($candidature) {
            $region = $candidature->candidat?->region;

            return $region?->value ?? 'NON_SPECIFIEE';
        });

        $data = collect();
        $numero = 1;

        foreach ($grouped as $regionValue => $candidaturesParRegion) {
            $regionLabel = $candidaturesParRegion->first()?->candidat?->region?->label() ?? 'Non spécifiée';

            $data->push([
                $regionLabel,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ]);

            foreach ($candidaturesParRegion as $candidature) {
                $candidat = $candidature->candidat;

                $data->push([
                    '',
                    $numero++,
                    $candidature->code_cand_def ?? $candidature->code_cand_temp ?? '',
                    strtoupper($candidat->nom_cand ?? ''),
                    ucwords(strtolower($candidat->prenom_cand ?? '')),
                    $candidat->filiere?->libelle_filiere ?? '',
                    $candidature->centreExamen?->nom_centre ?? '',
                    $candidature->statut_candidature?->value ?? '',
                ]);
            }

            $data->push(['', '', '', '', '', '', '', '']);
        }

        return $this->export($data, $headers, $filename);
    }

    public function exportCandidatsParFiliere(Collection $candidatures, string $filename = 'candidats_par_filiere.xlsx')
    {
        $headers = [
            'Filière',
            'N°',
            'Code candidat',
            'Nom',
            'Prénom',
            'Région',
            'Centre d\'examen',
            'Statut',
        ];

        $grouped = $candidatures->groupBy(function ($candidature) {
            return $candidature->candidat?->filiere?->id ?? 'SANS_FILIERE';
        });

        $data = collect();
        $numero = 1;

        foreach ($grouped as $filiereId => $candidaturesParFiliere) {
            $filiereLabel = $candidaturesParFiliere->first()?->candidat?->filiere?->libelle_filiere ?? 'Sans filière';

            $data->push([
                $filiereLabel,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ]);

            foreach ($candidaturesParFiliere as $candidature) {
                $candidat = $candidature->candidat;

                $data->push([
                    '',
                    $numero++,
                    $candidature->code_cand_def ?? $candidature->code_cand_temp ?? '',
                    strtoupper($candidat->nom_cand ?? ''),
                    ucwords(strtolower($candidat->prenom_cand ?? '')),
                    $candidat->region?->label() ?? ($candidat->region?->value ?? ''),
                    $candidature->centreExamen?->nom_centre ?? '',
                    $candidature->statut_candidature?->value ?? '',
                ]);
            }

            $data->push(['', '', '', '', '', '', '', '']);
        }

        return $this->export($data, $headers, $filename);
    }

    public function exportFicheConcours(
        object $concours,
        ?object $session,
        Collection $plannings,
        array $stats = [],
        ?array $paymentConfig = null,
        ?Collection $centres = null,
        ?Collection $filieres = null,
        string $filename = 'fiche_concours.xlsx'
    ) {
        $headers = ['Section', 'Champ', 'Valeur', 'Détails'];
        $data = collect();

        $data->push(['Informations générales', 'Concours', $concours->libelle_concours ?? '', '']);
        $data->push(['Informations générales', 'Session', $session?->libelle_session ?? '', '']);
        $data->push([
            'Informations générales',
            'Date ouverture inscriptions',
            $session?->date_ouverture_inscription?->format('d/m/Y') ?? '',
            '',
        ]);
        $data->push([
            'Informations générales',
            'Date clôture inscriptions',
            $session?->date_fermeture_inscription?->format('d/m/Y') ?? '',
            '',
        ]);
        $data->push([
            'Informations générales',
            'Date limite dépôt',
            $concours->date_limite_depot?->format('d/m/Y') ?? '',
            '',
        ]);
        $data->push(['Informations générales', 'Frais inscription', $concours->frais_inscription ?? '', '']);
        $data->push(['Informations générales', 'Places max', $concours->nbre_max_places ?? '', '']);
        $data->push(['Informations générales', 'Statut', $concours->est_actif ? 'Actif' : 'Inactif', '']);
        $data->push(['Statistiques', 'Total candidatures', $stats['total_candidatures'] ?? 0, '']);
        $data->push(['Statistiques', 'Total validées', $stats['total_validees'] ?? 0, '']);

        if ($paymentConfig) {
            $data->push(['Paiement', 'Montant', $paymentConfig['montant'] ?? '', $paymentConfig['devise'] ?? '']);
            $data->push(['Paiement', 'Frais paiement', $paymentConfig['frais_paiement'] ?? '', $paymentConfig['devise'] ?? '']);
            $data->push(['Paiement', 'Montant total', $paymentConfig['montant_total'] ?? '', $paymentConfig['devise'] ?? '']);
            $data->push([
                'Paiement',
                'Date limite paiement',
                $paymentConfig['date_limite']?->format('d/m/Y') ?? '',
                '',
            ]);
            $data->push(['Paiement', 'Type paiement', $paymentConfig['type_paiement'] ?? '', '']);
            $data->push([
                'Paiement',
                'Banques acceptées',
                is_array($paymentConfig['banques_acceptees'] ?? null) ? implode(', ', $paymentConfig['banques_acceptees']) : '',
                '',
            ]);
            $data->push([
                'Paiement',
                'Validation automatique',
                ! empty($paymentConfig['validation_auto']) ? 'Oui' : 'Non',
                '',
            ]);
            $data->push(['Paiement', 'Instructions', $paymentConfig['instructions'] ?? '', '']);
        }

        if ($centres && $centres->isNotEmpty()) {
            foreach ($centres as $centre) {
                $data->push([
                    'Centres',
                    $centre->libelle_centre ?? '',
                    $centre->ville_centre ?? '',
                    $centre->region?->libelle?->label() ?? '',
                ]);
            }
        }

        if ($filieres && $filieres->isNotEmpty()) {
            foreach ($filieres as $filiere) {
                $data->push([
                    'Filières',
                    $filiere['libelle_filiere'] ?? '',
                    $filiere['nombre_places'] ?? '',
                    $filiere['candidatures_validees'] ?? '',
                ]);
            }
        }

        if ($plannings->isNotEmpty()) {
            foreach ($plannings as $planning) {
                $data->push([
                    'Planning',
                    $planning->date_epreuve?->format('d/m/Y') ?? '',
                    $planning->epreuve->intitule ?? '',
                    ($planning->centre->libelle_centre ?? '') . ' ' . ($planning->getHeureDebutFormatee() ?? ''),
                ]);
            }
        }

        return $this->export($data, $headers, $filename);
    }

    public function exportEtatDocuments(Collection $candidatures, object $concours, string $filename = 'etat_documents.xlsx')
    {
        $documentsRequis = $concours->documentsRequis ?? collect();

        $headers = ['N°', 'Code candidat', 'Nom', 'Prénom'];
        if ($documentsRequis->isEmpty()) {
            $headers[] = 'Statut';
        } else {
            foreach ($documentsRequis as $doc) {
                $headers[] = $doc->libelle_document ?? 'Document';
            }
        }

        $numero = 1;
        $data = $candidatures->map(function ($candidature) use (&$numero, $documentsRequis) {
            $candidat = $candidature->candidat;
            $row = [
                $numero++,
                $candidature->code_cand_def ?? $candidature->code_cand_temp ?? '',
                strtoupper($candidat->nom_cand ?? ''),
                ucwords(strtolower($candidat->prenom_cand ?? '')),
            ];

            if ($documentsRequis->isEmpty()) {
                $row[] = 'Aucun document requis';
            } else {
                foreach ($documentsRequis as $docRequis) {
                    $row[] = 'À vérifier';
                }
            }

            return $row;
        });

        return $this->export($data, $headers, $filename);
    }

    public function exportRepartitionCandidats(Collection $filieres, array $candidaturesParCentre, object $concours, string $filename = 'repartition_candidats.xlsx')
    {
        $headers = ['Section', 'Libellé', 'Places/Candidats', 'Détails'];
        $data = collect();

        $data->push(['Concours', $concours->libelle_concours ?? '', '', '']);
        $data->push(['', '', '', '']);

        if ($filieres->isNotEmpty()) {
            $data->push(['Répartition par filière', '', '', '']);
            foreach ($filieres as $filiere) {
                $data->push([
                    'Filière',
                    $filiere['libelle_filiere'] ?? '',
                    $filiere['nombre_places'] ?? '',
                    ($filiere['candidatures_validees'] ?? 0) . ' candidats validés',
                ]);
            }
            $data->push(['', '', '', '']);
        }

        if (! empty($candidaturesParCentre)) {
            $data->push(['Répartition par centre', '', '', '']);
            foreach ($candidaturesParCentre as $item) {
                $data->push([
                    'Centre',
                    $item['centre'] ?? '',
                    $item['count'] ?? 0,
                    $item['ville'] ?? '',
                ]);
            }
        }

        return $this->export($data, $headers, $filename);
    }

    public function exportStatistiquesConcours(
        object $concours,
        ?object $session,
        array $stats,
        Collection $filieres,
        Collection $centres,
        ?array $paymentConfig,
        string $filename = 'statistiques_concours.xlsx'
    ) {
        $headers = ['Catégorie', 'Indicateur', 'Valeur', 'Détails'];
        $data = collect();

        $data->push(['Concours', 'Libellé', $concours->libelle_concours ?? '', '']);
        $data->push(['Concours', 'Session', $session?->libelle_session ?? '', '']);
        $data->push(['Concours', 'Places totales', $concours->nbre_max_places ?? '', '']);
        $data->push(['', '', '', '']);

        $data->push(['Candidatures', 'Total', $stats['total_candidatures'] ?? 0, '']);
        $data->push(['Candidatures', 'Validées', $stats['validees'] ?? 0, '']);
        $data->push(['Candidatures', 'Soumises', $stats['soumises'] ?? 0, '']);
        $data->push(['Candidatures', 'Rejetées', $stats['rejetees'] ?? 0, '']);
        $data->push(['Candidatures', 'Brouillon', $stats['brouillon'] ?? 0, '']);
        $data->push(['', '', '', '']);

        if ($filieres->isNotEmpty()) {
            $data->push(['Filières', 'Nombre de filières', $filieres->count(), '']);
            $totalPlacesFilieres = $filieres->sum('nombre_places');
            $data->push(['Filières', 'Total places filières', $totalPlacesFilieres, '']);
            $data->push(['', '', '', '']);
        }

        if ($centres->isNotEmpty()) {
            $data->push(['Centres', 'Nombre de centres', $centres->count(), '']);
            $data->push(['', '', '', '']);
        }

        if ($paymentConfig) {
            $data->push(['Paiement', 'Montant', $paymentConfig['montant'] ?? '', $paymentConfig['devise'] ?? '']);
            $data->push(['Paiement', 'Frais', $paymentConfig['frais_paiement'] ?? '', $paymentConfig['devise'] ?? '']);
            $data->push(['Paiement', 'Total', $paymentConfig['montant_total'] ?? '', $paymentConfig['devise'] ?? '']);
        }

        return $this->export($data, $headers, $filename);
    }
}
