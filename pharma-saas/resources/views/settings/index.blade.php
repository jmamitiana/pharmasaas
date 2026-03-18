@extends('layouts.app')

@section('title', __('settings'))

@section('content')
<form action="{{ route('settings.update') }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Application Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">{{ __('settings') }} - Application</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app_name') }}</label>
                    <input type="text" name="app_name" value="{{ $appSettings['app_name'] }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('currency') }}</label>
                    <select name="currency" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="MGA" {{ $appSettings['currency'] === 'MGA' ? 'selected' : '' }}>MGA (Ariary)</option>
                        <option value="EUR" {{ $appSettings['currency'] === 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
                        <option value="USD" {{ $appSettings['currency'] === 'USD' ? 'selected' : '' }}>USD (Dollar)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('timezone') }}</label>
                    <select name="timezone" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="Indian/Antananarivo" {{ $appSettings['timezone'] === 'Indian/Antananarivo' ? 'selected' : '' }}>Indian/Antananarivo</option>
                        <option value="Europe/Paris" {{ $appSettings['timezone'] === 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                        <option value="UTC" {{ $appSettings['timezone'] === 'UTC' ? 'selected' : '' }}>UTC</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('language') }}</label>
                    <select name="language" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="fr" {{ $appSettings['language'] === 'fr' ? 'selected' : '' }}>Français</option>
                        <option value="en" {{ $appSettings['language'] === 'en' ? 'selected' : '' }}>English</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('stock_refresh') }} (minutes)</label>
                    <input type="number" name="stock_refresh_interval" value="{{ $appSettings['stock_refresh_interval'] }}" min="1" max="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="auto_backup" id="auto_backup" {{ $appSettings['auto_backup'] ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <label for="auto_backup" class="ml-2 text-sm text-gray-700">{{ __('auto_backup') }}</label>
                </div>
            </div>
        </div>
        
        <!-- Pharmacy Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">{{ __('pharmacy_name') }}</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('pharmacy_name') }}</label>
                    <input type="text" name="pharmacy_name" value="{{ $pharmacySettings['pharmacy_name'] }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('address') }}</label>
                    <textarea name="address" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ $pharmacySettings['address'] }}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('phone') }}</label>
                    <input type="text" name="phone" value="{{ $pharmacySettings['phone'] }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('email') }}</label>
                    <input type="email" name="email" value="{{ $pharmacySettings['email'] }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('tax_number') }}</label>
                    <input type="text" name="tax_number" value="{{ $pharmacySettings['tax_number'] }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('license_number') }}</label>
                    <input type="text" name="license_number" value="{{ $pharmacySettings['license_number'] }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-6 flex justify-end">
        <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
            {{ __('save') }}
        </button>
    </div>
</form>
@endsection
