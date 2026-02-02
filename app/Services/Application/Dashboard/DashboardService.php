<?php

namespace App\Services\Application\Dashboard;

use App\Models\Ecole;
use App\Models\Concours;
use App\Models\Candidat;
use App\Models\Paiement;
use App\Models\Candidature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Enums\StatutPaiement;
use App\Enums\StatutVerificationDocument;

class DashboardService
{
    /**
     * Obtenir les statistiques globales (Super Admin)
     * 
     * @return array Statistiques globales du système
     */
    public function getGlobalStats(): array
    {
        return Cache::remember('dashboard_global_stats', 300, function () {
            return [
                'ecoles' => $this->getEcolesStats(),
                'concours' => $this->getConcoursStats(),
                'candidats' => $this->getCandidatsStats(),
                'financier' => $this->getFinancialStats(),
                'completion' => $this->getCompletionStats(),
                'evolution' => $this->getEvolutionStats(),
                'top_ecoles' => $this->getTopEcoles(),
                'prochaines_echeances' => $this->getProchainesEcheances(),
                'alertes' => $this->getAlertes(),
            ];
        });
    }

    /**
     * Obtenir les statistiques d'une école spécifique (Admin École)
     * 
     * @param string $ecoleId UUID de l'école
     * @return array Statistiques de l'école
     */
    public function getEcoleStats(string $ecoleId): array
    {
        return Cache::remember("dashboard_ecole_stats_{$ecoleId}", 300, function () use ($ecoleId) {
            $ecole = Ecole::findOrFail($ecoleId);

            return [
                'ecole' => [
                    'id' => $ecole->id,
                    'code' => $ecole->code_ecole,
                    'libelle' => $ecole->libelle_ecole,
                    'region' => $ecole->region,
                ],
                'concours' => $this->getConcoursStatsForEcole($ecoleId),
                'candidats' => $this->getCandidatsStatsForEcole($ecoleId),
                'financier' => $this->getFinancialStatsForEcole($ecoleId),
                'completion' => $this->getCompletionStatsForEcole($ecoleId),
                'alertes' => $this->getAlertesForEcole($ecoleId),
                'prochaines_echeances' => $this->getProchainesEcheancesForEcole($ecoleId),
                'activite_recente' => $this->getActiviteRecente($ecoleId),
            ];
        });
    }

    /**
     * Statistiques des écoles
     */
    private function getEcolesStats(): array
    {
        return [
            'total' => Ecole::count(),
            'actives' => Ecole::where('est_actif', true)->count(),
            'inactives' => Ecole::where('est_actif', false)->count(),
        ];
    }

    /**
     * Statistiques des concours (global)
     */
    private function getConcoursStats(): array
    {
        $now = Carbon::now();

        return [
            'total' => Concours::count(),
            'ouverts' => Concours::where('est_actif', true)
                ->where('date_limite_depot', '>=', $now)
                ->count(),
            'fermes' => Concours::where('date_limite_depot', '<', $now)->count(),
            'en_preparation' => Concours::where('est_actif', false)
                ->where('date_limite_depot', '>', $now)
                ->count(),
        ];
    }

    /**
     * Statistiques des candidats (global)
     */
    private function getCandidatsStats(): array
    {
        return [
            'total' => Candidat::count(),
            'actifs' => Candidat::whereHas('utilisateur', function ($query) {
                $query->where('est_actif', true);
            })->count(),
            'total_candidatures' => Candidature::count(),
        ];
    }

    /**
     * Statistiques financières (global)
     */
    private function getFinancialStats(): array
    {
        $paiementsValides = Paiement::where('statut', StatutPaiement::VERIFIED);

        return [
            'montant_total_collecte' => $paiementsValides->sum('montant'),
            'nombre_paiements_valides' => $paiementsValides->count(),
            'paiements_en_attente' => Paiement::where('statut', StatutPaiement::PENDING)->count(),
            'montant_en_attente' => Paiement::where('statut', StatutPaiement::PENDING)->sum('montant'),
        ];
    }

    /**
     * Taux de complétion des dossiers (global)
     */
    private function getCompletionStats(): array
    {
        $totalCandidatures = Candidature::count();

        if ($totalCandidatures === 0) {
            return [
                'taux_completion' => 0,
                'dossiers_complets' => 0,
                'dossiers_incomplets' => 0,
            ];
        }


        $dossiersComplets = Candidature::where('documents_complets', true)->count();

        return [
            'taux_completion' => round(($dossiersComplets / $totalCandidatures) * 100, 2),
            'dossiers_complets' => $dossiersComplets,
            'dossiers_incomplets' => $totalCandidatures - $dossiersComplets,
        ];
    }

