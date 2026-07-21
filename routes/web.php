<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;


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

Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
Route::get('/about', function () {
  return view('about');
})->name('about');
Route::get('/contact', function () {
  return view('contact');
})->name('contact');


/*
|--------------------------------------------------------------------------
| Super Admin Routes (Protected with auth:admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:admin'])->prefix('superadmin')->name('superadmin.')->group(function () {
  Route::get('/dashboard', function () {
    $user = auth()->guard('admin')->user();
    if ($user->role_id !== 1) {
      abort(403, 'Unauthorized access.');
    }
    return view('superadmin.dashboard');
  })->name('dashboard');
  Route::resource('roles', RoleController::class)->except(['show']);
});


/*
|--------------------------------------------------------------------------
| Admin Routes (Protected with auth:admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
  // Dashboard & Profile
  Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
  Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
  Route::post('/profile/update', [AdminController::class, 'updateProfile'])->name('profile.update');
  Route::post('/password/update', [AuthController::class, 'updatePassword'])->name('password.update');

  // Customer Management
  Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
  Route::post('/customers/store', [CustomerController::class, 'store'])->name('customers.store');
  Route::post('/customers/update', [CustomerController::class, 'update'])->name('customers.update');
  Route::get('/customers/status/{status}/{id}', [CustomerController::class, 'changeStatus'])->name('customers.status');
  Route::delete('/customers/delete/{id}', [CustomerController::class, 'destroy'])->name('customers.delete');
  Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
  Route::get('/customers/edit/{id}', [CustomerController::class, 'edit'])->name('customers.edit');

  // User Management (Combined here)
  Route::get('/users', [UserController::class, 'index'])->name('user.index');
  Route::get('/users/create', [UserController::class, 'create'])->name('user.create');
  Route::post('/users', [UserController::class, 'store'])->name('user.store');
  Route::get('/users/{id}/{guard}/edit', [UserController::class, 'edit'])->name('user.edit');
  Route::put('/users/{id}/{guard}', [UserController::class, 'update'])->name('user.update');
  Route::delete('/users/{id}/{guard}', [UserController::class, 'destroy'])->name('user.destroy');
  Route::put('/update-role/{id}/{guard}', [UserController::class, 'updateRole'])->name('user.updateRole');

  // Product Management
  Route::get('/products', [ProductController::class, 'index'])->name('products.index');
  Route::get('/products/delete/{id}', [ProductController::class, 'destroy'])->name('products.delete');

  // Invoice Management
  Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
  Route::put('/invoices/{id}', [InvoiceController::class, 'update'])->name('invoices.update');
  Route::get('/invoices/status/{id}/{status}', [InvoiceController::class, 'updateStatus'])->name('invoices.status');
  Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
});


/*
|--------------------------------------------------------------------------
| Global Shared Product Routes (Outside prefixes)
|--------------------------------------------------------------------------
*/

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
  // These are redundant and should be removed eventually, but kept for now based on your request.
  Route::get('/products', [ProductController::class, 'index'])->name('products');
  Route::get('/invoice-list', [InvoiceController::class, 'index'])->name('invoices');
  Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
  Route::get('/invoices/status/{id}/{status}', [InvoiceController::class, 'updateStatus'])->name('invoices.status');
  Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
  Route::get('/invoices/edit/{id}', [InvoiceController::class, 'edit'])->name('invoices.edit');
  Route::get('/Customer', [CustomerController::class, 'index'])->name('Customer');
  Route::get('/adminProducts', [ProductController::class, 'index'])->name('adminProducts');
});

Route::middleware(['auth:customer'])->group(function () {
  Route::get('/customer/products', [ProductController::class, 'customerProducts'])->name('customer.products');
  Route::get('/customer/invoices', [InvoiceController::class, 'customerInvoices'])->name('customer.invoices');
});
