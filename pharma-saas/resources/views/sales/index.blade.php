@extends('layouts.app')

@section('title', __('sales'))

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">{{ __('sales') }}</h3>
        <a href="{{ route('sales.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
            {{ __('add') }} {{ __('sales') }}
        </a>
    </div>
    
    <div class="p-6">
        <form method="GET" class="mb-4 flex gap-4">
            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                class="border border-gray-300 rounded-md px-3 py-2">
            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                class="border border-gray-300 rounded-md px-3 py-2">
            <select name="status" class="border border-gray-300 rounded-md px-3 py-2">
                <option value="">{{ __('all') }}</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                {{ __('filter') }}
            </button>
        </form>
        
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('date') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('total') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Paid</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('status') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($sales as $sale)
                    <tr>
                        <td class="px-4 py-3">{{ $sale->reference }}</td>
                        <td class="px-4 py-3">{{ $sale->sale_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-medium">{{ number_format($sale->total, 0, ',', ' ') }} MGA</td>
                        <td class="px-4 py-3">{{ number_format($sale->paid_amount, 0, ',', ' ') }} MGA</td>
                        <td class="px-4 py-3">
                            @if($sale->payment_status === 'paid')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                            @elseif($sale->payment_status === 'partial')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Partial</span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('sales.show', $sale) }}" class="text-primary-600 hover:text-primary-900 mr-2">View</a>
                            <a href="{{ route('sales.receipt', $sale) }}" class="text-blue-600 hover:text-blue-900">Receipt</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $sales->links() }}
        </div>
    </div>
</div>
@endsection
