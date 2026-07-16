<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateAdminOrSuper
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('admin')->check() || Auth::guard('superadmin')->check()) {
            return $next($request);
        }

        return redirect()->route('login')->with('error', 'You must be logged in as an Admin or Super Admin.');
    }
}
