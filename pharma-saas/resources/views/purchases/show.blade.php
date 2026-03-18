@extends('layouts.app')

@section('title', __('purchase') . ' #' . $purchase->reference)

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ __('purchase') }} #{{ $purchase->reference }}</h2>
            <p class="text-gray-600">{{ $purchase->purchase_date?->format('d/m/Y H:i') }}</p>
        </div>
        <div class="flex gap-2">
            @if($purchase->status === 'pending')
            <form action="{{ route('purchases.receive', $purchase) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Mark as Received
                </button>
            </form>
            @endif
            <a href="{{ route('purchases.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                {{ __('back') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Details</h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-gray-600">Reference:</dt>
                    <dd class="font-medium">{{ $purchase->reference }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Date:</dt>
                    <dd class="font-medium">{{ $purchase->purchase_date?->format('d/m/Y H:i') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">{{ __('suppliers') }}:</dt>
                    <dd class="font-medium">{{ $purchase->supplier?->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Created by:</dt>
                    <dd class="font-medium">{{ $purchase->user?->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">{{ __('status') }}:</dt>
                    <dd class="font-medium">
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'received' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800'
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded text-sm {{ $statusColors[$purchase->status] ?? 'bg-gray-100' }}">
                            {{ ucfirst($purchase->status) }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>

        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Financial</h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-gray-600">Subtotal:</dt>
                    <dd class="font-medium">{{ number_format($purchase->subtotal, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Discount:</dt>
                    <dd class="font-medium">{{ number_format($purchase->discount_amount, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Total:</dt>
                    <dd class="font-medium font-bold">{{ number_format($purchase->total, 2) }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('purchases') }} Items</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiry</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purchase Price</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($purchase->items as $item)
                    <tr>
                        <td class="px-4 py-3">{{ $item->product?->name }}</td>
                        <td class="px-4 py-3">{{ $item->batch_number }}</td>
                        <td class="px-4 py-3">{{ $item->expiry_date?->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $item->quantity }}</td>
                        <td class="px-4 py-3">{{ number_format($item->purchase_price, 2) }}</td>
                        <td class="px-4 py-3">{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
