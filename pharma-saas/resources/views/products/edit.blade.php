@extends('layouts.app')

@section('title', __('edit') . ' ' . __('products'))

@section('content')
<form action="{{ route('products.update', $product) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('products') }} Information</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('name') }} *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('code') }} *</label>
                    <input type="text" name="code" value="{{ old('code', $product->code) }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Barcode</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('category') }}</label>
                    <select name="category_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">-- Select --</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('suppliers') }}</label>
                    <select name="supplier_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">-- Select --</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ $product->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('price') }} & {{ __('stock') }}</h3>
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purchase {{ __('price') }}</label>
                        <input type="number" name="purchase_price" step="0.01" value="{{ old('purchase_price', $product->purchase_price) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Selling {{ __('price') }}</label>
                        <input type="number" name="selling_price" step="0.01" value="{{ old('selling_price', $product->selling_price) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min {{ __('stock') }}</label>
                        <input type="number" name="min_stock" step="0.01" value="{{ old('min_stock', $product->min_stock) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max {{ __('stock') }}</label>
                        <input type="number" name="max_stock" step="0.01" value="{{ old('max_stock', $product->max_stock) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                    <input type="text" name="unit" value="{{ old('unit', $product->unit) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dosage</label>
                    <input type="text" name="dosage" value="{{ old('dosage', $product->dosage) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Form</label>
                    <select name="form" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">-- Select --</option>
                        <option value="Tablet" {{ $product->form == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                        <option value="Capsule" {{ $product->form == 'Capsule' ? 'selected' : '' }}>Capsule</option>
                        <option value="Syrup" {{ $product->form == 'Syrup' ? 'selected' : '' }}>Syrup</option>
                        <option value="Injection" {{ $product->form == 'Injection' ? 'selected' : '' }}>Injection</option>
                        <option value="Cream" {{ $product->form == 'Cream' ? 'selected' : '' }}>Cream</option>
                        <option value="Ointment" {{ $product->form == 'Ointment' ? 'selected' : '' }}>Ointment</option>
                        <option value="Drops" {{ $product->form == 'Drops' ? 'selected' : '' }}>Drops</option>
                    </select>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="requires_prescription" id="requires_prescription" {{ $product->requires_prescription ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <label for="requires_prescription" class="ml-2 text-sm text-gray-700">Requires Prescription</label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" {{ $product->is_active ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">{{ __('active') }}</label>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-6 flex justify-end">
        <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 mr-2">
            {{ __('cancel') }}
        </a>
        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
            {{ __('save') }}
        </button>
    </div>
</form>
@endsection
