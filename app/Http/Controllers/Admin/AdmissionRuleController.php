<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdmissionRules\UpsertAdmissionRuleRequest;
use App\Services\Domain\AdmissionRules\AdmissionRuleService;
use Illuminate\Http\JsonResponse;

class AdmissionRuleController extends Controller
{
    public function __construct(
        private readonly AdmissionRuleService $admissionRuleService
    ) {}

    /**
     * Récupérer la règle d'admission pour un concours/session
     */
    public function show(string $concoursId, string $sessionId): JsonResponse
    {
        $rule = $this->admissionRuleService->getActiveRule($concoursId, $sessionId);

        if (! $rule) {
            return api_success(null, 'Aucune règle d\'admission configurée');
        }

        return api_success($rule);
    }

    /**
     * Créer ou mettre à jour une règle d'admission
     */
    public function upsert(
        UpsertAdmissionRuleRequest $request,
        string $concoursId,
        string $sessionId
    ): JsonResponse {
        $rule = $this->admissionRuleService->upsertRule(
            $concoursId,
            $sessionId,
            $request->validated()
        );

        return api_created($rule, 'Règle d\'admission configurée avec succès');
    }

    /**
     * Supprimer la règle d'admission
     */
    public function destroy(string $concoursId, string $sessionId): JsonResponse
    {
        $deleted = $this->admissionRuleService->deleteRules($concoursId, $sessionId);

        if (! $deleted) {
            return api_error('Aucune règle à supprimer', 404);
        }

        return api_deleted('Règle d\'admission supprimée avec succès');
    }
}
