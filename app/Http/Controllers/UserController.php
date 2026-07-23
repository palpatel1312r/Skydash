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

  //////////////////////////////////////////////////////

  public function store(Request $request)
  {
    $messages = [
      'name.required' => 'Please enter the full name of the user.',
      'email.required' => 'We need an email address to create the account.',
      'email.email' => 'Please enter a valid email format.',
      'email.unique' => 'This email address is already registered.',
      'email.regex' => 'Email must be a valid @gmail.com address.',

      // ✅ SEPARATE ERRORS FOR BOTH PASSWORD FIELDS
      'password.required' => 'A password is required.',
      'password.min' => 'The password must be at least 4 characters long.',
      'password_confirmation.required' => 'Please confirm your password.',
      'password_confirmation.same' => 'The password confirmation does not match.',

      'user_type.required' => 'Please select if this user is an Admin or Customer.',
    ];

    // ✅ SEPARATE VALIDATION FOR PASSWORD AND CONFIRM PASSWORD
    $request->validate([
      'name' => 'required|string|max:255',
      'password' => 'required|string|min:4',
      'password_confirmation' => 'required|same:password',
      'user_type' => 'required|in:super_admin,admin,customer',
    ], $messages);

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
      $roleIdToAssign = null;
      $adminRole = Role::where('name', 'Admin')->first();
      $superAdminRole = Role::where('name', 'Super Admin')->first();

      if ($request->user_type === 'super_admin') {
        $roleIdToAssign = $superAdminRole ? $superAdminRole->id : 1;
      } elseif ($request->user_type === 'admin') {
        $roleIdToAssign = $adminRole ? $adminRole->id : 2;
      }

      // ✅ EMERGENCY FALLBACK
      if (!$roleIdToAssign && $request->user_type === 'super_admin') {
        $roleIdToAssign = 1;
      } elseif (!$roleIdToAssign && $request->user_type === 'admin') {
        $roleIdToAssign = 2;
      }

      if ($request->user_type === 'super_admin' || $request->user_type === 'admin') {
        Admin::create([
          'name' => $request->name,
          'email' => $request->email,
          'password' => bcrypt($request->password),
          'role_id' => $roleIdToAssign, // ✅ Assigns 1 or 2
          'status' => 'Active',
        ]);
      } elseif ($request->user_type === 'customer') {
        Customer::create([
          'fullname' => $request->name,
          'email' => $request->email,
          'password' => bcrypt($request->password),
          'role_id' => $request->role_id, // ✅ Use the role picked from the dropdown!
          'status' => 'Active',
        ]);
      }

      return redirect()->route('admin.user.index')->with('success', 'New user created successfully!');
    } catch (\Illuminate\Database\QueryException $e) {
      // 🛑 EXACT FIX: If it's a duplicate entry error, redirect back to the form
      if ($e->errorInfo[1] == 1062) {
        return redirect()->back()->withErrors(['email' => 'This email address is already taken.'])->withInput();
      }

      // For any other database error
      return redirect()->back()->with('error', 'Database Error: ' . $e->getMessage());
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to create user: ' . $e->getMessage());
    }
  }

  //////////////////////////////////////////////////////

  public function update(Request $request, $id, $guard)
  {
    $messages = [
      'name.required' => 'Please enter the full name of the user.',
      'email.required' => 'Please enter the email address.',
      'email.email' => 'Please enter a valid email format.',
      'email.unique' => 'This email address is already registered.',
      'email.regex' => 'Email must be a valid @gmail.com address.',
      'user_type.required' => 'Please select a User Type.',
      'role_id.required' => 'Please select a role for this user.',
    ];
    $rules = [
      'name' => 'required|string|max:255',
      'user_type' => 'required|in:super_admin,admin,customer',
    ];
    if ($request->user_type === 'customer') {
      $rules['role_id'] = 'required|exists:roles,id';
    }

    // Email rules (Must be unique in the NEW table)
    $emailRules = ['required', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'];

    if ($request->user_type === 'super_admin' || $request->user_type === 'admin') {
      $emailRules[] = 'unique:admins,email,' . $id; // Ignore current ID if already in admins
    } else {
      $emailRules[] = 'unique:customer,email,' . $id; // Ignore current ID if already in customers
    }

    $rules['email'] = $emailRules;

    if ($request->filled('password')) {
      $rules['password'] = 'string|min:4';
      $rules['password_confirmation'] = 'required_with:password|same:password';
    }

    $request->validate($rules, $messages);

    try {
      $newUserType = $request->user_type;

      // ✅ DEFINE THE ROLE ID FOR ADMIN TRANSITIONS HERE
      $roleIdToAssign = null;
      $adminRole = Role::where('name', 'Admin')->first();
      $superAdminRole = Role::where('name', 'Super Admin')->first();

      if ($request->user_type === 'super_admin') {
        $roleIdToAssign = $superAdminRole ? $superAdminRole->id : 1; // Fallback to 1
      } elseif ($request->user_type === 'admin') {
        $roleIdToAssign = $adminRole ? $adminRole->id : 2; // Fallback to 2
      }

      // 1. If the user type hasn't changed, just update normally
      if ($guard === $newUserType || ($guard === 'admin' && in_array($newUserType, ['admin', 'super_admin']))) {
        if ($guard === 'admin') {
          $user = Admin::findOrFail($id);
          $user->name = $request->name;
          $user->email = $request->email;
        } else {
          $user = Customer::findOrFail($id);
          $user->fullname = $request->name;
          $user->email = $request->email;
        }

        if ($request->filled('password')) {
          $user->password = bcrypt($request->password);
        }

        // ✅ FIX: Allow Admin/Super Admin to change their role
        $user->role_id = ($guard === 'admin') ? $roleIdToAssign : $request->role_id;
        $user->save();
      } else {
        if ($guard === 'customer' && in_array($newUserType, ['super_admin', 'admin'])) {
          // Moving Customer to Admin
          $customer = Customer::findOrFail($id);
          Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->filled('password') ? bcrypt($request->password) : $customer->password,
            'role_id' => $roleIdToAssign, // ✅ Assigns 1 or 2
            'status' => 'Active',
          ]);
          $customer->delete();
        } elseif ($guard === 'admin' && $newUserType === 'customer') {
          // Moving Admin to Customer
          $admin = Admin::findOrFail($id);
          Customer::create([
            'fullname' => $request->name,
            'email' => $request->email,
            'password' => $request->filled('password') ? bcrypt($request->password) : $admin->password,
            'role_id' => $request->role_id, // ✅ Use the selected role from the dropdown
            'status' => 'Active',
          ]);
          $admin->delete();
        }
      }

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
