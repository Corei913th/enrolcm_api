<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Utilisateur;

class TestVerificationLink extends Command
{
  protected $signature = 'test:verification-link';
  protected $description = 'Unverify user and send verification link.';

  public function handle()
  {
    $email = 'bogningfredy972@gmail.com';
    $user = Utilisateur::where('email', $email)->first();

    if (!$user) {
      $this->error("User $email not found.");
      return;
    }

    $this->info("Unverifying user $email...");
    $user->email_verifie = false;
    $user->email_verifie_at = null;
    $user->save();

    $this->info("Sending verification email...");
    try {
      $user->sendEmailVerificationNotification();
      $this->info("Verification email SENT.");
    } catch (\Exception $e) {
      $this->error("Error sending verification: " . $e->getMessage());
    }
  }
}
