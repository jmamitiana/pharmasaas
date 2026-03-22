<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('settings', function () {
            return new \App\Services\SettingsService();
        });
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
        
        $this->loadSettingsFromDatabase();
    }

    protected function loadSettingsFromDatabase(): void
    {
        try {
            if (!\Illuminate\Support\Facades\DB::connection()->getPdo()) {
                return;
            }

            $tenantId = null;
            
            if (Auth::check()) {
                $tenantId = Auth::user()->tenant_id;
            }

            $settings = Setting::where('tenant_id', $tenantId)->get();
            
            foreach ($settings as $setting) {
                config([
                    'settings.' . $setting->key => $setting->value
                ]);
            }
        } catch (\Exception $e) {
            // Database not ready yet
        }
    }
}
