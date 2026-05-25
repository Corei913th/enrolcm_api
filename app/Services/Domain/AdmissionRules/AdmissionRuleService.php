<?php

namespace App\Services\Domain\AdmissionRules;

use App\Exceptions\Business\AdmissionRuleException;
use App\Models\AdmissionRule;
use App\Services\Domain\AdmissionRules\Repositories\AdmissionRuleRepository;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use App\Traits\HasActivityLogger;

class AdmissionRuleService
{
    use HasActivityLogger;

    protected string $exceptionClass = AdmissionRuleException::class;

    public function __construct(
        private readonly AdmissionRuleRepository $repository,
        ActivityLoggerService $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Récupérer la règle active pour un concours/session
     *
     * @throws AdmissionRuleException
     */
    public function getActiveRule(string $concoursId, string $sessionId): ?AdmissionRule
    {
        try {
            return $this->repository->getActiveRule($concoursId, $sessionId);
        } catch (\Exception $e) {
            logServiceError('Erreur lors de la récupération de la règle d\'admission', $e, compact('concoursId', 'sessionId'));
            throw new AdmissionRuleException('Impossible de récupérer la règle d\'admission');
        }
    }

    /**
     * Créer ou mettre à jour une règle d'admission
     *
     * @throws AdmissionRuleException
     */
    public function upsertRule(string $concoursId, string $sessionId, array $data): AdmissionRule
    {
        try {
            return runTransaction(function () use ($concoursId, $sessionId, $data) {
                $this->repository->deactivateRules($concoursId, $sessionId);

                $rule = $this->repository->create([
                    'concours_id' => $concoursId,
                    'session_id' => $sessionId,
                    ...$data,
                    'est_actif' => true,
                ]);

                $this->logCreate('admission_rule', $rule->id, compact('concoursId', 'sessionId'));

                return $rule;
            }, 'AdmissionRuleService::upsertRule');
        } catch (\Exception $e) {
            logServiceError('Erreur lors de la création/mise à jour de la règle', $e, compact('concoursId', 'sessionId', 'data'));
            throw new AdmissionRuleException('Impossible de configurer la règle d\'admission');
        }
    }

    /**
     * Supprimer les règles d'admission pour un concours/session
     *
     * @return int Nombre de règles supprimées
     *
     * @throws AdmissionRuleException
     */
    public function deleteRules(string $concoursId, string $sessionId): int
    {
        try {
            return runTransaction(function () use ($concoursId, $sessionId) {
                $deleted = $this->repository->deleteRules($concoursId, $sessionId);

                if ($deleted > 0) {
                    $this->logOperation('delete', 'admission_rule', null, compact('concoursId', 'sessionId'));
                }

                return $deleted;
            }, 'AdmissionRuleService::deleteRules');
        } catch (\Exception $e) {
            logServiceError('Erreur lors de la suppression de la règle', $e, compact('concoursId', 'sessionId'));
            throw new AdmissionRuleException('Impossible de supprimer la règle d\'admission');
        }
    }

    /**
     * Vérifier si une règle existe pour un concours/session
     */
    public function hasRule(string $concoursId, string $sessionId): bool
    {
        try {
            return $this->repository->exists($concoursId, $sessionId);
        } catch (\Exception $e) {
            logServiceError('Erreur lors de la vérification de l\'existence de la règle', $e, compact('concoursId', 'sessionId'));

            return false;
        }
    }
}
