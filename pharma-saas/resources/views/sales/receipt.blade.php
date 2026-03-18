<!DOCTYPE html>
<html>
<head>
    <title>Receipt - {{ $sale->reference }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .receipt { max-width: 300px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; }
        .info { margin-bottom: 20px; }
        .info p { margin: 2px 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 5px; text-align: left; font-size: 12px; }
        .totals { border-top: 1px dashed #000; padding-top: 10px; }
        .totals .row { display: flex; justify-content: space-between; margin: 5px 0; }
        .totals .total { font-weight: bold; font-size: 14px; }
        .footer { text-align: center; margin-top: 20px; font-size: 10px; }
        @media print {
            body { margin: 0; padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h1>Pharmacy Receipt</h1>
        </div>
        
        <div class="info">
            <p><strong>Receipt:</strong> {{ $sale->reference }}</p>
            <p><strong>Date:</strong> {{ $sale->sale_date?->format('d/m/Y H:i') }}</p>
            <p><strong>Cashier:</strong> {{ $sale->user?->name ?? '-' }}</p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product?->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 0) }}</td>
                    <td>{{ number_format($item->subtotal, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="totals">
            <div class="row">
                <span>Subtotal:</span>
                <span>{{ number_format($sale->subtotal, 0) }}</span>
            </div>
            @if($sale->discount_amount > 0)
            <div class="row">
                <span>Discount:</span>
                <span>-{{ number_format($sale->discount_amount, 0) }}</span>
            </div>
            @endif
            <div class="row total">
                <span>TOTAL:</span>
                <span>{{ number_format($sale->total, 0) }}</span>
            </div>
            <div class="row">
                <span>Paid:</span>
                <span>{{ number_format($sale->paid_amount, 0) }}</span>
            </div>
            @if($sale->due_amount > 0)
            <div class="row">
                <span>Change/Due:</span>
                <span>{{ number_format($sale->due_amount, 0) }}</span>
            </div>
            @endif
        </div>
        
        <div class="footer">
            <p>Thank you for your purchase!</p>
            <p>Please keep this receipt</p>
        </div>
    </div>
    
    <script>
        window.print();
    </script>
</body>
</html>
