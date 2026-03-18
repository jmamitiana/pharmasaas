<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        
        $stocks = Stock::where('tenant_id', $tenantId)
            ->with(['product.category', 'batch'])
            ->when($request->low_stock, fn($q) => $q->whereRaw('quantity <= (SELECT min_stock FROM products WHERE products.id = stocks.product_id)'))
            ->paginate(20);

        return view('stock.index', compact('stocks'));
    }

    public function refresh()
    {
        $tenantId = Auth::user()->tenant_id;
        
        $products = Product::where('tenant_id', $tenantId)->with('stocks')->get();
        
        $data = $products->map(function ($product) {
            $totalStock = $product->stocks->sum('quantity');
            return [
                'id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'quantity' => $totalStock,
                'min_stock' => $product->min_stock,
                'is_low_stock' => $totalStock <= $product->min_stock,
            ];
        });

        return response()->json($data);
    }
}
