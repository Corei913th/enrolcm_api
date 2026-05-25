<?php

namespace App\Providers;

use App\Services\Infrastructure\Export\ExcelExportService;
use App\Services\Infrastructure\Export\PdfExportService;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use App\Services\Infrastructure\QrCode\QrCodeService;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ActivityLoggerService::class, function ($app) {
            return new ActivityLoggerService;
        });

        $this->app->singleton(ExcelExportService::class, function ($app) {
            return $app->make(ExcelExportService::class);
        });

        $this->app->singleton(PdfExportService::class, function ($app) {
            return $app->make(PdfExportService::class);
        });

        $this->app->singleton(QrCodeService::class, function ($app) {
            return new QrCodeService;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
