<?php

namespace Tests\Feature;

use App\DTOs\Concours\CreateConcoursDTO;
use App\DTOs\Concours\UpdateConcoursDTO;
use App\Enums\StatutSession;
use App\Models\Concours;
use App\Models\Session;
use App\Models\SpecConcours;
use App\Services\Concours\ConcoursService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class ConcoursWorkflowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private ConcoursService $concoursService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->concoursService = app(ConcoursService::class);
    }

    /** @test */
    public function it_creates_concours_template_minimal()
    {
        // Given: données minimales
        $data = [
            'libelle_concours' => 'Concours Template Test',
            'description' => 'Version template pour tests'
        ];

        // When: création du concours
        $concours = $this->concoursService->create(CreateConcoursDTO::fromRequest($data));

        // Then: concours créé sans specs/session
        $this->assertInstanceOf(Concours::class, $concours);
        $this->assertEquals('Concours Template Test', $concours->libelle_concours);
        $this->assertEquals('Version template pour tests', $concours->description);
        $this->assertNull($concours->spec_concours_id);
        $this->assertNull($concours->date_examen);
        $this->assertNull($concours->date_limite_depot);
        $this->assertEquals(0, $concours->nbre_max_places);
        $this->assertEquals(0, $concours->sessions()->count());
    }

    /** @test */
    public function it_creates_concours_with_specs_only()
    {
        // Given: spec_concours existante
        $spec = SpecConcours::create([
            'nom_spec' => 'Test spec',
            'desc_infos_concours' => 'Test spec description',
            'frais_inscription' => 30000,
            'carte_nationale_identite' => true
        ]);

        $data = [
            'libelle_concours' => 'Concours Avec Specs',
            'spec_concours_id' => $spec->id,
            'description' => 'Avec spécifications'
        ];

        // When: création
        $concours = $this->concoursService->create(CreateConcoursDTO::fromRequest($data));

        // Then: specs liées, pas de session
        $this->assertEquals($spec->id, $concours->spec_concours_id);
        $this->assertEquals(0, $concours->sessions()->count());
    }

    /** @test */
    public function it_creates_concours_with_session_and_dates()
    {
        // Given: session active
        $session = Session::create([
            'libelle_session' => '2025-2026',
            'est_actif' => true,
            'statut_session' => StatutSession::OUVERT
        ]);

        $data = [
            'libelle_concours' => 'Concours Complet',
            'session_id' => $session->id,
            'date_debut' => '2025-06-15',
            'date_limite_depot' => '2025-05-15', // AVANT date examen (cohérent)
            'nombre_places' => 300,
            'spec_concours_id' => null
        ];

        // When: création
        $concours = $this->concoursService->create(CreateConcoursDTO::fromRequest($data));

        // Then: concours créé avec dates cohérentes
        $this->assertEquals('2025-06-15', $concours->date_examen->format('Y-m-d'));
        $this->assertEquals('2025-05-15', $concours->date_limite_depot->format('Y-m-d'));

        // Then: session liée avec dates et places
        $this->assertEquals(1, $concours->sessions()->count());
        $this->assertEquals($session->id, $concours->sessions()->first()->id);
        $this->assertEquals('2025-06-15', $concours->date_examen->format('Y-m-d'));
        $this->assertEquals('2025-05-15', $concours->date_limite_depot->format('Y-m-d'));
        $this->assertEquals(300, $concours->nbre_max_places);
    }

    /** @test */
    public function it_creates_concours_complet_specs_session_dates()
    {
        // Given: spec et session
        $spec = SpecConcours::create([
            'nom_spec' => 'Spec complète ' . time(),
            'desc_infos_concours' => 'Spec complète description',
            'frais_inscription' => 25000,
            'carte_nationale_identite' => true
        ]);
        $session = Session::create([
            'libelle_session' => '2026-2027',
            'est_actif' => true,
            'statut_session' => StatutSession::OUVERT
        ]);

        $data = [
            'libelle_concours' => 'ENAM Session 2025 Complet ' . time(),
            'spec_concours_id' => $spec->id,
            'session_id' => $session->id,
            'date_debut' => '2025-06-15',
            'date_limite_depot' => '2025-05-15', // AVANT date examen (cohérent)
            'nombre_places' => 500,
            'description' => 'Tous les champs remplis'
        ];

        // When: création complète
        $concours = $this->concoursService->create(CreateConcoursDTO::fromRequest($data));

        // Then: concours créé avec toutes les données
        $this->assertEquals($spec->id, $concours->spec_concours_id);
        $this->assertEquals(1, $concours->sessions()->count());
        $this->assertEquals('2025-06-15', $concours->date_examen->format('Y-m-d'));
        $this->assertEquals('2025-05-15', $concours->date_limite_depot->format('Y-m-d'));
        $this->assertEquals(500, $concours->nbre_max_places);
        $this->assertEquals('Tous les champs remplis', $concours->description);

        // Then: tout est configuré
        $this->assertEquals($spec->id, $concours->spec_concours_id);
        $this->assertEquals(1, $concours->sessions()->count());
        $this->assertEquals('2025-06-15', $concours->date_examen->format('Y-m-d'));
        $this->assertEquals(500, $concours->nbre_max_places);
        $this->assertEquals('Tous les champs remplis', $concours->description);
    }

    /** @test */
    public function it_updates_concours_to_add_session()
    {
        // Given: concours template
        $concours = Concours::create([
            'libelle_concours' => 'Template à mettre à jour'
        ]);

        $session = Session::create([
            'libelle_session' => '2025-2026',
            'est_actif' => true,
            'statut_session' => StatutSession::OUVERT
        ]);

        $updateData = [
            'session_id' => $session->id,
            'date_debut' => '2025-06-15',
            'date_limite_depot' => '2025-03-31',
            'nombre_places' => 400
        ];

        $dto = UpdateConcoursDTO::fromRequest($updateData);

        // When: mise à jour pour ajouter session
        $updated = $this->concoursService->update($concours->id, $dto);

        // Then: session attachée avec config
        $this->assertEquals(1, $updated->sessions()->count());
        $this->assertEquals($session->id, $updated->sessions()->first()->id);
        $this->assertEquals('2025-06-15', $updated->date_examen->format('Y-m-d'));
        $this->assertEquals(400, $updated->nbre_max_places);
    }

    /** @test */
    public function it_updates_concours_to_change_session()
    {
        // Given: concours avec session existante
        $session1 = Session::create([
            'libelle_session' => '2024-2025',
            'est_actif' => true,
            'statut_session' => StatutSession::OUVERT
        ]);

        $concours = $this->concoursService->create(
            CreateConcoursDTO::fromRequest([
                'libelle_concours' => 'Concours Changement Session',
                'session_id' => $session1->id,
                'date_debut' => '2024-06-15',
                'nombre_places' => 200
            ])
        );

        $session2 = Session::create([
            'libelle_session' => '2025-2026',
            'est_actif' => true,
            'statut_session' => StatutSession::OUVERT
        ]);

        // When: changement de session
        $updated = $this->concoursService->update(
            $concours->id,
            UpdateConcoursDTO::fromRequest([
                'session_id' => $session2->id,
                'date_debut' => '2025-06-15',
                'nombre_places' => 300
            ])
        );

        // Then: nouvelle session, anciennes données remplacées
        $this->assertEquals(1, $updated->sessions()->count());
        $this->assertEquals($session2->id, $updated->sessions()->first()->id);
        $this->assertEquals('2025-06-15', $updated->date_examen->format('Y-m-d'));
        $this->assertEquals(300, $updated->nbre_max_places);
    }

    /** @test */
    public function it_validates_dates_when_session_provided()
    {
        $session = Session::create([
            'libelle_session' => '2025-2026',
            'est_actif' => true,
            'statut_session' => StatutSession::OUVERT
        ]);

        // Test: date limite APRÈS date examen (invalide)
        $this->expectException(\Exception::class);

        $this->concoursService->create(
            CreateConcoursDTO::fromRequest([
                'libelle_concours' => 'Test Dates Invalides',
                'session_id' => $session->id,
                'date_debut' => '2025-06-15',
                'date_limite_depot' => '2025-06-20', // APRÈS date examen = invalide
                'spec_concours_id' => null
            ])
        );
    }

    /** @test */
    public function it_validates_unique_libelle_per_session()
    {
        $session = Session::create([
            'libelle_session' => '2025-2026',
            'est_actif' => true,
            'statut_session' => StatutSession::OUVERT
        ]);

        // Créer premier concours
        $this->concoursService->create(
            CreateConcoursDTO::fromRequest([
                'libelle_concours' => 'Concours Unique',
                'session_id' => $session->id,
                'spec_concours_id' => null
            ])
        );

        // Test: même libellé pour même session = erreur
        $this->expectException(\Exception::class);

        $this->concoursService->create(
            CreateConcoursDTO::fromRequest([
                'libelle_concours' => 'Concours Unique', // Même nom
                'session_id' => $session->id,              // Même session
                'spec_concours_id' => null
            ])
        );
    }

    /** @test */
    public function it_allows_same_libelle_for_different_sessions()
    {
        $session1 = Session::create([
            'libelle_session' => '2024-2025',
            'est_actif' => true,
            'statut_session' => StatutSession::OUVERT
        ]);

        $session2 = Session::create([
            'libelle_session' => '2025-2026',
            'est_actif' => true,
            'statut_session' => StatutSession::OUVERT
        ]);

        // Créer concours pour session 1
        $concours1 = $this->concoursService->create(
            CreateConcoursDTO::fromRequest([
                'libelle_concours' => 'Même Nom Différentes Sessions',
                'session_id' => $session1->id,
                'spec_concours_id' => null
            ])
        );

        // Même nom pour session 2 = OK
        $concours2 = $this->concoursService->create(
            CreateConcoursDTO::fromRequest([
                'libelle_concours' => 'Même Nom Différentes Sessions',
                'session_id' => $session2->id,
                'spec_concours_id' => null
            ])
        );

        $this->assertNotEquals($concours1->id, $concours2->id);
        $this->assertEquals($concours1->libelle_concours, $concours2->libelle_concours);
        $this->assertNotEquals(
            $concours1->sessions()->first()->id,
            $concours2->sessions()->first()->id
        );
    }

    /** @test */
    public function it_prevents_creation_with_inactive_session()
    {
        $inactiveSession = Session::create([
            'libelle_session' => 'Session Inactive',
            'est_actif' => false, // INACTIVE
            'statut_session' => StatutSession::FERME
        ]);

        $this->expectException(\Exception::class);

        $this->concoursService->create(
            CreateConcoursDTO::fromRequest([
                'libelle_concours' => 'Test Session Inactive',
                'session_id' => $inactiveSession->id,
                'spec_concours_id' => null
            ])
        );
    }
}
