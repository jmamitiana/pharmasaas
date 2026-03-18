<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SaleApiController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        
        $sales = Sale::where('tenant_id', $tenantId)
            ->with(['user', 'items.product'])
            ->when($request->date_from, fn($q, $d) => $q->whereDate('sale_date', '>=', $d))
            ->when($request->date_to, fn($q, $d) => $q->whereDate('sale_date', '<=', $d))
            ->orderBy('sale_date', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json($sales);
    }

    public function store(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,mvola,orange_money,bank_transfer',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($tenantId, $validated) {
            $sale = Sale::create([
                'tenant_id' => $tenantId,
                'reference' => 'SAL-' . strtoupper(Str::random(8)),
                'user_id' => Auth::id(),
                'sale_date' => now(),
                'subtotal' => 0,
                'total' => 0,
                'paid_amount' => $validated['paid_amount'],
                'payment_status' => 'pending',
                'payment_method' => $validated['payment_method'],
            ]);

            $subtotal = 0;

            foreach ($validated['items'] as $item) {
                $batch = Batch::where('product_id', $item['product_id'])
                    ->where('quantity', '>', 0)
                    ->whereDate('expiry_date', '>', now())
                    ->orderBy('expiry_date', 'asc')
                    ->first();

                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $itemSubtotal;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'batch_id' => $batch?->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $itemSubtotal,
                ]);

                if ($batch) {
                    $batch->decrement('quantity', $item['quantity']);
                }

                $stock = Stock::where('product_id', $item['product_id'])->first();
                if ($stock) {
                    $stock->decrement('quantity', $item['quantity']);
                }
            }

            $total = $subtotal;
            $dueAmount = max(0, $total - $validated['paid_amount']);
            $paymentStatus = $dueAmount <= 0 ? 'paid' : ($validated['paid_amount'] > 0 ? 'partial' : 'pending');

            $sale->update([
                'subtotal' => $subtotal,
                'total' => $total,
                'due_amount' => $dueAmount,
                'payment_status' => $paymentStatus,
            ]);

            return response()->json($sale->load('items'), 201);
        });
    }

    public function show(Sale $sale)
    {
        if ($sale->tenant_id !== Auth::user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($sale->load(['user', 'items.product', 'items.batch']));
    }
}
