<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductApiController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        
        $products = Product::where('tenant_id', $tenantId)
            ->with(['category', 'stocks', 'batches'])
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%"))
            ->when($request->category_id, fn($q, $id) => $q->where('category_id', $id))
            ->when($request->low_stock, fn($q) => $q->whereRaw('(SELECT SUM(quantity) FROM stocks WHERE stocks.product_id = products.id) <= products.min_stock'))
            ->paginate($request->per_page ?? 20);

        return response()->json($products);
    }

    public function show(Product $product)
    {
        if ($product->tenant_id !== Auth::user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $product->load(['category', 'supplier', 'stocks', 'batches']);

        return response()->json($product);
    }

    public function search(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        
        $products = Product::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->q}%")
                  ->orWhere('code', 'like', "%{$request->q}%")
                  ->orWhere('barcode', 'like', "%{$request->q}%");
            })
            ->with('stocks')
            ->limit(20)
            ->get();

        return response()->json($products);
    }
}
