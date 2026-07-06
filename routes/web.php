<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvoiceController;

// ============================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================
Route::middleware(['prevent.back.history'])->group(function () {
  Route::get('/getLogin', [AuthController::class, 'getLogin'])->name('getLogin');
  Route::post('/PostLogin', [AuthController::class, 'PostLogin'])->name('PostLogin');
  Route::get('/getRegister', [AuthController::class, 'getRegister'])->name('getRegister');
  Route::post('/PostRegister', [AuthController::class, 'PostRegister'])->name('PostRegister');
  Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ============================================
// PROTECTED ADMIN ROUTES
// ============================================
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

  // ============================================
  // INVOICE ROUTES
  // ============================================
  Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
  Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
  Route::get('/invoices/status/{id}/{status}', [InvoiceController::class, 'updateStatus'])->name('invoices.status');
  Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
});
