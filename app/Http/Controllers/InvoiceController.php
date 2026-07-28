<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    public function store(Request $request)
    {
        // Check if it's an AJAX request
        if (!$request->ajax()) {
            return response()->json(['error' => 'Invalid request method'], 405);
        }

        $request->validate(['total_rows' => 'required|integer|min:1']);

        $validator = Validator::make($request->all(), [
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
            'price.*.required'      => 'Please enter a valid price for the product.',
            'price.*.numeric'       => 'Price must be a valid number.',
            'tax_rate.required'     => 'Please enter the tax rate.',
            'tax_rate.numeric'      => 'Tax rate must be a valid number.',
            'tax_rate.min'          => 'Tax rate cannot be less than 0.',
            'invoice_number.required' => 'The invoice number is required.',
            'invoice_number.unique'   => 'This invoice number has already been taken.',
            'quantity.*.required'   => 'Please enter a valid quantity.',
            'quantity.*.integer'    => 'Quantity must be a whole number.',
            'quantity.*.min'        => 'Quantity must be at least 1.',
            'subtotal.*.required'   => 'Subtotal is required.',
            'subtotal.*.numeric'    => 'Subtotal must be a valid number.',
            'subtotal.*.min'        => 'Subtotal cannot be less than 0.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer = Customer::find($request->customer_id);
        if (!$customer) {
            return response()->json(['errors' => ['customer_id' => ['Customer not found!']]], 422);
        }

        $products = [];
        $subtotal = 0;

        foreach ($request->product_id as $key => $productId) {
            $product = Product::find($productId);
            if (!$product) {
                return response()->json(['errors' => ['product_id' => ['Product not found!']]], 422);
            }

            $price = floatval($request->price[$key]);
            $quantitySold = $request->quantity[$key] ?? 1;
            $productSubtotal = $price * $quantitySold;

            if (!$product->hasStock($quantitySold)) {
                return response()->json(['errors' => ['stock' => ['Not enough stock for product: ' . $product->title]]], 422);
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

        return response()->json([
            'success' => true,
            'message' => 'Invoice created successfully! Invoice #' . $invoice->invoice_number
        ]);
    }

    public function update(Request $request, $id)
    {
        // Check if it's an AJAX request
        if (!$request->ajax()) {
            return response()->json(['error' => 'Invalid request method'], 405);
        }

        $invoice = Invoice::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'invoice_date' => 'required|date',
            'customer_id'  => 'required|exists:customer,id',
            'product_id.*' => 'required|exists:product,id',
            'quantity.*'   => 'required|integer|min:1',
            'price.*'      => 'required|numeric|min:0',
            'tax_rate'     => 'required|numeric|min:0|max:100',
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
            'quantity.*.required'   => 'Please enter a valid quantity.',
            'quantity.*.integer'    => 'Quantity must be a whole number.',
            'quantity.*.min'        => 'Quantity must be at least 1.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $formattedDate = \Carbon\Carbon::parse($request->invoice_date)->format('Y-m-d');
        } catch (\Exception $e) {
            $formattedDate = now()->format('Y-m-d');
        }

        $customer = Customer::find($request->customer_id);

        // Restore stock for old products
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

        foreach ($request->product_id as $key => $productId) {
            $product = Product::find($productId);
            $price = $product->price;
            $quantitySold = $request->quantity[$key] ?? 1;
            $productSubtotal = $price * $quantitySold;

            if (!$product->hasStock($quantitySold)) {
                return response()->json(['errors' => ['stock' => ['Not enough stock for product: ' . $product->title]]], 422);
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

        $invoice->update([
            'invoice_date' => $formattedDate,
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

        return response()->json([
            'success' => true,
            'message' => 'Invoice updated successfully!'
        ]);
    }

    /////////////////////////////////////////////
    public function customerStore(Request $request)
    {
        // Add this debug line at the start
        Log::info('=== CUSTOMER STORE REQUEST ===');
        Log::info('All request data:', $request->all());

        $customer = Auth::guard('customer')->user();

        // PREVENT PAGE EXPIRED ERRORS
        $request->validate(['total_rows' => 'required|integer|min:1']);

        // 1. CREATE VALIDATOR INSTANCE FIRST
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'invoice_date'    => 'required|date',
            'tax_rate'        => 'required|numeric|min:0',
            'quantity.*'      => 'required|integer|min:1',
            'price.*'         => 'required|numeric|min:0',
            'subtotal.*'      => 'required|numeric|min:0',
            'product_id.*'    => 'required|exists:product,id',
        ], [
            // ✅ CUSTOM VALIDATION MESSAGES
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

        // 2. CHECK FAILURE FIRST AND RETURN 422 JSON
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
                    'errors' => [
                        'stock' => ['Not enough stock for product: ' . $product->title]
                    ]
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

        $taxRate = floatval($request->tax_rate);
        $taxAmount = $subtotal * ($taxRate / 100);
        $totalAmount = $subtotal + $taxAmount;
        $invoiceNumber = 'INV-CUST-' . date('Ymd') . '-' . rand(100, 999);

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

        // ✅ Return 200 JSON Success
        return response()->json([
            'success' => true,
            'message' => 'Invoice created successfully!'
        ]);
    }
    /////////////////////////////////////
    public function customerCreate()
    {
        $customer = Auth::guard('customer')->user();
        $products = Product::all(); // ✅ MUST FETCH PRODUCTS HERE!

        return view('Dashboard.customer pages.customer_invoice_create', compact('customer', 'products'));
    }

    public function customerInvoices(Request $request)
    {
        $customer = auth()->guard('customer')->user();
        $customers = Customer::all(); // ✅ Pass this for the filter dropdown

        $query = Invoice::where('customer_id', $customer->id);

        // Apply Customer Filter
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Apply Time Filter
        if ($request->filled('time')) {
            $now = now();
            switch ($request->time) {
                case 'today': // ✅ Added 'today' filter
                    $query->whereDate('invoice_date', $now->toDateString());
                    break;
                case '1_week':
                    $query->where('invoice_date', '>=', $now->copy()->subWeek());
                    break;
                case '2_week':
                    $query->where('invoice_date', '>=', $now->copy()->subWeeks(2));
                    break;
                case 'this_month':
                    $query->whereMonth('invoice_date', $now->month)->whereYear('invoice_date', $now->year);
                    break;
                case 'last_month':
                    $lastMonth = $now->copy()->subMonth();
                    $query->whereMonth('invoice_date', $lastMonth->month)->whereYear('invoice_date', $lastMonth->year);
                    break;
            }
        }
        $invoices = $query->orderBy('invoice_date', 'desc')->paginate(5);

        return view('Dashboard.customer pages.customer_invoices', compact('invoices', 'customer', 'customers'));
    }
    public function index(Request $request)
    {
        $query = Invoice::with('customer')->orderBy('created_at', 'desc');

        // ✅ Apply Customer Filter
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // ✅ Apply Time Filter
        if ($request->filled('time')) {
            $now = now();
            switch ($request->time) {
                case 'today':
                    $query->whereDate('invoice_date', $now->toDateString());
                    break;
                case '1_week':
                    $query->where('invoice_date', '>=', $now->copy()->subWeek());
                    break;
                case '2_week':
                    $query->where('invoice_date', '>=', $now->copy()->subWeeks(2));
                    break;
                case 'this_month':
                    $query->whereMonth('invoice_date', $now->month)->whereYear('invoice_date', $now->year);
                    break;
                case 'last_month':
                    $lastMonth = $now->copy()->subMonth();
                    $query->whereMonth('invoice_date', $lastMonth->month)->whereYear('invoice_date', $lastMonth->year);
                    break;
            }
        }

        // ✅ Get paginated results (10 per page)
        $invoices = $query->paginate(5);

        // ✅ Group products logic (kept your existing logic)
        foreach ($invoices as $invoice) {
            $grouped = [];
            if (is_array($invoice->products)) {
                foreach ($invoice->products as $product) {
                    $key = $product['product_id'];
                    if (!isset($grouped[$key])) {
                        $grouped[$key] = $product;
                    } else {
                        $grouped[$key]['quantity'] += $product['quantity'];
                        $grouped[$key]['subtotal'] += $product['subtotal'];
                    }
                }
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
        return view('Dashboard.invoice pages.invoice_form', compact('customers', 'products'));
    }

    public function edit($id)
    {
        $invoice = Invoice::with('customer')->findOrFail($id);
        $customers = Customer::all();
        $products = Product::all();

        return view('Dashboard.invoice pages.invoice_form', compact('invoice', 'customers', 'products'));
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
        if (auth()->guard('customer')->check()) {
            $customer = auth()->guard('customer')->user();
            if ($invoice->customer_id !== $customer->id) {
                abort(403, 'Unauthorized access to this invoice.');
            }
        }
        return view('Dashboard.invoice_view', compact('invoice'));
    }
}
