@extends('layouts.app')

@section('title', __('add') . ' ' . __('purchases'))

@section('content')
<form action="{{ route('purchases.store') }}" method="POST">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('purchases') }} Items</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Product</label>
                    <select id="productSelect" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">-- Select Product --</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->purchase_price }}">
                            {{ $product->name }} ({{ $product->code }})
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="itemsTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch #</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiry</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purchase Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Selling Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody" class="bg-white divide-y divide-gray-200">
                            <tr id="emptyRow">
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">No items added yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div>
            <div class="bg-white rounded-lg shadow p-6 mb-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('purchases') }} Details</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('suppliers') }}</label>
                        <select name="supplier_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">-- Select --</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Discount</label>
                        <input type="number" name="discount_amount" step="0.01" min="0" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" value="0">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                    </div>
                    
                    <div class="border-t pt-4">
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total:</span>
                            <span id="totalDisplay">0.00</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('purchases.index') }}" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-center">
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
    const productName = option.dataset.name;
    const defaultPrice = parseFloat(option.dataset.price) || 0;
    
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
            <input type="text" name="items[${itemCount}][batch_number]" required class="w-24 border border-gray-300 rounded px-2 py-1">
        </td>
        <td class="px-4 py-2">
            <input type="date" name="items[${itemCount}][expiry_date]" required class="w-32 border border-gray-300 rounded px-2 py-1">
        </td>
        <td class="px-4 py-2">
            <input type="number" name="items[${itemCount}][quantity]" step="0.01" min="0.01" value="1" class="w-20 border border-gray-300 rounded px-2 py-1 item-qty">
        </td>
        <td class="px-4 py-2">
            <input type="number" name="items[${itemCount}][purchase_price]" step="0.01" min="0" value="${defaultPrice}" class="w-24 border border-gray-300 rounded px-2 py-1 item-price">
        </td>
        <td class="px-4 py-2">
            <input type="number" name="items[${itemCount}][selling_price]" step="0.01" min="0" class="w-24 border border-gray-300 rounded px-2 py-1">
        </td>
        <td class="px-4 py-2 item-subtotal">${defaultPrice.toFixed(2)}</td>
        <td class="px-4 py-2">
            <button type="button" onclick="removeItem(${itemCount})" class="text-red-600 hover:text-red-800">×</button>
        </td>
    `;
    
    tbody.appendChild(row);
    
    row.querySelector('.item-price').addEventListener('input', updateRowTotal);
    row.querySelector('.item-qty').addEventListener('input', updateRowTotal);
    
    updateTotals();
    itemCount++;
    this.value = '';
});

function updateRowTotal(e) {
    const row = e.target.closest('tr');
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
    row.querySelector('.item-subtotal').textContent = (price * qty).toFixed(2);
    updateTotals();
}

function updateTotals() {
    let subtotal = 0;
    document.querySelectorAll('.item-subtotal').forEach(el => {
        subtotal += parseFloat(el.textContent) || 0;
    });
    
    const discount = parseFloat(document.querySelector('input[name="discount_amount"]').value) || 0;
    const total = subtotal - discount;
    
    document.getElementById('totalDisplay').textContent = total.toFixed(2);
}

function removeItem(id) {
    document.getElementById('itemRow_' + id).remove();
    updateTotals();
    
    const tbody = document.getElementById('itemsBody');
    if (tbody.children.length === 0) {
        tbody.innerHTML = '<tr id="emptyRow"><td colspan="8" class="px-4 py-8 text-center text-gray-500">No items added yet.</td></tr>';
    }
}

document.querySelector('input[name="discount_amount"]').addEventListener('input', updateTotals);
</script>
@endsection
