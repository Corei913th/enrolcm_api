<?php

namespace App\Http\Controllers\Candidat;

use App\Exceptions\Business\EligibilityException;
use App\Http\Controllers\Controller;
use App\Services\Domain\Candidature\CandidatureService;
use App\Services\Domain\Candidature\ConvocationService;

class ConvocationController extends Controller
{
    public function __construct(
        private readonly ConvocationService $convocationService,
        private readonly CandidatureService $candidatureService
    ) {}

    /**
     * Télécharger la convocation pour une candidature
     *
     * @param  string  $id  ID de la candidature
     * @return mixed
     */
    public function download(string $id)
    {
        try {

            $candidature = $this->candidatureService->getCandidatureOrFail($id);

            $candidat = request()->user()->candidat;
            if ($candidature->candidat_id !== $candidat->utilisateur_id) {
                return api_error('Accès non autorisé à cette candidature', null, 403);
            }

            return $this->convocationService->downloadConvocation($candidature);
        } catch (EligibilityException $e) {
            return $e->render();
        } catch (\DomainException $e) {
            return api_error($e->getMessage(), null, 404);
        } catch (\Exception $e) {
            return api_error('Erreur lors du téléchargement de la convocation', ['error' => $e->getMessage()], 500);
        }
    }
}
