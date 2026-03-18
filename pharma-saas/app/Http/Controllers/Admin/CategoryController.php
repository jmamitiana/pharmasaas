<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $categories = Category::where('tenant_id', $tenantId)
            ->with('children')
            ->whereNull('parent_id')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $tenantId = Auth::user()->tenant_id;
        $parentCategories = Category::where('tenant_id', $tenantId)->whereNull('parent_id')->get();
        return view('categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $validated['tenant_id'] = $tenantId;
        $validated['is_active'] = true;

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', __('Category created successfully'));
    }

    public function edit(Category $category)
    {
        $this->authorizeTenant($category);
        $tenantId = Auth::user()->tenant_id;
        $parentCategories = Category::where('tenant_id', $tenantId)->whereNull('parent_id')->where('id', '!=', $category->id)->get();
        return view('categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $this->authorizeTenant($category);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', __('Category updated successfully'));
    }

    public function destroy(Category $category)
    {
        $this->authorizeTenant($category);
        $category->delete();

        return redirect()->route('categories.index')->with('success', __('Category deleted successfully'));
    }

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }
    }
}
