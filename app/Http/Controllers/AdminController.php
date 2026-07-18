<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
// use App\Models\Admin;

class AdminController extends Controller
{
    public function index()
    {
        return view('Dashboard.admin_dashboard');
    }
    public function profile()
    {
        $admin = Auth::guard('admin')->user();
        return view('Dashboard.Profile', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 1. Update the main Admin table (Name & Email)
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->save();

        // 2. Find or Create the Profile record (Linked to Admin)
        $profile = $admin->profile;
        if (!$profile) {
            $profile = new \App\Models\Profile();
            $profile->profileable_type = get_class($admin); // Automatically sets to 'App\Models\Admin'
            $profile->profileable_id = $admin->id;
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

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully!');
    }

}
