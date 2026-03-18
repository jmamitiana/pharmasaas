@extends('layouts.app')

@section('title', __('sale') . ' #' . $sale->reference)

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ __('sale') }} #{{ $sale->reference }}</h2>
            <p class="text-gray-600">{{ $sale->sale_date?->format('d/m/Y H:i') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('sales.receipt', $sale) }}" target="_blank" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                Print Receipt
            </a>
            <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
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
                    <dd class="font-medium">{{ $sale->reference }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Date:</dt>
                    <dd class="font-medium">{{ $sale->sale_date?->format('d/m/Y H:i') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Cashier:</dt>
                    <dd class="font-medium">{{ $sale->user?->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">{{ __('status') }}:</dt>
                    <dd class="font-medium">
                        @php
                            $statusColors = [
                                'paid' => 'bg-green-100 text-green-800',
                                'partial' => 'bg-yellow-100 text-yellow-800',
                                'pending' => 'bg-red-100 text-red-800'
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded text-sm {{ $statusColors[$sale->payment_status] ?? 'bg-gray-100' }}">
                            {{ ucfirst($sale->payment_status) }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>

        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment</h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-gray-600">Payment Method:</dt>
                    <dd class="font-medium">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Subtotal:</dt>
                    <dd class="font-medium">{{ number_format($sale->subtotal, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Discount:</dt>
                    <dd class="font-medium">{{ number_format($sale->discount_amount, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Total:</dt>
                    <dd class="font-medium font-bold">{{ number_format($sale->total, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Paid:</dt>
                    <dd class="font-medium">{{ number_format($sale->paid_amount, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Due:</dt>
                    <dd class="font-medium">{{ number_format($sale->due_amount, 2) }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('sales') }} Items</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($sale->items as $item)
                    <tr>
                        <td class="px-4 py-3">{{ $item->product?->name }}</td>
                        <td class="px-4 py-3">{{ $item->batch?->batch_number ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $item->quantity }}</td>
                        <td class="px-4 py-3">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-4 py-3">{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
