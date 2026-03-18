<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Batch;
use App\Models\Stock;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $purchases = Purchase::where('tenant_id', $tenantId)
            ->with(['supplier', 'user'])
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $tenantId = Auth::user()->tenant_id;
        $suppliers = Supplier::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $products = Product::where('tenant_id', $tenantId)->where('is_active', true)->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        
        $validated = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.purchase_price' => 'required|numeric|min:0',
            'items.*.selling_price' => 'required|numeric|min:0',
            'items.*.batch_number' => 'required|string',
            'items.*.expiry_date' => 'required|date',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($tenantId, $validated) {
            $subtotal = collect($validated['items'])->sum(fn($item) => $item['quantity'] * $item['purchase_price']);
            $total = $subtotal - ($validated['discount_amount'] ?? 0);

            $purchase = Purchase::create([
                'tenant_id' => $tenantId,
                'reference' => 'PUR-' . strtoupper(Str::random(8)),
                'supplier_id' => $validated['supplier_id'],
                'user_id' => Auth::id(),
                'purchase_date' => now(),
                'subtotal' => $subtotal,
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'total' => $total,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $purchaseItem = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'batch_number' => $item['batch_number'],
                    'expiry_date' => $item['expiry_date'],
                    'quantity' => $item['quantity'],
                    'purchase_price' => $item['purchase_price'],
                    'selling_price' => $item['selling_price'],
                    'subtotal' => $item['quantity'] * $item['purchase_price'],
                ]);

                Batch::updateOrCreate(
                    [
                        'product_id' => $item['product_id'],
                        'batch_number' => $item['batch_number'],
                    ],
                    [
                        'tenant_id' => $tenantId,
                        'expiry_date' => $item['expiry_date'],
                        'purchase_price' => $item['purchase_price'],
                        'selling_price' => $item['selling_price'],
                    ]
                )->increment('quantity', $item['quantity']);

                $stock = Stock::firstOrCreate(
                    ['product_id' => $item['product_id'], 'tenant_id' => $tenantId]
                );
                $stock->increment('quantity', $item['quantity']);
            }

            return redirect()->route('purchases.index')->with('success', __('Purchase created successfully'));
        });
    }

    public function show(Purchase $purchase)
    {
        $this->authorizeTenant($purchase);
        $purchase->load(['supplier', 'user', 'items.product']);
        
        return view('purchases.show', compact('purchase'));
    }

    public function receive(Purchase $purchase)
    {
        $this->authorizeTenant($purchase);
        
        $purchase->update(['status' => 'received']);
        
        return redirect()->route('purchases.index')->with('success', __('Purchase marked as received'));
    }

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }
    }
}
