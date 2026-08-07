<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerInvoiceController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UserController;
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
| Authentication Routes (Public)
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'autoLogin'])->name('login.auto');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

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
| Super Admin & Admin Routes (Auth: admin)
|--------------------------------------------------------------------------
*/

// Superadmin Dashboard
Route::get('/superadmin/dashboard', function () {
  $user = auth()->guard('admin')->user();
  if ($user->role_id !== 1) {
    abort(403, 'Unauthorized access.');
  }
  return view('Superadmin.Superadmin_Dashboard');
})->name('Superadmin.Superadmin_Dashboard')->middleware('auth:admin');

Route::middleware(['auth:admin'])->group(function () {
  Route::get('/superadmin/dashboard', [SuperAdminController::class, 'dashboard'])->name('Superadmin.Superadmin_Dashboard');
});


Route::resource('roles', RoleController::class)->except(['show'])->middleware('auth:admin');

Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard')->middleware('auth:admin');
Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile')->middleware('auth:admin');
Route::post('/admin/profile/update', [AdminController::class, 'updateProfile'])->name('admin.profile.update')->middleware('auth:admin');


// Admin - Customer Management
Route::get('/admin/customers', [CustomerController::class, 'index'])->name('admin.customers.index')->middleware('auth:admin');
Route::post('/admin/customers/store', [CustomerController::class, 'store'])->name('admin.customers.store')->middleware('auth:admin');
Route::match(['put', 'post'], '/admin/customers/update', [CustomerController::class, 'update'])->name('admin.customers.update')->middleware('auth:admin');
Route::get('/admin/customers/status/{status}/{id}', [CustomerController::class, 'changeStatus'])->name('admin.customers.status')->middleware('auth:admin');
Route::delete('/admin/customers/delete/{id}', [CustomerController::class, 'destroy'])->name('admin.customers.delete')->middleware('auth:admin');
Route::get('/admin/customers/create', [CustomerController::class, 'create'])->name('admin.customers.create')->middleware('auth:admin');
Route::get('/admin/customers/edit/{id}', [CustomerController::class, 'edit'])->name('admin.customers.edit')->middleware('auth:admin');

// Admin - User Management
Route::get('/admin/users', [UserController::class, 'index'])->name('admin.user.index')->middleware('auth:admin');
Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.user.create')->middleware('auth:admin');
Route::post('/admin/users', [UserController::class, 'store'])->name('admin.user.store')->middleware('auth:admin');
Route::get('/admin/users/{id}/{guard}/edit', [UserController::class, 'edit'])->name('admin.user.edit')->middleware('auth:admin');
Route::put('/admin/users/{id}/{guard}', [UserController::class, 'update'])->name('admin.user.update')->middleware('auth:admin');
Route::delete('/admin/users/{id}/{guard}', [UserController::class, 'destroy'])->name('admin.user.destroy')->middleware('auth:admin');
Route::put('/admin/update-role/{id}/{guard}', [UserController::class, 'updateRole'])->name('admin.user.updateRole')->middleware('auth:admin');

// Admin - Product Management
Route::get('/admin/products', [ProductController::class, 'index'])->name('products')->middleware('auth:admin');
Route::get('/admin/products/delete/{id}', [ProductController::class, 'destroy'])->name('admin.products.delete')->middleware('auth:admin');

// Admin - Invoice Management
Route::get('/admin/invoices', [InvoiceController::class, 'index'])->name('invoices')->middleware('auth:admin');
Route::get('/admin/invoices/{id}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit')->middleware('auth:admin');
Route::post('/admin/invoices', [InvoiceController::class, 'store'])->name('admin.invoices.store')->middleware('auth:admin');
Route::put('/admin/invoices/{id}', [InvoiceController::class, 'update'])->name('admin.invoices.update')->middleware('auth:admin');
Route::get('/admin/invoices/status/{id}/{status}', [InvoiceController::class, 'updateStatus'])->name('admin.invoices.status')->middleware('auth:admin');
Route::delete('/admin/invoices/{id}', [InvoiceController::class, 'destroy'])->name('admin.invoices.destroy')->middleware('auth:admin');


/*
|--------------------------------------------------------------------------
| Product CRUD Routes (Protected by middleware)
|--------------------------------------------------------------------------
*/

Route::post('/products/add', [ProductController::class, 'store'])->name('products.add')->middleware('auth:admin');
Route::match(['put', 'post'], '/products/update', [ProductController::class, 'update'])->name('products.update')->middleware('auth:admin');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create')->middleware('auth:admin');
Route::get('/products/edit/{id}', [ProductController::class, 'edit'])->name('products.edit')->middleware('auth:admin');


/*
|--------------------------------------------------------------------------
| Customer Routes (Auth: customer)
|--------------------------------------------------------------------------
*/

Route::get('/customer/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard')->middleware('auth:customer');
Route::get('/customer/profile', [CustomerController::class, 'profile'])->name('customer.profile')->middleware('auth:customer');
Route::post('/customer/profile/update', [CustomerController::class, 'updateProfile'])->name('customer.profile.update')->middleware('auth:customer');
// Route::get('/customer/products', [ProductController::class, 'customerProducts'])->name('customer.products')->middleware('auth:customer');
Route::get('/customer/invoices', [CustomerInvoiceController::class, 'customerInvoices'])->name('customer.invoices')->middleware('auth:customer');
Route::get('/customer/invoices/create', [CustomerInvoiceController::class, 'customerCreate'])->name('customer.invoices.create')->middleware('auth:customer');
Route::post('/customer/invoices', [CustomerInvoiceController::class, 'customerStore'])->name('customer.invoices.store')->middleware('auth:customer');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('admin.cart.cart')->middleware('auth:customer');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add')->middleware('auth:customer');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove')->middleware('auth:customer');
Route::patch('/cart/update/{id}', [CartController::class, 'updateQuantity'])->name('cart.update')->middleware('auth:customer');
Route::post('/cart/buynow', [CartController::class, 'buyNow'])->name('cart.buynow')->middleware('auth:customer');


/*
|--------------------------------------------------------------------------
| ✅ UNIFIED CHANGE PASSWORD ROUTES (For Both Admin & Customer)
|--------------------------------------------------------------------------
*/

// Admin password form
Route::get('/admin/password/form', [AuthController::class, 'showChangePasswordForm'])
  ->name('admin.password.form')
  ->middleware('auth:admin');

// Customer password form
Route::get('/customer/password/form', [AuthController::class, 'showChangePasswordForm'])
  ->name('customer.password.form')
  ->middleware('auth:customer');

// Shared password update endpoint (Both guards can use this)
Route::post('/password/update', [AuthController::class, 'updatePassword'])
  ->name('password.update')
  ->middleware('auth:admin,customer');


/*
|--------------------------------------------------------------------------
| Static Pages
|--------------------------------------------------------------------------
*/

Route::get('/about', function () {
  return view('about');
})->name('about');
Route::get('/contact', function () {
  return view('contact');
})->name('contact');
