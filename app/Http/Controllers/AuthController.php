<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function getLogin()
    {
        return view('Dashboard.Login');
    }
    /////////////////
    public function PostLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:4',
        ]);

        if (Auth::guard('customer')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin')->with('success', 'Login successful!');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->withInput($request->only('email'));
    }
    ////////////////////////////////////////////////////////////////////////////////////////////
    public function getRegister()
    {
        return view('Dashboard.Register');
    }
    ///////////////
    public function PostRegister(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:customer,email',
            'password' => 'required|min:4|confirmed',
        ]);

        customer::create([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->route('getLogin')->with('success', 'Login successful!');
        } else {
            return redirect()->route('getLogin')->with('error', 'Password or email is incorrect');
        }
    }
    ///////////////////////////////////////////////////////////
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('getLogin')->with('success', 'Logged out successfully!');
    }
}
