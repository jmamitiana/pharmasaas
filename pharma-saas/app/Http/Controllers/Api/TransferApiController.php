<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Models\TransferItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TransferApiController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        
        $transfers = Transfer::where('tenant_id', $tenantId)
            ->with(['fromWarehouse', 'toWarehouse', 'user', 'items.product'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->orderBy('transfer_date', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json($transfers);
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

        return DB::transaction(function () use ($tenantId, $validated) {
            $transfer = Transfer::create([
                'tenant_id' => $tenantId,
                'reference' => 'TRF-' . strtoupper(Str::random(8)),
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

            return response()->json($transfer->load('items'), 201);
        });
    }

    public function show(Transfer $transfer)
    {
        if ($transfer->tenant_id !== Auth::user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($transfer->load(['fromWarehouse', 'toWarehouse', 'user', 'items.product']));
    }

    public function receive(Transfer $transfer)
    {
        if ($transfer->tenant_id !== Auth::user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $transfer->update(['status' => 'received']);

        return response()->json($transfer);
    }
}
