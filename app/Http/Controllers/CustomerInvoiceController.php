<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CustomerInvoiceController extends Controller
{
  public function customerInvoices(Request $request)
  {
    $customer = auth()->guard('customer')->user();
    $customers = Customer::all();
    $query = Invoice::where('customer_id', $customer->id);

    if ($request->filled('start_date') && $request->filled('end_date')) {
      $startDateTime = \Carbon\Carbon::parse($request->start_date)->startOfDay();
      $endDateTime = \Carbon\Carbon::parse($request->end_date)->endOfDay();
      $query->whereBetween('invoice_date', [$startDateTime, $endDateTime]);
    }

    $invoices = $query->orderBy('invoice_date', 'desc')->get();

    return view('Customer_Pages.customer_invoices', compact('invoices', 'customer', 'customers'))->with('request', $request);
  }
  public function customerStore(Request $request)
  {
    Log::info('=== CUSTOMER STORE REQUEST ===');
    Log::info('All request data:', $request->all());

    $customer = Auth::guard('customer')->user();
    $request->validate(['total_rows' => 'required|integer|min:1']);

    $validator = Validator::make($request->all(), [
      'invoice_date'    => 'required|date',
      'tax_rate'        => 'required|numeric|min:0',
      'quantity.*'      => 'required|integer|min:1',
      'price.*'         => 'required|numeric|min:0',
      'subtotal.*'      => 'required|numeric|min:0',
      'product_id.*'    => 'required|exists:product,id',
    ], [
      'product_id.*.required' => 'Please select a valid Product from the dropdown.',
      'product_id.*.exists'   => 'The selected product does not exist.',
      'price.*.required'      => 'Please enter a valid price for the product.',
      'price.*.numeric'       => 'Price must be a valid number.',
      'subtotal.*.required'   => 'Please enter a valid subtotal for the product.',
      'subtotal.*.numeric'    => 'Subtotal must be a valid number.',
      'subtotal.*.min'        => 'Subtotal cannot be less than 0.',
      'tax_rate.required'     => 'Please enter the tax rate.',
      'tax_rate.numeric'      => 'Tax rate must be a valid number.',
      'tax_rate.min'          => 'Tax rate cannot be less than 0.',
      'quantity.*.required'   => 'Please enter a valid quantity.',
      'quantity.*.integer'    => 'Quantity must be a whole number.',
      'quantity.*.min'        => 'Quantity must be at least 1.',
    ]);

    if ($validator->fails()) {
      Log::info('=== VALIDATION FAILED ===');
      Log::info('Validation errors:', $validator->errors()->toArray());
      return response()->json(['errors' => $validator->errors()], 422);
    }

    $products = [];
    $subtotal = 0;

    foreach ($request->product_id as $key => $productId) {
      $product = Product::find($productId);
      $price = floatval($request->price[$key]);
      $quantitySold = $request->quantity[$key] ?? 1;
      $productSubtotal = $price * $quantitySold;

      if (!$product->hasStock($quantitySold)) {
        return response()->json([
          'errors' => ['stock' => ['Not enough stock for product: ' . $product->title]]
        ], 422);
      }

      $products[] = [
        'product_id'    => $productId,
        'product_name'  => $product->title,
        'price'         => $price,
        'quantity'      => $quantitySold,
        'subtotal'      => $productSubtotal,
      ];

      $product->decreaseStock($quantitySold);
      $subtotal += $productSubtotal;
    }

    // Clear the customer's cart after successful invoice creation
    Cart::where('customer_id', $customer->id)->delete();

    $taxRate = floatval($request->tax_rate);
    $taxAmount = $subtotal * ($taxRate / 100);
    $totalAmount = $subtotal + $taxAmount;
    $invoiceNumber = 'INV-' . date('Ymd') . '-' . rand(100, 999);

    Invoice::create([
      'invoice_number'    => $invoiceNumber,
      'invoice_date'      => $request->invoice_date,
      'customer_id'       => $customer->id,
      'customer_name'     => $customer->fullname,
      'customer_email'    => $customer->email,
      'customer_phone'    => $customer->phone ?? 'N/A',
      'customer_address'  => $customer->address ?? 'N/A',
      'products'          => $products,
      'subtotal'          => $subtotal,
      'tax_rate'          => $taxRate,
      'tax_amount'        => $taxAmount,
      'total_amount'      => $totalAmount,
    ]);
    // Clear cart only after successful creation
    Cart::where('customer_id', $customer->id)->delete();

    return response()->json([
      'success' => true,
      'message' => 'Invoice created successfully!'
    ]);
  }


  public function customerCreate()
  {
    $customer = Auth::guard('customer')->user();
    $products = Product::all();

    // ✅ Default: empty cart items collection
    $cartItems = collect();

    // 1. Check if user came from the Cart page (from_cart=1)
    $fromCart = request()->query('from_cart');

    if ($fromCart) {
      // Load the actual cart items from the database
      $cartItems = Cart::where('customer_id', $customer->id)
        ->with('product')
        ->get();
    }

    // 2. Check if user came from "Buy Now" (product_id in URL)
    $productId = request()->query('product_id');
    $quantity = request()->query('quantity', 1);

    if ($productId && !$fromCart) {
      $product = Product::find($productId);
      if ($product) {
        $tempItem = (object) [
          'product_id' => $product->id,
          'quantity'   => $quantity,
          'product'    => $product,
        ];
        $cartItems = collect([$tempItem]);
      }
    }

    return view('Customer_Pages.customer_invoice_create', compact('products', 'cartItems'));
  }
}
