<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Models\TransferItem;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $transfers = Transfer::where('tenant_id', $tenantId)
            ->with(['fromWarehouse', 'toWarehouse', 'user'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20);

        return view('transfers.index', compact('transfers'));
    }

    public function create()
    {
        $tenantId = Auth::user()->tenant_id;
        $warehouses = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->get();

        return view('transfers.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        
        $validated = $request->validate([
            'from_warehouse_id' => 'nullable|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $transfer = Transfer::create([
            'tenant_id' => $tenantId,
            'reference' => 'TRF-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'from_warehouse_id' => $validated['from_warehouse_id'],
            'to_warehouse_id' => $validated['to_warehouse_id'],
            'user_id' => Auth::id(),
            'transfer_date' => now(),
            'status' => 'pending',
        ]);

        foreach ($validated['items'] as $item) {
            TransferItem::create([
                'transfer_id' => $transfer->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        return redirect()->route('transfers.index')->with('success', __('Transfer created successfully'));
    }

    public function show(Transfer $transfer)
    {
        $this->authorizeTenant($transfer);
        $transfer->load(['fromWarehouse', 'toWarehouse', 'user', 'items.product']);
        
        return view('transfers.show', compact('transfer'));
    }

    public function receive(Transfer $transfer)
    {
        $this->authorizeTenant($transfer);
        
        $transfer->update(['status' => 'received']);
        
        return redirect()->route('transfers.index')->with('success', __('Transfer marked as received'));
    }

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }
    }
}
