<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
  public function create()
  {
    return view('Admin.User Pages.User_form');
  }

  public function index(Request $request)
  {
    $roles = Role::pluck('name', 'id');

    // 1. Fetch all admins and customers
    $admins = Admin::all()->map(function ($admin) use ($roles) {
      $roleName = $roles[$admin->role_id] ?? 'Admin';
      return [
        'id' => $admin->id,
        'name' => $admin->name,
        'email' => $admin->email,
        'role_name' => $roleName,
        'role_id' => $admin->role_id,
        'guard' => 'admin',
        'user_type' => 'admin', // For filtering
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
        'role_id' => $customer->role_id,
        'guard' => 'customer',
        'user_type' => 'customer', // For filtering
        'created_at' => $customer->created_at,
      ];
    });

    // 2. Merge collections
    $users = $admins->merge($customers);

    // 3. Apply Filters
    if ($request->filled('role')) {
      $users = $users->filter(function ($user) use ($request) {
        return $user['role_id'] == $request->role;
      });
    }

    if ($request->filled('user_type')) {
      $users = $users->filter(function ($user) use ($request) {
        return $user['user_type'] === $request->user_type;
      });
    }

    // 4. Sort by date
    $users = $users->sortByDesc('created_at');

    // 5. Get distinct roles and types for dropdowns
    $allRoles = Role::all();
    $userTypes = [['value' => 'admin', 'label' => 'Admin'], ['value' => 'customer', 'label' => 'Customer']];

    // 6. Return view with request object
    return view('Admin.User Pages.User', compact('users', 'allRoles', 'userTypes'))->with('request', $request);
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
      'password_confirmation.required' => 'Please confirm your password.',
      'password_confirmation.same' => 'The password confirmation does not match.',
      'user_type.required' => 'Please select if this user is an Admin or Customer.',
    ];

    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'password' => 'required|string|min:4',
      'password_confirmation' => 'required|same:password',
      'user_type' => 'required|in:super_admin,admin,customer',
    ], $messages);

    // Add Email Rules Dynamically
    $emailRules = ['required', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'];
    if ($request->user_type === 'super_admin' || $request->user_type === 'admin') {
      $emailRules[] = 'unique:admins,email';
    } elseif ($request->user_type === 'customer') {
      $emailRules[] = 'unique:customer,email';
    }
    $validator->addRules(['email' => $emailRules]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    try {
      $roleIdToAssign = null;
      $adminRole = Role::where('name', 'Admin')->first();
      $superAdminRole = Role::where('name', 'Super Admin')->first();

      if ($request->user_type === 'super_admin') {
        $roleIdToAssign = $superAdminRole ? $superAdminRole->id : 1;
      } elseif ($request->user_type === 'admin') {
        $roleIdToAssign = $adminRole ? $adminRole->id : 2;
      }

      if ($request->user_type === 'super_admin' || $request->user_type === 'admin') {
        Admin::create([
          'name' => $request->name,
          'email' => $request->email,
          'password' => bcrypt($request->password),
          'role_id' => $roleIdToAssign,
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

      return response()->json([
        'success' => true,
        'message' => 'New user created successfully!'
      ]);
    } catch (\Exception $e) {
      return response()->json(['errors' => ['general' => ['Failed to create user: ' . $e->getMessage()]]], 422);
    }
  }
  public function edit($id, $guard)
  {
    if ($guard === 'admin') {
      $user = Admin::with('role')->findOrFail($id);
      $user['guard'] = 'admin';
    } elseif ($guard === 'customer') {
      $user = Customer::with('role')->findOrFail($id);
      $user['guard'] = 'customer';
      $user['name'] = $user['fullname'];
    } else {
      return redirect()->back()->with('error', 'Invalid user type provided.');
    }

    $currentAdmin = Auth::guard('admin')->user();
    $isCurrentUserSuperAdmin = ($currentAdmin->role && $currentAdmin->role->name === 'Super Admin');
    $isTargetSuperAdmin = ($user->role && $user->role->name === 'Super Admin');
    $isTargetSelf = ($currentAdmin->id == $id && $guard == 'admin');

    // ✅ BLOCK: The logged-in user cannot edit themselves (even if they are Super Admin).
    if ($isTargetSelf) {
      return redirect()->route('admin.user.index')->with('error', 'You cannot edit your own account here. Please use the Profile page.');
    }

    // ✅ BLOCK: Regular Admin cannot edit Super Admin
    if (!$isCurrentUserSuperAdmin && $isTargetSuperAdmin) {
      return redirect()->route('admin.user.index')->with('error', 'You do not have permission to edit this user.');
    }

    return view('Admin.User Pages.User_form', compact('user'));
  }

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

    $emailRules = ['required', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'];
    if ($request->user_type === 'super_admin' || $request->user_type === 'admin') {
      $emailRules[] = 'unique:admins,email,' . $id;
    } else {
      $emailRules[] = 'unique:customer,email,' . $id;
    }
    $rules['email'] = $emailRules;

    if ($request->filled('password')) {
      $rules['password'] = 'string|min:4';
      $rules['password_confirmation'] = 'required_with:password|same:password';
    }

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    try {
      $targetUser = match ($guard) {
        'admin' => Admin::with('role')->findOrFail($id),
        'customer' => Customer::with('role')->findOrFail($id),
        default => throw new \Exception('Invalid user type provided.'),
      };

      $currentAdmin = Auth::guard('admin')->user();
      $isCurrentUserSuperAdmin = ($currentAdmin->role && $currentAdmin->role->name === 'Super Admin');
      $isTargetSuperAdmin = ($targetUser->role && $targetUser->role->name === 'Super Admin');
      $isTargetSelf = ($currentAdmin->id == $id && $guard == 'admin');

      // ✅ BLOCK: Cannot update self
      if ($isTargetSelf) {
        return response()->json(['errors' => ['general' => ['You cannot update your own account here. Please use the Profile page.']]], 403);
      }

      // ✅ BLOCK: Regular Admin cannot update Super Admin
      if (!$isCurrentUserSuperAdmin && $isTargetSuperAdmin) {
        return response()->json(['errors' => ['general' => ['You do not have permission to update this user.']]], 403);
      }

      // Proceed with logic to swap between Admin/Customer tables
      $newUserType = $request->user_type;
      $roleIdToAssign = null;
      $adminRole = Role::where('name', 'Admin')->first();
      $superAdminRole = Role::where('name', 'Super Admin')->first();

      if ($request->user_type === 'super_admin') {
        $roleIdToAssign = $superAdminRole ? $superAdminRole->id : 1;
      } elseif ($request->user_type === 'admin') {
        $roleIdToAssign = $adminRole ? $adminRole->id : 2;
      }

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
        $user->role_id = ($guard === 'admin') ? $roleIdToAssign : $request->role_id;
        $user->save();
      } else {
        if ($guard === 'customer' && in_array($newUserType, ['super_admin', 'admin'])) {
          $customer = Customer::findOrFail($id);
          Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->filled('password') ? bcrypt($request->password) : $customer->password,
            'role_id' => $roleIdToAssign,
            'status' => 'Active',
          ]);
          $customer->delete();
        } elseif ($guard === 'admin' && $newUserType === 'customer') {
          $admin = Admin::findOrFail($id);
          Customer::create([
            'fullname' => $request->name,
            'email' => $request->email,
            'password' => $request->filled('password') ? bcrypt($request->password) : $admin->password,
            'role_id' => $request->role_id,
            'status' => 'Active',
          ]);
          $admin->delete();
        }
      }

      return response()->json([
        'success' => true,
        'message' => 'User updated successfully!'
      ]);
    } catch (\Exception $e) {
      return response()->json(['errors' => ['general' => ['Failed to update user: ' . $e->getMessage()]]], 422);
    }
  }

  public function updateRole(Request $request, $id, $guard)
  {
    $request->validate(['role_id' => 'required|exists:roles,id']);

    try {
      $user = match ($guard) {
        'admin' => Admin::with('role')->findOrFail($id),
        'customer' => Customer::with('role')->findOrFail($id),
        default => throw new \Exception('Invalid user type provided.'),
      };

      $currentAdmin = Auth::guard('admin')->user();
      $isCurrentUserSuperAdmin = ($currentAdmin->role && $currentAdmin->role->name === 'Super Admin');
      $isTargetSuperAdmin = ($user->role && $user->role->name === 'Super Admin');
      $isTargetSelf = ($currentAdmin->id == $id && $guard == 'admin');

      // ✅ BLOCK: Cannot change own role
      if ($isTargetSelf) {
        return redirect()->back()->with('error', 'You cannot change your own role here.');
      }

      // ✅ BLOCK: Regular Admin cannot change Super Admin's role
      if (!$isCurrentUserSuperAdmin && $isTargetSuperAdmin) {
        return redirect()->back()->with('error', 'You do not have permission to change the role of this user.');
      }

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
        'admin' => Admin::with('role')->findOrFail($id),
        'customer' => Customer::with('role')->findOrFail($id),
        default => throw new \Exception('Invalid user type provided.'),
      };

      $currentAdmin = Auth::guard('admin')->user();
      $isCurrentUserSuperAdmin = ($currentAdmin->role && $currentAdmin->role->name === 'Super Admin');
      $isTargetSuperAdmin = ($user->role && $user->role->name === 'Super Admin');
      $isTargetSelf = ($currentAdmin->id == $id && $guard == 'admin');

      // ✅ BLOCK: Cannot delete self
      if ($isTargetSelf) {
        return redirect()->route('admin.user.index')->with('error', 'You cannot delete your own account.');
      }

      // ✅ BLOCK: Regular Admin cannot delete Super Admin
      if (!$isCurrentUserSuperAdmin && $isTargetSuperAdmin) {
        return redirect()->route('admin.user.index')->with('error', 'You do not have permission to delete this user.');
      }

      $user->delete();
      return redirect()->route('admin.user.index')->with('success', 'User deleted successfully!');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to delete user: ' . $e->getMessage());
    }
  }
}
