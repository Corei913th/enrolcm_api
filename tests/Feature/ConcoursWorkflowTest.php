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

    /** @test */
    public function it_can_attach_template_concours_to_session()
    {
        // Given: concours template et session active
        $template = Concours::create([
            'libelle_concours' => 'Template à attacher'
        ]);

        $session = Session::create([
            'libelle_session' => 'Session d\'attachement',
            'est_actif' => true,
            'statut_session' => StatutSession::OUVERT
        ]);

        // When: attacher avec configuration
        $config = [
            'date_examen' => '2025-06-15',
            'date_limite_depot' => '2025-05-15',
            'nbre_max_places' => 300
        ];

        $attached = $this->concoursService->attachToSession($template->id, $session->id, $config);

        // Then: concours attaché avec config
        $this->assertEquals(1, $attached->sessions()->count());
        $this->assertEquals($session->id, $attached->sessions()->first()->id);
        $this->assertEquals('2025-06-15', $attached->date_examen->format('Y-m-d'));
        $this->assertEquals('2025-05-15', $attached->date_limite_depot->format('Y-m-d'));
        $this->assertEquals(300, $attached->nbre_max_places);

        // Vérifier que l'état a été créé
        $this->assertDatabaseHas('etat_concours_session', [
            'concours_session_concours_id' => $template->id,
            'concours_session_session_id' => $session->id,
        ]);
    }

    /** @test */
    public function it_can_attach_template_concours_to_session_minimal()
    {
        // Given: concours template et session (sans config supplémentaire)
        $template = Concours::create([
            'libelle_concours' => 'Template minimal'
        ]);

        $session = Session::create([
            'libelle_session' => 'Session minimale',
            'est_actif' => true,
            'statut_session' => StatutSession::OUVERT
        ]);

        // When: attacher sans configuration
        $attached = $this->concoursService->attachToSession($template->id, $session->id);

        // Then: concours attaché, config par défaut préservée
        $this->assertEquals(1, $attached->sessions()->count());
        $this->assertEquals($session->id, $attached->sessions()->first()->id);
        $this->assertNull($attached->date_examen); // Pas modifié
        $this->assertEquals(0, $attached->nbre_max_places); // Défaut

        // État créé
        $this->assertDatabaseHas('etat_concours_session', [
            'concours_session_concours_id' => $template->id,
            'concours_session_session_id' => $session->id,
        ]);
    }

    /** @test */
    public function it_prevents_attaching_already_attached_concours()
    {
        // Given: concours déjà attaché à une session
        $session1 = Session::create([
            'libelle_session' => 'Session 1',
            'est_actif' => true,
            'statut_session' => StatutSession::OUVERT
        ]);

        $concours = $this->concoursService->create(
            CreateConcoursDTO::fromRequest([
                'libelle_concours' => 'Concours déjà attaché',
                'session_id' => $session1->id,
                'spec_concours_id' => null
            ])
        );

        $session2 = Session::create([
            'libelle_session' => 'Session 2',
            'est_actif' => true,
            'statut_session' => StatutSession::OUVERT
        ]);

        // When/Then: essayer d'attacher à une autre session = erreur
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Ce concours est déjà attaché à une session');

        $this->concoursService->attachToSession($concours->id, $session2->id);
    }

    /** @test */
    public function it_prevents_attaching_to_inactive_session()
    {
        // Given: concours template et session inactive
        $template = Concours::create([
            'libelle_concours' => 'Template test'
        ]);

        $inactiveSession = Session::create([
            'libelle_session' => 'Session inactive',
            'est_actif' => false,
            'statut_session' => StatutSession::FERME
        ]);

        // When/Then: attachement à session inactive = erreur
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Impossible d\'attacher à une session inactive');

        $this->concoursService->attachToSession($template->id, $inactiveSession->id);
    }

    /** @test */
    public function it_validates_date_examen_matches_session_period_annuelle()
    {
        // Given: session annuelle et concours template
        $session = Session::create([
            'libelle_session' => '2026-2027',
            'est_actif' => true,
            'statut_session' => StatutSession::OUVERT
        ]);

        $template = Concours::create([
            'libelle_concours' => 'Test Cohérence Annuelle'
        ]);

        // When/Then: date dans la période = OK
        $result = $this->concoursService->attachToSession($template->id, $session->id, [
            'date_examen' => '2026-06-15', // Dans 2026-2027
            'nbre_max_places' => 100
        ]);

        $this->assertEquals(1, $result->sessions()->count());
        $this->assertEquals('2026-06-15', $result->date_examen->format('Y-m-d'));
    }

    /** @test */
    public function it_validates_date_examen_matches_session_period_mensuelle()
    {
        // Given: session mensuelle et concours template
        $session = Session::create([
            'libelle_session' => 'MAI 2026',
            'est_actif' => true,
            'statut_session' => StatutSession::OUVERT
        ]);

        $template = Concours::create([
            'libelle_concours' => 'Test Cohérence Mensuelle'
        ]);

        // When/Then: date dans la période déduite = OK
        $result = $this->concoursService->attachToSession($template->id, $session->id, [
            'date_examen' => '2026-05-15', // En mai 2026
            'nbre_max_places' => 100
        ]);

        $this->assertEquals(1, $result->sessions()->count());
    }

    /** @test */
    public function it_rejects_date_examen_outside_session_period()
    {
        // Given: session 2026-2027 et date en 2028
        $session = Session::create([
            'libelle_session' => '2026-2027',
            'est_actif' => true,
            'statut_session' => StatutSession::OUVERT
        ]);

        $template = Concours::create([
            'libelle_concours' => 'Test Hors Période'
        ]);

        // When/Then: date hors période = erreur
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('ne correspond pas à la période de la session');

        $this->concoursService->attachToSession($template->id, $session->id, [
            'date_examen' => '2028-06-15', // Hors 2026-2027
            'nbre_max_places' => 100
        ]);
    }

    /** @test */
    public function it_rejects_session_year_before_2025()
    {
        // Given: session avec année 2024 (invalide)
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('ne peut pas être inférieure à 2025');

        // Cette validation est faite lors du parsing, donc on la teste indirectement
        $service = new \ReflectionClass(\App\Services\Concours\ConcoursService::class);
        $instance = $service->newInstanceWithoutConstructor();

        // Appel direct de la méthode privée pour test
        $method = $service->getMethod('parseSessionPeriod');
        $method->setAccessible(true);
        $method->invoke($instance, '2024-2025');
    }
}
