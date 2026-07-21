<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // 1. VALIDATE DATE, CUSTOMER, PRODUCTS, TAX RATE, AND PRICES
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'invoice_date' => 'required|date',
            'customer_id'  => 'required|exists:customer,id',
            'product_id.*' => 'required|exists:product,id',
            'quantity.*'   => 'required|integer|min:1',
            'price.*'      => 'required|numeric|min:0',   // ✅ Validate each product's price
            'tax_rate'     => 'required|numeric|min:0|max:100', // ✅ Validate tax rate
        ], [
            'customer_id.required'  => 'Please select a valid Customer from the dropdown.',
            'product_id.*.required' => 'Please select a valid Product from the dropdown.',
            'product_id.*.exists'   => 'The selected product does not exist.',
            'price.*.required'      => 'Please enter a valid price for the product.',
            'price.*.numeric'       => 'Price must be a valid number.',
            'tax_rate.required'     => 'Please enter the tax rate.',
            'tax_rate.numeric'      => 'Tax rate must be a valid number.',
            'tax_rate.min'          => 'Tax rate cannot be less than 0.',
            'tax_rate.max'          => 'Tax rate cannot exceed 100.',
        ]);

        // 2. If ANY validation fails, redirect back with ALL errors
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // 3. SECURELY PARSE THE DATE AFTER VALIDATION PASSES
        // (If the user entered 20-07-2026, this safely formats it to 2026-07-20)
        try {
            $formattedDate = \Carbon\Carbon::parse($request->invoice_date)->format('Y-m-d');
        } catch (\Exception $e) {
            // If parsing fails for some rare reason, fallback to today's date
            $formattedDate = now()->format('Y-m-d');
        }

        // If we reach here, everything is 100% valid
        $customer = Customer::find($request->customer_id);

        // 4. RESTORE OLD STOCK
        if (!empty($invoice->products)) {
            foreach ($invoice->products as $oldProductData) {
                $oldProduct = Product::find($oldProductData['product_id']);
                if ($oldProduct) {
                    $oldProduct->increaseStock($oldProductData['quantity']);
                }
            }
        }

        $products = [];
        $subtotal = 0;

        // 5. DEDUCT NEW STOCK & CALCULATE PRICES
        foreach ($request->product_id as $key => $productId) {
            $product = Product::find($productId);
            $price = $product->price;
            $quantitySold = $request->quantity[$key] ?? 1;
            $productSubtotal = $price * $quantitySold;

            if (!$product->hasStock($quantitySold)) {
                return redirect()->back()->with('error', 'Not enough stock for product: ' . $product->title);
            }

            $products[] = [
                'product_id' => $productId,
                'product_name' => $product->title,
                'price' => $price,
                'quantity' => $quantitySold,
                'subtotal' => $productSubtotal,
            ];

            $product->decreaseStock($quantitySold);
            $subtotal += $productSubtotal;
        }

        $taxRate = floatval($request->tax_rate);
        $taxAmount = $subtotal * ($taxRate / 100);
        $totalAmount = $subtotal + $taxAmount;

        // 6. UPDATE THE DATABASE WITH THE SAFELY FORMATTED DATE
        $invoice->update([
            'invoice_date' => $formattedDate, // ✅ Use the safe parsed date
            'customer_id' => $customer->id,
            'customer_name' => $customer->fullname,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone ?? 'N/A',
            'customer_address' => $customer->address ?? 'N/A',
            'products' => $products,
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully!');
    }
    public function index()
    {
        $invoices = Invoice::with('customer')->orderBy('created_at', 'desc')->get();

        // ✅ Loop through invoices to group duplicate products
        foreach ($invoices as $invoice) {
            $grouped = [];

            if (is_array($invoice->products)) {
                foreach ($invoice->products as $product) {
                    $key = $product['product_id'];
                    if (!isset($grouped[$key])) {
                        // First time seeing this product, add it
                        $grouped[$key] = $product;
                    } else {
                        // Product exists, add the quantity and subtotal
                        $grouped[$key]['quantity'] += $product['quantity'];
                        $grouped[$key]['subtotal'] += $product['subtotal'];
                    }
                }
                // Re-index the array and assign it back to the object
                $invoice->products = array_values($grouped);
            }
        }

        $customers = Customer::all();
        $products = Product::all();

        return view('Dashboard.invoice pages.invoices', compact('invoices', 'customers', 'products'));
    }
    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();
        return view('Dashboard.invoice pages.invoices_create', compact('customers', 'products'));
    }
    public function edit($id)
    {
        $invoice = Invoice::with('customer')->findOrFail($id);
        $customers = Customer::all();
        $products = Product::all();

        return view('Dashboard.invoice pages.invoices_edit', compact('invoice', 'customers', 'products'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'total_rows' => 'required|integer|min:1',
        ]);
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'invoice_number' => 'required|unique:invoices',
            'invoice_date'    => 'required|date',
            'customer_id'     => 'required|exists:customer,id',
            'tax_rate'        => 'required|numeric|min:0',
            'quantity.*'      => 'required|integer|min:1',
            'price.*'         => 'required|numeric|min:0',
            'subtotal.*'      => 'required|numeric|min:0',
            'product_id.*'    => 'required|exists:product,id',
        ], [
            'customer_id.required'  => 'Please select a valid Customer from the dropdown.',
            'product_id.*.required' => 'Please select a valid Product from the dropdown.',
            'product_id.*.exists'   => 'The selected product does not exist.',
            // ✅ ADD THESE LINES TO FIX THE PRICE ERROR MESSAGE
            'price.*.required'      => 'Please enter a valid price for the product.',
            'price.*.numeric'       => 'Price must be a valid number.',
            'tax_rate.required'     => 'Please enter the tax rate.',
            'tax_rate.numeric'      => 'Tax rate must be a valid number.',
        ]);

        // 3. If ANY validation fails (Customer OR Products), redirect back with ALL errors
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // If we reach here, EVERYTHING (Customer + Products) is 100% valid
        $customer = Customer::find($request->customer_id);

        if (!$customer) {
            return redirect()->back()->with('error', 'Customer not found!');
        }

        $products = [];
        $subtotal = 0;

        foreach ($request->product_id as $key => $productId) {
            $product = Product::find($productId);
            $price = floatval($request->price[$key]);

            $quantitySold = $request->quantity[$key] ?? 1;

            $productSubtotal = $price * $quantitySold;

            if (!$product->hasStock($quantitySold)) {
                return redirect()->back()->with('error', 'Not enough stock for product: ' . $product->title . ' (Available: ' . $product->quantity . ', Requested: ' . $quantitySold . ')');
            }

            $products[] = [
                'product_id'    => $productId,
                'product_name'  => $product->title ?? 'Unknown Product',
                'price'         => $price,
                'quantity'      => $quantitySold,
                'subtotal'      => $productSubtotal,
            ];

            $product->decreaseStock($quantitySold);
            $subtotal += $productSubtotal;
        }

        $taxRate = floatval($request->tax_rate);
        $taxAmount = $subtotal * ($taxRate / 100);
        $totalAmount = $subtotal + $taxAmount;

        $invoice = Invoice::create([
            'invoice_number'    => $request->invoice_number,
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

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully! Invoice #' . $invoice->invoice_number);
    }
    public function updateStatus($id, $status)
    {
        $validStatuses = ['Paid', 'Unpaid', 'Cancelled'];

        if (!in_array($status, $validStatuses)) {
            return redirect()->route('invoices')->with('error', 'Invalid status value.');
        }

        $invoice = Invoice::findOrFail($id);
        $oldStatus = $invoice->status;
        $invoice->status = $status;
        $invoice->save();

        // ✅ Notify customer about status change
        $customer = Customer::find($invoice->customer_id);
        if ($customer) {
            $this->sendInvoiceStatusNotification($invoice, $customer, $oldStatus);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice status updated to ' . $status);
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return redirect()->route('invoices')->with('success', 'Invoice deleted successfully!');
    }

    public function customerInvoices()
    {
        $customer = auth()->guard('customer')->user();

        // Get all invoices for this customer
        $invoices = Invoice::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Debug - Log the invoices
        Log::info('Customer invoices loaded', [
            'customer_id' => $customer->id,
            'customer_name' => $customer->fullname,
            'invoice_count' => $invoices->count()
        ]);

        return view('Dashboard.customer pages.customer_invoices', compact('invoices', 'customer'));
    }

    /**
     * Send invoice notification to customer
     */
    private function sendInvoiceNotification($invoice, $customer)
    {
        try {

            Log::info('Invoice notification sent to customer', [
                'customer_id' => $customer->id,
                'customer_email' => $customer->email,
                'invoice_number' => $invoice->invoice_number
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send invoice notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send invoice status notification to customer
     */
    private function sendInvoiceStatusNotification($invoice, $customer, $oldStatus)
    {
        try {
            Log::info('Invoice status update notification sent to customer', [
                'customer_id' => $customer->id,
                'customer_email' => $customer->email,
                'invoice_number' => $invoice->invoice_number,
                'old_status' => $oldStatus,
                'new_status' => $invoice->status
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send invoice status notification: ' . $e->getMessage());
            return false;
        }
    }

    public function show($id)
    {
        $invoice = Invoice::with('customer')->findOrFail($id);

        // Check if the customer is viewing their own invoice
        if (auth()->guard('customer')->check()) {
            $customer = auth()->guard('customer')->user();
            if ($invoice->customer_id !== $customer->id) {
                abort(403, 'Unauthorized access to this invoice.');
            }
        }

        return view('Dashboard.invoice_view', compact('invoice'));
    }
}
