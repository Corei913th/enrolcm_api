<?php

namespace Tests\Unit\Checkers;

use App\Models\SpecConcours;
use App\Services\Domain\Candidature\Checkers\EligibilityChecker;
use App\Services\Domain\Candidature\DocumentService;
use Tests\TestCase;

class EligibilityCheckerTest extends TestCase
{
    private EligibilityChecker $checker;

    private SpecConcours $spec;

    protected function setUp(): void
    {
        parent::setUp();

        $documentService = $this->createMock(DocumentService::class);
        $this->checker = new EligibilityChecker($documentService);

        $this->spec = SpecConcours::factory()->create([
            'age_minimum' => 18,
            'age_maximum' => 30,
            'series_bac_acceptees' => ['S', 'C', 'D'],
            'nationalites_acceptees' => ['Camerounaise', 'Française'],
        ]);
    }

    /** @test */
    public function candidat_eligible_avec_criteres_valides()
    {
        $data = [
            'date_naissance' => now()->subYears(22)->format('Y-m-d'),
            'serie_bac' => 'S',
            'nationalite' => 'Camerounaise',
        ];

        $result = $this->checker->checkPreRegistrationEligibility($data, $this->spec);

        $this->assertTrue($result['eligible']);
        $this->assertEmpty($result['reasons']);
    }

    /** @test */
    public function candidat_trop_jeune()
    {
        $data = [
            'date_naissance' => now()->subYears(16)->format('Y-m-d'), // 16 ans
            'serie_bac' => 'S',
            'nationalite' => 'Camerounaise',
        ];

        $result = $this->checker->checkPreRegistrationEligibility($data, $this->spec);

        $this->assertFalse($result['eligible']);
        $this->assertNotEmpty($result['reasons']);
        $this->assertStringContainsString('Âge minimum', $result['reasons'][0]);
    }

    /** @test */
    public function candidat_trop_age()
    {
        $data = [
            'date_naissance' => now()->subYears(35)->format('Y-m-d'), // 35 ans
            'serie_bac' => 'S',
            'nationalite' => 'Camerounaise',
        ];

        $result = $this->checker->checkPreRegistrationEligibility($data, $this->spec);

        $this->assertFalse($result['eligible']);
        $this->assertStringContainsString('Âge maximum', $result['reasons'][0]);
    }

    /** @test */
    public function candidat_age_limite_minimum_valide()
    {
        $data = [
            'date_naissance' => now()->subYears(18)->format('Y-m-d'), // Exactement 18 ans
            'serie_bac' => 'S',
            'nationalite' => 'Camerounaise',
        ];

        $result = $this->checker->checkPreRegistrationEligibility($data, $this->spec);

        $this->assertTrue($result['eligible']);
    }

    /** @test */
    public function candidat_age_limite_maximum_valide()
    {
        $data = [
            'date_naissance' => now()->subYears(30)->format('Y-m-d'), // Exactement 30 ans
            'serie_bac' => 'S',
            'nationalite' => 'Camerounaise',
        ];

        $result = $this->checker->checkPreRegistrationEligibility($data, $this->spec);

        $this->assertTrue($result['eligible']);
    }

    /** @test */
    public function serie_bac_non_acceptee()
    {
        $data = [
            'date_naissance' => now()->subYears(22)->format('Y-m-d'),
            'serie_bac' => 'A', // Non acceptée
            'nationalite' => 'Camerounaise',
        ];

        $result = $this->checker->checkPreRegistrationEligibility($data, $this->spec);

        $this->assertFalse($result['eligible']);
        $this->assertStringContainsString('baccalauréat non acceptée', $result['reasons'][0]);
    }

    /** @test */
    public function nationalite_non_acceptee()
    {
        $data = [
            'date_naissance' => now()->subYears(22)->format('Y-m-d'),
            'serie_bac' => 'S',
            'nationalite' => 'Américaine', // Non acceptée
        ];

        $result = $this->checker->checkPreRegistrationEligibility($data, $this->spec);

        $this->assertFalse($result['eligible']);
        $this->assertStringContainsString('Nationalité non acceptée', $result['reasons'][0]);
    }

    /** @test */
    public function spec_sans_restriction_age_accepte_tous()
    {
        $specSansRestriction = SpecConcours::factory()->create([
            'age_minimum' => null,
            'age_maximum' => null,
            'series_bac_acceptees' => ['S'],
            'nationalites_acceptees' => ['Camerounaise'],
        ]);

        $data = [
            'date_naissance' => now()->subYears(16)->format('Y-m-d'), // Trop jeune normalement
            'serie_bac' => 'S',
            'nationalite' => 'Camerounaise',
        ];

        $result = $this->checker->checkPreRegistrationEligibility($data, $specSansRestriction);

        $this->assertTrue($result['eligible']);
    }

    /** @test */
    public function spec_sans_restriction_serie_bac_accepte_tous()
    {
        $specSansRestriction = SpecConcours::factory()->create([
            'age_minimum' => 18,
            'age_maximum' => 30,
            'series_bac_acceptees' => null, // Toutes séries acceptées
            'nationalites_acceptees' => ['Camerounaise'],
        ]);

        $data = [
            'date_naissance' => now()->subYears(22)->format('Y-m-d'),
            'serie_bac' => 'Z', // N'importe quelle série
            'nationalite' => 'Camerounaise',
        ];

        $result = $this->checker->checkPreRegistrationEligibility($data, $specSansRestriction);

        $this->assertTrue($result['eligible']);
    }

    /** @test */
    public function spec_sans_restriction_nationalite_accepte_tous()
    {
        $specSansRestriction = SpecConcours::factory()->create([
            'age_minimum' => 18,
            'age_maximum' => 30,
            'series_bac_acceptees' => ['S'],
            'nationalites_acceptees' => null, // Toutes nationalités acceptées
        ]);

        $data = [
            'date_naissance' => now()->subYears(22)->format('Y-m-d'),
            'serie_bac' => 'S',
            'nationalite' => 'Chinoise', // N'importe quelle nationalité
        ];

        $result = $this->checker->checkPreRegistrationEligibility($data, $specSansRestriction);

        $this->assertTrue($result['eligible']);
    }

    /** @test */
    public function retourne_toutes_les_raisons_ineligibilite()
    {
        $data = [
            'date_naissance' => now()->subYears(16)->format('Y-m-d'), // Trop jeune
            'serie_bac' => 'A',                                        // Non acceptée
            'nationalite' => 'Américaine',                             // Non acceptée
        ];

        $result = $this->checker->checkPreRegistrationEligibility($data, $this->spec);

        $this->assertFalse($result['eligible']);
        $this->assertCount(3, $result['reasons']);
    }
}
