<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
  public function create()
  {
    session(['active_menu' => 'products']);
    return view('Dashboard.product pages.products_create');
  }

  public function edit($id)
  {
    session(['active_menu' => 'products']);
    $product = Product::findOrFail($id);
    return view('Dashboard.product pages.products_update', compact('product'));
  }
  public function index()
  {
    session(['active_menu' => 'products']);


    $products = Product::orderBy('created_at', 'desc')->get();
    Log::info('Products count: ' . $products->count());

    return view('Dashboard.product pages.products', compact('products'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'price' => 'required|numeric|min:0',
      'quantity' => 'required|integer|min:0',
      'category' => 'required|string',
      'type' => 'nullable|string',
      'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ], [
      // ✅ Custom error messages go here
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

    return redirect()->route('products')->with('success', 'Product added successfully!');
  }

  public function update(Request $request)
  {
    $product = Product::findOrFail($request->id);

    $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'price' => 'required|numeric|min:0',
      'quantity' => 'required|integer|min:0',
      'category' => 'required|string',
      'type' => 'nullable|string',
      'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ], [
      // ✅ Custom error messages go here
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

    return redirect()->route('products')->with('success', 'Product updated successfully!');
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
    return view('Dashboard.product pages.products', compact('products'));
  }
}
