<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_validates_email(): void
    {
        $response = $this->postJson('/api/v1/auth/check-email', []);

        $response->assertStatus(422);
    }

    public function test_existing_email_returns_available(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/auth/check-email', [
            'email' => $user->email,
        ]);

        $response->assertOk();
    }
}
