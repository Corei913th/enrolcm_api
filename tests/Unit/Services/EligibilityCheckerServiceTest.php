<?php

namespace Tests\Unit\Services;

use App\Models\Candidat;
use App\Models\SpecConcours;
use App\Services\Domain\Candidature\DocumentService;
use App\Services\Domain\Candidature\Checkers\EligibilityChecker;
use Carbon\Carbon;
use Tests\TestCase;

class EligibilityCheckerServiceTest extends TestCase
{
  private EligibilityChecker $service;
  private DocumentService $documentService;

  protected function setUp(): void
  {
    parent::setUp();
    $this->documentService = $this->createMock(DocumentService::class);
    $this->service = new EligibilityChecker($this->documentService);
  }

  /** @test */
  public function it_accepts_candidat_within_age_range()
  {
    $candidat = new Candidat([
      'date_naissance_cand' => Carbon::now()->subYears(20),
    ]);

    $spec = new SpecConcours([
      'age_minimum' => 18,
      'age_maximum' => 25,
    ]);

    $result = $this->service->checkAcademicEligibility($candidat, $spec);

    $this->assertTrue($result['eligible']);
    $this->assertEmpty($result['reasons']);
  }

  /** @test */
  public function it_rejects_candidat_below_minimum_age()
  {
    $candidat = new Candidat([
      'date_naissance_cand' => Carbon::now()->subYears(16),
    ]);

    $spec = new SpecConcours([
      'age_minimum' => 18,
      'age_maximum' => 25,
    ]);

    $result = $this->service->checkAcademicEligibility($candidat, $spec);

    $this->assertFalse($result['eligible']);
    $this->assertCount(1, $result['reasons']);
    $this->assertStringContainsString('Âge minimum requis: 18 ans', $result['reasons'][0]);
  }

  /** @test */
  public function it_rejects_candidat_above_maximum_age()
  {
    $candidat = new Candidat([
      'date_naissance_cand' => Carbon::now()->subYears(30),
    ]);

    $spec = new SpecConcours([
      'age_minimum' => 18,
      'age_maximum' => 25,
    ]);

    $result = $this->service->checkAcademicEligibility($candidat, $spec);

    $this->assertFalse($result['eligible']);
    $this->assertCount(1, $result['reasons']);
    $this->assertStringContainsString('Âge maximum autorisé: 25 ans', $result['reasons'][0]);
  }

  /** @test */
  public function it_accepts_all_ages_when_no_age_restriction()
  {
    $candidat = new Candidat([
      'date_naissance_cand' => Carbon::now()->subYears(50),
    ]);

    $spec = new SpecConcours([
      'age_minimum' => null,
      'age_maximum' => null,
    ]);

    $result = $this->service->checkAcademicEligibility($candidat, $spec);

    $this->assertTrue($result['eligible']);
    $this->assertEmpty($result['reasons']);
  }

  /** @test */
  public function it_accepts_candidat_with_accepted_bac_series()
  {
    $candidat = new Candidat([
      'serie_bac' => 'C',
      'date_naissance_cand' => Carbon::now()->subYears(20),
    ]);

    $spec = new SpecConcours([
      'series_bac_acceptees' => ['C', 'D', 'E'],
    ]);

    $result = $this->service->checkAcademicEligibility($candidat, $spec);

    $this->assertTrue($result['eligible']);
    $this->assertEmpty($result['reasons']);
  }

  /** @test */
  public function it_rejects_candidat_with_non_accepted_bac_series()
  {
    $candidat = new Candidat([
      'serie_bac' => 'A',
      'date_naissance_cand' => Carbon::now()->subYears(20),
    ]);

    $spec = new SpecConcours([
      'series_bac_acceptees' => ['C', 'D', 'E'],
    ]);

    $result = $this->service->checkAcademicEligibility($candidat, $spec);

    $this->assertFalse($result['eligible']);
    $this->assertCount(1, $result['reasons']);
    $this->assertStringContainsString('Série de baccalauréat non acceptée', $result['reasons'][0]);
    $this->assertStringContainsString('C, D, E', $result['reasons'][0]);
  }

  /** @test */
  public function it_accepts_all_bac_series_when_no_restriction()
  {
    $candidat = new Candidat([
      'serie_bac' => 'A',
      'date_naissance_cand' => Carbon::now()->subYears(20),
    ]);

    $spec = new SpecConcours([
      'series_bac_acceptees' => null,
    ]);

    $result = $this->service->checkAcademicEligibility($candidat, $spec);

    $this->assertTrue($result['eligible']);
    $this->assertEmpty($result['reasons']);
  }

  /** @test */
  public function it_accepts_all_bac_series_when_empty_array()
  {
    $candidat = new Candidat([
      'serie_bac' => 'F',
      'date_naissance_cand' => Carbon::now()->subYears(20),
    ]);

    $spec = new SpecConcours([
      'series_bac_acceptees' => [],
    ]);

    $result = $this->service->checkAcademicEligibility($candidat, $spec);

    $this->assertTrue($result['eligible']);
    $this->assertEmpty($result['reasons']);
  }

