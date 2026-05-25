<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidat_can_login(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/api/v1/auth/candidat/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['user', 'access_token', 'refresh_token', 'token_type', 'expires_in'],
            ]);
    }

    public function test_candidat_cannot_login_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/api/v1/auth/candidat/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/api/v1/auth/logout');

        $response->assertOk()
            ->assertJson(['success' => true]);
    }
}
