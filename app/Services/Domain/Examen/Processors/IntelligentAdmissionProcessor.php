<?php

namespace App\Services\Domain\Examen\Processors;

use App\Enums\CategorieAdmission;
use App\Enums\DecisionAdmission;
use App\Helpers\AdmissionHelper;
use App\Helpers\DateHelper;
use App\Models\AdmissionRule;
use App\Models\ResultatFinal;
use App\Traits\HasActivityLogger;
use Illuminate\Support\Collection;

class IntelligentAdmissionProcessor
{
    use HasActivityLogger;

    private const WAITING_LIST_PERCENTAGE = 0.2;

    /**
     * Process intelligent admissions
     */
    public function process(
        Collection $results,
        int $totalPlaces,
        array $regionalQuotas = [],
        ?AdmissionRule $rule = null
    ): array {
        $rule = $rule ?? AdmissionRule::getDefault();

        // 1. Categorize candidates
        $this->categorize($results, $rule);

        // 2. Calculate tie-breaking scores
        $this->calculateTieBreakingScores($results, $rule->criteres_prioritaires ?? ['age', 'region', 'main_subjects']);

        // 3. Sort by priority
        $results = $this->sortByPriority($results);

        // 4. Calculate regional places
        $regionalPlaces = AdmissionHelper::calculateRegionalPlaces($totalPlaces, $regionalQuotas);

        // 5. Assign places
        $stats = $this->assignPlaces($results, $totalPlaces, $regionalPlaces, $rule);

        // 6. Assign final ranks
        $this->assignRanks($results);

        // 7. Save
        $results->each(function ($result) {

            $result->save();
        });

        return $stats;
    }

    /**
     * Categorize candidates by their average
     */
    private function categorize(Collection $results, AdmissionRule $rule): void
    {
        foreach ($results as $result) {
            $result->decision = null;
            $result->est_admis = false;
            $result->rang = null;
            $result->score_departage = null;

            if ($result->moyenne_generale == 0) {
                $result->categorie_admission = CategorieAdmission::ELIMINATOIRE;
                $result->decision = DecisionAdmission::REFUSEE;
            } elseif ($result->moyenne_generale >= $rule->seuil_admission_standard) {
                $result->categorie_admission = CategorieAdmission::STANDARD;
            } elseif ($result->moyenne_generale >= $rule->seuil_admission_minimum) {
                $result->categorie_admission = CategorieAdmission::CONDITIONNEL;
            } else {
                $result->categorie_admission = CategorieAdmission::ELIMINATOIRE;
                $result->decision = DecisionAdmission::REFUSEE;
                // est_admis is already false
            }
        }
    }

    /**
     * Calculate tie-breaking score for each candidate
     */
    private function calculateTieBreakingScores(Collection $results, array $criteria): void
    {
        $regionalCount = [];

        foreach ($results as $result) {
            $candidature = $result->candidature;
            if (! $candidature) {
                continue;
            }

            $score = 0;

            foreach ($criteria as $index => $criterion) {
                $weight = (count($criteria) - $index) * 1000;

                $score += match ($criterion) {
                    'age' => $this->calculateAgeScore($candidature, $weight),
                    'region' => $this->calculateRegionalScore($candidature, $regionalCount, $weight),
                    'main_subjects' => $this->calculateMainSubjectsScore($candidature, $weight),
                    default => 0,
                };
            }

            $result->score_departage = $score;
        }
    }

    /**
     * Calculate age-based score
     */
    private function calculateAgeScore($candidature, float $weight): float
    {
        $candidate = $candidature->candidat;
        if (! $candidate?->date_naissance) {
            return 0;
        }

        $age = DateHelper::calculateAge($candidate->date_naissance);

        return AdmissionHelper::calculateAgeScore($age, $weight);
    }

    /**
     * Calculate regional balance score
     */
    private function calculateRegionalScore($candidature, array &$regionalCount, float $weight): float
    {
        $region = AdmissionHelper::getRegion($candidature);
        $regionalCount[$region] = ($regionalCount[$region] ?? 0) + 1;

        // Fewer candidates from region = better score (balancing)
        return $weight / ($regionalCount[$region] ?? 1);
    }

    /**
     * Calculate main subjects score
     */
    private function calculateMainSubjectsScore($candidature, float $weight): float
    {
        $notes = $candidature->notes()
            ->where('est_definitive', true)
            ->with('epreuve')
            ->get();

        if ($notes->isEmpty()) {
            return 0;
        }

        $maxCoeff = $notes->max(fn ($note) => $note->epreuve->coefficient ?? 1);
        $mainNotes = $notes->filter(fn ($note) => ($note->epreuve->coefficient ?? 1) >= $maxCoeff);

        if ($mainNotes->isEmpty()) {
            return 0;
        }

        $average = $mainNotes->avg('valeur');

        return ($average / 20) * $weight;
    }

