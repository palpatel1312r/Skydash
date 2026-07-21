<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
  public function create()
  {
    return view('Dashboard.User_form');
  }

  public function edit($id, $guard)
  {
    if ($guard === 'admin') {
      $user = Admin::findOrFail($id);
      $user['guard'] = 'admin';
    } elseif ($guard === 'customer') {
      $user = Customer::findOrFail($id);
      $user['guard'] = 'customer';
      $user['name'] = $user['fullname'];
    } else {
      return redirect()->back()->with('error', 'Invalid user type provided.');
    }

    return view('Dashboard.User_form', compact('user'));
  }

  public function index()
  {
    $roles = Role::pluck('name', 'id');

    $admins = Admin::all()->map(function ($admin) use ($roles) {
      $roleName = $roles[$admin->role_id] ?? 'Admin';
      return [
        'id' => $admin->id,
        'name' => $admin->name,
        'email' => $admin->email,
        'role_name' => $roleName,
        'guard' => 'admin',
        'created_at' => $admin->created_at,
      ];
    });

    $customers = Customer::all()->map(function ($customer) use ($roles) {
      $roleName = $roles[$customer->role_id] ?? 'Customer';
      return [
        'id' => $customer->id,
        'name' => $customer->fullname,
        'email' => $customer->email,
        'role_name' => $roleName,
        'guard' => 'customer',
        'created_at' => $customer->created_at,
      ];
    });

    $users = $admins->merge($customers)->sortByDesc('created_at');

    return view('Dashboard.User', compact('users'));
  }

  public function store(Request $request)
  {
    $messages = [
      'name.required' => 'Please enter the full name of the user.',
      'email.required' => 'We need an email address to create the account.',
      'email.email' => 'Please enter a valid email format.',
      'email.unique' => 'This email address is already registered.',
      'email.regex' => 'Email must be a valid @gmail.com address.',
      'password.required' => 'A password is required.',
      'password.min' => 'The password must be at least 4 characters long.',
      'password.confirmed' => 'The password confirmation does not match.',
      'user_type.required' => 'Please select if this user is an Admin or Customer.',
      'role_id.required_if' => 'Please select a specific role for this user.',
      'role_id.exists' => 'The selected role does not exist in the system.',
    ];

    $request->validate([
      'name' => 'required|string|max:255',
      'password' => 'required|string|min:4|confirmed',
      'user_type' => 'required|in:super_admin,admin,customer',
      'role_id' => 'required_if:user_type,customer|exists:roles,id'
    ], $messages);

    // ✅ DYNAMIC EMAIL UNIQUE VALIDATION
    $emailRules = [
      'required',
      'email',
      'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'
    ];

    if ($request->user_type === 'super_admin' || $request->user_type === 'admin') {
      $emailRules[] = 'unique:admins,email';
    } elseif ($request->user_type === 'customer') {
      $emailRules[] = 'unique:customer,email';
    }

    $request->validate([
      'email' => $emailRules
    ], $messages);

    try {
      if ($request->user_type === 'super_admin' || $request->user_type === 'admin') {
        Admin::create([
          'name' => $request->name,
          'email' => $request->email,
          'password' => bcrypt($request->password),
          'role_id' => $request->role_id,
          'status' => 'Active',
        ]);
      } elseif ($request->user_type === 'customer') {
        Customer::create([
          'fullname' => $request->name,
          'email' => $request->email,
          'password' => bcrypt($request->password),
          'role_id' => $request->role_id,
          'status' => 'Active',
        ]);
      }

      return redirect()->route('admin.user.index')->with('success', 'New user created successfully!');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to create user: ' . $e->getMessage());
    }
  }

  public function update(Request $request, $id, $guard)
  {
    $rules = [
      'name' => 'required|string|max:255',
      'role_id' => 'required|exists:roles,id'
    ];

    // Only require password if it's filled in during update
    if ($request->filled('password')) {
      $rules['password'] = 'string|min:4|confirmed';
    }

    $request->validate($rules, [
      'name.required' => 'Please enter the full name of the user.',
      'role_id.required' => 'Please select a role for this user.',
    ]);

    try {
      if ($guard === 'admin') {
        $user = Admin::findOrFail($id);
        $user->name = $request->name;
      } elseif ($guard === 'customer') {
        $user = Customer::findOrFail($id);
        $user->fullname = $request->name;
      } else {
        return redirect()->back()->with('error', 'Invalid user type provided.');
      }

      if ($request->filled('password')) {
        $user->password = bcrypt($request->password);
      }

      $user->role_id = $request->role_id;
      $user->save();

      return redirect()->route('admin.user.index')->with('success', 'User updated successfully!');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to update user: ' . $e->getMessage());
    }
  }

  public function updateRole(Request $request, $id, $guard)
  {
    $request->validate(['role_id' => 'required|exists:roles,id']);

    try {
      $user = match ($guard) {
        'admin' => Admin::findOrFail($id),
        'customer' => Customer::findOrFail($id),
        default => throw new \Exception('Invalid user type provided.'),
      };

      $user->role_id = $request->role_id;
      $user->save();

      return redirect()->route('admin.user.index')->with('success', 'User role updated successfully!');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to update role: ' . $e->getMessage());
    }
  }

  public function destroy($id, $guard)
  {
    try {
      $user = match ($guard) {
        'admin' => Admin::findOrFail($id),
        'customer' => Customer::findOrFail($id),
        default => throw new \Exception('Invalid user type provided.'),
      };

      $user->delete();
      return redirect()->route('admin.user.index')->with('success', 'User deleted successfully!');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to delete user: ' . $e->getMessage());
    }
  }
}
