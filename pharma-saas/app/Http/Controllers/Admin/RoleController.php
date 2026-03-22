<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected array $defaultPermissions = [
        'manage_products',
        'manage_stock',
        'manage_sales',
        'manage_transfers',
        'manage_purchases',
        'manage_users',
        'manage_settings',
    ];

    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        
        $roles = Role::where('tenant_id', $tenantId)
            ->with('permissions')
            ->paginate(20);

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::where('tenant_id', Auth::user()->tenant_id)->get();
        
        if ($permissions->isEmpty()) {
            $permissions = $this->seedDefaultPermissions();
        }

        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,NULL,id,tenant_id,' . Auth::user()->tenant_id,
            'permissions' => 'required|array',
        ]);

        $tenantId = Auth::user()->tenant_id;
        
        $role = Role::create([
            'name' => $request->name,
            'tenant_id' => $tenantId,
            'guard_name' => 'web',
        ]);

        $permissions = Permission::whereIn('id', $request->permissions)
            ->where('tenant_id', $tenantId)
            ->get();
        
        $role->syncPermissions($permissions);

        return redirect()->route('roles.index')->with('success', __('role_created'));
    }

    public function show(Role $role)
    {
        $this->authorizeTenant($role);
        
        $role->load('permissions', 'users');
        
        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $this->authorizeTenant($role);
        
        $tenantId = Auth::user()->tenant_id;
        
        $allPermissions = Permission::where('tenant_id', $tenantId)->get();
        
        if ($allPermissions->isEmpty()) {
            $allPermissions = $this->seedDefaultPermissions();
        }

        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('roles.edit', compact('role', 'allPermissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $this->authorizeTenant($role);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id . ',id,tenant_id,' . Auth::user()->tenant_id,
            'permissions' => 'required|array',
        ]);

        $tenantId = Auth::user()->tenant_id;
        
        $role->update([
            'name' => $request->name,
        ]);

        $permissions = Permission::whereIn('id', $request->permissions)
            ->where('tenant_id', $tenantId)
            ->get();
        
        $role->syncPermissions($permissions);

        return redirect()->route('roles.index')->with('success', __('role_updated'));
    }

    public function destroy(Role $role)
    {
        $this->authorizeTenant($role);
        
        if ($role->name === 'admin') {
            return redirect()->route('roles.index')->with('error', __('cannot_delete_admin_role'));
        }
        
        $role->delete();
        
        return redirect()->route('roles.index')->with('success', __('role_deleted'));
    }

    public function permissions()
    {
        $tenantId = Auth::user()->tenant_id;
        
        $permissions = Permission::where('tenant_id', $tenantId)->get();
        
        if ($permissions->isEmpty()) {
            $permissions = $this->seedDefaultPermissions();
        }

        return view('permissions.index', compact('permissions'));
    }

    protected function seedDefaultPermissions(): \Illuminate\Database\Eloquent\Collection
    {
        $tenantId = Auth::user()->tenant_id;
        
        $permissions = [];
        
        foreach ($this->defaultPermissions as $permission) {
            $permissions[] = Permission::firstOrCreate(
                ['name' => $permission, 'tenant_id' => $tenantId, 'guard_name' => 'web'],
                ['name' => $permission, 'tenant_id' => $tenantId, 'guard_name' => 'web']
            );
        }
        
        return collect($permissions);
    }

    protected function authorizeTenant(Role $role): void
    {
        if ($role->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }
    }
}
