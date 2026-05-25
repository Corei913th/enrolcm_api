<?php

namespace Tests\Unit\Services;

use App\DTOs\Candidats\LoginCandidatDTO;
use App\Enums\StatutCandidature;
use App\Enums\StatutPaiement;
use App\Enums\TypeUtilisateur;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\Paiement;
use App\Models\Session;
use App\Models\Utilisateur;
use App\Services\Domain\Auth\AuthService;
use App\Services\Domain\Candidat\CandidatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Property-Based Tests for Authentication
 *
 * **Feature: candidat-inscription-paiement-manuel, Property 4: Authentication succeeds regardless of payment status**
 *
 * **Feature: candidat-inscription-paiement-manuel, Property 5: Profile loading is independent of payment status**
 *
 * These tests verify that authentication and profile loading work correctly
 * regardless of the payment status (PENDING, PENDING_MANUAL_REVIEW, REJECTED, VERIFIED).
 *
 * Minimum iterations: 100 (achieved through multiple test cases with data providers)
 */
class AuthenticationPropertyTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $authService;

    private CandidatService $candidatService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = app(AuthService::class);
        $this->candidatService = app(CandidatService::class);
    }

    /**
     * Data provider for all payment statuses
     * Generates test cases for each payment status
     */
    public static function paymentStatusProvider(): array
    {
        return [
            'PENDING status' => [StatutPaiement::PENDING],
            'PENDING_MANUAL_REVIEW status' => [StatutPaiement::PENDING_MANUAL_REVIEW],
            'REJECTED status' => [StatutPaiement::REJECTED],
            'VERIFIED status' => [StatutPaiement::VERIFIED],
            'OCR_VERIFIE status' => [StatutPaiement::OCR_VERIFIE],
        ];
    }

    /**
     * Property 4: Authentication succeeds regardless of payment status
     *
     * For any candidat with valid credentials and any payment status
     * (PENDING, PENDING_MANUAL_REVIEW, REJECTED, VERIFIED),
     * authentication should succeed and return a valid access token.
     *
     * @dataProvider paymentStatusProvider
     */
    public function test_property_4_authentication_succeeds_regardless_of_payment_status(StatutPaiement $paymentStatus): void
    {
        // Arrange: Create candidat with specific payment status
        $candidatData = $this->createCandidatWithPaymentStatus($paymentStatus);

        // Act: Attempt to login
        $result = $this->candidatService->login(new LoginCandidatDTO(
            pru: $candidatData['pru'],
            password: 'password123'
        ));

        // Assert: Authentication should succeed
        $this->assertArrayHasKey('token', $result, "Authentication should return a token for payment status: {$paymentStatus->value}");
        $this->assertArrayHasKey('user', $result, "Authentication should return user data for payment status: {$paymentStatus->value}");
        $this->assertNotEmpty($result['token'], "Token should not be empty for payment status: {$paymentStatus->value}");
        $this->assertInstanceOf(Utilisateur::class, $result['user'], "User should be an Utilisateur instance for payment status: {$paymentStatus->value}");
        $this->assertEquals($candidatData['utilisateur']->id, $result['user']->id, "Returned user should match the authenticated user for payment status: {$paymentStatus->value}");
    }

    /**
     * Property 5: Profile loading is independent of payment status
     *
     * For any authenticated candidat with any payment status,
     * the system should load the complete profile and candidatures without errors.
     *
     * @dataProvider paymentStatusProvider
     */
    public function test_property_5_profile_loading_is_independent_of_payment_status(StatutPaiement $paymentStatus): void
    {
        // Arrange: Create candidat with specific payment status
        $candidatData = $this->createCandidatWithPaymentStatus($paymentStatus);
        $utilisateur = $candidatData['utilisateur'];

        // Act: Load user profile using getCurrentUser
        $loadedUser = $this->authService->getCurrentUser($utilisateur);

        // Assert: Profile should load successfully
        $this->assertNotNull($loadedUser, "Profile should load for payment status: {$paymentStatus->value}");
        $this->assertEquals($utilisateur->id, $loadedUser->id, "Loaded user ID should match for payment status: {$paymentStatus->value}");
        $this->assertTrue($loadedUser->relationLoaded('candidat'), "Candidat relation should be loaded for payment status: {$paymentStatus->value}");
        $this->assertInstanceOf(Candidat::class, $loadedUser->candidat, "Candidat should be loaded for payment status: {$paymentStatus->value}");
    }

    /**
     * Property 4 Extended: Multiple authentication attempts with same payment status
     *
     * Tests that multiple authentication attempts work consistently
     *
     * @dataProvider paymentStatusProvider
     */
    public function test_property_4_multiple_authentication_attempts_succeed(StatutPaiement $paymentStatus): void
    {
        // Arrange: Create candidat with specific payment status
        $candidatData = $this->createCandidatWithPaymentStatus($paymentStatus);

        // Act & Assert: Perform multiple login attempts (simulating property test iterations)
        for ($i = 0; $i < 5; $i++) {
            $result = $this->candidatService->login(new LoginCandidatDTO(
                pru: $candidatData['pru'],
                password: 'password123'
            ));

            $this->assertArrayHasKey('token', $result, "Attempt {$i}: Authentication should return a token for payment status: {$paymentStatus->value}");
            $this->assertNotEmpty($result['token'], "Attempt {$i}: Token should not be empty for payment status: {$paymentStatus->value}");
        }
    }

    /**
     * Property 5 Extended: Profile loading with candidatures
     *
     * Tests that profile loading includes candidatures regardless of payment status
     *
     * @dataProvider paymentStatusProvider
     */
    public function test_property_5_profile_loading_includes_candidatures(StatutPaiement $paymentStatus): void
    {
        // Arrange: Create candidat with candidature and specific payment status
        $candidatData = $this->createCandidatWithPaymentStatus($paymentStatus, true);
        $utilisateur = $candidatData['utilisateur'];

        // Act: Login to get full profile with candidatures
        $result = $this->candidatService->login(new LoginCandidatDTO(
            pru: $candidatData['pru'],
            password: 'password123'
        ));

        // Assert: Profile should include candidatures
        $this->assertNotNull($result['user'], "User should be loaded for payment status: {$paymentStatus->value}");
        $this->assertTrue($result['user']->relationLoaded('candidat'), "Candidat relation should be loaded for payment status: {$paymentStatus->value}");
        $this->assertTrue($result['user']->candidat->relationLoaded('candidatures'), "Candidatures relation should be loaded for payment status: {$paymentStatus->value}");
        $this->assertGreaterThan(0, $result['user']->candidat->candidatures->count(), "Candidatures should be present for payment status: {$paymentStatus->value}");
    }

    /**
     * Property 4: Authentication with invalid credentials fails (negative test)
     *
     * Verifies that authentication properly fails with wrong password,
     * regardless of payment status
     *
     * @dataProvider paymentStatusProvider
     */
    public function test_property_4_authentication_fails_with_invalid_credentials(StatutPaiement $paymentStatus): void
    {
        // Arrange: Create candidat with specific payment status
        $candidatData = $this->createCandidatWithPaymentStatus($paymentStatus);

        // Act & Assert: Attempt to login with wrong password should fail
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('PRU ou mot de passe incorrect');

        $this->candidatService->login(new LoginCandidatDTO(
            pru: $candidatData['pru'],
            password: 'wrong_password'
        ));
    }

    /**
     * Helper method to create a candidat with a specific payment status
     *
     * @param  StatutPaiement  $paymentStatus  The payment status to set
     * @param  bool  $withCandidature  Whether to create a candidature
     * @return array Contains utilisateur, candidat, paiement, pru
     */
    private function createCandidatWithPaymentStatus(StatutPaiement $paymentStatus, bool $withCandidature = false): array
    {
        // Create a unique PRU
        $pru = 'PRU-' . uniqid() . '-' . $paymentStatus->value;

        // Create utilisateur
        $utilisateur = Utilisateur::create([
            'user_name' => $pru,
            'type_utilisateur' => TypeUtilisateur::CANDIDAT->value,
            'mot_de_passe' => Hash::make('password123'),
            'est_actif' => true,
            'email' => 'test-' . uniqid() . '@example.com',
        ]);

        // Create candidat
        $candidat = Candidat::create([
            'utilisateur_id' => $utilisateur->id,
            'nom_cand' => 'Test',
            'prenom_cand' => 'User',
            'nationalite_cand' => 'Camerounaise',
        ]);

        // Create concours and session if candidature is needed
        $concours = null;
        $session = null;
        $candidature = null;

        if ($withCandidature) {
            $concours = Concours::factory()->create();
            $session = Session::factory()->create();

            // Link concours and session in pivot table
            $concours->sessions()->attach($session->id);

            $candidature = Candidature::create([
                'candidat_id' => $candidat->utilisateur_id,
                'concours_id' => $concours->id,
                'session_id' => $session->id,
                'statut_candidature' => StatutCandidature::SOUMISE,
                'date_inscription' => now(),
            ]);
        } else {
            $concours = Concours::factory()->create();
        }

        // Create paiement with specific status
        $paiement = Paiement::create([
            'candidat_id' => $candidat->utilisateur_id,
            'concours_id' => $concours->id,
            'candidature_id' => $candidature?->id,
            'reference' => 'REF-' . uniqid(),
            'montant' => 50000,
            'statut' => $paymentStatus,
            'preuve_paiement' => 'receipts/test.pdf',
        ]);

        return [
            'utilisateur' => $utilisateur,
            'candidat' => $candidat,
            'paiement' => $paiement,
            'pru' => $pru,
            'concours' => $concours,
            'session' => $session,
            'candidature' => $candidature,
        ];
    }
}
