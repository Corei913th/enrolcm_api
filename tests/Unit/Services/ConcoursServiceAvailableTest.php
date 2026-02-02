<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Concours;
use App\Models\Ecole;
use App\Models\SpecConcours;
use App\Models\Session;
use App\Models\Filiere;
use App\Models\Centre;
use App\Models\ConcoursPaiement;
use App\Models\DocumentRequis;
use App\Services\Domain\Concours\ConcoursService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConcoursServiceAvailableTest extends TestCase
{
  use RefreshDatabase;

  private ConcoursService $service;

  protected function setUp(): void
  {
    parent::setUp();
    $this->service = app(ConcoursService::class);
  }

  /** @test */
  public function it_returns_only_ready_concours()
  {
    // Créer un concours COMPLET et PRÊT
    $readyConcours = $this->createReadyConcours();

    // Créer un concours INCOMPLET (sans filière)
    $incompleteConcours = Concours::factory()->create([
      'est_actif' => true,
      'date_limite_depot' => now()->addMonth()
    ]);

    // Appeler getAvailableConcours
    $result = $this->service->getAvailableConcours(10);

    // Vérifier que seul le concours prêt est retourné
    $this->assertEquals(1, $result->total());
    $this->assertEquals($readyConcours->id, $result->items()[0]->id);
  }

  /** @test */
  public function it_excludes_concours_without_filieres()
  {
    $concours = Concours::factory()->create([
      'spec_concours_id' => SpecConcours::factory()->create()->id,
      'est_actif' => true,
      'date_limite_depot' => now()->addMonth()
    ]);

    $session = Session::factory()->create();
    $concours->sessions()->attach($session->id);

    $centre = Centre::factory()->create();
    $concours->centers()->attach($centre->id, [
      'id' => \Illuminate\Support\Str::uuid(),
      'est_actif' => true
    ]);

    ConcoursPaiement::factory()->create([
      'concours_id' => $concours->id,
      'est_actif' => true
    ]);

    DocumentRequis::factory()->create([
      'concours_id' => $concours->id,
      'est_actif' => true
    ]);

    // Pas de filière attachée !

    $result = $this->service->getAvailableConcours(10);

    $this->assertEquals(0, $result->total());
  }

  /** @test */
  public function it_excludes_concours_without_payment_config()
  {
    $concours = Concours::factory()->create([
      'spec_concours_id' => SpecConcours::factory()->create()->id,
      'est_actif' => true,
      'date_limite_depot' => now()->addMonth()
    ]);

    $session = Session::factory()->create();
    $filiere = Filiere::factory()->create();
    $centre = Centre::factory()->create();

    $concours->sessions()->attach($session->id);
    $concours->filieres()->attach($filiere->id, [
      'nombre_places' => 50,
      'session_id' => $session->id
    ]);
    $concours->centers()->attach($centre->id, [
      'id' => \Illuminate\Support\Str::uuid(),
      'est_actif' => true
    ]);

    DocumentRequis::factory()->create([
      'concours_id' => $concours->id,
      'est_actif' => true
    ]);

    // Pas de config paiement !

    $result = $this->service->getAvailableConcours(10);

    $this->assertEquals(0, $result->total());
  }

  /** @test */
  public function it_excludes_inactive_concours()
  {
    $concours = $this->createReadyConcours();
    $concours->update(['est_actif' => false]);

    $result = $this->service->getAvailableConcours(10);

    $this->assertEquals(0, $result->total());
  }

  /** @test */
  public function it_excludes_concours_with_past_deadline()
  {
    $concours = $this->createReadyConcours();
    $concours->update(['date_limite_depot' => now()->subDay()]);

    $result = $this->service->getAvailableConcours(10);

    $this->assertEquals(0, $result->total());
  }

  /** @test */
  public function it_returns_multiple_ready_concours()
  {
    $concours1 = $this->createReadyConcours();
    $concours2 = $this->createReadyConcours();
    $concours3 = $this->createReadyConcours();

    $result = $this->service->getAvailableConcours(10);

    $this->assertEquals(3, $result->total());
  }

  /**
   * Helper pour créer un concours complet et prêt
   */
  private function createReadyConcours(): Concours
  {
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

    $concours->sessions()->attach($session->id);
    $concours->filieres()->attach($filiere->id, [
      'nombre_places' => 50,
      'session_id' => $session->id
    ]);
    $concours->centers()->attach($centre->id, [
      'id' => \Illuminate\Support\Str::uuid(),
      'est_actif' => true
    ]);

    ConcoursPaiement::factory()->create([
      'concours_id' => $concours->id,
      'est_actif' => true
    ]);

    DocumentRequis::factory()->create([
      'concours_id' => $concours->id,
      'est_actif' => true
    ]);

    return $concours->fresh();
  }
}
