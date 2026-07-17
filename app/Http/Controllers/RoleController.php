<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('superadmin.roles', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles',
        ], [
            'name.required' => 'Please enter a name for the new role.',
            'name.unique' => 'This role name already exists.',
        ]);

        Role::create(['name' => $request->name]);
        return redirect()->route('superadmin.roles.index')->with('success', 'Role created successfully!');
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
        ], [
            'name.required' => 'Please enter a new name for this role.',
            'name.unique' => 'This role name is already taken.',
        ]);

        $role->update(['name' => $request->name]);
        return redirect()->route('superadmin.roles.index')->with('success', 'Role updated successfully!');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('superadmin.roles.index')->with('success', 'Role deleted successfully!');
    }
}
