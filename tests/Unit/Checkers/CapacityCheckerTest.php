<?php

namespace Tests\Unit\Checkers;

use App\Enums\StatutCandidature;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\Filiere;
use App\Models\Session;
use App\Models\SpecConcours;
use App\Services\Domain\Candidature\Checkers\CapacityChecker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CapacityCheckerTest extends TestCase
{
    use RefreshDatabase;

    private CapacityChecker $checker;

    private Concours $concours;

    private Session $session;

    private Filiere $filiere;

    protected function setUp(): void
    {
        parent::setUp();

        $this->checker = $this->app->make(CapacityChecker::class);

        $this->filiere = Filiere::factory()->create();
        $this->session = Session::factory()->create(['est_actif' => true]);

        $this->concours = Concours::factory()->create([
            'capacite_max' => 100,
            'date_ouverture_inscription' => now()->subDays(10),
            'date_fermeture_inscription' => now()->addDays(10),
        ]);

        SpecConcours::factory()->create([
            'concours_id' => $this->concours->id,
        ]);

        $this->concours->filieres()->attach($this->filiere->id);
    }

    /** @test */
    public function get_reserved_places_compte_candidatures_valides()
    {
        // Créer 3 candidatures VALIDE
        Candidature::factory()->count(3)->create([
            'concours_id' => $this->concours->id,
            'session_id' => $this->session->id,
            'statut_candidature' => StatutCandidature::VALIDE,
        ]);

        // Créer 2 candidatures SOUMISE
        Candidature::factory()->count(2)->create([
            'concours_id' => $this->concours->id,
            'session_id' => $this->session->id,
            'statut_candidature' => StatutCandidature::SOUMISE,
        ]);

        $reserved = $this->checker->getReservedPlaces($this->concours, $this->session->id);

        $this->assertEquals(5, $reserved);
    }

    /** @test */
    public function get_reserved_places_exclut_candidatures_rejetees()
    {
        // Créer 3 candidatures VALIDE
        Candidature::factory()->count(3)->create([
            'concours_id' => $this->concours->id,
            'session_id' => $this->session->id,
            'statut_candidature' => StatutCandidature::VALIDE,
        ]);

        // Créer 2 candidatures REJETEE (ne doivent pas être comptées)
        Candidature::factory()->count(2)->create([
            'concours_id' => $this->concours->id,
            'session_id' => $this->session->id,
            'statut_candidature' => StatutCandidature::REJETEE,
        ]);

        $reserved = $this->checker->getReservedPlaces($this->concours, $this->session->id);

        $this->assertEquals(3, $reserved);
    }

    /** @test */
    public function get_reserved_places_filtre_par_filiere()
    {
        $filiere2 = Filiere::factory()->create();
        $this->concours->filieres()->attach($filiere2->id);

        // Créer 3 candidats pour filiere1
        $candidats1 = Candidat::factory()->count(3)->create(['filiere_id' => $this->filiere->id]);
        foreach ($candidats1 as $candidat) {
            Candidature::factory()->create([
                'candidat_id' => $candidat->utilisateur_id,
                'concours_id' => $this->concours->id,
                'session_id' => $this->session->id,
                'statut_candidature' => StatutCandidature::VALIDE,
            ]);
        }

        // Créer 2 candidats pour filiere2
        $candidats2 = Candidat::factory()->count(2)->create(['filiere_id' => $filiere2->id]);
        foreach ($candidats2 as $candidat) {
            Candidature::factory()->create([
                'candidat_id' => $candidat->utilisateur_id,
                'concours_id' => $this->concours->id,
                'session_id' => $this->session->id,
                'statut_candidature' => StatutCandidature::VALIDE,
            ]);
        }

        $reserved1 = $this->checker->getReservedPlaces($this->concours, $this->session->id, $this->filiere->id);
        $reserved2 = $this->checker->getReservedPlaces($this->concours, $this->session->id, $filiere2->id);

        $this->assertEquals(3, $reserved1);
        $this->assertEquals(2, $reserved2);
    }

    /** @test */
    public function can_accept_new_candidature_retourne_true_si_places_disponibles()
    {
        // Créer 50 candidatures (capacité max = 100)
        Candidature::factory()->count(50)->create([
            'concours_id' => $this->concours->id,
            'session_id' => $this->session->id,
            'statut_candidature' => StatutCandidature::VALIDE,
        ]);

        $canAccept = $this->checker->canAcceptNewCandidature($this->concours, $this->session->id);

        $this->assertTrue($canAccept);
    }

    /** @test */
    public function can_accept_new_candidature_retourne_false_si_capacite_atteinte()
    {
        // Créer 100 candidatures (capacité max = 100)
        Candidature::factory()->count(100)->create([
            'concours_id' => $this->concours->id,
            'session_id' => $this->session->id,
            'statut_candidature' => StatutCandidature::VALIDE,
        ]);

        $canAccept = $this->checker->canAcceptNewCandidature($this->concours, $this->session->id);

        $this->assertFalse($canAccept);
    }

    /** @test */
    public function can_accept_new_candidature_retourne_false_si_concours_ferme()
    {
        // Fermer le concours
        $this->concours->update([
            'date_fermeture_inscription' => now()->subDay(),
        ]);

        $canAccept = $this->checker->canAcceptNewCandidature($this->concours, $this->session->id);

        $this->assertFalse($canAccept);
    }

    /** @test */
    public function get_capacity_report_retourne_statistiques_completes()
    {
        // Créer 45 candidatures
        Candidature::factory()->count(45)->create([
            'concours_id' => $this->concours->id,
            'session_id' => $this->session->id,
            'statut_candidature' => StatutCandidature::VALIDE,
        ]);

        $report = $this->checker->getCapacityReport($this->concours, $this->session->id);

        $this->assertArrayHasKey('available', $report);
        $this->assertArrayHasKey('occupied', $report);
        $this->assertArrayHasKey('reserved', $report);
        $this->assertArrayHasKey('free', $report);
        $this->assertArrayHasKey('can_accept', $report);
        $this->assertArrayHasKey('fill_rate', $report);

        $this->assertEquals(100, $report['available']);
        $this->assertEquals(45, $report['reserved']);
        $this->assertEquals(55, $report['free']);
        $this->assertTrue($report['can_accept']);
        $this->assertEquals(45.0, $report['fill_rate']);
    }

    /** @test */
    public function get_capacity_report_calcule_taux_remplissage_correct()
    {
        // Créer 75 candidatures (75% de remplissage)
        Candidature::factory()->count(75)->create([
            'concours_id' => $this->concours->id,
            'session_id' => $this->session->id,
            'statut_candidature' => StatutCandidature::VALIDE,
        ]);

        $report = $this->checker->getCapacityReport($this->concours, $this->session->id);

        $this->assertEquals(75.0, $report['fill_rate']);
        $this->assertEquals(25, $report['free']);
    }

    /** @test */
    public function get_capacity_report_gere_capacite_pleine()
    {
        // Créer 100 candidatures (100% de remplissage)
        Candidature::factory()->count(100)->create([
            'concours_id' => $this->concours->id,
            'session_id' => $this->session->id,
            'statut_candidature' => StatutCandidature::VALIDE,
        ]);

        $report = $this->checker->getCapacityReport($this->concours, $this->session->id);

        $this->assertEquals(100.0, $report['fill_rate']);
        $this->assertEquals(0, $report['free']);
        $this->assertFalse($report['can_accept']);
    }
}
