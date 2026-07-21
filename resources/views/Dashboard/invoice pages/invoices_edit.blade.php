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
                                    <i class="mdi mdi-file-document-edit-outline text-primary"></i> Update Invoice
                                    #{{ $invoice->invoice_number }}
                                </h4>
                                <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="mdi mdi-arrow-left"></i> Back to Invoices
                                </a>
                            </div>

                            <form action="{{ route('admin.invoices.update', $invoice->id) }}" method="POST"
                                id="invoiceForm" autocomplete="off" novalidate>
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="total_rows" id="total_rows"
                                    value="{{ old('total_rows', count($invoice->products)) }}">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Invoice Number</label>
                                            <input type="text" name="invoice_number" class="form-control"
                                                value="{{ old('invoice_number', $invoice->invoice_number) }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Invoice Date</label>
                                            <input type="date" name="invoice_date"
                                                class="form-control @error('invoice_date') is-invalid @enderror"
                                                value="{{ old('invoice_date', \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d')) }}">
                                            @error('invoice_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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
                                                {{ old('customer_id', $invoice->customer_id) == $customer->id ? 'selected' : '' }}>
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
                                    @php
                                        $rowCount = old('total_rows', count($invoice->products));
                                    @endphp

                                    @for ($i = 0; $i < $rowCount; $i++)
                                        @php
                                            $oldProductId = old('product_id.' . $i);
                                            $hasOldData = !is_null($oldProductId);
                                            $existingProduct = $invoice->products[$i] ?? null;

                                            $selectedProductId = $hasOldData
                                                ? $oldProductId
                                                : $existingProduct['product_id'] ?? '';

                                            // ✅ FIX: Only set Qty to 1 if a product is actually selected
                                            $selectedQty = '';
                                            if (!empty($selectedProductId)) {
                                                $selectedQty = $hasOldData
                                                    ? old('quantity.' . $i)
                                                    : $existingProduct['quantity'] ?? 1;
                                            }

                                            $selectedPrice = $hasOldData
                                                ? old('price.' . $i)
                                                : $existingProduct['price'] ?? '';
                                            $selectedSubtotal = $hasOldData
                                                ? old('subtotal.' . $i)
                                                : $existingProduct['subtotal'] ?? '';
                                        @endphp

                                        <div class="row product-row align-items-end pr-0"
                                            data-row-id="{{ $i + 1 }}">

                                            {{-- 1. Product Dropdown (Width 4) --}}
                                            <div class="col-md-4">
                                                <label>Select Product</label>
                                                <select name="product_id[]"
                                                    class="form-control product-select @error('product_id.' . $i) is-invalid @enderror"
                                                    onchange="updateProductDetails(this)"
                                                    style="color: #333; background-color: #ffffff !important;">
                                                    <option value="">-- Select Product --</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}"
                                                            data-price="{{ $product->price }}"
                                                            data-name="{{ $product->title }}"
                                                            {{ $selectedProductId == $product->id ? 'selected' : '' }}>
                                                            {{ $product->title }} -
                                                            ₹{{ number_format($product->price, 2) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('product_id.' . $i)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- 2. Qty (Width 2) --}}
                                            <div class="col-md-2">
                                                <label>Qty</label>
                                                <input type="number" name="quantity[]"
                                                    class="form-control product-quantity @error('quantity.' . $i) is-invalid @enderror"
                                                    value="{{ $selectedQty }}" min="1" placeholder="Qty"
                                                    oninput="updateProductDetailsFromQuantity(this); clearFieldError(this)">
                                                @error('quantity.' . $i)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- 3. Price (Width 2) --}}
                                            <div class="col-md-2">
                                                <label>Price (₹)</label>
                                                <input type="number" name="price[]"
                                                    class="form-control product-price @error('price.' . $i) is-invalid @enderror"
                                                    placeholder="0.00" step="0.01" value="{{ $selectedPrice }}"
                                                    style="background-color: #ffffff !important;"
                                                    oninput="updateSubtotalFromPrice(this); clearFieldError(this)">
                                                @error('price.' . $i)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- 4. Subtotal (Width 2) --}}
                                            <div class="col-md-2">
                                                <label>Subtotal (₹)</label>
                                                <input type="text" name="subtotal[]"
                                                    class="form-control product-subtotal" value="{{ $selectedSubtotal }}"
                                                    readonly
                                                    style="background-color: #ffffff !important; text-align: right;">
                                            </div>

                                            {{-- 5. ✅ FIXED Remove Button (Width 2, Pushed to Right & Bottom) --}}
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

                                <button type="button" class="btn btn-success btn-sm mt-2" onclick="addProductRow()">
                                    <i class="mdi mdi-plus"></i> Add More Product
                                </button>

                                <hr>

                                <div class="row justify-content-end">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Tax Rate (%)</label>
                                            <input type="number" name="tax_rate"
                                                class="form-control @error('tax_rate') is-invalid @enderror"
                                                value="{{ old('tax_rate', $invoice->tax_rate) }}" step="0.01"
                                                oninput="calculateTotal(); clearFieldError(this)" placeholder="Tax Rate">
                                            @error('tax_rate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save"></i> Update Invoice
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
        /* 1. Reset Bootstrap 5's native invalid border styling */
        .form-control:invalid,
        .form-select:invalid,
        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #ced4da !important;
            /* Default gray by default */
            background-image: none !important;
        }

        /* 2. ONLY turn red if Laravel specifically adds the 'is-invalid' class */
        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #dc3545 !important;
        }

        /* ✅ FORCE REMOVE RED BORDER when is-invalid is GONE */
        .form-control:not(.is-invalid),
        .form-select:not(.is-invalid) {
            border-color: #ced4da !important;
        }

        /* 3. Hide error messages by default */
        .invalid-feedback {
            display: none !important;
        }

        /* 4. Show error messages ONLY when is-invalid is present */
        .is-invalid~.invalid-feedback {
            display: block !important;
        }

        /* ✅ ALIGNMENT FIX: Prevent layout shift */
        .product-row .col-md-4,
        .product-row .col-md-2,
        .product-row .col-md-3,
        .product-row .col-md-1 {
            min-height: 85px !important;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .product-row {
            display: flex;
            align-items: flex-start !important;
            margin-bottom: 20px;
        }

        .product-row .col-md-1.d-flex {
            min-height: 85px !important;
        }
    </style>

    <script>
        function clearFieldError(field) {
            // 1. Remove the class that makes it red (Bootstrap 5 standard)
            field.classList.remove('is-invalid');

            // 2. Force reset the native HTML5 validity so Bootstrap stops blocking it
            field.setCustomValidity('');

            // 3. Find the parent column and delete the error message from DOM
            const column = field.closest('[class*="col-md-"]');
            if (column) {
                const errorDiv = column.querySelector('.invalid-feedback');
                if (errorDiv) {
                    errorDiv.remove(); // ✅ Permanently delete the error text
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
                row.classList.remove('hide-fields');

                const price = parseFloat(option.dataset.price) || 0;
                priceInput.value = price.toFixed(2);

                // ✅ Set Qty to 1
                quantityInput.value = 1;

                // ✅ EXPLICITLY CLEAR THE QTY ERROR HERE!
                clearFieldError(quantityInput);

                updateSubtotalFromPrice(priceInput);
            } else {
                row.classList.add('hide-fields');

                priceInput.value = '';
                quantityInput.value = '';
                subtotalInput.value = '';
                calculateTotal();
            }
        }

        function updateProductDetailsFromQuantity(input) {
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
            const quantityInput = row.querySelector('.product-quantity');
            const subtotalInput = row.querySelector('.product-subtotal');
            const price = parseFloat(input.value) || 0;
            const quantity = parseInt(quantityInput.value) || 1;
            subtotalInput.value = (price * quantity).toFixed(2);
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

            // Clear deeply on new row
            row.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            row.querySelectorAll('.invalid-feedback').forEach(errorDiv => errorDiv.remove());

            // ✅ NEW: Make sure the new row starts HIDDEN
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
            calculateTotal();

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

            // On submit, clean up
            document.getElementById('invoiceForm').addEventListener('submit', function() {
                document.querySelectorAll('.invalid-feedback').forEach(errorDiv => errorDiv.remove());
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            });

            calculateTotal();
        });
    </script>
@endsection
