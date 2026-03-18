<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Services\BackupService;
use App\Services\StockPredictionService;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $backupService = new BackupService();
            $tenants = \App\Models\Tenant::where('status', 'active')->get();
            
            foreach ($tenants as $tenant) {
                $settings = \App\Models\Setting::where('tenant_id', $tenant->id)->get();
                $autoBackup = $settings->where('key', 'auto_backup')->first();
                
                if ($autoBackup && $autoBackup->value === 'true') {
                    $backupService->createBackup($tenant->id);
                }
            }
        })->dailyAt('02:00');

        $schedule->call(function () {
            $stockService = new StockPredictionService();
            $tenants = \App\Models\Tenant::where('status', 'active')->get();
            
            foreach ($tenants as $tenant) {
                $stockService->runBatchPrediction($tenant->id);
            }
        })->dailyAt('06:00');

        $schedule->call(function () {
            $backupService = new BackupService();
            $backupService->deleteOldBackups(30);
        })->dailyAt('03:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
