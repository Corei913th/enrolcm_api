<?php

namespace Tests\Feature;

use App\Models\Ecole;
use App\Models\User;
use App\Enums\RegionCameroun;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EcoleApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un utilisateur et générer un token
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;

        // Configurer le storage pour les tests
        Storage::fake('public');
    }

    /** @test */
    public function it_can_create_ecole_without_files()
    {
        $data = [
            'code_ecole' => 'TEST001',
            'libelle_ecole' => 'École de Test',
            'region' => RegionCameroun::CENTRE,
            'localisation' => 'Yaoundé',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/ecoles', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'code_ecole',
                    'libelle_ecole',
                ]
            ]);

        $this->assertDatabaseHas('ecoles', [
            'code_ecole' => 'TEST001',
        ]);
    }

    /** @test */
    public function it_can_create_ecole_with_files()
    {
        $logo = UploadedFile::fake()->image('logo.png', 100, 100);
        $embleme = UploadedFile::fake()->image('embleme.png', 100, 100);

        $data = [
            'code_ecole' => 'TEST002',
            'libelle_ecole' => 'École avec Fichiers',
            'region' => RegionCameroun::CENTRE,
            'logo' => $logo,
            'embleme' => $embleme,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->post('/api/ecoles', $data);

        $response->assertStatus(201);

        $ecole = Ecole::where('code_ecole', 'TEST002')->first();
        $this->assertNotNull($ecole->logo_path);
        $this->assertNotNull($ecole->embleme_path);
    }

    /** @test */
    public function it_can_list_ecoles()
    {
        Ecole::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/ecoles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta'
            ]);
    }

    /** @test */
    public function it_can_show_ecole_by_id()
    {
        $ecole = Ecole::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson("/api/ecoles/{$ecole->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $ecole->id,
                    'code_ecole' => $ecole->code_ecole,
                ]
            ]);
    }

    /** @test */
    public function it_can_show_ecole_by_code()
    {
        $ecole = Ecole::factory()->create(['code_ecole' => 'TESTCODE']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/ecoles/code/TESTCODE');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'code_ecole' => 'TESTCODE',
                ]
            ]);
    }

    /** @test */
    public function it_can_update_ecole()
    {
        $ecole = Ecole::factory()->create();

        $data = [
            'libelle_ecole' => 'École Modifiée',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/ecoles/{$ecole->id}", $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('ecoles', [
            'id' => $ecole->id,
            'libelle_ecole' => 'École Modifiée',
        ]);
    }

    /** @test */
    public function it_can_delete_ecole()
    {
        $ecole = Ecole::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson("/api/ecoles/{$ecole->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('ecoles', [
            'id' => $ecole->id,
        ]);
    }

    /** @test */
    public function it_can_toggle_status()
    {
        $ecole = Ecole::factory()->create(['est_actif' => true]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->patchJson("/api/ecoles/{$ecole->id}/toggle-status");

        $response->assertStatus(200);

        $this->assertDatabaseHas('ecoles', [
            'id' => $ecole->id,
            'est_actif' => false,
        ]);
    }

    /** @test */
    public function it_can_list_active_ecoles()
    {
        Ecole::factory()->count(3)->create(['est_actif' => true]);
        Ecole::factory()->count(2)->create(['est_actif' => false]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/ecoles/actives');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/ecoles', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code_ecole', 'libelle_ecole', 'region']);
    }

    /** @test */
    public function it_validates_unique_code()
    {
        Ecole::factory()->create(['code_ecole' => 'DUPLICATE']);

        $data = [
            'code_ecole' => 'DUPLICATE',
            'libelle_ecole' => 'Test',
            'region' => RegionCameroun::CENTRE,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/ecoles', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code_ecole']);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->getJson('/api/ecoles');
        $response->assertStatus(401);
    }
}
