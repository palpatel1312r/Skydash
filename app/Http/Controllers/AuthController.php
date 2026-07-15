<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use  Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('Dashboard.Login');
    }

    public function autoLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->email;
        $password = $request->password;

        Log::info('Auto login attempt', ['email' => $email]);

        // 1. Check if user is an Admin
        $admin = Admin::where('email', $email)->first();
        if ($admin && Hash::check($password, $admin->password)) {
            Log::info('Admin login successful', ['email' => $email]);
            Auth::guard('admin')->login($admin);
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        // 2. Check if user is a Customer
        $customer = Customer::where('email', $email)->first();
        if ($customer && Hash::check($password, $customer->password)) {
            Log::info('Customer login successful', ['email' => $email]);

            // Check if customer is active
            if ($customer->status !== 'Active') {
                return back()->withErrors([
                    'email' => 'Your account is not active. Please contact support.',
                ])->withInput($request->except('password'));
            }

            Auth::guard('customer')->login($customer);
            $request->session()->regenerate();

            // Debug - Verify login worked
            if (Auth::guard('customer')->check()) {
                Log::info('Customer session created', ['customer_id' => Auth::guard('customer')->id()]);
                return redirect()->route('customer.dashboard');
            } else {
                Log::error('Customer login failed after successful authentication');
                return back()->withErrors([
                    'email' => 'Login failed. Please try again.',
                ])->withInput($request->except('password'));
            }
        }

        // 3. No user found
        Log::warning('Login failed - No matching user', ['email' => $email]);

        if (Admin::where('email', $email)->exists() || Customer::where('email', $email)->exists()) {
            return back()->withErrors([
                'email' => 'Invalid password.',
            ])->withInput($request->except('password'));
        }

        return back()->withErrors([
            'email' => 'No account found with this email.',
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
