<?php

namespace Tests\Feature;

use App\Models\Ecole;
use App\Models\User;
use App\Enums\RegionCameroun;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EcoleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_list_ecoles()
    {
        Ecole::factory()->count(5)->create();

        $response = $this->actingAs($this->user)
            ->getJson('/api/ecoles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta'
            ]);
    }

    /** @test */
    public function it_can_create_ecole()
    {
        $data = [
            'code_ecole' => 'TEST',
            'libelle_ecole' => 'École de Test',
            'region' => RegionCameroun::CENTRE,
            'localisation' => 'Yaoundé',
            'email_ecole' => 'test@ecole.cm',
            'telephone_ecole' => '+237222000000',
        ];

        $response = $this->actingAs($this->user)
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
            'code_ecole' => 'TEST',
            'libelle_ecole' => 'École de Test',
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/ecoles', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code_ecole', 'libelle_ecole', 'region']);
    }

    /** @test */
    public function it_can_show_ecole()
    {
        $ecole = Ecole::factory()->create();

        $response = $this->actingAs($this->user)
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
    public function it_can_update_ecole()
    {
        $ecole = Ecole::factory()->create();

        $data = [
            'libelle_ecole' => 'École Modifiée',
            'localisation' => 'Douala',
        ];

        $response = $this->actingAs($this->user)
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

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/ecoles/{$ecole->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('ecoles', [
            'id' => $ecole->id,
        ]);
    }

    /** @test */
    public function it_can_toggle_ecole_status()
    {
        $ecole = Ecole::factory()->active()->create();

        $response = $this->actingAs($this->user)
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
        Ecole::factory()->active()->count(3)->create();
        Ecole::factory()->inactive()->count(2)->create();

        $response = $this->actingAs($this->user)
            ->getJson('/api/ecoles/actives');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_can_get_ecole_by_code()
    {
        $ecole = Ecole::factory()->create([
            'code_ecole' => 'TEST123'
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/ecoles/code/TEST123');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'code_ecole' => 'TEST123',
                ]
            ]);
    }

    /** @test */
    public function it_returns_404_for_invalid_code()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/ecoles/code/INVALID');

        $response->assertStatus(404);
    }
}
