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

        $customer = Customer::create([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'Customer',
            'status' => 'Active',
        ]);

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.dashboard')->with('success', 'Registration successful!');
        } else {
            return redirect()->route('login')->with('error', 'Registration successful but login failed.');
        }
    }

    public function index()
    {
        $customers = Customer::all();
        Log::info('Customers found: ' . $customers->count());
        return view('Dashboard.Customer', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:customer,email',
            'role' => 'required|string',
            'status' => 'required|string',
        ]);

        Customer::create([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'password' => Hash::make('password123'),
            'role' => $request->role,
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
        ]);

        $customer->update($request->only(['fullname', 'email', 'role', 'status']));

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
}
