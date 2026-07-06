<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('customer')->orderBy('created_at', 'desc')->get();
        $customers = Customer::where('status', 'Active')->get();
        $products = Product::all();

        return view('admin.invoices.index', compact('invoices', 'customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|unique:invoices',
            'invoice_date' => 'required|date',
            'customer_id' => 'required|exists:customer,id',
            // 👇 FIXED: Changed 'products' to 'product'
            'product_id.*' => 'required|exists:product,id',
            'price.*' => 'required|numeric|min:0',
            'subtotal.*' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0',
        ]);

        // Get customer
        $customer = Customer::find($request->customer_id);

        // Prepare products array
        $products = [];
        $subtotal = 0;

        foreach ($request->product_id as $key => $productId) {
            $product = Product::find($productId);
            $price = floatval($request->price[$key]);
            $productSubtotal = floatval($request->subtotal[$key]);

            $products[] = [
                'product_id' => $productId,
                'product_name' => $product->title,
                'price' => $price,
                'subtotal' => $productSubtotal,
            ];

            $subtotal += $productSubtotal;
        }

        $taxRate = floatval($request->tax_rate);
        $taxAmount = $subtotal * ($taxRate / 100);
        $totalAmount = $subtotal + $taxAmount;

        $invoice = Invoice::create([
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'customer_id' => $request->customer_id,
            'customer_name' => $customer->fullname,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'customer_address' => $customer->address,
            'products' => $products,
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'status' => 'Unpaid',
        ]);

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice created successfully! Invoice #' . $invoice->invoice_number);
    }

    public function updateStatus($id, $status)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->status = $status;
        $invoice->save();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice status updated to ' . $status);
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully!');
    }
}
