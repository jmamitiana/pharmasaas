@extends('layouts.app')

@section('title', __('transfers'))

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">{{ __('transfers') }}</h3>
        <a href="{{ route('transfers.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
            {{ __('add') }} {{ __('transfers') }}
        </a>
    </div>
    
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">From</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">To</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('date') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('status') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transfers as $transfer)
                    <tr>
                        <td class="px-4 py-3">{{ $transfer->reference }}</td>
                        <td class="px-4 py-3">{{ $transfer->fromWarehouse?->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $transfer->toWarehouse?->name }}</td>
                        <td class="px-4 py-3">{{ $transfer->transfer_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            @if($transfer->status === 'received')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ __('completed') }}</span>
                            @elseif($transfer->status === 'in_transit')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">In Transit</span>
                            @elseif($transfer->status === 'cancelled')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ __('cancelled') }}</span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('pending') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('transfers.show', $transfer) }}" class="text-primary-600 hover:text-primary-900 mr-2">View</a>
                            @if($transfer->status === 'pending')
                            <form action="{{ route('transfers.receive', $transfer) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900">Receive</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No transfers found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $transfers->links() }}
        </div>
    </div>
</div>
@endsection
