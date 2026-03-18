<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SettingsService;
use App\Services\BackupService;
use App\Services\StockPredictionService;
use App\Services\PaymentService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingsService::class, function ($app) {
            return new SettingsService();
        });

        $this->app->singleton(BackupService::class, function ($app) {
            return new BackupService();
        });

        $this->app->singleton(StockPredictionService::class, function ($app) {
            return new StockPredictionService();
        });

        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService();
        });
    }

    public function boot(): void
    {
        //
    }
}
