<?php

namespace App\Services\Domain\Candidature;

use App\Exceptions\Business\EligibilityException;
use App\Models\Candidature;
use App\Models\Convocation;
use App\Services\Domain\Candidature\Checkers\EligibilityChecker;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use App\Services\Infrastructure\Pdf\ConvocationPdfService;
use App\Traits\HasSmartCache;

class ConvocationService
{
    use HasSmartCache;

    public function __construct(
        private readonly EligibilityChecker $eligibilityChecker,
        private readonly ConvocationPdfService $convocationPdfService,
        private readonly ActivityLoggerService $logger
    ) {}

    protected function getModelTags(): array
    {
        return ['convocations'];
    }

    /**
     * Génère une convocation pour une candidature (avec cache)
     *
     * @throws EligibilityException si non éligible
     */
    public function generateConvocation(Candidature $candidature): Convocation
    {

        $eligibilityResult = $this->eligibilityChecker->canGenerateConvocation($candidature);

        if (! $eligibilityResult['eligible']) {
            throw new EligibilityException(
                $eligibilityResult['reasons'],
                'Impossible de générer la convocation : candidature non éligible'
            );
        }

        return $this->rememberDetail(
            $candidature->id,
            function () use ($candidature) {
                $convocation = $candidature->convocation;

                if (! $convocation) {
                    $convocation = new Convocation;
                    $convocation->candidature_id = $candidature->id;
                    $convocation->numero_convocation = $this->generateNumeroConvocation();
                    $convocation->date_generation = now();
                    $convocation->save();

                    $this->logger->logActivity('generate_convocation', 'convocation', $convocation->id, [
                        'candidature_id' => $candidature->id,
                        'numero' => $convocation->numero_convocation,
                    ]);
                }

                return $convocation;
            },
            'convocation_candidature'
        );
    }

    /**
     * Télécharge la convocation en PDF
     *
     * @return mixed
     *
     * @throws EligibilityException si non éligible
     */
    public function downloadConvocation(Candidature $candidature)
    {
        // Vérifier l'éligibilité
        $eligibilityResult = $this->eligibilityChecker->canGenerateConvocation($candidature);

        if (! $eligibilityResult['eligible']) {
            throw new EligibilityException(
                $eligibilityResult['reasons'],
                'Impossible de télécharger la convocation : candidature non éligible'
            );
        }

        // Générer ou récupérer la convocation
        $convocation = $this->generateConvocation($candidature);

        // Générer le PDF
        $pdf = $this->convocationPdfService->genererConvocation($candidature);

        $convocation->marquerTelechargee();

        $this->logger->logActivity('download_convocation', 'convocation', $convocation->id, [
            'candidature_id' => $candidature->id,
        ]);

        // Retourner le PDF en téléchargement
        $filename = sprintf(
            'convocation_%s_%s.pdf',
            $candidature->code_cand_def ?? $candidature->id,
            now()->format('Ymd')
        );

        return $pdf->download($filename);
    }

    /**
     * Génère un numéro de convocation unique
     */
    private function generateNumeroConvocation(): string
    {
        // Format: CONV-ANNEE-NUMERO (ex: CONV-2024-000123)
        $annee = now()->year;

        return $this->rememberStatic("last_convocation_number_{$annee}", function () use ($annee) {
            $dernier = Convocation::whereYear('created_at', $annee)->count() + 1;

            return sprintf('CONV-%d-%06d', $annee, $dernier);
        });
    }

    /**
     * Obtenir les statistiques des convocations
     */
    public function getStats(): array
    {
        return $this->rememberStatic('convocations_stats', function () {
            return [
                'total' => Convocation::count(),
                'telecharges' => Convocation::whereNotNull('date_telechargement')->count(),
                'par_annee' => Convocation::selectRaw('YEAR(created_at) as annee, COUNT(*) as count')
                    ->groupBy('annee')
                    ->orderBy('annee', 'desc')
                    ->pluck('count', 'annee')
                    ->toArray(),
            ];
        });
    }
}
