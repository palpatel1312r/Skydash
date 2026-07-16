<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperadminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('superadmin')->check()) {
            return $next($request);
        }

        // If not, redirect to login or show error
        return redirect()->route('login')->with('error', 'You are not authorized to access this page.');
    }
}