  /** @test */
  public function it_accepts_candidat_with_accepted_nationality()
  {
    $candidat = new Candidat([
      'nationalite_cand' => 'Camerounaise',
      'date_naissance_cand' => Carbon::now()->subYears(20),
    ]);

    $spec = new SpecConcours([
      'nationalites_acceptees' => ['Camerounaise', 'Française'],
    ]);

    $result = $this->service->checkAcademicEligibility($candidat, $spec);

    $this->assertTrue($result['eligible']);
    $this->assertEmpty($result['reasons']);
  }

  /** @test */
  public function it_rejects_candidat_with_non_accepted_nationality()
  {
    $candidat = new Candidat([
      'nationalite_cand' => 'Nigériane',
      'date_naissance_cand' => Carbon::now()->subYears(20),
    ]);

    $spec = new SpecConcours([
      'nationalites_acceptees' => ['Camerounaise', 'Française'],
    ]);

    $result = $this->service->checkAcademicEligibility($candidat, $spec);

    $this->assertFalse($result['eligible']);
    $this->assertCount(1, $result['reasons']);
    $this->assertStringContainsString('Nationalité non acceptée', $result['reasons'][0]);
    $this->assertStringContainsString('Camerounaise, Française', $result['reasons'][0]);
  }

  /** @test */
  public function it_accepts_all_nationalities_when_no_restriction()
  {
    $candidat = new Candidat([
      'nationalite_cand' => 'Nigériane',
      'date_naissance_cand' => Carbon::now()->subYears(20),
    ]);

    $spec = new SpecConcours([
      'nationalites_acceptees' => null,
    ]);

    $result = $this->service->checkAcademicEligibility($candidat, $spec);

    $this->assertTrue($result['eligible']);
    $this->assertEmpty($result['reasons']);
  }

  /** @test */
  public function it_accepts_all_nationalities_when_empty_array()
  {
    $candidat = new Candidat([
      'nationalite_cand' => 'Sénégalaise',
      'date_naissance_cand' => Carbon::now()->subYears(20),
    ]);

    $spec = new SpecConcours([
      'nationalites_acceptees' => [],
    ]);

    $result = $this->service->checkAcademicEligibility($candidat, $spec);

    $this->assertTrue($result['eligible']);
    $this->assertEmpty($result['reasons']);
  }

  /** @test */
  public function it_returns_multiple_reasons_when_multiple_criteria_fail()
  {
    $candidat = new Candidat([
      'date_naissance_cand' => Carbon::now()->subYears(16),
      'serie_bac' => 'A',
      'nationalite_cand' => 'Nigériane',
    ]);

    $spec = new SpecConcours([
      'age_minimum' => 18,
      'age_maximum' => 25,
      'series_bac_acceptees' => ['C', 'D'],
      'nationalites_acceptees' => ['Camerounaise'],
    ]);

    $result = $this->service->checkAcademicEligibility($candidat, $spec);

    $this->assertFalse($result['eligible']);
    $this->assertCount(3, $result['reasons']);
  }

  // Tests for checkPreRegistrationEligibility

  /** @test */
  public function it_accepts_pre_registration_with_valid_eligibility_data()
  {
    $eligibilityData = [
      'date_naissance' => Carbon::now()->subYears(20)->format('Y-m-d'),
      'serie_bac' => 'C',
      'nationalite' => 'Camerounaise',
    ];

    $spec = new SpecConcours([
      'age_minimum' => 18,
      'age_maximum' => 25,
      'series_bac_acceptees' => ['C', 'D', 'E'],
      'nationalites_acceptees' => ['Camerounaise', 'Française'],
    ]);

    $result = $this->service->checkPreRegistrationEligibility($eligibilityData, $spec);

    $this->assertTrue($result['eligible']);
    $this->assertEmpty($result['reasons']);
  }

  /** @test */
  public function it_rejects_pre_registration_with_age_below_minimum()
  {
    $eligibilityData = [
      'date_naissance' => Carbon::now()->subYears(16)->format('Y-m-d'),
      'serie_bac' => 'C',
      'nationalite' => 'Camerounaise',
    ];

    $spec = new SpecConcours([
      'age_minimum' => 18,
      'age_maximum' => 25,
      'series_bac_acceptees' => ['C', 'D', 'E'],
      'nationalites_acceptees' => ['Camerounaise'],
    ]);

    $result = $this->service->checkPreRegistrationEligibility($eligibilityData, $spec);

    $this->assertFalse($result['eligible']);
    $this->assertCount(1, $result['reasons']);
    $this->assertStringContainsString('Âge minimum requis: 18 ans', $result['reasons'][0]);
  }

  /** @test */
  public function it_rejects_pre_registration_with_age_above_maximum()
  {
    $eligibilityData = [
      'date_naissance' => Carbon::now()->subYears(30)->format('Y-m-d'),
      'serie_bac' => 'C',
      'nationalite' => 'Camerounaise',
    ];

    $spec = new SpecConcours([
      'age_minimum' => 18,
      'age_maximum' => 25,
      'series_bac_acceptees' => ['C', 'D', 'E'],
      'nationalites_acceptees' => ['Camerounaise'],
    ]);

    $result = $this->service->checkPreRegistrationEligibility($eligibilityData, $spec);

    $this->assertFalse($result['eligible']);
    $this->assertCount(1, $result['reasons']);
    $this->assertStringContainsString('Âge maximum autorisé: 25 ans', $result['reasons'][0]);
  }

