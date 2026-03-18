@extends('layouts.app')

@section('title', __('add') . ' ' . __('sales'))

@section('content')
<form action="{{ route('sales.store') }}" method="POST" id="saleForm">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('sales') }} Items</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Products</label>
                    <select id="productSelect" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">-- Select Product --</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}" data-stock="{{ $product->available_stock }}">
                            {{ $product->name }} ({{ $product->code }}) - Stock: {{ $product->available_stock }} - {{ number_format($product->selling_price, 2) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="itemsTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody" class="bg-white divide-y divide-gray-200">
                            <tr id="emptyRow">
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">No items added yet. Select a product above.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Details</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method *</label>
                        <select name="payment_method" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="mvola">MVola</option>
                            <option value="orange_money">Orange Money</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                        <input type="text" id="subtotal" readonly class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50" value="0.00">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Discount</label>
                        <input type="number" name="discount_amount" id="discountAmount" step="0.01" min="0" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" value="0">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total</label>
                        <input type="text" id="total" readonly class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 font-bold text-lg" value="0.00">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Paid Amount *</label>
                        <input type="number" name="paid_amount" id="paidAmount" step="0.01" min="0" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" value="0">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 flex gap-2">
                <a href="{{ route('sales.index') }}" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-center">
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
    const price = parseFloat(option.dataset.price);
    const stock = parseInt(option.dataset.stock);
    const productName = option.text.split(' - ')[0];
    
    if (stock <= 0) {
        alert('Product out of stock');
        return;
    }
    
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
            <input type="number" name="items[${itemCount}][unit_price]" step="0.01" min="0" value="${price}" class="w-24 border border-gray-300 rounded px-2 py-1 item-price">
        </td>
        <td class="px-4 py-2">
            <input type="number" name="items[${itemCount}][quantity]" step="0.01" min="0.01" max="${stock}" value="1" class="w-20 border border-gray-300 rounded px-2 py-1 item-qty">
        </td>
        <td class="px-4 py-2 item-subtotal">${price.toFixed(2)}</td>
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
    
    const discount = parseFloat(document.getElementById('discountAmount').value) || 0;
    const total = subtotal - discount;
    
    document.getElementById('subtotal').value = subtotal.toFixed(2);
    document.getElementById('total').value = total.toFixed(2);
    document.getElementById('paidAmount').value = total.toFixed(2);
}

function removeItem(id) {
    document.getElementById('itemRow_' + id).remove();
    updateTotals();
    
    const tbody = document.getElementById('itemsBody');
    if (tbody.children.length === 0) {
        tbody.innerHTML = '<tr id="emptyRow"><td colspan="5" class="px-4 py-8 text-center text-gray-500">No items added yet.</td></tr>';
    }
}

document.getElementById('discountAmount').addEventListener('input', updateTotals);
</script>
@endsection
