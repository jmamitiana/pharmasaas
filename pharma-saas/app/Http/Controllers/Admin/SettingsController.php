<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $tenantId = Auth::user()->tenant_id;
        
        $appSettings = [
            'app_name' => Setting::getValue('app_name', 'Pharma SaaS', $tenantId),
            'currency' => Setting::getValue('currency', 'MGA', $tenantId),
            'timezone' => Setting::getValue('timezone', 'Indian/Antananarivo', $tenantId),
            'language' => Setting::getValue('language', 'fr', $tenantId),
            'auto_backup' => Setting::getValue('auto_backup', true, $tenantId),
            'stock_refresh_interval' => Setting::getValue('stock_refresh_interval', 5, $tenantId),
        ];

        $pharmacySettings = [
            'pharmacy_name' => Setting::getValue('pharmacy_name', '', $tenantId),
            'address' => Setting::getValue('address', '', $tenantId),
            'phone' => Setting::getValue('phone', '', $tenantId),
            'email' => Setting::getValue('email', '', $tenantId),
            'tax_number' => Setting::getValue('tax_number', '', $tenantId),
            'license_number' => Setting::getValue('license_number', '', $tenantId),
        ];

        return view('settings.index', compact('appSettings', 'pharmacySettings'));
    }

    public function update(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        
        $appSettings = $request->validate([
            'app_name' => 'required|string|max:255',
            'currency' => 'required|string|max:10',
            'timezone' => 'required|string|max:50',
            'language' => 'required|in:fr,en',
            'auto_backup' => 'boolean',
            'stock_refresh_interval' => 'required|integer|min:1|max:60',
        ]);

        foreach ($appSettings as $key => $value) {
            Setting::setValue($key, $value, $tenantId, is_bool($value) ? 'boolean' : 'string');
        }

        $pharmacySettings = $request->validate([
            'pharmacy_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'tax_number' => 'nullable|string|max:50',
            'license_number' => 'nullable|string|max:50',
        ]);

        foreach ($pharmacySettings as $key => $value) {
            Setting::setValue($key, $value, $tenantId, 'string');
        }

        return redirect()->route('settings.index')->with('success', __('Settings updated successfully'));
    }

    public function general()
    {
        $tenant = Tenant::find(Auth::user()->tenant_id);
        
        return view('settings.general', compact('tenant'));
    }

    public function updateGeneral(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $tenant->update($validated);

        return redirect()->route('settings.general')->with('success', __('General settings updated successfully'));
    }
}
