<?php

namespace Tests\Unit\Services;

use App\Exceptions\Business\EligibilityException;
use App\Models\Candidat;
use App\Models\Concours;
use App\Models\Session;
use App\Models\SpecConcours;
use App\Services\Domain\Candidature\CandidatureService;
use App\Services\Domain\Candidature\DocumentService;
use App\Services\Domain\Candidature\EligibilityCheckerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CandidatureServiceAcademicEligibilityTest extends TestCase
{
    use RefreshDatabase;

    private CandidatureService $candidatureService;

    private EligibilityCheckerService $eligibilityChecker;

    protected function setUp(): void
    {
        parent::setUp();

        $documentService = $this->app->make(DocumentService::class);
        $this->eligibilityChecker = new EligibilityCheckerService($documentService);
        $this->candidatureService = new CandidatureService($this->eligibilityChecker, $documentService);
    }

    /** @test */
    public function it_creates_candidature_when_candidat_meets_all_academic_criteria()
    {
        // Arrange
        $spec = SpecConcours::factory()->create([
            'age_minimum' => 18,
            'age_maximum' => 30,
            'series_bac_acceptees' => ['C', 'D'],
            'nationalites_acceptees' => ['Camerounaise', 'Française'],
        ]);

        $concours = Concours::factory()->create([
            'spec_concours_id' => $spec->id,
        ]);

        $session = Session::factory()->create();

        // Link concours and session
        $concours->sessions()->attach($session->id);

        $candidat = Candidat::factory()->create([
            'date_naissance_cand' => now()->subYears(25),
            'serie_bac' => 'C',
            'nationalite_cand' => 'Camerounaise',
        ]);

        // Act
        $candidature = $this->candidatureService->createCandidature(
            $candidat,
            $concours->id,
            $session
        );

        // Assert
        $this->assertNotNull($candidature);
        $this->assertEquals($candidat->utilisateur_id, $candidature->candidat_id);
        $this->assertEquals($concours->id, $candidature->concours_id);
    }

    /** @test */
    public function it_rejects_candidature_when_candidat_is_too_young()
    {
        // Arrange
        $spec = SpecConcours::factory()->create([
            'age_minimum' => 18,
            'age_maximum' => 30,
        ]);

        $concours = Concours::factory()->create([
            'spec_concours_id' => $spec->id,
        ]);

        $session = Session::factory()->create();

        $candidat = Candidat::factory()->create([
            'date_naissance_cand' => now()->subYears(16), // Too young
        ]);

        // Act & Assert
        $this->expectException(EligibilityException::class);
        $this->expectExceptionMessage('Le candidat ne remplit pas les critères d\'éligibilité académique pour ce concours');

        $this->candidatureService->createCandidature(
            $candidat,
            $concours->id,
            $session
        );
    }

    /** @test */
    public function it_rejects_candidature_when_candidat_is_too_old()
    {
        // Arrange
        $spec = SpecConcours::factory()->create([
            'age_minimum' => 18,
            'age_maximum' => 30,
        ]);

        $concours = Concours::factory()->create([
            'spec_concours_id' => $spec->id,
        ]);

        $session = Session::factory()->create();

        $candidat = Candidat::factory()->create([
            'date_naissance_cand' => now()->subYears(35), // Too old
        ]);

        // Act & Assert
        $this->expectException(EligibilityException::class);

        $this->candidatureService->createCandidature(
            $candidat,
            $concours->id,
            $session
        );
    }

    /** @test */
    public function it_rejects_candidature_when_serie_bac_not_accepted()
    {
        // Arrange
        $spec = SpecConcours::factory()->create([
            'series_bac_acceptees' => ['C', 'D'],
        ]);

        $concours = Concours::factory()->create([
            'spec_concours_id' => $spec->id,
        ]);

        $session = Session::factory()->create();

        $candidat = Candidat::factory()->create([
            'serie_bac' => 'A', // Not accepted
        ]);

        // Act & Assert
        $this->expectException(EligibilityException::class);

        $this->candidatureService->createCandidature(
            $candidat,
            $concours->id,
            $session
        );
    }

    /** @test */
    public function it_rejects_candidature_when_nationalite_not_accepted()
    {
        // Arrange
        $spec = SpecConcours::factory()->create([
            'nationalites_acceptees' => ['Camerounaise'],
        ]);

        $concours = Concours::factory()->create([
            'spec_concours_id' => $spec->id,
        ]);

        $session = Session::factory()->create();

        $candidat = Candidat::factory()->create([
            'nationalite_cand' => 'Française', // Not accepted
        ]);

        // Act & Assert
        $this->expectException(EligibilityException::class);

        $this->candidatureService->createCandidature(
            $candidat,
            $concours->id,
            $session
        );
    }

    /** @test */
    public function it_accepts_all_ages_when_no_age_restriction()
    {
        // Arrange
        $spec = SpecConcours::factory()->create([
            'age_minimum' => null,
            'age_maximum' => null,
        ]);

        $concours = Concours::factory()->create([
            'spec_concours_id' => $spec->id,
        ]);

        $session = Session::factory()->create();
        $concours->sessions()->attach($session->id);

        $candidat = Candidat::factory()->create([
            'date_naissance_cand' => now()->subYears(50), // Any age
        ]);

        // Act
        $candidature = $this->candidatureService->createCandidature(
            $candidat,
            $concours->id,
            $session
        );

        // Assert
        $this->assertNotNull($candidature);
    }

    /** @test */
    public function it_accepts_all_series_when_no_series_restriction()
    {
        // Arrange
        $spec = SpecConcours::factory()->create([
            'series_bac_acceptees' => null,
        ]);

        $concours = Concours::factory()->create([
            'spec_concours_id' => $spec->id,
        ]);

        $session = Session::factory()->create();
        $concours->sessions()->attach($session->id);

        $candidat = Candidat::factory()->create([
            'serie_bac' => 'Z', // Any series
        ]);

        // Act
        $candidature = $this->candidatureService->createCandidature(
            $candidat,
            $concours->id,
            $session
        );

        // Assert
        $this->assertNotNull($candidature);
    }

    /** @test */
    public function it_accepts_all_nationalities_when_no_nationality_restriction()
    {
        // Arrange
        $spec = SpecConcours::factory()->create([
            'nationalites_acceptees' => null,
        ]);

        $concours = Concours::factory()->create([
            'spec_concours_id' => $spec->id,
        ]);

        $session = Session::factory()->create();
        $concours->sessions()->attach($session->id);

        $candidat = Candidat::factory()->create([
            'nationalite_cand' => 'Martienne', // Any nationality
        ]);

        // Act
        $candidature = $this->candidatureService->createCandidature(
            $candidat,
            $concours->id,
            $session
        );

        // Assert
        $this->assertNotNull($candidature);
    }

    /** @test */
    public function it_creates_candidature_when_concours_has_no_spec()
    {
        // Arrange
        $concours = Concours::factory()->create([
            'spec_concours_id' => null,
        ]);

        $session = Session::factory()->create();
        $concours->sessions()->attach($session->id);

        $candidat = Candidat::factory()->create();

        // Act
        $candidature = $this->candidatureService->createCandidature(
            $candidat,
            $concours->id,
            $session
        );

        // Assert
        $this->assertNotNull($candidature);
    }

    /** @test */
    public function it_provides_detailed_reasons_when_multiple_criteria_fail()
    {
        // Arrange
        $spec = SpecConcours::factory()->create([
            'age_minimum' => 18,
            'age_maximum' => 30,
            'series_bac_acceptees' => ['C', 'D'],
            'nationalites_acceptees' => ['Camerounaise'],
        ]);

        $concours = Concours::factory()->create([
            'spec_concours_id' => $spec->id,
        ]);

        $session = Session::factory()->create();

        $candidat = Candidat::factory()->create([
            'date_naissance_cand' => now()->subYears(16), // Too young
            'serie_bac' => 'A', // Not accepted
            'nationalite_cand' => 'Française', // Not accepted
        ]);

        // Act & Assert
        try {
            $this->candidatureService->createCandidature(
                $candidat,
                $concours->id,
                $session
            );
            $this->fail('Expected EligibilityException was not thrown');
        } catch (EligibilityException $e) {
            $reasons = $e->getReasons();
            $this->assertCount(3, $reasons);
            $this->assertStringContainsString('Âge minimum', $reasons[0]);
            $this->assertStringContainsString('Série de baccalauréat', $reasons[1]);
            $this->assertStringContainsString('Nationalité', $reasons[2]);
        }
    }
}
