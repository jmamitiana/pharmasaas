@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Today's Sales -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-primary-100 rounded-full">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">{{ __('sales_today') }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($todaySales, 0, ',', ' ') }} MGA</p>
            </div>
        </div>
    </div>
    
    <!-- Monthly Sales -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-full">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">{{ __('sales_month') }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($monthlySales, 0, ',', ' ') }} MGA</p>
            </div>
        </div>
    </div>
    
    <!-- Total Products -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-full">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">{{ __('total_products') }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalProducts }}</p>
            </div>
        </div>
    </div>
    
    <!-- Low Stock -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-red-100 rounded-full">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">{{ __('low_stock_products') }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ $lowStockProducts }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Stock Risk Section -->
@if(count($stockRisks) > 0)
<div class="bg-white rounded-lg shadow mb-8">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">{{ __('stock_risk') }}</h3>
    </div>
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('products') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('stock') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('sales') }} / {{ __('day') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('days_left') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($stockRisks as $risk)
                    <tr>
                        <td class="px-4 py-3">{{ $risk['product_name'] }}</td>
                        <td class="px-4 py-3">{{ $risk['current_stock'] }}</td>
                        <td class="px-4 py-3">{{ number_format($risk['avg_daily_sales'], 2) }}</td>
                        <td class="px-4 py-3">{{ $risk['days_until_stockout'] }}</td>
                        <td class="px-4 py-3">
                            @if($risk['risk_level'] === 'critical')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ __('critical') }}</span>
                            @elseif($risk['risk_level'] === 'high')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">{{ __('high') }}</span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('medium') }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Recent Sales -->
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">{{ __('sales') }} ({{ __('recent') }})</h3>
    </div>
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('date') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('total') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentSales as $sale)
                    <tr>
                        <td class="px-4 py-3">{{ $sale->reference }}</td>
                        <td class="px-4 py-3">{{ $sale->sale_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ number_format($sale->total, 0, ',', ' ') }} MGA</td>
                        <td class="px-4 py-3">
                            @if($sale->payment_status === 'paid')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ __('paid') }}</span>
                            @elseif($sale->payment_status === 'partial')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('partial') }}</span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ __('pending') }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
