@extends('layouts.app')

@section('title', __('warehouses'))

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">{{ __('warehouses') }}</h3>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
            {{ __('add') }} {{ __('warehouses') }}
        </button>
    </div>
    
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('code') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('name') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('address') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('status') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($warehouses as $warehouse)
                    <tr>
                        <td class="px-4 py-3">{{ $warehouse->code }}</td>
                        <td class="px-4 py-3">
                            {{ $warehouse->name }}
                            @if($warehouse->is_default)
                            <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Default</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $warehouse->address }}</td>
                        <td class="px-4 py-3">
                            @if($warehouse->is_active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ __('active') }}</span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ __('inactive') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <button class="text-blue-600 hover:text-blue-900 mr-2">Edit</button>
                            <button class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No warehouses found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $warehouses->links() }}
        </div>
    </div>
</div>
@endsection
