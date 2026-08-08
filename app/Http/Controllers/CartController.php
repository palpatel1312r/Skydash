<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::where('customer_id', Auth::guard('customer')->id())
            ->with('product')
            ->get();

        return view('admin.cart.cart', compact('cartItems'));
    }

    public function updateQuantity(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::where('id', $id)
            ->where('customer_id', Auth::guard('customer')->id())
            ->firstOrFail();

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        $subtotal = $cartItem->product->price * $cartItem->quantity;

        $total = Cart::where('customer_id', Auth::guard('customer')->id())
            ->with('product')
            ->get()
            ->sum(fn($item) => $item->product->price * $item->quantity);

        return response()->json([
            'success'   => true,
            'subtotal'  => number_format($subtotal, 2),
            'total'     => number_format($total, 2),
            'cartCount' => Cart::where('customer_id', Auth::guard('customer')->id())->sum('quantity'),
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:product,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $customerId = Auth::guard('customer')->id();

        if (!$customerId) {
            return response()->json(['message' => 'You must be logged in as a customer.'], 401);
        }

        // ✅ CHECK: If product already exists in cart, UPDATE quantity instead of creating a new row
        $cartItem = Cart::where('customer_id', $customerId)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            // Increase existing quantity
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Create new cart row
            Cart::create([
                'customer_id' => $customerId,
                'product_id'  => $request->product_id,
                'quantity'    => $request->quantity,
            ]);
        }

        return response()->json([
            'success'   => true,
            'message'   => 'Item added to cart!',
            'cartCount' => Cart::where('customer_id', $customerId)->sum('quantity'),
        ]);
    }
    public function buyNow(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:product,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $customerId = Auth::guard('customer')->id();

        if (!$customerId) {
            return response()->json(['message' => 'You must be logged in as a customer.'], 401);
        }

        // DON'T clear the cart - just redirect to invoice with product_id parameter
        // The invoice controller will handle showing only this product

        return response()->json([
            'success' => true,
            'message' => 'Redirecting to checkout...',
            'redirect_url' => route('customer.invoices.create', [
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'from_buy_now' => 1
            ])
        ]);
    }

    public function remove($id)
    {
        $cart = Cart::where('id', $id)
            ->where('customer_id', Auth::guard('customer')->id())
            ->firstOrFail();

        $cart->delete();

        return redirect()->route('admin.cart.cart')->with('success', 'Item removed from cart.');
    }
}
