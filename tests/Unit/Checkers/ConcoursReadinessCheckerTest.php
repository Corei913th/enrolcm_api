<?php

namespace Tests\Unit\Checkers;

use Tests\TestCase;
use App\Models\Concours;
use App\Models\Ecole;
use App\Models\SpecConcours;
use App\Models\Session;
use App\Models\Filiere;
use App\Models\Centre;
use App\Models\ConcoursPaiement;
use App\Models\DocumentRequis;
use App\Services\Domain\Concours\Checkers\ConcoursReadinessChecker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConcoursReadinessCheckerTest extends TestCase
{
  use RefreshDatabase;

  private ConcoursReadinessChecker $checker;

  protected function setUp(): void
  {
    parent::setUp();
    $this->checker = new ConcoursReadinessChecker();
  }

  /** @test */
  public function it_returns_not_ready_when_concours_is_inactive()
  {
    $concours = Concours::factory()->create(['est_actif' => false]);

    $result = $this->checker->check($concours);

    $this->assertFalse($result['ready']);
    $this->assertContains('Le concours n\'est pas actif', $result['reasons']);
  }

  /** @test */
  public function it_returns_not_ready_when_date_limite_is_past()
  {
    $concours = Concours::factory()->create([
      'est_actif' => true,
      'date_limite_depot' => now()->subDay()
    ]);

    $result = $this->checker->check($concours);

    $this->assertFalse($result['ready']);
    $this->assertContains('La date limite de dépôt est dépassée', $result['reasons']);
  }

  /** @test */
  public function it_returns_not_ready_when_spec_concours_is_missing()
  {
    $concours = Concours::factory()->create([
      'est_actif' => true,
      'spec_concours_id' => null
    ]);

    $result = $this->checker->check($concours);

    $this->assertFalse($result['ready']);
    $this->assertContains('Aucun critère d\'éligibilité défini', $result['reasons']);
  }

  /** @test */
  public function it_returns_not_ready_when_no_filieres_attached()
  {
    $concours = Concours::factory()->create(['est_actif' => true]);

    $result = $this->checker->check($concours);

    $this->assertFalse($result['ready']);
    $this->assertContains('Aucune filière disponible', $result['reasons']);
  }

  /** @test */
  public function it_returns_ready_when_all_conditions_are_met()
  {
    // Créer un concours complet
    $ecole = Ecole::factory()->create();
    $spec = SpecConcours::factory()->create();
    $session = Session::factory()->create();
    $filiere = Filiere::factory()->create();
    $centre = Centre::factory()->create();

    $concours = Concours::factory()->create([
      'ecole_id' => $ecole->id,
      'spec_concours_id' => $spec->id,
      'est_actif' => true,
      'date_limite_depot' => now()->addMonth()
    ]);

    // Attacher les relations
    $concours->sessions()->attach($session->id);
    $concours->filieres()->attach($filiere->id, [
      'nombre_places' => 50,
      'session_id' => $session->id
    ]);
    $concours->centers()->attach($centre->id, [
      'id' => \Illuminate\Support\Str::uuid(),
      'est_actif' => true
    ]);

    // Créer config paiement
    ConcoursPaiement::factory()->create([
      'concours_id' => $concours->id,
      'est_actif' => true
    ]);

    // Créer document requis
    DocumentRequis::factory()->create([
      'concours_id' => $concours->id,
      'est_actif' => true
    ]);

    $result = $this->checker->check($concours);

    $this->assertTrue($result['ready']);
    $this->assertEmpty($result['reasons']);
  }

  /** @test */
  public function it_throws_exception_when_ensure_ready_fails()
  {
    $concours = Concours::factory()->create(['est_actif' => false]);

    $this->expectException(\DomainException::class);
    $this->expectExceptionMessage('Ce concours n\'est pas disponible pour l\'inscription');

    $this->checker->ensureReady($concours);
  }
}
