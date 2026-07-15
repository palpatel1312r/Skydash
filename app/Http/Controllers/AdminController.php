<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminController extends Controller
{
    public function index()
    {
        return view('index');
    }

    // ✅ ONLY ONE profile method for Admin
    public function profile()
    {
        $admin = Auth::guard('admin')->user();
        return view('Dashboard.Profile', compact('admin'));
    }

    // ✅ Update Profile method
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

        $data = $request->only(['name', 'email', 'phone', 'address']);

        // Handle Profile Image Upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($admin->profile_image && file_exists(storage_path('app/public/' . $admin->profile_image))) {
                unlink(storage_path('app/public/' . $admin->profile_image));
            }

            // Store new image
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $data['profile_image'] = $path;
        }

        $admin->update($data);

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully!');
    }
}
