<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Batch;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $products = Product::where('tenant_id', $tenantId)
            ->with(['category', 'supplier', 'stocks'])
            ->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"))
            ->when($request->category_id, fn($q, $id) => $q->where('category_id', $id))
            ->when($request->status, fn($q, $status) => $q->where('is_active', $status === 'active'))
            ->paginate(20);

        $categories = Category::where('tenant_id', $tenantId)->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $tenantId = Auth::user()->tenant_id;
        $categories = Category::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $suppliers = Supplier::where('tenant_id', $tenantId)->where('is_active', true)->get();

        return view('products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:products|max:50',
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'max_stock' => 'nullable|numeric|min:0',
            'dosage' => 'nullable|string|max:100',
            'form' => 'nullable|string|max:100',
            'requires_prescription' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['tenant_id'] = $tenantId;
        $validated['code'] = $validated['code'] ?? strtoupper(Str::random(8));
        
        $product = Product::create($validated);

        Stock::create([
            'tenant_id' => $tenantId,
            'product_id' => $product->id,
            'quantity' => 0,
        ]);

        return redirect()->route('products.index')->with('success', __('Product created successfully'));
    }

    public function show(Product $product)
    {
        $this->authorizeTenant($product);
        
        $product->load(['category', 'supplier', 'batches', 'stocks']);
        
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorizeTenant($product);
        
        $tenantId = Auth::user()->tenant_id;
        $categories = Category::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $suppliers = Supplier::where('tenant_id', $tenantId)->where('is_active', true)->get();

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeTenant($product);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:products,code,' . $product->id . '|max:50',
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'max_stock' => 'nullable|numeric|min:0',
            'dosage' => 'nullable|string|max:100',
            'form' => 'nullable|string|max:100',
            'requires_prescription' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', __('Product updated successfully'));
    }

    public function destroy(Product $product)
    {
        $this->authorizeTenant($product);
        
        $product->delete();

        return redirect()->route('products.index')->with('success', __('Product deleted successfully'));
    }

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }
    }
}
