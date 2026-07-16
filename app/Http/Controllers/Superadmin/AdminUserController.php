<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = Admin::all();
        return view('superadmin.admins', compact('admins'));
    }

    public function store(Request $request)
    {
        // ✅ SECURITY CHECK: Only Superadmin can create admins
        $user = auth()->guard('admin')->user();
        if ($user->role !== 'Superadmin') {
            abort(403, 'Unauthorized: Only Super Admin can create new admins.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:4',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|string',
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'status' => $request->status,
        ]);

        return redirect()->route('superadmin.admins.index')->with('success', 'Admin created successfully!');
    }

    public function update(Request $request)
    {
        // ✅ SECURITY CHECK: Only Superadmin can update admins
        $user = auth()->guard('admin')->user();
        if ($user->role !== 'Superadmin') {
            abort(403, 'Unauthorized: Only Super Admin can update admins.');
        }

        $admin = Admin::findOrFail($request->id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $request->id,
            'role' => 'required|string',
            'status' => 'required|string',
        ]);

        $data = $request->only(['name', 'email', 'role', 'status']);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:4']);
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->route('superadmin.admins.index')->with('success', 'Admin updated successfully!');
    }

    public function destroy($id)
    {
        // ✅ SECURITY CHECK: Only Superadmin can delete admins
        $user = auth()->guard('admin')->user();
        if ($user->role !== 'Superadmin') {
            abort(403, 'Unauthorized: Only Super Admin can delete admins.');
        }

        $admin = Admin::findOrFail($id);

        // Prevent Super Admin from deleting themselves
        if (auth()->guard('admin')->id() == $admin->id) {
            return redirect()->back()->with('error', 'You cannot delete yourself!');
        }

        $admin->delete();
        return redirect()->route('superadmin.admins.index')->with('success', 'Admin deleted successfully!');
    }
}
