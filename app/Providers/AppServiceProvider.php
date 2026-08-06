<?php

namespace App\Providers;

use App\Models\Cart;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('Components.customerheader', function ($view) {
            $cartCount = 0;
            if (Auth::guard('customer')->check()) {
                $cartCount = Cart::where('customer_id', Auth::guard('customer')->id())->count();
            }
            $view->with('cartCount', $cartCount);
        });
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);
    }

    public function register(): void
    {
        //
    }
}
