<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $warehouses = Warehouse::where('tenant_id', $tenantId)->paginate(20);

        return view('warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        return view('warehouses.create');
    }

    public function store(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        $validated['tenant_id'] = $tenantId;
        $validated['is_active'] = true;

        if ($request->is_default) {
            Warehouse::where('tenant_id', $tenantId)->update(['is_default' => false]);
            $validated['is_default'] = true;
        }

        Warehouse::create($validated);

        return redirect()->route('warehouses.index')->with('success', __('Warehouse created successfully'));
    }

    public function edit(Warehouse $warehouse)
    {
        $this->authorizeTenant($warehouse);
        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $this->authorizeTenant($warehouse);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($request->is_default && !$warehouse->is_default) {
            Warehouse::where('tenant_id', Auth::user()->tenant_id)->update(['is_default' => false]);
            $validated['is_default'] = true;
        }

        $warehouse->update($validated);

        return redirect()->route('warehouses.index')->with('success', __('Warehouse updated successfully'));
    }

    public function destroy(Warehouse $warehouse)
    {
        $this->authorizeTenant($warehouse);
        
        if ($warehouse->is_default) {
            return redirect()->route('warehouses.index')->with('error', __('Cannot delete default warehouse'));
        }

        $warehouse->delete();

        return redirect()->route('warehouses.index')->with('success', __('Warehouse deleted successfully'));
    }

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }
    }
}
