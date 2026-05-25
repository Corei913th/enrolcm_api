<?php

namespace App\Services\Domain\AdmissionRules\Repositories;

use App\Models\AdmissionRule;
use Illuminate\Support\Facades\DB;

class AdmissionRuleRepository
{
    /**
     * Récupérer la règle active pour un concours/session
     */
    public function getActiveRule(string $concoursId, string $sessionId): ?AdmissionRule
    {
        return AdmissionRule::where('concours_id', $concoursId)
            ->where('session_id', $sessionId)
            ->where('est_actif', true)
            ->first();
    }

    /**
     * Désactiver toutes les règles pour un concours/session
     *
     * @return int Nombre de règles désactivées
     */
    public function deactivateRules(string $concoursId, string $sessionId): int
    {
        return DB::table('admission_rules')
            ->where('concours_id', $concoursId)
            ->where('session_id', $sessionId)
            ->update(['est_actif' => false]);
    }

    /**
     * Créer une nouvelle règle d'admission
     */
    public function create(array $data): AdmissionRule
    {
        return AdmissionRule::create($data);
    }

    /**
     * Supprimer les règles pour un concours/session
     *
     * @return int Nombre de règles supprimées
     */
    public function deleteRules(string $concoursId, string $sessionId): int
    {
        return DB::table('admission_rules')
            ->where('concours_id', $concoursId)
            ->where('session_id', $sessionId)
            ->delete();
    }

    /**
     * Vérifier si une règle existe pour un concours/session
     */
    public function exists(string $concoursId, string $sessionId): bool
    {
        return DB::table('admission_rules')
            ->where('concours_id', $concoursId)
            ->where('session_id', $sessionId)
            ->exists();
    }
}