  /** @test */
  public function it_rejects_pre_registration_with_invalid_bac_series()
  {
    $eligibilityData = [
      'date_naissance' => Carbon::now()->subYears(20)->format('Y-m-d'),
      'serie_bac' => 'A',
      'nationalite' => 'Camerounaise',
    ];

    $spec = new SpecConcours([
      'age_minimum' => 18,
      'age_maximum' => 25,
      'series_bac_acceptees' => ['C', 'D', 'E'],
      'nationalites_acceptees' => ['Camerounaise'],
    ]);

    $result = $this->service->checkPreRegistrationEligibility($eligibilityData, $spec);

    $this->assertFalse($result['eligible']);
    $this->assertCount(1, $result['reasons']);
    $this->assertStringContainsString('Série de baccalauréat non acceptée', $result['reasons'][0]);
    $this->assertStringContainsString('C, D, E', $result['reasons'][0]);
  }

  /** @test */
  public function it_rejects_pre_registration_with_invalid_nationality()
  {
    $eligibilityData = [
      'date_naissance' => Carbon::now()->subYears(20)->format('Y-m-d'),
      'serie_bac' => 'C',
      'nationalite' => 'Nigériane',
    ];

    $spec = new SpecConcours([
      'age_minimum' => 18,
      'age_maximum' => 25,
      'series_bac_acceptees' => ['C', 'D', 'E'],
      'nationalites_acceptees' => ['Camerounaise', 'Française'],
    ]);

    $result = $this->service->checkPreRegistrationEligibility($eligibilityData, $spec);

    $this->assertFalse($result['eligible']);
    $this->assertCount(1, $result['reasons']);
    $this->assertStringContainsString('Nationalité non acceptée', $result['reasons'][0]);
    $this->assertStringContainsString('Camerounaise, Française', $result['reasons'][0]);
  }

  /** @test */
  public function it_accepts_pre_registration_when_no_age_restriction()
  {
    $eligibilityData = [
      'date_naissance' => Carbon::now()->subYears(50)->format('Y-m-d'),
      'serie_bac' => 'C',
      'nationalite' => 'Camerounaise',
    ];

    $spec = new SpecConcours([
      'age_minimum' => null,
      'age_maximum' => null,
      'series_bac_acceptees' => ['C', 'D', 'E'],
      'nationalites_acceptees' => ['Camerounaise'],
    ]);

    $result = $this->service->checkPreRegistrationEligibility($eligibilityData, $spec);

    $this->assertTrue($result['eligible']);
    $this->assertEmpty($result['reasons']);
  }

  /** @test */
  public function it_accepts_pre_registration_when_no_bac_series_restriction()
  {
    $eligibilityData = [
      'date_naissance' => Carbon::now()->subYears(20)->format('Y-m-d'),
      'serie_bac' => 'A',
      'nationalite' => 'Camerounaise',
    ];

    $spec = new SpecConcours([
      'age_minimum' => 18,
      'age_maximum' => 25,
      'series_bac_acceptees' => [],
      'nationalites_acceptees' => ['Camerounaise'],
    ]);

    $result = $this->service->checkPreRegistrationEligibility($eligibilityData, $spec);

    $this->assertTrue($result['eligible']);
    $this->assertEmpty($result['reasons']);
  }

  /** @test */
  public function it_accepts_pre_registration_when_no_nationality_restriction()
  {
    $eligibilityData = [
      'date_naissance' => Carbon::now()->subYears(20)->format('Y-m-d'),
      'serie_bac' => 'C',
      'nationalite' => 'Nigériane',
    ];

    $spec = new SpecConcours([
      'age_minimum' => 18,
      'age_maximum' => 25,
      'series_bac_acceptees' => ['C', 'D', 'E'],
      'nationalites_acceptees' => [],
    ]);

    $result = $this->service->checkPreRegistrationEligibility($eligibilityData, $spec);

    $this->assertTrue($result['eligible']);
    $this->assertEmpty($result['reasons']);
  }

  /** @test */
  public function it_returns_multiple_reasons_for_pre_registration_when_multiple_criteria_fail()
  {
    $eligibilityData = [
      'date_naissance' => Carbon::now()->subYears(16)->format('Y-m-d'),
      'serie_bac' => 'A',
      'nationalite' => 'Nigériane',
    ];

    $spec = new SpecConcours([
      'age_minimum' => 18,
      'age_maximum' => 25,
      'series_bac_acceptees' => ['C', 'D'],
      'nationalites_acceptees' => ['Camerounaise'],
    ]);

    $result = $this->service->checkPreRegistrationEligibility($eligibilityData, $spec);

    $this->assertFalse($result['eligible']);
    $this->assertCount(3, $result['reasons']);
  }
}