    /**
     * Évolution des inscriptions sur 30 jours
     */
    private function getEvolutionStats(): array
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        $evolution = Candidature::where('created_at', '>=', $thirtyDaysAgo)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'inscriptions' => $item->count,
                ];
            });

        return [
            'periode' => '30_jours',
            'data' => $evolution->toArray(),
            'total_periode' => Candidature::where('created_at', '>=', $thirtyDaysAgo)->count(),
        ];
    }

    /**
     * Top 5 écoles avec le plus d'inscriptions
     */
    /**
     * Top 5 écoles avec le plus d'inscriptions
     */
    private function getTopEcoles(): array
    {

        return DB::table('ecoles')
            ->join('departements', 'ecoles.id', '=', 'departements.ecole_id')
            ->join('filieres', 'departements.id', '=', 'filieres.departement_id')
            ->join('concours_filiere', 'filieres.id', '=', 'concours_filiere.filiere_id')
            ->join('concours', 'concours_filiere.concours_id', '=', 'concours.id')
            ->join('candidatures', 'concours.id', '=', 'candidatures.concours_id')
            ->select(
                'ecoles.id',
                'ecoles.code_ecole',
                'ecoles.libelle_ecole',
                'ecoles.region',
                DB::raw('COUNT(DISTINCT candidatures.id) as total_inscriptions')
            )
            ->groupBy('ecoles.id', 'ecoles.code_ecole', 'ecoles.libelle_ecole', 'ecoles.region')
            ->orderBy('total_inscriptions', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($ecole) {
                return [
                    'id' => $ecole->id,
                    'code' => $ecole->code_ecole,
                    'libelle' => $ecole->libelle_ecole,
                    'region' => $ecole->region,
                    'total_inscriptions' => (int) $ecole->total_inscriptions,
                ];
            })
            ->toArray();
    }

    /**
     * Prochaines échéances (concours fermant dans 48h)
     */
    private function getProchainesEcheances(): array
    {
        $now = Carbon::now();
        $in48Hours = Carbon::now()->addHours(48);


        return Concours::with('ecole')
            ->where('date_limite_depot', '>', $now)
            ->where('date_limite_depot', '<=', $in48Hours)
            ->orderBy('date_limite_depot')
            ->get()
            ->map(function ($concours) {
                return [
                    'concours_id' => $concours->id,
                    'libelle_concours' => $concours->libelle_concours,
                    'ecole' => $concours->ecole?->libelle_ecole ?? 'N/A',
                    'date_cloture' => $concours->date_limite_depot->format('Y-m-d H:i:s'),
                    'heures_restantes' => $concours->date_limite_depot->diffInHours(Carbon::now()),
                ];
            })
            ->toArray();
    }

    /**
     * Alertes urgentes (global)
     */
    private function getAlertes(): array
    {

        return [
            'paiements_en_attente' => Paiement::where('statut', StatutPaiement::PENDING)->count(),
            'documents_a_valider' => DB::table('documents')
                ->where('statut_verification', StatutVerificationDocument::EN_ATTENTE->value)
                ->count(),
            'dossiers_incomplets' => Candidature::where('documents_complets', false)->count(),
        ];
    }

    /**
     * Statistiques des concours pour une école
     */
    private function getConcoursStatsForEcole(string $ecoleId): array
    {
        $now = Carbon::now();


        $concoursQuery = Concours::where('ecole_id', $ecoleId);

        return [
            'total' => $concoursQuery->count(),
            'actifs' => (clone $concoursQuery)->where('est_actif', true)
                ->where('date_limite_depot', '>=', $now)
                ->count(),
            'fermes' => (clone $concoursQuery)->where('date_limite_depot', '<', $now)->count(),
            'en_preparation' => (clone $concoursQuery)->where('est_actif', false)
                ->where('date_limite_depot', '>', $now)
                ->count(),
        ];
    }

    /**
     * Statistiques des candidats pour une école
     */
    private function getCandidatsStatsForEcole(string $ecoleId): array
    {
        $candidaturesQuery = Candidature::whereHas('concours', function ($query) use ($ecoleId) {
            $query->where('ecole_id', $ecoleId);
        });

        return [
            'total_candidatures' => $candidaturesQuery->count(),
            'candidats_uniques' => (clone $candidaturesQuery)->distinct('candidat_id')->count('candidat_id'),
        ];
    }

    /**
     * Statistiques financières pour une école
     */
    private function getFinancialStatsForEcole(string $ecoleId): array
    {
        $paiementsQuery = Paiement::whereHas('concours', function ($query) use ($ecoleId) {
            $query->where('ecole_id', $ecoleId);
        });

        return [
            'montant_total_collecte' => (clone $paiementsQuery)->where('statut', StatutPaiement::VERIFIED)->sum('montant'),
            'nombre_paiements_valides' => (clone $paiementsQuery)->where('statut', StatutPaiement::VERIFIED)->count(),
            'paiements_en_attente' => (clone $paiementsQuery)->where('statut', StatutPaiement::PENDING)->count(),
        ];
    }

    /**
     * Taux de complétion pour une école
     */
    private function getCompletionStatsForEcole(string $ecoleId): array
    {
        $candidaturesQuery = Candidature::whereHas('concours', function ($query) use ($ecoleId) {
            $query->where('ecole_id', $ecoleId);
        });

        $total = $candidaturesQuery->count();

        if ($total === 0) {
            return [
                'taux_completion' => 0,
                'dossiers_complets' => 0,
                'dossiers_incomplets' => 0,
            ];
        }


        $complets = (clone $candidaturesQuery)->where('documents_complets', true)->count();

        return [
            'taux_completion' => round(($complets / $total) * 100, 2),
            'dossiers_complets' => $complets,
            'dossiers_incomplets' => $total - $complets,
        ];
    }

    /**
     * Alertes pour une école
     */
    private function getAlertesForEcole(string $ecoleId): array
    {
        $paiementsEnAttente = Paiement::whereHas('concours', function ($query) use ($ecoleId) {
            $query->where('ecole_id', $ecoleId);
        })->where('statut', StatutPaiement::PENDING)->count();


        $documentsAValider = DB::table('documents')
            ->join('candidatures', 'documents.candidature_id', '=', 'candidatures.id')
            ->join('concours', 'candidatures.concours_id', '=', 'concours.id')
            ->where('concours.ecole_id', $ecoleId)
            ->where('documents.statut_verification', StatutVerificationDocument::EN_ATTENTE->value)
            ->count();


        $dossiersIncomplets = Candidature::whereHas('concours', function ($query) use ($ecoleId) {
            $query->where('ecole_id', $ecoleId);
        })->where('documents_complets', false)->count();

        return [
            'paiements_en_attente' => $paiementsEnAttente,
            'documents_a_valider' => $documentsAValider,
            'dossiers_incomplets' => $dossiersIncomplets,
        ];
    }

    /**
     * Prochaines échéances pour une école
     */
    private function getProchainesEcheancesForEcole(string $ecoleId): array
    {
        $now = Carbon::now();
        $in48Hours = Carbon::now()->addHours(48);


        return Concours::where('ecole_id', $ecoleId)
            ->where('date_limite_depot', '>', $now)
            ->where('date_limite_depot', '<=', $in48Hours)
            ->orderBy('date_limite_depot')
            ->get()
            ->map(function ($concours) {
                return [
                    'concours_id' => $concours->id,
                    'libelle_concours' => $concours->libelle_concours,
                    'date_cloture' => $concours->date_limite_depot->format('Y-m-d H:i:s'),
                    'heures_restantes' => $concours->date_limite_depot->diffInHours(Carbon::now()),
                ];
            })
            ->toArray();
    }

    /**
     * Activité récente pour une école
     */
    private function getActiviteRecente(string $ecoleId): array
    {
        // Dernières inscriptions (7 derniers jours)
        $dernieresInscriptions = Candidature::with(['candidat.utilisateur', 'concours'])
            ->whereHas('concours', function ($query) use ($ecoleId) {
                $query->where('ecole_id', $ecoleId);
            })
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($candidature) {
                return [
                    'type' => 'inscription',
                    'candidat' => $candidature->candidat->utilisateur->user_name ?? 'N/A',
                    'concours' => $candidature->concours->libelle_concours,
                    'date' => $candidature->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // Dernières validations de paiements
        $dernieresValidations = Paiement::with(['candidat.utilisateur', 'concours'])
            ->whereHas('concours', function ($query) use ($ecoleId) {
                $query->where('ecole_id', $ecoleId);
            })
            ->where('statut', StatutPaiement::VERIFIED)
            ->where('updated_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($paiement) {
                return [
                    'type' => 'validation_paiement',
                    'candidat' => $paiement->candidat->utilisateur->user_name ?? 'N/A',
                    'concours' => $paiement->concours->libelle_concours,
                    'montant' => $paiement->montant,
                    'date' => $paiement->updated_at->format('Y-m-d H:i:s'),
                ];
            });

        // Fusionner et trier par date
        $activites = $dernieresInscriptions->concat($dernieresValidations)
            ->sortByDesc('date')
            ->take(15)
            ->values();

        return $activites->toArray();
    }
}
