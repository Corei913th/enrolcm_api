<?php

namespace Tests\Unit\Services;

use App\Enums\StatutPaiement;
use App\Models\Concours;
use App\Models\Paiement;
use App\Services\Domain\Paiement\PaiementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaiementServicePriorityTest extends TestCase
{
    use RefreshDatabase;

    private PaiementService $paiementService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paiementService = app(PaiementService::class);
    }

    /** @test */
    public function it_prioritizes_pending_manual_review_payments()
    {
        // Créer un concours
        $concours = Concours::factory()->create();

        // Créer des paiements avec différents statuts (sans candidature)
        $paiementManuel = Paiement::factory()->create([
            'concours_id' => $concours->id,
            'candidature_id' => null,
            'statut' => StatutPaiement::PENDING_MANUAL_REVIEW,
            'created_at' => now()->subHours(2),
        ]);

        $paiementPending = Paiement::factory()->create([
            'concours_id' => $concours->id,
            'candidature_id' => null,
            'statut' => StatutPaiement::PENDING,
            'created_at' => now()->subHours(1), // Plus récent
        ]);

        $paiementVerified = Paiement::factory()->create([
            'concours_id' => $concours->id,
            'candidature_id' => null,
            'statut' => StatutPaiement::VERIFIED,
            'created_at' => now(),
        ]);

        // Récupérer les paiements avec priorité
        $result = $this->paiementService->getPaymentsWithPriority(10);

        // Le premier paiement doit être celui avec PENDING_MANUAL_REVIEW
        $this->assertEquals($paiementManuel->id, $result->items()[0]->id);
        $this->assertEquals(StatutPaiement::PENDING_MANUAL_REVIEW, $result->items()[0]->statut);
    }

    /** @test */
    public function it_filters_payments_by_status()
    {
        // Créer un concours
        $concours = Concours::factory()->create();

        // Créer des paiements avec différents statuts (sans candidature)
        Paiement::factory()->create([
            'concours_id' => $concours->id,
            'candidature_id' => null,
            'statut' => StatutPaiement::PENDING_MANUAL_REVIEW,
        ]);

        Paiement::factory()->create([
            'concours_id' => $concours->id,
            'candidature_id' => null,
            'statut' => StatutPaiement::VERIFIED,
        ]);

        Paiement::factory()->create([
            'concours_id' => $concours->id,
            'candidature_id' => null,
            'statut' => StatutPaiement::REJECTED,
        ]);

        // Filtrer par PENDING_MANUAL_REVIEW
        $result = $this->paiementService->getPaymentsWithPriority(10, StatutPaiement::PENDING_MANUAL_REVIEW->value);

        // Tous les résultats doivent avoir le statut PENDING_MANUAL_REVIEW
        $this->assertCount(1, $result->items());
        $this->assertEquals(StatutPaiement::PENDING_MANUAL_REVIEW, $result->items()[0]->statut);
    }

    /** @test */
    public function it_sorts_by_creation_date_within_same_priority()
    {
        // Créer un concours
        $concours = Concours::factory()->create();

        // Créer plusieurs paiements avec le même statut (sans candidature)
        $paiement1 = Paiement::factory()->create([
            'concours_id' => $concours->id,
            'candidature_id' => null,
            'statut' => StatutPaiement::PENDING_MANUAL_REVIEW,
            'created_at' => now()->subHours(3),
        ]);

        $paiement2 = Paiement::factory()->create([
            'concours_id' => $concours->id,
            'candidature_id' => null,
            'statut' => StatutPaiement::PENDING_MANUAL_REVIEW,
            'created_at' => now()->subHours(1),
        ]);

        // Récupérer les paiements
        $result = $this->paiementService->getPaymentsWithPriority(10);

        // Le plus récent doit être en premier
        $this->assertEquals($paiement2->id, $result->items()[0]->id);
        $this->assertEquals($paiement1->id, $result->items()[1]->id);
    }
}
