<?php

use App\Jobs\CleanupTempFilesJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Planifier le nettoyage des fichiers temporaires tous les jours à 2h du matin
Schedule::job(new CleanupTempFilesJob(24))->dailyAt('02:00');

// Generate alerts for all candidates daily at 6:00 AM
Schedule::command('alerts:generate --all')->dailyAt('06:00');
