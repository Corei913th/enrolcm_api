<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;


class CandidatureHelper
{
    /**
     * Check if candidature has all required candidate fields filled
     * Based on registration form template (fiche-inscription.blade.php)
     * 
     * @param string $candidatureId
     * @return array ['valid' => bool, 'missing' => array]
     */
    public static function hasRequiredCandidateFields(string $candidatureId): array
    {
        $result = DB::table('candidatures')
            ->join('candidats', 'candidatures.candidat_id', '=', 'candidats.utilisateur_id')
            ->leftJoin('utilisateurs', 'candidats.utilisateur_id', '=', 'utilisateurs.id')
            ->where('candidatures.id', $candidatureId)
            ->select([
                // Identity section (lines 507-532)
                'candidats.nom_cand',
                'candidats.prenom_cand',
                'candidats.date_naissance_cand',
                'candidats.lieu_naissance_cand',
                'candidats.sexe_cand',
                'candidats.nationalite_cand',

                // Contact section (lines 536-557)
                'utilisateurs.telephone',
                'utilisateurs.email',
                'candidats.adresse_cand',
                'candidats.ville_etablissement',

                // Academic section (lines 560-590)
                'candidats.filiere_id',
                'candidats.serie_bac',
                'candidats.annee_obtention_bac',
                'candidats.premiere_langue',
                'candidats.etablissement_origine',
            ])
            ->first();

        if (!$result) {
            return ['valid' => false, 'missing' => ['Candidate not found']];
        }

        $missing = [];

        // Identity fields (critical)
        if (empty($result->nom_cand)) $missing[] = 'Nom';
        if (empty($result->prenom_cand)) $missing[] = 'Prénom';
        if (empty($result->date_naissance_cand)) $missing[] = 'Date de naissance';
        if (empty($result->lieu_naissance_cand)) $missing[] = 'Lieu de naissance';
        if (empty($result->sexe_cand)) $missing[] = 'Genre';
        if (empty($result->nationalite_cand)) $missing[] = 'Nationalité';

        // Contact fields
        if (empty($result->telephone)) $missing[] = 'Téléphone';
        if (empty($result->adresse_cand)) $missing[] = 'Adresse';

        // Academic fields (required for eligibility)
        if (empty($result->serie_bac)) $missing[] = 'Série du Bac';
        if (empty($result->annee_obtention_bac)) $missing[] = 'Année du Bac';
        // etablissement_origine is optional, not required for validation

        return [
            'valid' => empty($missing),
            'missing' => $missing
        ];
    }

    /**
     * Check if candidature has complete documents (optimized single query)
     * 
     * @param string $candidatureId
     * @return bool
     */
    public static function hasCompleteDocuments(string $candidatureId): bool
    {
        // Get concours_id for this candidature
        $concoursId = DB::table('candidatures')
            ->where('id', $candidatureId)
            ->value('concours_id');

        if (!$concoursId) {
            return false;
        }

        // Count required documents
        $requiredCount = DB::table('documents_requis')
            ->where('concours_id', $concoursId)
            ->where('est_obligatoire', true)
            ->count();

        if ($requiredCount === 0) {
            return true; // No required documents
        }

        // Count validated documents for this candidature
        $validatedCount = DB::table('documents')
            ->join('documents_requis', 'documents.document_requis_id', '=', 'documents_requis.id')
            ->where('documents.candidature_id', $candidatureId)
            ->where('documents_requis.est_obligatoire', true)
            ->where('documents.statut_verification', 'VALIDE')
            ->count();

        return $validatedCount === $requiredCount;
    }

    /**
     * Check if candidature has valid payment (optimized)
     * 
     * @param string $candidatureId
     * @return bool
     */
    public static function hasValidPayment(string $candidatureId): bool
    {
        return DB::table('paiements')
            ->where('candidature_id', $candidatureId)
            ->where('statut', 'VERIFIED')
            ->exists();
    }

    /**
     * Check if candidature has assigned exam center
     * 
     * @param string $candidatureId
     * @return bool
     */
    public static function hasExamCenter(string $candidatureId): bool
    {
        return DB::table('candidatures')
            ->where('id', $candidatureId)
            ->whereNotNull('centre_examen_id')
            ->exists();
    }

    /**
     * Check if planning is defined for candidature's concours
     * 
     * @param string $candidatureId
     * @return bool
     */
    public static function hasPlanningDefined(string $candidatureId): bool
    {
        return DB::table('candidatures')
            ->join('planning_epreuves', function ($join) {
                $join->on('candidatures.concours_id', '=', 'planning_epreuves.concours_id')
                    ->on('candidatures.session_id', '=', 'planning_epreuves.session_id');
            })
            ->where('candidatures.id', $candidatureId)
            ->where('planning_epreuves.est_actif', true)
            ->exists();
    }

    /**
     * Get candidatures with complete documents (bulk operation)
     * Returns array of candidature IDs
     * 
     * @param string $concoursId
     * @param string $sessionId
     * @return array
     */
    public static function getCandidaturesWithCompleteDocuments(string $concoursId, string $sessionId): array
    {
        $requiredCount = DB::table('documents_requis')
            ->where('concours_id', $concoursId)
            ->where('est_obligatoire', true)
            ->count();

        if ($requiredCount === 0) {
            return DB::table('candidatures')
                ->where('concours_id', $concoursId)
                ->where('session_id', $sessionId)
                ->pluck('id')
                ->toArray();
        }

        return DB::table('candidatures')
            ->where('candidatures.concours_id', $concoursId)
            ->where('candidatures.session_id', $sessionId)
            ->whereIn('candidatures.id', function ($query) use ($requiredCount) {
                $query->select('documents.candidature_id')
                    ->from('documents')
                    ->join('documents_requis', 'documents.document_requis_id', '=', 'documents_requis.id')
                    ->where('documents_requis.est_obligatoire', true)
                    ->where('documents.statut_verification', 'VALIDE')
                    ->groupBy('documents.candidature_id')
                    ->havingRaw('COUNT(*) = ?', [$requiredCount]);
            })
            ->pluck('id')
            ->toArray();
    }
}
