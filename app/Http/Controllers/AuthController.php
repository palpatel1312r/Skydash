<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        // 1. Try Admin Login (For BOTH Regular Admin and Superadmin!)
        if (Auth::guard('admin')->attempt(['email' => $email, 'password' => $password])) {
            $user = Auth::guard('admin')->user();

            // 🛑 CHECK IF ADMIN IS BLOCKED
            if ($user->status !== 'Active') {
                Auth::guard('admin')->logout();
                return back()->withErrors([
                    'blocked' => 'Your account has been blocked. Please contact the Super Admin.',
                ])->withInput($request->except('password'));
            }

            $request->session()->regenerate();

            // Redirect based on role
            if ($user->role == 'Superadmin') {
                Log::info('Superadmin login successful', ['email' => $email]);
                return redirect()->route('superadmin.dashboard');
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
                    'blocked' => 'Your account is not active. Please contact support.',
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
