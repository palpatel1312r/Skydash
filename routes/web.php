<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InvoiceController;


Route::middleware(['auth:admin'])->prefix('superadmin')->name('superadmin.')->group(function () {

  Route::get('/dashboard', function () {
    $user = auth()->guard('admin')->user();
    if ($user->role_id !== 1) {
      abort(403, 'Unauthorized access.');
    }
    return view('superadmin.dashboard');
  })->name('dashboard');
  Route::resource('roles', App\Http\Controllers\RoleController::class)->except(['show']);
});

/*
|--------------------------------------------------------------------------
| Root Route
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
  if (auth()->guard('admin')->check()) {
    return redirect()->route('admin.dashboard');
  }
  if (auth()->guard('customer')->check()) {
    return redirect()->route('customer.dashboard');
  }
  return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'autoLogin'])->name('login.auto');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');


/*
|--------------------------------------------------------------------------
| Public Routes (No middleware)
|--------------------------------------------------------------------------
*/

// ✅ These routes are exposed globally so the views can generate links
Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');

/*
|--------------------------------------------------------------------------
| Admin Routes (Protected with auth:admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
  // Dashboard
  Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
  Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
  Route::post('/profile/update', [AdminController::class, 'updateProfile'])->name('profile.update');
  Route::post('/password/update', [AuthController::class, 'updatePassword'])->name('password.update');

  // Customer Management (Admin Only)
  Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
  Route::post('/customers/store', [CustomerController::class, 'store'])->name('customers.store');
  Route::post('/customers/update', [CustomerController::class, 'update'])->name('customers.update');
  Route::get('/customers/status/{status}/{id}', [CustomerController::class, 'changeStatus'])->name('customers.status');
  Route::delete('/customers/delete/{id}', [CustomerController::class, 'destroy'])->name('customers.delete');
  Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
  Route::get('/customers/edit/{id}', [CustomerController::class, 'edit'])->name('customers.edit');


  // Admin Product Routes
  Route::get('/products', [ProductController::class, 'index'])->name('products.index');
  Route::get('/products/delete/{id}', [ProductController::class, 'destroy'])->name('products.delete');

  // ✅ Admin Invoice Routes (These perform actions, so they stay inside the guard)
  Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
  Route::put('/invoices/{id}', [InvoiceController::class, 'update'])->name('invoices.update');
  Route::get('/invoices/status/{id}/{status}', [InvoiceController::class, 'updateStatus'])->name('invoices.status');
  Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
});

Route::post('/products/add', [ProductController::class, 'store'])->name('products.add');
Route::post('/products/update', [ProductController::class, 'update'])->name('products.update');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::get('/products/edit/{id}', [ProductController::class, 'edit'])->name('products.edit');
Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('admin.password.form');
/*
|--------------------------------------------------------------------------
| Customer Routes (Protected with auth:customer)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:customer'])->prefix('customer')->name('customer.')->group(function () {
  // Dashboard
  Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
  Route::get('/profile', [CustomerController::class, 'profile'])->name('profile');
  Route::post('/profile/update', [CustomerController::class, 'updateProfile'])->name('profile.update');
  Route::get('/products', [ProductController::class, 'customerProducts'])->name('products');
  Route::get('/invoices', [InvoiceController::class, 'customerInvoices'])->name('invoices');
  Route::post('/password/update', [CustomerController::class, 'updatePassword'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Shared Routes (Accessible by both Admin and Customer)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:admin'])->group(function () {
  Route::get('/products', [ProductController::class, 'index'])->name('products');
  Route::get('/invoice-list', [InvoiceController::class, 'index'])->name('invoices');
  Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
  Route::get('/invoices/status/{id}/{status}', [InvoiceController::class, 'updateStatus'])->name('invoices.status');
  Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

  Route::get('/invoices/edit/{id}', [InvoiceController::class, 'edit'])->name('invoices.edit');

  Route::get('/Customer', [CustomerController::class, 'index'])->name('Customer');
  Route::get('/adminProducts', [ProductController::class, 'index'])->name('adminProducts');
});

// Customer shared routes
Route::middleware(['auth:customer'])->group(function () {
  Route::get('/customer/products', [ProductController::class, 'customerProducts'])->name('customer.products');
  Route::get('/customer/invoices', [InvoiceController::class, 'customerInvoices'])->name('customer.invoices');
});

Route::get('/about', function () {
  return view('about');
})->name('about');

Route::get('/contact', function () {
  return view('contact');
})->name('contact');
