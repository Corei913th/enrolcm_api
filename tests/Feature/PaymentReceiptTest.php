<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Utilisateur;
use App\Models\Candidat;
use App\Models\PaymentReceipt;
use App\Enums\TypeUtilisateur;
use App\Enums\StatutVerificationPaiement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

class PaymentReceiptTest extends TestCase
{
    use RefreshDatabase;

    protected Utilisateur $candidatUser;
    protected Candidat $candidat;
    protected Utilisateur $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');

        // Créer un candidat
        $this->candidatUser = Utilisateur::factory()->create([
            'type_utilisateur' => TypeUtilisateur::CANDIDAT,
        ]);

        $this->candidat = Candidat::create([
            'utilisateur_id' => $this->candidatUser->id,
            'nom_cand' => 'Test',
            'prenom_cand' => 'Candidat',
            'nationalite_cand' => 'Camerounaise',
            'numero_recu' => 'TEST-001',
        ]);

        // Créer un admin
        $this->adminUser = Utilisateur::factory()->create([
            'type_utilisateur' => TypeUtilisateur::ADMIN,
        ]);
    }

    /** @test */
    public function candidat_can_upload_receipt()
    {
        Sanctum::actingAs($this->candidatUser);

        $file = UploadedFile::fake()->image('receipt.jpg');

        $response = $this->postJson('/api/payment/receipts/upload', [
            'receipt_image' => $file,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'numero_recu',
                    'statut_verification',
                ],
            ]);

        $this->assertDatabaseHas('payment_receipts', [
            'candidat_id' => $this->candidat->utilisateur_id,
        ]);
    }

    /** @test */
    public function candidat_cannot_upload_invalid_file()
    {
        Sanctum::actingAs($this->candidatUser);

        $file = UploadedFile::fake()->create('document.pdf');

        $response = $this->postJson('/api/payment/receipts/upload', [
            'receipt_image' => $file,
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function candidat_can_view_their_receipt()
    {
        Sanctum::actingAs($this->candidatUser);

        $receipt = PaymentReceipt::create([
            'candidat_id' => $this->candidat->utilisateur_id,
            'numero_recu' => 'REC-001',
            'montant' => 5000,
            'image_path' => 'receipts/test.jpg',
            'statut_verification' => StatutVerificationPaiement::EN_ATTENTE,
        ]);

        $response = $this->getJson('/api/payment/receipts/my-receipt');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $receipt->id,
                    'numero_recu' => 'REC-001',
                ],
            ]);
    }

    /** @test */
    public function candidat_gets_404_when_no_receipt()
    {
        Sanctum::actingAs($this->candidatUser);

        $response = $this->getJson('/api/payment/receipts/my-receipt');

        $response->assertStatus(404);
    }

    /** @test */
    public function admin_can_view_pending_receipts()
    {
        Sanctum::actingAs($this->adminUser);

        PaymentReceipt::create([
            'candidat_id' => $this->candidat->utilisateur_id,
            'numero_recu' => 'REC-001',
            'montant' => 5000,
            'image_path' => 'receipts/test.jpg',
            'statut_verification' => StatutVerificationPaiement::EN_ATTENTE,
        ]);

        $response = $this->getJson('/api/payment/admin/receipts/pending');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta',
            ]);
    }

    /** @test */
    public function admin_can_verify_receipt()
    {
        Sanctum::actingAs($this->adminUser);

        $receipt = PaymentReceipt::create([
            'candidat_id' => $this->candidat->utilisateur_id,
            'numero_recu' => 'REC-001',
            'montant' => 5000,
            'image_path' => 'receipts/test.jpg',
            'statut_verification' => StatutVerificationPaiement::EN_ATTENTE,
        ]);

        $response = $this->postJson("/api/payment/admin/receipts/{$receipt->id}/verify", [
            'statut' => 'verifie',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('payment_receipts', [
            'id' => $receipt->id,
            'statut_verification' => 'verifie',
            'verified_by' => $this->adminUser->id,
        ]);
    }

    /** @test */
    public function admin_can_reject_receipt_with_reason()
    {
        Sanctum::actingAs($this->adminUser);

        $receipt = PaymentReceipt::create([
            'candidat_id' => $this->candidat->utilisateur_id,
            'numero_recu' => 'REC-001',
            'montant' => 4500,
            'image_path' => 'receipts/test.jpg',
            'statut_verification' => StatutVerificationPaiement::EN_ATTENTE,
        ]);

        $response = $this->postJson("/api/payment/admin/receipts/{$receipt->id}/verify", [
            'statut' => 'rejete',
            'motif_rejet' => 'Montant incorrect',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('payment_receipts', [
            'id' => $receipt->id,
            'statut_verification' => 'rejete',
            'motif_rejet' => 'Montant incorrect',
        ]);
    }

    /** @test */
    public function admin_cannot_reject_without_reason()
    {
        Sanctum::actingAs($this->adminUser);

        $receipt = PaymentReceipt::create([
            'candidat_id' => $this->candidat->utilisateur_id,
            'numero_recu' => 'REC-001',
            'montant' => 5000,
            'image_path' => 'receipts/test.jpg',
            'statut_verification' => StatutVerificationPaiement::EN_ATTENTE,
        ]);

        $response = $this->postJson("/api/payment/admin/receipts/{$receipt->id}/verify", [
            'statut' => 'rejete',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['motif_rejet']);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_endpoints()
    {
        $response = $this->postJson('/api/payment/receipts/upload');
        $response->assertStatus(401);

        $response = $this->getJson('/api/payment/receipts/my-receipt');
        $response->assertStatus(401);

        $response = $this->getJson('/api/payment/admin/receipts/pending');
        $response->assertStatus(401);
    }
}
