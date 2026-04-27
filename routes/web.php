<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

// Public Routes (No Authentication Required)
Route::middleware(['prevent.back.history'])->group(function () {
  // Login Routes
  Route::get('/getLogin', [AuthController::class, 'getLogin'])->name('getLogin');
  Route::post('/PostLogin', [AuthController::class, 'PostLogin'])->name('PostLogin');

  // Registration Routes
  Route::get('/getRegister', [AuthController::class, 'getRegister'])->name('getRegister');
  Route::post('/PostRegister', [AuthController::class, 'PostRegister'])->name('PostRegister');

  // Logout Route (should be accessible without auth)
  Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Protected Admin Routes (Authentication Required)
Route::middleware(['auth:customer', 'prevent.back.history'])->group(function () {
  // Dashboard
  Route::get('/', [AdminController::class, 'index'])->name('admin');
  Route::get('/adminProfile', [AdminController::class, 'Profile'])->name('Profile');

  // Product Routes
  Route::get('/adminProducts', [AdminController::class, 'products'])->name('products');
  Route::get('/deleteProduct/{id}', [AdminController::class, 'deleteProduct'])->name('deleteProduct');
  Route::post('/AddNewProduct', [AdminController::class, 'AddNewProduct'])->name('AddNewProduct');
  Route::post('/UpdateProduct', [AdminController::class, 'UpdateProduct'])->name('UpdateProduct');

  // Customer Routes
  Route::get('/adminCustomer', [AdminController::class, 'Customer'])->name('Customer');
  Route::post('/AddNewCustomer', [AdminController::class, 'AddNewCustomer'])->name('AddNewCustomer');
  Route::post('/UpdateCustomer', [AdminController::class, 'UpdateCustomer'])->name('UpdateCustomer');
  Route::get('/changeCustomerStatus/{status}/{id}', [AdminController::class, 'changeCustomerStatus'])->name('changeCustomerStatus');

  // Order Routes
  Route::get('/adminOrder', [AdminController::class, 'Order'])->name('Order');
  Route::post('/OurOrder', [AdminController::class, 'OurOrder'])->name('OurOrder');
  Route::get('/Orders', [AdminController::class, 'ShowOrders'])->name('ShowOrders');
  Route::post('/AddNewOrder', [AdminController::class, 'AddNewOrder'])->name('AddNewOrder');
  Route::get('/changeOrderStatus/{status}/{id}', [AdminController::class, 'changeOrderStatus'])->name('changeOrderStatus');
});

    //     $request->validate([
    //         'customername' => 'required|string',
    //         // 'email' => 'required|email',
    //         'bill' => 'required|string',
    //         'phone' => 'required|string',
    //         'address' => 'required|string',
    //         'order_status' => 'required|string',

    //         'customerstatus' => 'required|string',

    //     ]);

    //     // Insert into orders table
    //     $order = new Order();
    //     $order->customername = $request->input('customername');
    //     // $order->email = $request->input('email');
    //     $order->bill = $request->input('bill');
    //     $order->phone = $request->input('phone');
    //     $order->address = $request->input('address');
    //     $order->order_status = $request->input('order_status');
    //     $order->customerstatus = $request->input('customerstatus');
    //     $order->save();

    //     return redirect()->back()->with('success', 'Order added successfully!');
    // }
    // public function ShowOrders()
    // {
    //     $orders = Order::all();
    //     return view('Dashboard.Orders', ['Order' => $orders]);