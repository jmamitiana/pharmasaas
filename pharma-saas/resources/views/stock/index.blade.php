@extends('layouts.app')

@section('title', __('stock'))

@push('scripts')
<script>
    const REFRESH_INTERVAL = {{ config('app.stock_refresh_interval', 300000) }};
    
    function refreshStock() {
        fetch('{{ route("stock.refresh") }}', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Stock refreshed:', data.length, 'products');
            showNotification('Stock refreshed successfully', 'success');
        })
        .catch(error => {
            console.error('Error refreshing stock:', error);
            showNotification('Error refreshing stock', 'error');
        });
    }
    
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    setInterval(refreshStock, REFRESH_INTERVAL);
</script>
@endpush

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">{{ __('stock') }} Management</h3>
        <button onclick="refreshStock()" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
            {{ __('refresh') }}
        </button>
    </div>
    
    <div class="p-6">
        <form method="GET" class="mb-4 flex gap-4">
            <label class="flex items-center">
                <input type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }} class="mr-2">
                {{ __('low_stock') }} only
            </label>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                {{ __('filter') }}
            </button>
        </form>
        
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('products') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('category') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('batch') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('quantity') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Min {{ __('stock') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($stocks as $stock)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $stock->product?->name }}</div>
                            <div class="text-sm text-gray-500">{{ $stock->product?->code }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $stock->product?->category?->name }}</td>
                        <td class="px-4 py-3">
                            @if($stock->batch)
                            <div>{{ $stock->batch->batch_number }}</div>
                            <div class="text-xs text-gray-500">Exp: {{ $stock->batch->expiry_date->format('d/m/Y') }}</div>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $stock->quantity }}</td>
                        <td class="px-4 py-3">{{ $stock->product?->min_stock }}</td>
                        <td class="px-4 py-3">
                            @if($stock->quantity <= ($stock->product?->min_stock ?? 0))
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ __('low_stock') }}</span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">OK</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No stock data found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $stocks->links() }}
        </div>
    </div>
</div>
@endsection
