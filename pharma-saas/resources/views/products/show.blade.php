@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $product->name }}</h2>
            <p class="text-gray-600">{{ __('code') }}: {{ $product->code }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('products.edit', $product) }}" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                {{ __('edit') }}
            </a>
            <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                {{ __('back') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('products') }} Details</h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-gray-600">{{ __('category') }}:</dt>
                    <dd class="font-medium">{{ $product->category?->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">{{ __('suppliers') }}:</dt>
                    <dd class="font-medium">{{ $product->supplier?->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Dosage:</dt>
                    <dd class="font-medium">{{ $product->dosage ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Form:</dt>
                    <dd class="font-medium">{{ $product->form ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Unit:</dt>
                    <dd class="font-medium">{{ $product->unit ?? 'piece' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Prescription Required:</dt>
                    <dd class="font-medium">{{ $product->requires_prescription ? 'Yes' : 'No' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">{{ __('status') }}:</dt>
                    <dd class="font-medium">
                        <span class="px-2 py-1 rounded text-sm {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->is_active ? __('active') : __('inactive') }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>

        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('price') }} & {{ __('stock') }}</h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-gray-600">Purchase {{ __('price') }}:</dt>
                    <dd class="font-medium">{{ number_format($product->purchase_price, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Selling {{ __('price') }}:</dt>
                    <dd class="font-medium">{{ number_format($product->selling_price, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Min {{ __('stock') }}:</dt>
                    <dd class="font-medium">{{ $product->min_stock }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Max {{ __('stock') }}:</dt>
                    <dd class="font-medium">{{ $product->max_stock ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Current {{ __('stock') }}:</dt>
                    <dd class="font-medium">
                        @php $totalStock = $product->stocks->sum('quantity'); @endphp
                        <span class="{{ $totalStock <= $product->min_stock ? 'text-red-600 font-bold' : '' }}">
                            {{ $totalStock }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    @if($product->batches->count() > 0)
    <div class="mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Batches</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiry Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purchase {{ __('price') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Selling {{ __('price') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($product->batches as $batch)
                    <tr>
                        <td class="px-4 py-3">{{ $batch->batch_number }}</td>
                        <td class="px-4 py-3">{{ $batch->expiry_date?->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $batch->quantity }}</td>
                        <td class="px-4 py-3">{{ number_format($batch->purchase_price, 2) }}</td>
                        <td class="px-4 py-3">{{ number_format($batch->selling_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
