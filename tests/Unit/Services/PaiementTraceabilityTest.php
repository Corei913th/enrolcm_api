<?php

namespace Tests\Unit\Services;

use App\Enums\StatutPaiement;
use App\Models\Concours;
use App\Models\ConcoursPaiement;
use App\Models\Paiement;
use App\Models\Utilisateur;
use App\Services\Domain\Paiement\PaiementService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaiementTraceabilityTest extends TestCase
{
    use RefreshDatabase;

    private PaiementService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->service = app(PaiementService::class);
    }

    /** @test */
    public function manual_payment_creation_records_timestamp()
    {
        // Arrange
        $concours = Concours::factory()->create();
        $config = ConcoursPaiement::factory()->create([
            'concours_id' => $concours->id,
            'est_actif' => true,
            'montant' => 50000,
        ]);

        $file = UploadedFile::fake()->image('receipt.jpg');

        // Act
        $paiement = $this->service->createPayment(
            $concours->id,
            'REF-TEST-001',
            50000,
            $file
        );

        // Assert - Requirement 9.1: created_at must be recorded
        $this->assertNotNull($paiement->created_at);
        $this->assertInstanceOf(Carbon::class, $paiement->created_at);
        $this->assertTrue($paiement->created_at->lessThanOrEqualTo(now()));
    }

    /** @test */
    public function payment_validation_records_admin_and_timestamp()
    {
        // Arrange
        $admin = Utilisateur::factory()->create();
        $paiement = Paiement::factory()
            ->withoutCandidature()
            ->create([
                'statut' => StatutPaiement::PENDING_MANUAL_REVIEW,
                'validated_at' => null,
                'validated_by' => null,
            ]);

        // Act
        $result = $this->service->manualValidate($paiement->id, $admin->id);

        // Assert - Requirement 9.2: validated_by and validated_at must be recorded
        $this->assertNotNull($result->validated_by);
        $this->assertEquals($admin->id, $result->validated_by);
        $this->assertNotNull($result->validated_at);
        $this->assertInstanceOf(Carbon::class, $result->validated_at);
        $this->assertEquals(StatutPaiement::VERIFIED, $result->statut);
    }

    /** @test */
    public function payment_rejection_records_admin_timestamp_and_reason()
    {
        // Arrange
        $admin = Utilisateur::factory()->create();
        $paiement = Paiement::factory()
            ->withoutCandidature()
            ->create([
                'statut' => StatutPaiement::PENDING_MANUAL_REVIEW,
                'validated_at' => null,
                'validated_by' => null,
                'validation_notes' => null,
            ]);

        $motif = 'Montant incorrect sur le reçu';

        // Act
        $result = $this->service->reject($paiement->id, $motif, $admin->id);

        // Assert - Requirement 9.3: validated_by, validated_at, and motif_rejet must be recorded
        $this->assertNotNull($result->validated_by);
        $this->assertEquals($admin->id, $result->validated_by);
        $this->assertNotNull($result->validated_at);
        $this->assertInstanceOf(Carbon::class, $result->validated_at);
        $this->assertEquals(StatutPaiement::REJECTED, $result->statut);
        $this->assertStringContainsString($motif, $result->validation_notes);
    }

    /** @test */
    public function validation_preserves_existing_validation_notes()
    {
        // Arrange
        $admin = Utilisateur::factory()->create();
        $paiement = Paiement::factory()
            ->withoutCandidature()
            ->create([
                'statut' => StatutPaiement::PENDING_MANUAL_REVIEW,
                'validation_notes' => 'Note initiale',
            ]);

        // Act
        $result = $this->service->manualValidate($paiement->id, $admin->id);

        // Assert - Validation notes should be appended, not replaced
        $this->assertStringContainsString('Note initiale', $result->validation_notes);
        $this->assertStringContainsString('Validé manuellement', $result->validation_notes);
    }

    /** @test */
    public function rejection_preserves_existing_validation_notes()
    {
        // Arrange
        $admin = Utilisateur::factory()->create();
        $paiement = Paiement::factory()
            ->withoutCandidature()
            ->create([
                'statut' => StatutPaiement::PENDING_MANUAL_REVIEW,
                'validation_notes' => 'Note initiale',
            ]);

        $motif = 'Document illisible';

        // Act
        $result = $this->service->reject($paiement->id, $motif, $admin->id);

        // Assert - Validation notes should be appended, not replaced
        $this->assertStringContainsString('Note initiale', $result->validation_notes);
        $this->assertStringContainsString($motif, $result->validation_notes);
    }

    /** @test */
    public function model_valider_method_records_traceability()
    {
        // Arrange
        $admin = Utilisateur::factory()->create();
        $paiement = Paiement::factory()
            ->withoutCandidature()
            ->create([
                'statut' => StatutPaiement::PENDING,
            ]);

        // Act
        $paiement->valider($admin->id);
        $paiement->refresh();

        // Assert
        $this->assertEquals(StatutPaiement::VERIFIED, $paiement->statut);
        $this->assertEquals($admin->id, $paiement->validated_by);
        $this->assertNotNull($paiement->validated_at);
        $this->assertNull($paiement->motif_rejet);
    }

    /** @test */
    public function model_rejeter_method_records_traceability()
    {
        // Arrange
        $admin = Utilisateur::factory()->create();
        $paiement = Paiement::factory()
            ->withoutCandidature()
            ->create([
                'statut' => StatutPaiement::PENDING,
            ]);

        $motif = 'Reçu falsifié';

        // Act
        $paiement->rejeter($motif, $admin->id);
        $paiement->refresh();

        // Assert
        $this->assertEquals(StatutPaiement::REJECTED, $paiement->statut);
        $this->assertEquals($admin->id, $paiement->validated_by);
        $this->assertNotNull($paiement->validated_at);
        $this->assertEquals($motif, $paiement->motif_rejet);
    }

    /** @test */
    public function timestamps_are_automatically_managed()
    {
        // Arrange
        $concours = Concours::factory()->create();
        $config = ConcoursPaiement::factory()->create([
            'concours_id' => $concours->id,
            'est_actif' => true,
            'montant' => 50000,
        ]);

        $file = UploadedFile::fake()->image('receipt.jpg');

        // Act
        $paiement = $this->service->createPayment(
            $concours->id,
            'REF-AUTO-001',
            50000,
            $file
        );

        // Assert - Laravel timestamps should be automatic
        $this->assertNotNull($paiement->created_at);
        $this->assertNotNull($paiement->updated_at);

        // Update the payment
        $paiement->update(['validation_notes' => 'Test note']);
        $paiement->refresh();

        // updated_at should have changed
        $this->assertTrue($paiement->updated_at->greaterThanOrEqualTo($paiement->created_at));
    }

    /** @test */
    public function validation_date_is_recorded_at_validation_time()
    {
        // Arrange
        $admin = Utilisateur::factory()->create();
        $paiement = Paiement::factory()
            ->withoutCandidature()
            ->create([
                'statut' => StatutPaiement::PENDING_MANUAL_REVIEW,
            ]);

        $beforeValidation = now();

        // Act
        sleep(1); // Ensure time difference
        $result = $this->service->manualValidate($paiement->id, $admin->id);

        // Assert - validated_at should be close to now, not created_at
        $this->assertTrue($result->validated_at->greaterThanOrEqualTo($beforeValidation));
        $this->assertTrue($result->validated_at->greaterThan($result->created_at));
    }

    /** @test */
    public function rejection_date_is_recorded_at_rejection_time()
    {
        // Arrange
        $admin = Utilisateur::factory()->create();
        $paiement = Paiement::factory()
            ->withoutCandidature()
            ->create([
                'statut' => StatutPaiement::PENDING_MANUAL_REVIEW,
            ]);

        $beforeRejection = now();

        // Act
        sleep(1); // Ensure time difference
        $result = $this->service->reject($paiement->id, 'Test rejection', $admin->id);

        // Assert - validated_at should be close to now, not created_at
        $this->assertTrue($result->validated_at->greaterThanOrEqualTo($beforeRejection));
        $this->assertTrue($result->validated_at->greaterThan($result->created_at));
    }
}
