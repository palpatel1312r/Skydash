<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'date_range' => 'nullable|in:today,yesterday,this_week,last_week,this_month,last_month,custom',
            'start_date' => 'nullable|required_if:date_range,custom|date_format:Y-m-d',
            'end_date' => 'nullable|required_if:date_range,custom|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        $query = Invoice::with('customer')->orderBy('created_at', 'desc');

        // Date Range Filter
        if ($request->filled('date_range')) {
            $now = now();
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('invoice_date', $now->toDateString());
                    break;
                case 'yesterday':
                    $query->whereDate('invoice_date', $now->copy()->subDay()->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('invoice_date', [
                        $now->copy()->startOfWeek()->toDateString(),
                        $now->copy()->endOfWeek()->toDateString(),
                    ]);
                    break;
                case 'last_week':
                    $lastWeekStart = $now->copy()->subWeek()->startOfWeek()->toDateString();
                    $lastWeekEnd = $now->copy()->subWeek()->endOfWeek()->toDateString();
                    $query->whereBetween('invoice_date', [$lastWeekStart, $lastWeekEnd]);
                    break;
                case 'this_month':
                    $query->whereBetween('invoice_date', [
                        $now->copy()->startOfMonth()->toDateString(),
                        $now->copy()->endOfMonth()->toDateString(),
                    ]);
                    break;
                case 'last_month':
                    $lastMonth = $now->copy()->startOfMonth()->subMonth();
                    $query->whereBetween('invoice_date', [
                        $lastMonth->copy()->startOfMonth()->toDateString(),
                        $lastMonth->copy()->endOfMonth()->toDateString(),
                    ]);
                    break;
                case 'custom':
                    if ($request->filled('start_date') && $request->filled('end_date')) {
                        $query->whereBetween('invoice_date', [$request->start_date, $request->end_date]);
                    }
                    break;
            }
        }

        // Customer Filter
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        $invoices = $query->get();

        // Group products for display
        $invoices->transform(function ($invoice) {
            if (is_array($invoice->products)) {
                $grouped = [];
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
            return $invoice;
        });

        $stats = $this->getInvoiceStats($query);
        $customers = Customer::orderBy('fullname')->get();
        $products = Product::orderBy('title')->get();

        return view('Admin.Invoice pages.invoices', compact('invoices', 'customers', 'products', 'stats'));
    }

    private function getInvoiceStats($baseQuery)
    {
        $clone = clone $baseQuery;
        return [
            'total_invoices' => (clone $clone)->count(),
            'total_revenue' => (clone $clone)->sum('total_amount'),
            'paid_count' => (clone $clone)->where('status', 'Paid')->count(),
            'unpaid_count' => (clone $clone)->where('status', 'Unpaid')->count(),
            'total_tax' => (clone $clone)->sum('tax_amount'),
        ];
    }

    public function create()
    {
        $customers = Customer::where('status', 'Active')->get();
        $products = Product::all();
        return view('Admin.Invoice pages.admin_invoice_form', compact('customers', 'products'));
    }

    public function edit($id)
    {
        $invoice = Invoice::with('customer')->findOrFail($id);
        $customers = Customer::all();
        $products = Product::all();
        return view('Admin.Invoice pages.admin_invoice_form', compact('invoice', 'customers', 'products'));
    }

    public function store(Request $request)
    {
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
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . rand(100, 999);

        $invoice = Invoice::create([
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

        return response()->json([
            'success' => true,
            'message' => 'Invoice created successfully! Invoice #' . $invoice->invoice_number
        ]);
    }

    public function update(Request $request, $id)
    {
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
            'subtotal.*'   => 'required|numeric|min:0',
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
            'subtotal.*.required'   => 'Subtotal is required.',
            'subtotal.*.numeric'    => 'Subtotal must be a valid number.',
            'subtotal.*.min'        => 'Subtotal cannot be less than 0.',
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

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();
        return redirect()->route('invoices')->with('success', 'Invoice deleted successfully!');
    }
}
