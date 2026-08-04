<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
  public function dashboard()
  {
    // 1. STATS CARDS DATA
    $totalProducts = Product::count();
    $activeDealers = Customer::where('status', 'Active')->count();
    $totalOrders = Invoice::count();

    // Today's Revenue (Ensure your Invoice table has a 'grand_total' column)
    $todayRevenue = Invoice::whereDate('created_at', now()->toDateString())->sum('total_amount');

    // Low Stock Count (Adjust column name if yours is 'quantity' or 'stock')
    $lowStockCount = Product::where('quantity', '<=', 10)->count();

    // 2. SLIDER DATA
    $newArrivals = Product::orderBy('created_at', 'desc')->take(8)->get();
    // Fallback to random if you don't have 'sold_count' column yet
    $bestSellers = Product::inRandomOrder()->take(8)->get();

    // 3. RECENT ACTIVITY LOG (Mock data for now)
    $recentActivities = [
      ['user' => 'Nilesh Traders', 'action' => 'Placed order #INV-2026-001', 'time' => '2 mins ago'],
      ['user' => 'Mahesh Electronics', 'action' => 'Updated stock for Product X', 'time' => '15 mins ago'],
      ['user' => 'System', 'action' => 'Low Stock Alert: Product Y (3 left)', 'time' => '1 hour ago'],
      ['user' => 'Priya Wholesale', 'action' => 'Registered as a new Dealer', 'time' => '2 hours ago'],
    ];

    return view('Superadmin.Superadmin_Dashboard', compact(
      'totalProducts',
      'activeDealers',
      'totalOrders',
      'todayRevenue',
      'lowStockCount',
      'newArrivals',
      'bestSellers',
      'recentActivities'
    ));
  }
}
