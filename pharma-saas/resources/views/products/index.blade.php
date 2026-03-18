@extends('layouts.app')

@section('title', __('products'))

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">{{ __('products') }}</h3>
        <a href="{{ route('products.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
            {{ __('add') }} {{ __('products') }}
        </a>
    </div>
    
    <div class="p-6">
        <form method="GET" class="mb-4 flex gap-4">
            <input type="text" name="search" placeholder="{{ __('search') }}..." value="{{ request('search') }}" 
                class="border border-gray-300 rounded-md px-3 py-2 w-64">
            <select name="category_id" class="border border-gray-300 rounded-md px-3 py-2">
                <option value="">{{ __('all') }} {{ __('categories') }}</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                {{ __('filter') }}
            </button>
        </form>
        
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('code') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('name') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('category') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('stock') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('price') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('status') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($products as $product)
                    <tr>
                        <td class="px-4 py-3">{{ $product->code }}</td>
                        <td class="px-4 py-3">{{ $product->name }}</td>
                        <td class="px-4 py-3">{{ $product->category?->name }}</td>
                        <td class="px-4 py-3">
                            @php $stock = $product->stocks->sum('quantity') @endphp
                            @if($stock <= $product->min_stock)
                            <span class="text-red-600 font-medium">{{ $stock }}</span>
                            @else
                            <span class="text-green-600">{{ $stock }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ number_format($product->selling_price, 0, ',', ' ') }} MGA</td>
                        <td class="px-4 py-3">
                            @if($product->is_active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ __('active') }}</span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ __('inactive') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('products.show', $product) }}" class="text-primary-600 hover:text-primary-900 mr-2">View</a>
                            <a href="{{ route('products.edit', $product) }}" class="text-blue-600 hover:text-blue-900 mr-2">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
