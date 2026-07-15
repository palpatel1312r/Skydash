<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CustomerAuth
{
  public function handle($request, Closure $next)
  {
    if (!Auth::guard('customer')->check()) {
      return redirect()->route('login')->with('error', 'Please login to access this page.');
    }
    return $next($request);
  }
}
