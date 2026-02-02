<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Candidat;
use App\Services\Domain\Notification\Generators\AlertGeneratorService;

class GenerateAlertsCommand extends Command
{
  /**
   * The name and signature of the console command.
   */
  protected $signature = 'alerts:generate 
                            {--candidat= : Specific candidate ID}
                            {--all : Generate for all candidates}';

  /**
   * The console command description.
   */
  protected $description = 'Generate automatic alerts for candidates';

  /**
   * Execute the console command
   *
   * @param AlertGeneratorService $alertGenerator
   * @return int
   */
  public function handle(AlertGeneratorService $alertGenerator): int
  {
    $candidatId = $this->option('candidat');
    $all = $this->option('all');

    if (!$candidatId && !$all) {
      $this->error('Please specify --candidat=ID or --all');
      return self::FAILURE;
    }

    if ($candidatId) {
      return $this->generateForCandidate($candidatId, $alertGenerator);
    }

    return $this->generateForAll($alertGenerator);
  }

  /**
   * Generate alerts for a specific candidate
   *
   * @param string $candidatId
   * @param AlertGeneratorService $alertGenerator
   * @return int
   */
  private function generateForCandidate(string $candidatId, AlertGeneratorService $alertGenerator): int
  {
    $candidat = Candidat::with(['candidatures.concours', 'candidatures.convocation', 'candidatures.resultatFinal'])
      ->find($candidatId);

    if (!$candidat) {
      $this->error("Candidate {$candidatId} not found");
      return self::FAILURE;
    }

    $this->info("Generating alerts for {$candidat->nom_cand} {$candidat->prenom_cand}...");

    $alerts = $alertGenerator->generateCandidateAlerts($candidat);

    $this->info("✓ " . count($alerts) . " alert(s) generated");

    return self::SUCCESS;
  }

  /**
   * Generate alerts for all candidates
   *
   * @param AlertGeneratorService $alertGenerator
   * @return int
   */
  private function generateForAll(AlertGeneratorService $alertGenerator): int
  {
    $this->info('Generating alerts for all candidates...');

    $candidats = Candidat::with(['candidatures.concours', 'candidatures.convocation', 'candidatures.resultatFinal'])
      ->get();

    $bar = $this->output->createProgressBar($candidats->count());
    $bar->start();

    $totalAlerts = 0;

    foreach ($candidats as $candidat) {
      $alerts = $alertGenerator->generateCandidateAlerts($candidat);
      $totalAlerts += count($alerts);
      $bar->advance();
    }

    $bar->finish();
    $this->newLine(2);

    $this->info("✓ {$totalAlerts} alert(s) generated for {$candidats->count()} candidate(s)");

    return self::SUCCESS;
  }
}
