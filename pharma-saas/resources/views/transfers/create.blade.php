@extends('layouts.app')

@section('title', __('add') . ' ' . __('transfers'))

@section('content')
<form action="{{ route('transfers.store') }}" method="POST">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('transfers') }} Items</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Product</label>
                    <select id="productSelect" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">-- Select Product --</option>
                    </select>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="itemsTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody" class="bg-white divide-y divide-gray-200">
                            <tr id="emptyRow">
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500">No items added yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div>
            <div class="bg-white rounded-lg shadow p-6 mb-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('transfers') }} Details</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From {{ __('warehouses') }}</label>
                        <select name="from_warehouse_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">Main Stock</option>
                            @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">To {{ __('warehouses') }} *</label>
                        <select name="to_warehouse_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">-- Select --</option>
                            @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('transfers.index') }}" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-center">
                    {{ __('cancel') }}
                </a>
                <button type="submit" class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                    {{ __('save') }}
                </button>
            </div>
        </div>
    </div>
</form>

<script>
let itemCount = 0;

document.getElementById('productSelect').addEventListener('change', function() {
    if (!this.value) return;
    
    const option = this.options[this.selectedIndex];
    const productId = this.value;
    const productName = option.text;
    
    const tbody = document.getElementById('itemsBody');
    const emptyRow = document.getElementById('emptyRow');
    if (emptyRow) emptyRow.remove();
    
    const row = document.createElement('tr');
    row.id = 'itemRow_' + itemCount;
    row.innerHTML = `
        <td class="px-4 py-2">
            <input type="hidden" name="items[${itemCount}][product_id]" value="${productId}">
            ${productName}
        </td>
        <td class="px-4 py-2">
            <input type="number" name="items[${itemCount}][quantity]" step="0.01" min="0.01" value="1" class="w-24 border border-gray-300 rounded px-2 py-1">
        </td>
        <td class="px-4 py-2">
            <button type="button" onclick="removeItem(${itemCount})" class="text-red-600 hover:text-red-800">×</button>
        </td>
    `;
    
    tbody.appendChild(row);
    itemCount++;
    this.value = '';
});

function removeItem(id) {
    document.getElementById('itemRow_' + id).remove();
    
    const tbody = document.getElementById('itemsBody');
    if (tbody.children.length === 0) {
        tbody.innerHTML = '<tr id="emptyRow"><td colspan="3" class="px-4 py-8 text-center text-gray-500">No items added yet.</td></tr>';
    }
}
</script>
@endsection
