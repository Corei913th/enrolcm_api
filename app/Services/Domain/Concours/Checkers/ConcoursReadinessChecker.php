<?php

namespace App\Services\Domain\Concours\Checkers;

use App\Models\Concours;
use Illuminate\Support\Facades\DB;

/**
 * Vérifie qu'un concours est prêt pour l'inscription des candidats
 */
class ConcoursReadinessChecker
{
    /**
     * Vérifie si un concours est prêt pour l'inscription
     *
     * @return array ['ready' => bool, 'reasons' => array]
     */
    public function check(Concours $concours): array
    {
        $reasons = [];

        // 1. Vérifier que le concours est actif
        if (! $concours->est_actif) {
            $reasons[] = 'Le concours n\'est pas actif';
        }

        // 2. Vérifier que la date limite n'est pas dépassée
        if ($concours->date_limite_depot && $concours->date_limite_depot->isPast()) {
            $reasons[] = 'La date limite de dépôt est dépassée';
        }

        // 3. Vérifier qu'il y a une spec concours
        if (! $concours->spec_concours_id) {
            $reasons[] = 'Aucun critère d\'éligibilité défini';
        }

        // 4. Vérifier qu'il y a au moins 1 filière
        $nbFilieres = DB::table('concours_filiere')
            ->where('concours_id', $concours->id)
            ->count();

        if ($nbFilieres === 0) {
            $reasons[] = 'Aucune filière disponible';
        }

        // 5. Vérifier qu'il y a au moins 1 session
        $nbSessions = DB::table('concours_session')
            ->where('concours_id', $concours->id)
            ->count();

        if ($nbSessions === 0) {
            $reasons[] = 'Aucune session disponible';
        }

        // 6. Vérifier qu'il y a une configuration de paiement active
        $hasConfigPaiement = DB::table('concours_paiements')
            ->where('concours_id', $concours->id)
            ->where('est_actif', true)
            ->exists();

        if (! $hasConfigPaiement) {
            $reasons[] = 'Configuration de paiement manquante';
        }

        // 7. Vérifier qu'il y a au moins 1 centre d'examen actif
        $nbCentres = DB::table('concours_centre')
            ->where('concours_id', $concours->id)
            ->where('est_actif', true)
            ->count();

        if ($nbCentres === 0) {
            $reasons[] = 'Aucun centre d\'examen disponible';
        }

        // 8. Vérifier qu'il y a au moins 1 document requis actif
        $nbDocuments = DB::table('documents_requis')
            ->where('concours_id', $concours->id)
            ->where('est_actif', true)
            ->count();

        if ($nbDocuments === 0) {
            $reasons[] = 'Aucun document requis configuré';
        }

        return [
            'ready' => empty($reasons),
            'reasons' => $reasons,
        ];
    }

    /**
     * Vérifie si un concours est prêt et lance une exception si non
     *
     * @throws \DomainException
     */
    public function ensureReady(Concours $concours): void
    {
        $result = $this->check($concours);

        if (! $result['ready']) {
            throw new \DomainException(
                'Ce concours n\'est pas disponible pour l\'inscription. ' .
                  implode(', ', $result['reasons'])
            );
        }
    }
}
