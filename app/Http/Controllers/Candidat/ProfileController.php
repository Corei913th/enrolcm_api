<?php

namespace App\Http\Controllers\Candidat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Candidats\UpdateCandidatProfileRequest;
use App\Services\Domain\Candidat\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileService $profileService
    ) {}

    /**
     * Récupérer le profil du candidat
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $candidat = $request->user()->candidat;

            if (! $candidat) {
                return api_error('Profil candidat introuvable', null, 404);
            }

            $profile = $this->profileService->getProfile($candidat);

            return api_success($profile, 'Profil récupéré avec succès');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Mettre à jour le profil du candidat
     */
    public function update(UpdateCandidatProfileRequest $request): JsonResponse
    {
        try {
            $candidat = $request->user()->candidat;

            if (! $candidat) {
                return api_error('Profil candidat introuvable', null, 404);
            }

            $updatedProfile = $this->profileService->updateProfile($candidat, $request->validated());

            return api_success($updatedProfile, 'Profil mis à jour avec succès');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }
}
