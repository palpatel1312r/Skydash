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
                                                value="INV-{{ date('Ymd') }}-{{ rand(100, 999) }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Invoice Date</label>
                                            <input type="date" name="invoice_date" class="form-control"
                                                value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Select Customer</label>
                                    <select name="customer_id" id="customerSelect"
                                        class="form-control @error('customer_id') is-invalid @enderror"
                                        style="color: #333; background-color: #ffffff !important;">
                                        <option value="">-- Select Customer --</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->fullname }} ({{ $customer->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr>

                                <h5>Products</h5>
                                <div id="global-alert-container" style="min-height: 10px;"></div>
                                <div id="product-rows">
                                    <div class="row product-row align-items-end pr-0" data-row-id="1">
                                        {{-- Product Dropdown (Wider) --}}
                                        <div class="col-md-5">
                                            <label>Select Product</label>
                                            <select name="product_id[]" class="form-control product-select"
                                                onchange="updateProductDetails(this)"
                                                style="color: #333; background-color: #ffffff !important;">
                                                <option value="">-- Select Product --</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}"
                                                        data-name="{{ $product->title }}"
                                                        {{ is_array(old('product_id')) && in_array($product->id, old('product_id')) ? 'selected' : '' }}>
                                                        {{ $product->title }} - ₹{{ number_format($product->price, 2) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Qty (Tiny) --}}
                                        <div class="col-md-1">
                                            <label>Qty</label>
                                            <input type="number" name="quantity[]" class="form-control product-quantity"
                                                value="1" min="1">
                                        </div>

                                        {{-- Price (Small) --}}
                                        <div class="col-md-2">
                                            <label>Price (₹)</label>
                                            <input type="number" name="price[]" class="form-control product-price"
                                                placeholder="Price" step="0.01" readonly
                                                style="background-color: #ffffff !important;">
                                        </div>

                                        {{-- Subtotal (Standard) --}}
                                        <div class="col-md-2">
                                            <label>Subtotal (₹)</label>
                                            <input type="text" name="subtotal[]" class="form-control product-subtotal"
                                                readonly style="background-color: #ffffff !important;">
                                        </div>

                                        {{-- Remove Button (Width = 2) --}}
                                        <div class="col-md-2 d-flex flex-row align-items-end justify-content-end"
                                            style="padding-bottom: 5px; gap: 5px;">
                                            <button type="button" class="btn btn-danger btn-sm remove-row"
                                                onclick="removeProductRow(this)"
                                                style="height: 38px; font-size: 12px; padding: 0 8px; white-space: nowrap;">
                                                <i class="mdi mdi-delete" style="font-size: 14px;"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Error message outside the row so it stays visible --}}
                                @error('product_id.0')
                                    <div class="text-danger small mt-1" id="product-error-msg">{{ $message }}</div>
                                @enderror

                                {{-- SINGLE ADD BUTTON AT THE BOTTOM --}}
                                <button type="button" class="btn btn-success btn-sm mt-2" onclick="addProductRow()">
                                    <i class="mdi mdi-plus"></i> Add More Product
                                </button>

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

    <style>
        /* ✅ FORCE WHITE BACKGROUND ON ALL READONLY INPUTS AND SELECTS */
        input[readonly].form-control,
        input[readonly],
        .form-control[readonly],
        .form-control[readonly]:focus,
        .form-control[readonly]:active {
            background-color: #ffffff !important;
            border-color: #ced4da !important;
            color: #212529 !important;
            opacity: 1 !important;
            cursor: default !important;
        }

        /* ✅ Fix disabled/faded buttons */
        .btn-danger.remove-row:disabled,
        .btn-danger.remove-row.disabled {
            opacity: 1 !important;
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: #fff !important;
        }

        /* ✅ Fix invalid selects (keep white background) */
        .form-control.is-invalid {
            background-color: #ffffff !important;
            background-image: none !important;
        }
    </style>

    <script>
        // Add Product Row
        function addProductRow() {
            const row = document.querySelector('.product-row').cloneNode(true);
            const container = document.getElementById('product-rows');

            // Clear inputs
            row.querySelectorAll('input').forEach(input => input.value = '');
            row.querySelector('.product-subtotal').value = '';
            row.querySelector('.product-quantity').value = 1;

            // Reset select
            const select = row.querySelector('.product-select');
            if (select) select.selectedIndex = 0;

            // Add event listener to the new select
            select.addEventListener('change', function() {
                this.classList.remove('is-invalid');

                // ✅ FIX: Find the global error div by ID and remove it completely
                const errorMsg = document.getElementById('product-error-msg');
                if (errorMsg) {
                    errorMsg.remove();
                }
            });

            const qtyInput = row.querySelector('.product-quantity');
            qtyInput.addEventListener('input', function() {
                const productSelect = this.closest('.product-row').querySelector('.product-select');
                if (productSelect.value) updateProductDetails(productSelect);
            });

            container.appendChild(row);
            calculateTotal();
        }

        function removeProductRow(button) {
            const rows = document.querySelectorAll('.product-row');

            // ✅ ERROR LOGIC: Check if only 1 row is left
            if (rows.length === 1) {
                // Find the container
                const alertContainer = document.getElementById('global-alert-container');

                // Create the error alert
                const errorHtml = `
                <div id="last-product-error" class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> You cannot remove the last product. You must have at least one product.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;

                // Insert the error into the container
                alertContainer.insertAdjacentHTML('beforeend', errorHtml);

                // ✅ CHANGED: Auto-fade out and remove the error after 3 seconds
                setTimeout(function() {
                    const errorAlert = document.getElementById('last-product-error');
                    if (errorAlert) {
                        errorAlert.style.transition = 'opacity 0.5s ease';
                        errorAlert.style.opacity = '0';
                        setTimeout(() => errorAlert.remove(), 100);
                    }
                }, 2000);

                return; // Stop the deletion
            }

            // If more than 1 row, proceed with removal
            button.closest('.product-row').remove();
            calculateTotal();
        }

        function updateProductDetails(select) {
            const row = select.closest('.product-row');
            const priceInput = row.querySelector('.product-price');
            const subtotalInput = row.querySelector('.product-subtotal');
            const quantityInput = row.querySelector('.product-quantity');
            const selectedOption = select.options[select.selectedIndex];

            // Check if a valid product is selected
            if (selectedOption && selectedOption.value && selectedOption.getAttribute('data-price')) {
                const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
                priceInput.value = price.toFixed(2);

                const quantity = parseInt(quantityInput.value) || 1;
                subtotalInput.value = (price * quantity).toFixed(2);
            } else {
                priceInput.value = '';
                subtotalInput.value = '';
            }

            calculateTotal();
        }

        function calculateTotal() {
            const rows = document.querySelectorAll('.product-row');
            let subtotal = 0;

            rows.forEach(row => {
                const subtotalInput = row.querySelector('.product-subtotal');
                if (subtotalInput && subtotalInput.value !== '') {
                    subtotal += parseFloat(subtotalInput.value) || 0;
                }
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


            customerSelect.addEventListener('change', function() {
                this.classList.remove('is-invalid');
                const errorDiv = this.closest('.form-group').querySelector(
                    '.invalid-feedback, .text-danger');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                    errorDiv.textContent = '';
                }
            });

            document.querySelectorAll('.product-select').forEach(select => {
                select.addEventListener('change', function() {
                    this.classList.remove('is-invalid');

                    // ✅ Find the error by ID and remove it completely
                    const errorMsg = document.getElementById('product-error-msg');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                });
            });

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
