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

                            <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm" novalidate>
                                @csrf
                                <input type="hidden" name="total_rows" id="total_rows" value="{{ old('total_rows', 1) }}">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Invoice Number</label>
                                            <input type="text" name="invoice_number" class="form-control"
                                                value="{{ old('invoice_number', 'INV-' . date('Ymd') . '-' . rand(100, 999)) }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Invoice Date</label>
                                            <input type="date" name="invoice_date" class="form-control"
                                                value="{{ old('invoice_date', date('Y-m-d')) }}">
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
                                        <div class="invalid-feedback" id="customer-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr>

                                <h5>Products</h5>
                                <div id="global-alert-container" style="min-height: 10px;"></div>
                                <div id="product-rows">
                                    @php
                                        $rowCount = old('total_rows', 1);
                                    @endphp

                                    @for ($i = 0; $i < $rowCount; $i++)
                                        <div class="row product-row align-items-end pr-0"
                                            data-row-id="{{ $i + 1 }}">

                                            {{-- Product Dropdown (Made smaller to free up space) --}}
                                            <div class="col-md-4">
                                                <label>Select Product:</label>
                                                <select name="product_id[]"
                                                    class="form-control product-select @error('product_id.' . $i) is-invalid @enderror"
                                                    onchange="updateProductDetails(this)"
                                                    style="color: #333; background-color: #ffffff !important;">
                                                    <option value="">-- Select Product --</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}"
                                                            data-price="{{ $product->price }}"
                                                            data-name="{{ $product->title }}"
                                                            {{ isset(old('product_id')[$i]) && old('product_id')[$i] == $product->id ? 'selected' : '' }}>
                                                            {{ $product->title }} -
                                                            ₹{{ number_format($product->price, 2) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('product_id.' . $i)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-2">
                                                <label>Qty:</label>
                                                <input type="number" name="quantity[]"
                                                    class="form-control product-quantity @error('quantity.' . $i) is-invalid @enderror"
                                                    value="{{ old('quantity.' . $i, '') }}" min="1"
                                                    placeholder="Qty" oninput="updateProductDetailsFromQuantity(this)"
                                                    onfocus="clearFieldError(this)">

                                                @error('quantity.' . $i)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            {{-- Price --}}
                                            <div class="col-md-2">
                                                <label>Price (₹):</label>
                                                <input type="number" name="price[]"
                                                    class="form-control product-price @error('price.' . $i) is-invalid @enderror"
                                                    placeholder="Price" step="0.01" value="{{ old('price.' . $i) }}"
                                                    style="background-color:#ffffff !important;"
                                                    oninput="updateSubtotalFromPrice(this)">
                                                @error('price.' . $i)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Subtotal --}}
                                            <div class="col-md-2">
                                                <label>Subtotal (₹)</label>
                                                <input type="text" name="subtotal[]"
                                                    class="form-control product-subtotal " placeholder="Subtotal"
                                                    value="{{ old('subtotal.' . $i, '') }}" readonly
                                                    style="background-color: #ffffff !important; ">
                                            </div>

                                            <div class="col-md-2 d-flex align-items-end justify-content-end pb-3">
                                                <button type="button" class="btn btn-danger btn-sm remove-row"
                                                    onclick="removeProductRow(this)"
                                                    style="height: 38px; font-size: 12px; padding: 0 12px; white-space: nowrap;">
                                                    <i class="mdi mdi-delete" style="font-size: 14px;"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    @endfor
                                </div>

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

                                            <input type="number" name="tax_rate"
                                                class="form-control @error('tax_rate') is-invalid @enderror"
                                                value="{{ old('tax_rate', 10) }}" step="0.01" placeholder="Tax Rate"
                                                oninput="calculateTotal(); clearFieldError(this)">

                                            @error('tax_rate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Subtotal</label>
                                            <input type="text" name="subtotal_amount" id="subtotal_amount"
                                                class="form-control" value="{{ old('subtotal_amount', '0.00') }}"
                                                readonly style="background: #f8f9fa;">
                                        </div>
                                        <div class="form-group">
                                            <label>Tax Amount</label>
                                            <input type="text" name="tax_amount" id="tax_amount" class="form-control"
                                                value="{{ old('tax_amount', '0.00') }}" readonly
                                                style="background: #f8f9fa;">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Total Amount</strong></label>
                                            <input type="text" name="total_amount" id="total_amount"
                                                class="form-control" value="{{ old('total_amount', '0.00') }}" readonly
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

        /* ✅ CSS TO HANDLE VALIDATION ERRORS */
        .form-select.is-invalid,
        select.is-invalid,
        .form-control.is-invalid {
            border-color: #dc3545 !important;
            border-width: 1px !important;
            border-style: solid !important;
            padding-right: calc(0.75em + 2.375rem) !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.1h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right calc(0.375em + 0.1875rem) center !important;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
        }

        /* 2. Hide error messages by default */
        .invalid-feedback {
            display: none !important;
            color: #dc3545 !important;
            font-size: 80% !important;
            margin-top: 0.25rem !important;
        }

        /* 3. Only show error messages if the field has the is-invalid class */
        .is-invalid~.invalid-feedback {
            display: block !important;
        }

        /* ✅ ALIGNMENT FIX: Prevent layout shift */
        .product-row .col-md-5,
        .product-row .col-md-1,
        .product-row .col-md-2 {
            min-height: 85px !important;
            /* Locks the height even when error appears */
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .product-row {
            display: flex;
            align-items: flex-start !important;
            /* Aligns everything to the top */
            margin-bottom: 20px;
        }

        .product-row .col-md-2.d-flex {
            min-height: 85px !important;
            /* Locks the height for the Remove button column too */
        }
    </style>

    <script>
        // ✅ Fixed: Removes error from input AND the parent col-md wrapper
        function clearFieldError(field) {
            // 1. Remove the red border from the input itself
            field.classList.remove('is-invalid');

            // 2. Reset native browser validation
            field.setCustomValidity('');

            // 3. 🔥 CRITICAL FIX FOR EDIT PAGE: Find the wrapper column
            const column = field.closest('[class*="col-md-"]');
            if (column) {
                column.classList.remove('is-invalid'); // Removes red border from the wrapper

                // 4. Completely destroy the error message HTML
                const errorDiv = column.querySelector('.invalid-feedback');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
        }

        function updateProductDetails(select) {
            clearFieldError(select);

            const row = select.closest('.product-row');
            const priceInput = row.querySelector('.product-price');
            const subtotalInput = row.querySelector('.product-subtotal');
            const quantityInput = row.querySelector('.product-quantity');
            const option = select.options[select.selectedIndex];

            if (option.value) {
                // 🟢 PRODUCT SELECTED
                row.classList.remove('hide-fields');

                const price = parseFloat(option.dataset.price) || 0;
                priceInput.value = price.toFixed(2);

                // ✅ Fills Qty with 1 when a product is selected
                quantityInput.value = 1;

                // ✅ FORCE CLEAR QTY ERROR EXPLICITLY!
                clearFieldError(quantityInput);

                // Calculate subtotal
                updateSubtotalFromPrice(priceInput);
            } else {
                // 🔴 NO PRODUCT
                row.classList.add('hide-fields');

                priceInput.value = '';
                quantityInput.value = '';
                subtotalInput.value = '';
                calculateTotal();
            }
        }
        // ✅ FIXED: Clear error on Qty input
        function updateProductDetailsFromQuantity(input) {
            clearFieldError(input); // <--- THIS WAS MISSING!

            const row = input.closest('.product-row');
            const productSelect = row.querySelector('.product-select');
            const priceInput = row.querySelector('.product-price');
            const subtotalInput = row.querySelector('.product-subtotal');

            if (productSelect.value) {
                const price = parseFloat(priceInput.value) || 0;
                const quantity = parseInt(input.value) || 1;
                subtotalInput.value = (price * quantity).toFixed(2);
                calculateTotal();
            }
        }

        function updateSubtotalFromPrice(input) {
            clearFieldError(input);

            const row = input.closest('.product-row');

            const qty = parseFloat(row.querySelector('.product-quantity').value) || 1;
            const subtotal = row.querySelector('.product-subtotal');

            subtotal.value = ((parseFloat(input.value) || 0) * qty).toFixed(2);

            calculateTotal();
        }

        function addProductRow() {
            const row = document.querySelector('.product-row').cloneNode(true);
            const container = document.getElementById('product-rows');

            row.querySelectorAll('input').forEach(input => {
                if (input.type !== 'hidden') {
                    input.value = '';
                }
            });
            row.querySelector('.product-subtotal').value = '';
            row.querySelector('.product-quantity').value = '';

            const select = row.querySelector('.product-select');
            if (select) select.selectedIndex = 0;

            // 🔥 CLEAN UP CLONED ERRORS
            row.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            row.querySelectorAll('.invalid-feedback').forEach(errorDiv => errorDiv.remove());

            // ✅ Make sure the new row starts HIDDEN
            row.classList.add('hide-fields');

            select.addEventListener('change', function() {
                updateProductDetails(this);
            });

            const qtyInput = row.querySelector('.product-quantity');
            qtyInput.addEventListener('input', function() {
                updateProductDetailsFromQuantity(this);
            });

            const priceInput = row.querySelector('.product-price');
            priceInput.addEventListener('input', function() {
                updateSubtotalFromPrice(this);
            });

            container.appendChild(row);

            document.getElementById('total_rows').value = parseInt(document.getElementById('total_rows').value) + 1;
            calculateTotal();
        }

        function removeProductRow(button) {
            const rows = document.querySelectorAll('.product-row');

            if (rows.length === 1) {
                const alertContainer = document.getElementById('global-alert-container');
                const errorHtml = `
                <div id="last-product-error" class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> You cannot remove the last product. You must have at least one product.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
                alertContainer.insertAdjacentHTML('beforeend', errorHtml);

                setTimeout(function() {
                    const errorAlert = document.getElementById('last-product-error');
                    if (errorAlert) {
                        errorAlert.style.transition = 'opacity 0.5s ease';
                        errorAlert.style.opacity = '0';
                        setTimeout(() => errorAlert.remove(), 100);
                    }
                }, 2000);

                return;
            }

            button.closest('.product-row').remove();
            document.getElementById('total_rows').value = parseInt(document.getElementById('total_rows').value) - 1;
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
                clearFieldError(this);
            });

            document.querySelectorAll('.product-select').forEach(select => {
                select.addEventListener('change', function() {
                    updateProductDetails(this);
                });
            });

            document.querySelectorAll('.product-quantity').forEach(input => {
                input.addEventListener('input', function() {
                    updateProductDetailsFromQuantity(this);
                });
            });

            document.querySelectorAll('.product-price').forEach(input => {
                input.addEventListener('input', function() {
                    updateSubtotalFromPrice(this);
                });
            });

            // Optional: Clear all errors on form submit
            // document.getElementById('invoiceForm').addEventListener('submit', function() {
            //     document.querySelectorAll('.invalid-feedback').forEach(errorDiv => {
            //         errorDiv.style.display = '';
            //         errorDiv.textContent = '';
            //     });
            //     document.querySelectorAll('.is-invalid').forEach(field => {
            //         field.classList.remove('is-invalid');
            //     });
            // });

            calculateTotal();
        });
    </script>
@endsection
