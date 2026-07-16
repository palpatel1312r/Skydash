@extends('components.adminheader')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">
                                    <i class="mdi mdi-file-document-outline text-primary"></i> Create New Invoice
                                </h4>
                                <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="mdi mdi-arrow-left"></i> Back to Invoices
                                </a>
                            </div>

                            <form action="{{ route('invoices.store') }}" method="POST">
                                @csrf

                                {{-- Invoice Details --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Invoice Number</label>
                                            <input type="text" name="invoice_number" class="form-control"
                                                value="INV-{{ date('Ymd') }}-{{ rand(100, 999) }}" readonly required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Invoice Date</label>
                                            <input type="date" name="invoice_date" class="form-control"
                                                value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- Customer Selection --}}
                                <div class="form-group">
                                    <label>Select Customer</label>
                                    <select name="customer_id" id="customerSelect" class="form-control" required>
                                        <option value="">-- Select Customer --</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->fullname }}
                                                ({{ $customer->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <hr>

                                {{-- Products Section --}}
                                <h5>Products</h5>

                                {{-- ✅ ADDED: The missing wrapper with id="product-rows" --}}
                                <div id="product-rows">
                                    <div class="row product-row align-items-end pr-0" data-row-id="1">
                                        {{-- Select Product: 4 columns --}}
                                        <div class="col-md-4">
                                            <label>Select Product</label>
                                            <select name="product_id[]" class="form-control product-select"
                                                onchange="updateProductDetails(this)" required>
                                                <option value="">-- Select Product --</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}"
                                                        data-name="{{ $product->title }}">
                                                        {{ $product->title }} - ₹{{ number_format($product->price, 2) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Qty: 1 column (small) --}}
                                        <div class="col-md-1">
                                            <label>Qty</label>
                                            <input type="number" name="quantity[]" class="form-control product-quantity"
                                                value="1" min="1" required>
                                        </div>

                                        {{-- Price: 2 columns --}}
                                        <div class="col-md-2">
                                            <label>Price (₹)</label>
                                            <input type="number" name="price[]" class="form-control product-price"
                                                placeholder="Price" step="0.01" readonly style="background: #f8f9fa;">
                                        </div>

                                        {{-- Subtotal: 3 columns --}}
                                        <div class="col-md-3">
                                            <label>Subtotal (₹)</label>
                                            <input type="text" name="subtotal[]" class="form-control product-subtotal"
                                                readonly style="background: #f8f9fa;">
                                        </div>

                                        {{-- Buttons: col-md-2 --}}
                                        <div class="col-md-2 d-flex flex-row align-items-end justify-content-end"
                                            style="padding-bottom: 5px; gap: 5px;">
                                            <button type="button" class="btn btn-success btn-sm" onclick="addProductRow()"
                                                style="height: 38px; font-size: 12px; padding: 0 8px; white-space: nowrap;">
                                                <i class="mdi mdi-plus" style="font-size: 14px;"></i> Add
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm remove-row"
                                                onclick="removeProductRow(this)"
                                                style="height: 38px; font-size: 12px; padding: 0 8px; white-space: nowrap;">
                                                <i class="mdi mdi-delete" style="font-size: 14px;"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                {{-- Totals Section --}}
                                <div class="row">
                                    <div class="col-md-6 offset-md-6">
                                        <div class="form-group">
                                            <label>Tax Rate (%)</label>
                                            <input type="number" name="tax_rate" class="form-control" value="10"
                                                step="0.01" oninput="calculateTotal()" placeholder="Tax Rate">
                                        </div>
                                        <div class="form-group">
                                            <label>Subtotal</label>
                                            <input type="text" name="subtotal_amount" id="subtotal_amount"
                                                class="form-control" readonly style="background: #f8f9fa;">
                                        </div>
                                        <div class="form-group">
                                            <label>Tax Amount</label>
                                            <input type="text" name="tax_amount" id="tax_amount" class="form-control"
                                                readonly style="background: #f8f9fa;">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Total Amount</strong></label>
                                            <input type="text" name="total_amount" id="total_amount"
                                                class="form-control" readonly
                                                style="background: #f8f9fa; font-weight: bold; font-size: 1.2em;">
                                        </div>
                                    </div>
                                </div>

                                {{-- Form Actions --}}
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save"></i> Create Invoice
                                    </button>
                                    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                                        <i class="mdi mdi-arrow-left"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add Product Row
        function addProductRow() {
            const row = document.querySelector('.product-row').cloneNode(true);
            const container = document.getElementById('product-rows');
            row.querySelectorAll('input').forEach(input => input.value = '');
            row.querySelector('.product-subtotal').value = '';
            row.querySelector('.product-quantity').value = 1;
            const select = row.querySelector('.product-select');
            if (select) select.selectedIndex = 0;
            const qtyInput = row.querySelector('.product-quantity');
            qtyInput.addEventListener('input', function() {
                const productSelect = this.closest('.product-row').querySelector('.product-select');
                if (productSelect.value) updateProductDetails(productSelect);
            });
            container.appendChild(row);
            updateRowNumbers();
            calculateTotal();
        }

        function removeProductRow(button) {
            const rows = document.querySelectorAll('.product-row');
            if (rows.length > 1) {
                button.closest('.product-row').remove();
                updateRowNumbers();
                calculateTotal();
            }
        }

        function updateRowNumbers() {
            const rows = document.querySelectorAll('.product-row');
            rows.forEach((row, index) => {
                const labels = row.querySelectorAll('label');
                labels.forEach(label => {
                    const text = label.textContent.replace(/\d+/, index + 1);
                    label.textContent = text;
                });
            });
        }

        function updateProductDetails(select) {
            const row = select.closest('.product-row');
            const priceInput = row.querySelector('.product-price');
            const subtotalInput = row.querySelector('.product-subtotal');
            const quantityInput = row.querySelector('.product-quantity');
            const selectedOption = select.options[select.selectedIndex];

            if (selectedOption && selectedOption.value) {
                const price = selectedOption.getAttribute('data-price');
                priceInput.value = price || 0;

                // ✅ NEW: Recalculate subtotal using Price × Quantity
                const quantity = parseInt(quantityInput.value) || 1;
                subtotalInput.value = (price * quantity).toFixed(2);
            } else {
                priceInput.value = '';
                subtotalInput.value = '';
            }

            calculateTotal(); // ✅ Update the Grand Total
        }

        function calculateTotal() {
            const rows = document.querySelectorAll('.product-row');
            let subtotal = 0;
            rows.forEach(row => {
                const subtotalInput = row.querySelector('.product-subtotal');
                if (subtotalInput) subtotal += parseFloat(subtotalInput.value) || 0;
            });
            const taxRate = parseFloat(document.querySelector('input[name="tax_rate"]').value) || 0;
            const taxAmount = subtotal * (taxRate / 100);
            const total = subtotal + taxAmount;
            document.getElementById('subtotal_amount').value = subtotal.toFixed(2);
            document.getElementById('tax_amount').value = taxAmount.toFixed(2);
            document.getElementById('total_amount').value = total.toFixed(2);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const customerSelect = document.getElementById('customerSelect');
            const productRows = document.getElementById('product-rows');
            const addProductBtn = document.querySelector('button[onclick="addProductRow()"]');

            function toggleProductSection() {
                const selected = customerSelect.value;
                if (selected === '') {
                    productRows.style.opacity = '0.5';
                    productRows.style.pointerEvents = 'none';
                    if (addProductBtn) addProductBtn.disabled = true;
                } else {
                    productRows.style.opacity = '1';
                    productRows.style.pointerEvents = 'auto';
                    if (addProductBtn) addProductBtn.disabled = false;
                }
            }
            toggleProductSection();
            customerSelect.addEventListener('change', toggleProductSection);

            document.querySelectorAll('.product-quantity').forEach(input => {
                input.addEventListener('input', function() {
                    const productSelect = this.closest('.product-row').querySelector(
                        '.product-select');
                    if (productSelect.value) updateProductDetails(productSelect);
                });
            });
        });
    </script>
@endsection
