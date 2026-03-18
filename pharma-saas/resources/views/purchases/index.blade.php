@extends('layouts.app')

@section('title', __('purchases'))

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">{{ __('purchases') }}</h3>
        <a href="{{ route('purchases.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
            {{ __('add') }} {{ __('purchases') }}
        </a>
    </div>
    
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('suppliers') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('date') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('total') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('status') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($purchases as $purchase)
                    <tr>
                        <td class="px-4 py-3">{{ $purchase->reference }}</td>
                        <td class="px-4 py-3">{{ $purchase->supplier?->name }}</td>
                        <td class="px-4 py-3">{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-medium">{{ number_format($purchase->total, 0, ',', ' ') }} MGA</td>
                        <td class="px-4 py-3">
                            @if($purchase->status === 'received')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ __('completed') }}</span>
                            @elseif($purchase->status === 'ordered')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Ordered</span>
                            @elseif($purchase->status === 'cancelled')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ __('cancelled') }}</span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('pending') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('purchases.show', $purchase) }}" class="text-primary-600 hover:text-primary-900 mr-2">View</a>
                            @if($purchase->status === 'pending')
                            <form action="{{ route('purchases.receive', $purchase) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900">Receive</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $purchases->links() }}
        </div>
    </div>
</div>
@endsection
