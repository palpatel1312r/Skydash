<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function profile()
    {
        $admin = Auth::guard('admin')->user();

        // ✅ Load the role name
        $admin->role_name = $admin->role ? $admin->role->name : 'Admin';

        return view('Components.Profile', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        // ✅ FIXED: Add custom messages so "validation.required" is replaced with real English text
        $messages = [
            'name.required' => 'The full name field is required.',
            'email.required' => 'The email address field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already taken.',
        ];

        // 2. Validate inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], $messages);

        // 3. Update Admin details
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->save();

        // 4. Get or create the profile record
        $profile = $admin->profile;
        if (!$profile) {
            $profile = new \App\Models\Profile();
            $profile->profileable_type = get_class($admin);
            $profile->profileable_id = $admin->id;
        }

        $profile->phone = $request->phone;
        $profile->address = $request->address;

        // 5. Handle Image Upload
        if ($request->hasFile('profile_image')) {
            if ($profile->profile_image && file_exists(storage_path('app/public/' . $profile->profile_image))) {
                unlink(storage_path('app/public/' . $profile->profile_image));
            }

            $path = $request->file('profile_image')->store('profile_images', 'public');
            $profile->profile_image = $path;
        }

        $profile->save();

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully!');
    }

    public function dashboard()
    {
        // 1. STATS CARDS DATA
        $totalProducts = Product::count();
        $activeDealers = Customer::where('status', 'Active')->count();
        $totalOrders = Invoice::count();

        // Today's RevenuupdateProfilee
        $todayRevenue = Invoice::whereDate('created_at', now()->toDateString())->sum('total_amount');

        // Low Stock Count
        $lowStockCount = Product::where('quantity', '<=', 10)->count();

        // 2. SLIDER DATA
        $newArrivals = Product::orderBy('created_at', 'desc')->take(8)->get();
        $bestSellers = Product::inRandomOrder()->take(8)->get();

        // 3. RECENT ACTIVITY LOG (Mock data)
        $recentActivities = [
            ['user' => 'Nilesh Traders', 'action' => 'Placed order #INV-2026-001', 'time' => '2 mins ago'],
            ['user' => 'Mahesh Electronics', 'action' => 'Updated stock for Product X', 'time' => '15 mins ago'],
            ['user' => 'System', 'action' => 'Low Stock Alert: Product Y (3 left)', 'time' => '1 hour ago'],
            ['user' => 'Priya Wholesale', 'action' => 'Registered as a new Dealer', 'time' => '2 hours ago'],
        ];

        return view('Admin.Dashboard.Admin_Dashboard', compact(
            'totalProducts',
            'activeDealers',
            'totalOrders',
            'todayRevenue',
            'lowStockCount',
            'newArrivals',
            'bestSellers',
            'recentActivities'
        ));
    }
}