    /**
     * Sort results by priority
     */
    private function sortByPriority(Collection $results): Collection
    {
        return $results->sortByDesc(function ($result) {
            $categoryWeight = match ($result->categorie_admission) {
                CategorieAdmission::STANDARD => 1000000,
                CategorieAdmission::CONDITIONNEL => 500000,
                default => 0,
            };

            return $categoryWeight + ($result->moyenne_generale * 1000) + ($result->score_departage ?? 0);
        })->values();
    }

    /**
     * Assign places according to rules
     */
    private function assignPlaces(
        Collection $results,
        int $totalPlaces,
        array $regionalPlaces,
        AdmissionRule $rule
    ): array {
        $stats = [
            'admis' => 0,
            'admis_standard' => 0,
            'admis_conditionnel' => 0,
            'liste_attente' => 0,
            'non_admis' => $results->filter(fn ($r) => $r->decision === DecisionAdmission::REFUSEE)->count(),
        ];

        $regionalAdmitted = [];
        $conditionalUsed = 0;
        $maxConditional = AdmissionHelper::calculateMaxConditionalPlaces(
            $totalPlaces,
            $rule->pourcentage_places_conditionnelles
        );

        // Phase 1: Standard admissions
        foreach ($results as $result) {
            if ($result->categorie_admission !== CategorieAdmission::STANDARD) {
                continue;
            }
            if ($stats['admis'] >= $totalPlaces) {
                break;
            }

            if ($this->canAdmit($result, $regionalPlaces, $regionalAdmitted)) {
                $this->admit($result);
                $stats['admis']++;
                $stats['admis_standard']++;
                $this->incrementRegionalCount($result, $regionalAdmitted);
            } else {
                $this->logOperation('admission_quota_blocked', 'resultat', $result->candidature_id, [
                    'moyenne' => $result->moyenne_generale,
                    'region' => AdmissionHelper::getRegion($result->candidature),
                    'places_max_region' => $regionalPlaces[AdmissionHelper::getRegion($result->candidature)] ?? 'ND',
                    'admis_actuel_region' => $regionalAdmitted[AdmissionHelper::getRegion($result->candidature)] ?? 0,
                ]);
            }
        }

        // Phase 2: Conditional admissions
        if ($rule->permet_admission_conditionnelle && $stats['admis'] < $totalPlaces) {
            foreach ($results as $result) {
                if ($result->categorie_admission !== CategorieAdmission::CONDITIONNEL) {
                    continue;
                }
                if ($result->decision === DecisionAdmission::ADMIS) {
                    continue;
                }
                if ($stats['admis'] >= $totalPlaces || $conditionalUsed >= $maxConditional) {
                    break;
                }

                if ($this->canAdmit($result, $regionalPlaces, $regionalAdmitted)) {
                    $this->admit($result);
                    $stats['admis']++;
                    $stats['admis_conditionnel']++;
                    $conditionalUsed++;
                    $this->incrementRegionalCount($result, $regionalAdmitted);
                }
            }
        }

        // Phase 3: Waiting list
        $waitingListSize = (int) ($totalPlaces * self::WAITING_LIST_PERCENTAGE);
        foreach ($results as $result) {
            if ($result->decision === DecisionAdmission::ADMIS) {
                continue;
            }
            if ($result->categorie_admission === CategorieAdmission::ELIMINATOIRE) {
                continue;
            }
            if ($stats['liste_attente'] >= $waitingListSize) {
                break;
            }

            $this->waitingList($result);
            $stats['liste_attente']++;
        }

        // Phase 4: Rejected
        foreach ($results as $result) {
            if ($result->decision !== null) {
                continue;
            }

            $this->reject($result);
            $stats['non_admis']++;
        }

        return $stats;
    }

    /**
     * Check if candidate can be admitted (regional quota)
     */
    private function canAdmit($result, array $regionalPlaces, array $regionalAdmitted): bool
    {
        if (empty($regionalPlaces)) {
            return true;
        }

        $region = AdmissionHelper::getRegion($result->candidature);
        $admitted = $regionalAdmitted[$region] ?? 0;
        $max = $regionalPlaces[$region] ?? null;

        return $max === null || $admitted < $max;
    }

    /**
     * Increment regional admission count
     */
    private function incrementRegionalCount($result, array &$regionalAdmitted): void
    {
        $region = AdmissionHelper::getRegion($result->candidature);
        $regionalAdmitted[$region] = ($regionalAdmitted[$region] ?? 0) + 1;
    }

    /**
     * Assign final ranks
     */
    private function assignRanks(Collection $results): void
    {
        $rank = 1;
        foreach ($results as $result) {
            if ($result->categorie_admission !== CategorieAdmission::ELIMINATOIRE) {
                $result->rang = $rank++;
            }
        }
    }

    private function admit(ResultatFinal $result): void
    {
        $result->decision = DecisionAdmission::ADMIS;
        $result->est_admis = true;
    }

    private function waitingList(ResultatFinal $result): void
    {
        $result->decision = DecisionAdmission::LISTE_ATTENTE;
        $result->est_admis = false;
    }

    private function reject(ResultatFinal $result): void
    {
        $result->decision = DecisionAdmission::REFUSEE;
        $result->est_admis = false;
    }
}
