<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductController extends Controller
{
  /**
   * Display list of products
   */
  public function index()
  {
    $products = Product::all();

    // ✅ Debug - Check if products exist
    \Log::info('Products count: ' . $products->count());

    return view('Dashboard.products', compact('products'));
  }

  /**
   * Store a new product
   */
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
    ]);

    $product = new Product();
    $product->title = $request->title;
    $product->description = $request->description;
    $product->price = $request->price;
    $product->quantity = $request->quantity;
    $product->category = $request->category;
    $product->type = $request->type;

    if ($request->hasFile('image')) {
      $file = $request->file('image');
      $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
      $file->move(public_path('uploads/products'), $filename);
      $product->image = 'uploads/products/' . $filename;
    }

    $product->save();

    return redirect()->route('products')->with('success', 'Product added successfully!');
  }

  /**
   * Update product
   */
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
    return view('Dashboard.products', compact('products'));
  }
}
