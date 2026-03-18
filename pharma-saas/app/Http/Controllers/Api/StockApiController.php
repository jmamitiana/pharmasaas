<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockApiController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        
        $stocks = Stock::where('tenant_id', $tenantId)
            ->with(['product.category', 'batch'])
            ->when($request->product_id, fn($q, $id) => $q->where('product_id', $id))
            ->when($request->low_stock, fn($q) => $q->whereRaw('(SELECT SUM(quantity) FROM stocks WHERE stocks.product_id = stocks.product_id) <= (SELECT min_stock FROM products WHERE products.id = stocks.product_id)'))
            ->paginate($request->per_page ?? 20);

        return response()->json($stocks);
    }

    public function show(Product $product)
    {
        if ($product->tenant_id !== Auth::user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stocks = Stock::where('product_id', $product->id)
            ->with('batch')
            ->get();

        return response()->json([
            'product' => $product,
            'stocks' => $stocks,
            'total_quantity' => $stocks->sum('quantity'),
            'available_quantity' => $stocks->sum('quantity') - $stocks->sum('reserved_quantity'),
        ]);
    }

    public function refresh()
    {
        $tenantId = Auth::user()->tenant_id;
        
        $products = Product::where('tenant_id', $tenantId)->get();
        
        $data = $products->map(function ($product) {
            $stocks = $product->stocks;
            return [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_code' => $product->code,
                'total_quantity' => $stocks->sum('quantity'),
                'available_quantity' => $stocks->sum('quantity') - $stocks->sum('reserved_quantity'),
                'reserved_quantity' => $stocks->sum('reserved_quantity'),
                'min_stock' => $product->min_stock,
                'is_low_stock' => $stocks->sum('quantity') <= $product->min_stock,
                'is_out_of_stock' => $stocks->sum('quantity') <= 0,
            ];
        });

        return response()->json($data);
    }
}
