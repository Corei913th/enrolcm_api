<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Alert;
use App\Services\Domain\Notification\Generators\AlertGeneratorService;
use Illuminate\Support\Facades\DB;

class TestAlertResolution extends Command
{
  protected $signature = 'test:alert-resolution';
  protected $description = 'Verify that alerts are automatically resolved when conditions are met.';

  public function handle()
  {
    $this->info('Starting Alert Resolution Test...');

    // 1. Setup: User with Incomplete Profile & Alerts
    $candidature = Candidature::with(['candidat', 'concours'])->latest()->first();
    if (!$candidature) {
      $this->error('No candidature found. Run test:registration-workflow first.');
      return;
    }
    $candidat = $candidature->candidat;

    // Force incomplete profile
    $candidat->update(['numero_cni' => null]);

    // Force pending payment/docs
    $candidature->update([
      'paiement_valide' => false,
      'documents_complets' => false
    ]);

    /** @var AlertGeneratorService $generator */
    $generator = app(AlertGeneratorService::class);

    // Regenerate alerts
    $generator->generateCandidateAlerts($candidat);
    $generator->generateCandidatureAlerts($candidature);

    $this->info("Initial State:");
    $this->checkAlerts($candidature);

    // 2. Fix Profile -> Expect Profile Alert Gone
    $this->info("\n--- Fixing Profile (adding CNI, Lieu, Adresse) ---");
    $candidat->update([
      'numero_cni' => '1122334455',
      'lieu_naissance_cand' => 'Yaoundé',
      'adresse_cand' => 'Bastos',
      'sexe_cand' => 'M'
    ]);

    // Use generator to simulate "next check" or rely on cleanObsoleteAlerts if logic allows?
    // Note: Profile alert is checked in generateCandidateAlerts. 
    // Logic: isProfileIncomplete returns false -> alert is NOT created. 
    // But is existing one deleted? 
    // Reader: cleanObsoleteAlerts does NOT handle 'profile_incomplete'. 
    // Check: Does generateCandidateAlerts REMOVE alerts? No, it only creates.
    // Issue: Profile alert might STICK if not explicitly cleared.
    // Let's see if this test reveals that gap.

    // Manually trigger regeneration to see if logic handles removal (it probably doesn't)
    $generator->generateCandidateAlerts($candidat);
    $this->checkAlerts($candidature);


    // 3. Complete Documents -> Expect Missing Docs Alert Gone
    $this->info("\n--- Completing Documents ---");
    $candidature->update(['documents_complets' => true]);
    // Observer should trigger cleanObsoleteAlerts
    $this->checkAlerts($candidature);

    // 4. Validate Payment -> Expect Payment Alert Gone
    $this->info("\n--- Validating Payment ---");
    $candidature->update(['paiement_valide' => true]);
    // Observer should trigger cleanObsoleteAlerts
    $this->checkAlerts($candidature);
  }

  private function checkAlerts($candidature)
  {
    $alerts = Alert::where('candidature_id', $candidature->id)->get();
    $types = $alerts->pluck('type')->toArray();
    $this->line("Current Alerts: " . implode(', ', $types));

    if (in_array('profile_incomplete', $types)) $this->warn(" [!] Profile Incomplete alert present");
    if (in_array('missing_documents', $types)) $this->warn(" [!] Missing Documents alert present");
    if (in_array('payment_pending', $types)) $this->warn(" [!] Payment Pending alert present");
  }
}
