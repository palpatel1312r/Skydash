<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('role')->orderBy('created_at', 'desc')->get();
        $roles = \App\Models\Role::all();
        Log::info('Customers found: ' . $customers->count());

        return view('Dashboard.customer pages.Customer', compact('customers', 'roles'));
    }

    public function create()
    {
        $roles = \App\Models\Role::all();
        return view('Dashboard.customer pages.customers_create', compact('roles'));
    }
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('Dashboard.customer pages.customers_update', compact('customer'));
    }
    public function dashboard()
    {
        $customer = Auth::guard('customer')->user();
        return view('Dashboard.customer pages.customer_dashboard', compact('customer'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:customer,email',
            'status' => 'required|string',
        ]);

        // Get the customer role ID (assuming you have a role named 'customer')
        $customerRole = \App\Models\Role::where('name', 'customer')->first();

        if (!$customerRole) {
            return redirect()->back()->with('error', 'Customer role not found. Please create a "customer" role first.');
        }

        Customer::create([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'password' => Hash::make('1234'),
            'role_id' => $customerRole->id, // Auto-assign customer role
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
            'status' => 'required|string',
        ]);

        $customer->fullname = $request->fullname;
        $customer->email = $request->email;
        $customer->status = $request->status;
        $customer->save();

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

        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->save();

        $profile = $customer->profile;
        if (!$profile) {
            $profile = new \App\Models\Profile();
            $profile->profileable_type = get_class($customer);
            $profile->profileable_id = $customer->id;
        }

        $profile->phone = $request->phone;
        $profile->address = $request->address;

        if ($request->hasFile('profile_image')) {
            // ✅ FIXED: Correctly check and delete the old image path
            if ($profile->profile_image && file_exists(storage_path('app/public/profile_images/' . $profile->profile_image))) {
                unlink(storage_path('app/public/profile_images/' . $profile->profile_image));
            }

            // ✅ FIXED: Store correctly inside 'profile_images'
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $profile->profile_image = $path;
        }

        $profile->save();

        return redirect()->route('customer.profile')->with('success', 'Profile updated successfully!');
    }

    public function validateCurrentPassword(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
        } elseif (Auth::guard('customer')->check()) {
            $user = Auth::guard('customer')->user();
        } else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => true]);
        } else {
            return response()->json([
                'errors' => ['current_password' => ['The current password is incorrect.']]
            ], 422);
        }
    }
}
