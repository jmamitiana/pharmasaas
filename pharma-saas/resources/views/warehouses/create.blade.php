@extends('layouts.app')

@section('title', __('add') . ' ' . __('warehouses'))

@section('content')
<form action="{{ route('warehouses.store') }}" method="POST">
    @csrf
    
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('warehouses') }} Information</h3>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('name') }} *</label>
                <input type="text" name="name" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('code') }}</label>
                <input type="text" name="code" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" name="is_default" id="is_default" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                <label for="is_default" class="ml-2 text-sm text-gray-700">Set as Default Warehouse</label>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end gap-2">
            <a href="{{ route('warehouses.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                {{ __('cancel') }}
            </a>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                {{ __('save') }}
            </button>
        </div>
    </div>
</form>
@endsection
