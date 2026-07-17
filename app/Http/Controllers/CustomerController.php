<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function showRegisterForm()
    {
        return view('Dashboard.Register');
    }
    public function create()
    {
        $roles = \App\Models\Role::all();
        return view('Dashboard.customer pages.customers_create', compact('roles'));
    }
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $roles = \App\Models\Role::all();
        return view('Dashboard.customer pages.customers_update', compact('customer', 'roles'));
    }

    public function dashboard()
    {
        $customer = Auth::guard('customer')->user();
        return view('Dashboard.index', compact('customer'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:customer,email',
            'password' => 'required|min:4|confirmed',
        ]);

        // ✅ FIXED: Properly closed the array and added the missing brackets
        $customer = Customer::create([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 1, // Default Customer role ID
            'status' => 'Active',
        ]);

        if (Auth::guard('customer')->check()) {
            // ✅ SUCCESS: Stay logged in and go to dashboard
            return redirect()->route('customer.dashboard')->with('success', 'Registration successful!');
        } else {
            // ✅ FIXED: Pass the email back to the login form
            return redirect()->route('login')->withInput(['email' => $request->email])
                ->with('success', 'Registration successful! Please login with your credentials.');
        }
    }

    public function index()
    {
        $customers = Customer::with('role')->get();
        $roles = \App\Models\Role::all();
        Log::info('Customers found: ' . $customers->count());
        return view('Dashboard.Customer', compact('customers', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:customer,email',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|string',
        ], [
            // ✅ Custom error messages go here
            'fullname.required' => 'Please enter the full name of the customer.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'role_id.required' => 'Please select a valid role from the dropdown.',
            'status.required' => 'Please select a valid status.',
        ]);

        Customer::create([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'password' => Hash::make('1234'),
            'role_id' => $request->role_id,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully!');
    }
    public function update(Request $request)
    {
        $customer = Customer::findOrFail($request->id);

        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:customer,email,' . $request->id,
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|string',
        ], [
            'fullname.required' => 'Please enter the full name.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Enter a valid email address.',
            'email.unique' => 'This email is already taken.',
            'role_id.required' => 'Please select a valid role.',
            'status.required' => 'Please select a valid status.',
        ]);

        // 👇 FIXED: Update individual properties instead of using mass assignment
        $customer->fullname = $request->fullname;
        $customer->email = $request->email;
        $customer->role_id = $request->role_id; // This will now force the update
        $customer->status = $request->status;

        $customer->save(); // Save to database

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully!');
    }
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully!');
    }

    public function changeStatus($status, $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->status = $status;
        $customer->save();

        return redirect()->route('admin.customers.index')->with('success', 'Customer status updated!');
    }

    public function profile()
    {
        $customer = Auth::guard('customer')->user();
        return view('Dashboard.Profile', compact('customer'));
    }

    public function updateProfile(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customer,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'address']);

        if ($request->hasFile('profile_image')) {
            if ($customer->profile_image && file_exists(storage_path('app/public/' . $customer->profile_image))) {
                unlink(storage_path('app/public/' . $customer->profile_image));
            }
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $data['profile_image'] = $path;
        }

        $customer->update($data);

        return redirect()->route('customer.profile')->with('success', 'Profile updated successfully!');
    }
    public function updatePassword(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:4|confirmed',
        ], [
            'current_password.required' => 'Please enter your current password.',
            'new_password.required' => 'Please enter a new password.',
            'new_password.min' => 'The new password must be at least 4 characters.',
            'new_password.confirmed' => 'The password confirmation does not match.',
        ]);

        // Check if current password matches
        if (!Hash::check($request->current_password, $customer->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        // Update the password
        $customer->password = Hash::make($request->new_password);
        $customer->save();

        return redirect()->back()->with('success', 'Password changed successfully!');
    }
}
