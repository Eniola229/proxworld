<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/** Manages roles scoped to the "admin" guard only — never touches the "web" guard's roles. */
class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::where('guard_name', 'admin')
            ->withCount('permissions')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.roles.index', ['roles' => $roles]);
    }

    public function create()
    {
        return view('admin.roles.create', [
            'permissions' => Permission::where('guard_name', 'admin')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,NULL,id,guard_name,admin'],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'admin']);
        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('admin.roles.index')->with('success', "Role \"{$role->name}\" created.");
    }

    public function edit(Role $role)
    {
        abort_unless($role->guard_name === 'admin', 404);

        return view('admin.roles.edit', [
            'role' => $role->load('permissions'),
            'permissions' => Permission::where('guard_name', 'admin')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        abort_unless($role->guard_name === 'admin', 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$role->id.',id,guard_name,admin'],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('admin.roles.index')->with('success', "Role \"{$role->name}\" updated.");
    }

    public function destroy(Role $role)
    {
        abort_unless($role->guard_name === 'admin', 404);

        $stillAssigned = \Illuminate\Support\Facades\DB::table('model_has_roles')
            ->where('role_id', $role->id)
            ->exists();

        abort_if($stillAssigned, 422, 'Cannot delete a role that is still assigned to one or more admins. Reassign them first.');

        $role->delete();

        return back()->with('success', 'Role deleted.');
    }
}