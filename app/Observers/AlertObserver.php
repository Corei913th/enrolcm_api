<?php

namespace App\Observers;

use App\Models\Alert;
use App\Models\Candidature;
use Illuminate\Support\Facades\Cache;

class AlertObserver
{
  /**
   * Handle the Alert "created" event.
   */
  public function created(Alert $alert): void
  {
    $this->invalidateDashboardCache($alert);
  }

  /**
   * Handle the Alert "updated" event.
   */
  public function updated(Alert $alert): void
  {
    $this->invalidateDashboardCache($alert);
  }

  /**
   * Handle the Alert "deleted" event.
   */
  public function deleted(Alert $alert): void
  {
    $this->invalidateDashboardCache($alert);
  }

  /**
   * Invalidate the candidat dashboard cache for the related candidature
   */
  private function invalidateDashboardCache(Alert $alert): void
  {
    if ($alert->candidature_id) {
      $candidature = Candidature::find($alert->candidature_id);
      if ($candidature && $candidature->candidat_id) {
        Cache::forget("candidat_dashboard_{$candidature->candidat_id}");
      }
    }
  }
}
