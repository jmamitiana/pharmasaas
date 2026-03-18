<?php

namespace App\Services;

use App\Models\Setting;

class SettingsService
{
    public static function get(string $key, $default = null, ?int $tenantId = null)
    {
        return Setting::getValue($key, $default, $tenantId);
    }

    public static function set(string $key, $value, ?int $tenantId = null, string $type = 'string'): void
    {
        Setting::setValue($key, $value, $tenantId, $type);
    }

    public static function getAll(?int $tenantId = null): array
    {
        $settings = Setting::where('tenant_id', $tenantId)->get();
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = Setting::getValue($setting->key, null, $tenantId);
        }
        
        return $result;
    }

    public static function getAppSettings(?int $tenantId = null): array
    {
        return [
            'app_name' => self::get('app_name', 'Pharma SaaS', $tenantId),
            'currency' => self::get('currency', 'MGA', $tenantId),
            'timezone' => self::get('timezone', 'Indian/Antananarivo', $tenantId),
            'language' => self::get('language', 'fr', $tenantId),
            'auto_backup' => self::get('auto_backup', true, $tenantId),
            'stock_refresh_interval' => self::get('stock_refresh_interval', 5, $tenantId),
        ];
    }

    public static function getPharmacySettings(?int $tenantId = null): array
    {
        return [
            'pharmacy_name' => self::get('pharmacy_name', '', $tenantId),
            'address' => self::get('address', '', $tenantId),
            'phone' => self::get('phone', '', $tenantId),
            'email' => self::get('email', '', $tenantId),
            'tax_number' => self::get('tax_number', '', $tenantId),
            'license_number' => self::get('license_number', '', $tenantId),
        ];
    }
}
