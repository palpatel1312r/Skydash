<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleAssignmentController extends Controller
{
    public function index()
    {
        $roles = Role::all();

        // ✅ Get all admins EXCEPT the currently logged-in Super Admin
        $currentAdminId = Auth::guard('admin')->id();
        $admins = Admin::where('id', '!=', $currentAdminId)->get();

        // ✅ Get all customers
        $customers = Customer::all();

        return view('superadmin.assign_roles', compact('roles', 'admins', 'customers'));
    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'user_type' => 'required|in:admin,customer',
            'user_id' => 'required|integer',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($request->user_type === 'admin') {
            $user = Admin::findOrFail($request->user_id);
        } else {
            $user = Customer::findOrFail($request->user_id);
        }

        $user->role_id = $request->role_id;
        $user->save();

        return redirect()->back()->with('success', 'Role assigned successfully!');
    }
}
