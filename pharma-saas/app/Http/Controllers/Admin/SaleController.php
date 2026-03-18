<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $sales = Sale::where('tenant_id', $tenantId)
            ->with(['user', 'customer'])
            ->when($request->date_from, fn($q, $date) => $q->whereDate('sale_date', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->whereDate('sale_date', '<=', $date))
            ->when($request->status, fn($q, $status) => $q->where('payment_status', $status))
            ->latest()
            ->paginate(20);

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $tenantId = Auth::user()->tenant_id;
        $products = Product::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->with('stocks')
            ->get()
            ->map(function ($product) {
                $product->available_stock = $product->stocks->sum('quantity');
                return $product;
            });

        return view('sales.create', compact('products'));
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
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($tenantId, $validated) {
            $sale = Sale::create([
                'tenant_id' => $tenantId,
                'reference' => 'SAL-' . strtoupper(Str::random(8)),
                'user_id' => Auth::id(),
                'sale_date' => now(),
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'total' => 0,
                'paid_amount' => $validated['paid_amount'],
                'due_amount' => 0,
                'payment_status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $subtotal = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                $batch = Batch::where('product_id', $product->id)
                    ->where('quantity', '>', 0)
                    ->whereDate('expiry_date', '>', now())
                    ->orderBy('expiry_date', 'asc')
                    ->first();

                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $itemSubtotal;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'batch_id' => $batch?->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $itemSubtotal,
                ]);

                if ($batch) {
                    $batch->decrement('quantity', $item['quantity']);
                }

                $stock = Stock::where('product_id', $product->id)->first();
                if ($stock) {
                    $stock->decrement('quantity', $item['quantity']);
                }
            }

            $total = $subtotal - ($validated['discount_amount'] ?? 0);
            $dueAmount = max(0, $total - $validated['paid_amount']);
            $paymentStatus = $dueAmount <= 0 ? 'paid' : ($validated['paid_amount'] > 0 ? 'partial' : 'pending');

            $sale->update([
                'subtotal' => $subtotal,
                'total' => $total,
                'due_amount' => $dueAmount,
                'payment_status' => $paymentStatus,
            ]);

            return redirect()->route('sales.index')->with('success', __('Sale created successfully'));
        });
    }

    public function show(Sale $sale)
    {
        $this->authorizeTenant($sale);
        $sale->load(['user', 'items.product', 'items.batch']);
        
        return view('sales.show', compact('sale'));
    }

    public function receipt(Sale $sale)
    {
        $this->authorizeTenant($sale);
        $sale->load(['user', 'items.product', 'items.batch']);
        
        return view('sales.receipt', compact('sale'));
    }

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }
    }
}
