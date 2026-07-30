<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('Auth.Login');
    }
    public function showRegisterForm()
    {
        return view('Auth.Register');
    }
    public function showChangePasswordForm()
    {
        return view('Auth.change_password');
    }
    public function updatePassword(Request $request)
    {
        $user = null;
        $guard = null;

        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $guard = 'admin';
        } elseif (Auth::guard('customer')->check()) {
            $user = Auth::guard('customer')->user();
            $guard = 'customer';
        } else {
            return response()->json(['errors' => ['general' => ['You must be logged in to change your password.']]], 422);
        }

        // 1. VALIDATE
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => [
                'required',
                'min:4',
                'confirmed',
                function ($attribute, $value, $fail) use ($user) {
                    if (Hash::check($value, $user->password)) {
                        $fail('The new password cannot be the same as your current password.');
                    }
                },
            ],
            // ✅ FIX: 'messages' was moved OUT of the rules array, into the 3rd argument below
        ], [
            'current_password.required' => 'Please enter your current password.',
            'new_password.required' => 'Please enter a new password.',
            'new_password.min' => 'The new password must be at least 4 characters.',
            'new_password.confirmed' => 'The password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. CHECK CURRENT PASSWORD
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['errors' => ['current_password' => ['Current password is incorrect.']]], 422);
        }

        // 3. Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();

        // 4. LOGOUT THE USER
        Auth::guard($guard)->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 5. RETURN JSON SUCCESS
        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully! Please login with your new credentials.'
        ]);
    }
    public function register(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:customer,email',
            'password' => 'required|min:4|confirmed',
        ], [
            // ✅ Custom Validation Messages
            'fullname.required' => 'The full name field is required.',
            'email.required'    => 'The email field is required.',
            'email.email'       => 'Please enter a valid email address.',
            'email.unique'      => 'This email is already registered.',
            'password.required' => 'The password field is required.',
            'password.min'      => 'The password must be at least 4 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $customer = Customer::create([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 3, // Default Customer Role ID
            'status' => 'Active',
        ]);

        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.dashboard')->with('success', 'Registration successful!');
        } else {
            return redirect()->route('login')->withInput(['email' => $request->email])
                ->with('success', 'Registration successful! Please login with your credentials.');
        }
    }
    public function autoLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            // ✅ Custom Validation Messages
            'email.required' => 'The email field is required.',
            'email.email'    => 'Please enter a valid email address.',
            'password.required' => 'The password field is required.',
        ]);

        $email = $request->email;
        $password = $request->password;

        Log::info('Auto login attempt', ['email' => $email]);

        // 1. Try Admin Login (For BOTH Regular Admin and Superadmin!)
        if (Auth::guard('admin')->attempt(['email' => $email, 'password' => $password])) {
            $user = Auth::guard('admin')->user();

            // 🛑 CHECK IF ADMIN IS BLOCKED
            if ($user->status !== 'Active') {
                Auth::guard('admin')->logout();
                return back()->withErrors([
                    'email' => 'Your account has been blocked. Please contact the Super Admin.',
                ])->withInput($request->except('password'));
            }

            $request->session()->regenerate();

            // ✅ FIXED: Redirect based on role_id instead of string 'role'
            if ($user->role_id == 1) { // Super Admin has role_id = 1
                Log::info('Superadmin login successful', ['email' => $email]);
                return redirect()->route('Superadmin.Superadmin_Dashboard');
            }

            Log::info('Admin login successful', ['email' => $email]);
            return redirect()->route('admin.dashboard');
        }

        // 2. Try Customer Login
        if (Auth::guard('customer')->attempt(['email' => $email, 'password' => $password])) {
            Log::info('Customer login successful', ['email' => $email]);

            $customer = Auth::guard('customer')->user();
            if ($customer->status !== 'Active') {
                Auth::guard('customer')->logout();
                return back()->withErrors([
                    'email' => 'Your account is not active. Please contact support.',
                ])->withInput($request->except('password'));
            }

            $request->session()->regenerate();
            return redirect()->route('customer.dashboard');
        }

        // 3. No user found
        Log::warning('Login failed - No matching user', ['email' => $email]);

        return back()->withErrors([
            'email' => 'Invalid credentials. Please check your email and password.',
        ])->withInput($request->except('password'));
    }
    public function logout(Request $request)
    {
        $guard = 'customer';
        if (Auth::guard('admin')->check()) {
            $guard = 'admin';
        }

        Auth::guard($guard)->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully!');
    }
}
