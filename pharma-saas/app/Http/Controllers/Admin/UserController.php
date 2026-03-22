<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        
        $users = User::where('tenant_id', $tenantId)
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            })
            ->with('roles')
            ->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $tenantId = Auth::user()->tenant_id;
        $roles = Role::where('tenant_id', $tenantId)->get();
        
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,id',
        ]);

        $tenantId = Auth::user()->tenant_id;
        
        $user = User::create([
            'tenant_id' => $tenantId,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        $role = Role::findById($request->role, 'web');
        $user->assignRole($role);

        return redirect()->route('users.index')->with('success', __('user_created'));
    }

    public function show(User $user)
    {
        $this->authorizeTenant($user);
        
        $user->load('roles', 'sales', 'purchases', 'transfers');
        
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorizeTenant($user);
        
        $tenantId = Auth::user()->tenant_id;
        $roles = Role::where('tenant_id', $tenantId)->get();
        
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeTenant($user);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
            'role' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        $role = Role::findById($request->role, 'web');
        $user->syncRoles([$role]);

        return redirect()->route('users.index')->with('success', __('user_updated'));
    }

    public function destroy(User $user)
    {
        $this->authorizeTenant($user);
        
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', __('cannot_delete_self'));
        }
        
        $user->delete();
        
        return redirect()->route('users.index')->with('success', __('user_deleted'));
    }

    protected function authorizeTenant(User $user): void
    {
        if ($user->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }
    }
}
