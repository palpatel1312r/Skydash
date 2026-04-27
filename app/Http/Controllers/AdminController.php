<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Customer; // Don't forget to import the Customer model
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    //
    public function index()
    {
        return view('index');
    }
    public function Profile()
    {
        return view('Dashboard.Profile');
    }
    public function Customer()
    {
        $customers = Customer::all();
        return view('Dashboard.Customer', compact('customers'));
    }
    public function Order()
    {
        $Order = Order::all();
        return view('Dashboard.Orders', compact('Order'));
    }
    /////////////////////////////////////////////////////////////////////////
    public function changeCustomerStatus($status, $id)
    {

        $customers = Customer::find($id);

        $customers->status = $status;
        $customers->save();

        return redirect()->route('Customer')->with('success', 'User status updated successfully');
    }

    public function products()
    {
        $products = Product::all();
        return view('Dashboard.products', compact('products'));
    }
    public function deleteProduct($id)
    {
        $product = Product::find($id);
        $product->delete();
        return redirect()->back()->with('success', 'Product Deleted successfully!');
    }
    ////////////////////////////////////////////////////////////////////////

    public function AddNewProduct(Request $request)
    {

        $product = new Product();
        $product->title = $request->input('title');

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            // Save the file into /public/uploads/products
            $file->move(public_path('uploads/products'), $filename);

            // Save the path to database
            $product->image = 'uploads/products/' . $filename;
        } else {
            // Set default image if none uploaded
            $product->image = 'uploads/products/default.png';
        }

        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->quantity = $request->input('quantity');
        $product->category = $request->input('category');
        $product->type = $request->input('type');

        $product->save(); // Save the product to the database

        return redirect()->back()->with('success', 'Product added successfully!');
    }

    ////////////////////////////////////////////////////////////////////////

    public function UpdateProduct(Request $request)
    {
        $product = Product::find($request->id);
        $product->title = $request->title;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            // Save the file into /public/uploads/products
            $file->move(public_path('uploads/products'), $filename);

            // Save the new image path
            $product->image = 'uploads/products/' . $filename;
        }

        $product->description = $request->description;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->category = $request->category;
        $product->type = $request->type;

        $product->save(); // Save the product to the database

        return redirect()->back()->with('success', 'Product updated successfully!');
    }

    ////////////////////////////////////////////////////////////////////////

    public function UpdateCustomer(Request $request)
    {
        $customer = Customer::find($request->id);

        $customer->fullname = $request->fullname;
        $customer->email = $request->email;
        $customer->role = $request->role;
        $customer->status = $request->status;

        $customer->save(); // Save the product to the database

        return redirect()->back()->with('success', 'Customer updated successfully!');
    }

    ////////////////////////////////////////////////////////////////////////
    public function Orders(Request $request)
    {
        $orderItems = DB::table('orders')
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->select('products.title', 'products.picture', 'products.price', 'products.*', 'orders.*')
            ->get();
        return view('Dashboard.Orders', compact('orderItems'));
    }

    ///////////////////////////////////////////////////////////////////

    public function AddNewOrder(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email',
            'bill' => 'required|numeric|min:0',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'customer_status' => 'required|string',
            'status' => 'required|string|in:Pending,Accepted,Rejected,Delivered',
        ]);

        $order = new Order();
        $order->fullname = $request->input('fullname');
        $order->email = $request->input('email');
        $order->bill = $request->input('bill');
        $order->phone = $request->input('phone');
        $order->address = $request->input('address');
        $order->customer_status = $request->input('customer_status');
        $order->status = $request->input('status');
        $order->status = $request->input('status'); // Set orderstatus as well

        $order->save();

        return redirect()->back()->with('success', 'Order added successfully!');
    }

    //////////////////////////////////////////////////////////////////

    public function changeOrderStatus($status, $id)
    {
        $validStatuses = ['Pending', 'Accepted', 'Rejected', 'Delivered'];

        if (!in_array($status, $validStatuses)) {
            return redirect()->back()->with('error', 'Invalid status value.');
        }

        $order = Order::find($id);
        if ($order) {
            $order->status = $status;
            $order->save();
            return redirect()->back()->with('success', 'Order status updated to ' . $status);
        }
        return redirect()->back()->with('error', 'Order not found.');
    }
}
