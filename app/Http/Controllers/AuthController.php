<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('components.Login');
    }
    public function showRegisterForm()
    {
        return view('components.Register');
    }
    public function showChangePasswordForm()
    {
        return view('components.change_password');
    }
    public function updatePassword(Request $request)
    {
        // ✅ 1. DETECT IF ADMIN OR CUSTOMER IS LOGGED IN
        $user = null;
        $guard = null;

        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $guard = 'admin';
        } elseif (Auth::guard('customer')->check()) {
            $user = Auth::guard('customer')->user();
            $guard = 'customer';
        } else {
            return redirect()->route('login')->with('error', 'You must be logged in to change your password.');
        }

        // ✅ 2. VALIDATE (Including the custom "Not same as current" rule)
        $request->validate([
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
        ], [
            'current_password.required' => 'Please enter your current password.',
            'new_password.required' => 'Please enter a new password.',
            'new_password.min' => 'The new password must be at least 4 characters.',
            'new_password.confirmed' => 'The password confirmation does not match.',
        ]);

        // ✅ 3. CRITICAL FIX: CHECK CURRENT PASSWORD EARLY AND RETURN IF INCORRECT
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        // ✅ 4. Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();

        // ✅ 5. LOGOUT THE USER ONLY AFTER SUCCESSFUL UPDATE
        Auth::guard($guard)->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ✅ 6. Redirect to Login page with success message
        return redirect()->route('login')->with('success', 'Password changed successfully! Please login with your new credentials.');
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
    
    // ✅ FORGOT PASSWORD METHODS

    // Show the form to request a password reset link
    public function showForgotPasswordForm()
    {
        return view('components.forgot_password');
    }

    // Send the reset link email
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Try to find the user in Admins or Customers
        $admin = \App\Models\Admin::where('email', $request->email)->first();
        $customer = \App\Models\Customer::where('email', $request->email)->first();

        if (!$admin && !$customer) {
            return back()->withErrors(['email' => 'We can\'t find a user with that email address.']);
        }

        // Determine which guard to use
        $guard = $admin ? 'admin' : 'customer';

        try {
            $status = Password::broker($guard)->sendResetLink(
                ['email' => $request->email]
            );

            return $status === Password::RESET_LINK_SENT
                ? back()->with(['success' => 'Password reset link sent to your email!'])
                : back()->withErrors(['email' => __($status)]);

        } catch (\Exception $e) {
            // Fallback: If email fails, show the link on screen for testing purposes (Remove this after testing!)
            return back()->with('success', 'Password reset link would be sent to ' . $request->email . ' (Check your console for the URL if emails fail)');
        }
    }

    // Show the form to reset the password
    public function showResetPasswordForm($token)
    {
        return view('components.reset_password', ['token' => $token]);
    }

    // Handle the password reset submission
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:4|confirmed',
        ]);

        // Determine which guard based on the email
        $admin = \App\Models\Admin::where('email', $request->email)->first();
        $guard = $admin ? 'admin' : 'customer';

        $status = Password::broker($guard)->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Your password has been reset successfully!')
            : back()->withErrors(['email' => [__($status)]]);
    }
}