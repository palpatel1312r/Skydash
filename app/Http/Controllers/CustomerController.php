<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        // 1. Start the query with Role relationship
        $query = Customer::with('role')->orderBy('created_at', 'desc');

        // 2. Apply Role Filter
        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        // 3. Apply Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 4. Get Results
        $customers = $query->get();

        // 5. Get Dropdown Data
        $roles = \App\Models\Role::all();
        // Get unique statuses from the database
        $statuses = Customer::distinct()->pluck('status')->filter()->values();

        // 6. Pass $request back for Blade logic
        return view('Admin.Admin_Customer_Pages.Customer', compact('customers', 'roles', 'statuses'))->with('request', $request);
    }

    public function create()
    {
        $roles = \App\Models\Role::all();
        return view('Admin.Admin_Customer_Pages.Customer_form', compact('roles'));
    }
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('Admin.Admin_Customer_Pages.Customer_form', compact('customer'));
    }
    public function dashboard(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $query = Product::query();

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $products = $query->get();
        $categories = Product::select('category')->distinct()->whereNotNull('category')->pluck('category');

        $cartCount = Cart::where('customer_id', $customer->id)->sum('quantity');

        // Keep your existing stats if you have them
        return view('Customer_Pages.Customer_dashboard', compact(
            'customer',
            'products',
            'categories',
            'cartCount',
            'request'          // important for the filter label
        ));
    }
    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'unique:customer,email',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'
            ],
            'status' => 'required|string',
        ], [
            'fullname.required' => 'Please enter the customer\'s full name.',
            'status.required' => 'Please select a status for the customer.',
            'email.required' => 'Please enter the customer\'s email address.',
            'email.email' => 'Please enter a valid email format.',
            'email.unique' => 'This email address is already registered.',
            'email.regex' => 'Email must be a valid @gmail.com address.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customerRole = \App\Models\Role::where('name', 'customer')->first();
        if (!$customerRole) {
            return response()->json(['errors' => ['role' => ['Customer role not found.']]], 422);
        }

        Customer::create([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'password' => Hash::make('1234'),
            'role_id' => $customerRole->id,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully!'
        ]);
    }

    public function update(Request $request)
    {
        $customer = Customer::findOrFail($request->id);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'unique:customer,email,' . $request->id,
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'
            ],
            'status' => 'required|string',
        ], [
            'fullname.required' => 'Please enter the customer\'s full name.',
            'status.required' => 'Please select a status for the customer.',
            'email.required' => 'Please enter the customer\'s email address.',
            'email.email' => 'Please enter a valid email format.',
            'email.unique' => 'This email address is already registered.',
            'email.regex' => 'Email must be a valid @gmail.com address.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer->fullname = $request->fullname;
        $customer->email = $request->email;
        $customer->status = $request->status;
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully!'
        ]);
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
        return view('Components.Profile', compact('customer'));
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

    public function showChangePasswordForm()
    {
        $customer = Auth::guard('customer')->user();
        return view('Customer_Pages.customer_change_password', compact('customer'));
    }
}
