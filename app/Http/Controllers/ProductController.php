<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
  public function create()
  {
    $products = Product::all();  // Add this line
    $customers = Customer::all();  // Keep if you need customers
    return view('Admin.Product_Pages.product_form', compact('products', 'customers'));
  }

  public function edit($id)
  {
    $product = Product::findOrFail($id);
    $products = Product::all();  // Add this line
    $customers = Customer::all();  // Keep if you need customers
    return view('Admin.Product_Pages.product_form', compact('product', 'products', 'customers'));
  }

  public function index(Request $request)
  {
    // ✅ SECURITY CHECK
    if (auth()->guard('admin')->check()) {
      $user = auth()->guard('admin')->user();
      if ($user->role_id != 1 && $user->role_id != 2) {
        abort(403, 'Unauthorized access.');
      }
    } else {
      abort(403, 'Unauthorized access.');
    }

    $query = Product::query();

    // Apply Category Filter
    if ($request->filled('category')) {
      $query->where('category', $request->category);
    }
    // Apply Type Filter
    if ($request->filled('type')) {
      $query->where('type', $request->type);
    }

    $products = $query->orderBy('created_at', 'desc')->get();
    $categories = Product::distinct()->pluck('category')->filter()->values();
    $types = Product::distinct()->pluck('type')->filter()->values();

    // ✅ IF AJAX REQUEST: Return ONLY the table rows partial
    if ($request->ajax()) {
      return view('Admin.Product_Pages.partials.product_table_rows', compact('products'));
    }

    // ✅ NORMAL REQUEST: Return the full page
    return view('Admin.Product_Pages.products', compact('products', 'categories', 'types'))->with('request', $request);
  }

  public function store(Request $request)
  {
    // 1. CREATE VALIDATOR INSTANCE
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'price' => 'required|numeric|min:0',
      'quantity' => 'required|integer|min:0',
      'category' => 'required|string',
      'type' => 'nullable|string',
      'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ], [
      'title.required' => 'Please enter the product title.',
      'price.required' => 'Please enter the product price.',
      'price.numeric' => 'Price must be a valid number.',
      'price.min' => 'Price cannot be less than 0.',
      'quantity.required' => 'Please enter the product quantity.',
      'quantity.integer' => 'Quantity must be a whole number.',
      'quantity.min' => 'Quantity cannot be less than 0.',
      'category.required' => 'Please select a category for the product.',
      'image.image' => 'The file must be a valid image (JPEG, PNG, JPG, GIF).',
      'image.max' => 'The image size must not exceed 2MB.',
    ]);

    // 2. CHECK VALIDATION AND RETURN 422 JSON ON FAILURE
    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    $product = new Product();
    $product->title = $request->title;
    $product->description = $request->description;
    $product->price = $request->price;
    $product->quantity = $request->quantity;
    $product->category = $request->category;
    $product->type = $request->type;

    if ($request->hasFile('image')) {
      if ($product->image && file_exists(public_path($product->image))) {
        unlink(public_path($product->image));
      }
      $file = $request->file('image');
      $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
      $file->move(public_path('uploads/products'), $filename);
      $product->image = 'uploads/products/' . $filename;
    }

    $product->save();

    // 3. RETURN 200 JSON SUCCESS
    return response()->json([
      'success' => true,
      'message' => 'Product added successfully!'
    ]);
  }

  public function update(Request $request)
  {
    $product = Product::findOrFail($request->id);

    // 1. CREATE VALIDATOR INSTANCE
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'price' => 'required|numeric|min:0',
      'quantity' => 'required|integer|min:0',
      'category' => 'required|string',
      'type' => 'nullable|string',
      'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ], [
      'title.required' => 'Please enter the product title.',
      'price.required' => 'Please enter the product price.',
      'price.numeric' => 'Price must be a valid number.',
      'price.min' => 'Price cannot be less than 0.',
      'quantity.required' => 'Please enter the product quantity.',
      'quantity.integer' => 'Quantity must be a whole number.',
      'quantity.min' => 'Quantity cannot be less than 0.',
      'category.required' => 'Please select a category for the product.',
      'image.image' => 'The file must be a valid image (JPEG, PNG, JPG, GIF).',
      'image.max' => 'The image size must not exceed 2MB.',
    ]);

    // 2. CHECK VALIDATION AND RETURN 422 JSON ON FAILURE
    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    $product->title = $request->title;
    $product->description = $request->description;
    $product->price = $request->price;
    $product->quantity = $request->quantity;
    $product->category = $request->category;
    $product->type = $request->type;

    if ($request->hasFile('image')) {
      if ($product->image && file_exists(public_path($product->image))) {
        unlink(public_path($product->image));
      }
      $file = $request->file('image');
      $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
      $file->move(public_path('uploads/products'), $filename);
      $product->image = 'uploads/products/' . $filename;
    }

    $product->save();

    // 3. RETURN 200 JSON SUCCESS
    return response()->json([
      'success' => true,
      'message' => 'Product updated successfully!'
    ]);
  }
  /**
   * Delete product
   */
  public function destroy($id)
  {
    $product = Product::findOrFail($id);
    if ($product->image && file_exists(public_path($product->image))) {
      unlink(public_path($product->image));
    }
    $product->delete();

    return redirect()->route('products')->with('success', 'Product deleted successfully!');
  }

  /**
   * Customer products view
   */
  public function customerProducts()
  {
    $products = Product::all();
    return view('Admin.Product_Pages.products', compact('products'));
  }
}
