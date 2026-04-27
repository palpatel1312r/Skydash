<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateCustomer
{
  /**
   * Handle an incoming request.
   */
  public function handle(Request $request, Closure $next)
  {
    if (!Auth::guard('customer')->check()) {
      return redirect()->route('login')->with('error', 'Please login to access this page.');
    }

    return $next($request);
  }
}
