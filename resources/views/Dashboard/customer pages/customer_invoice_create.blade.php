@extends('components.adminheader')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title">
                                <i class="mdi mdi-file-document-outline text-primary"></i> Create New Invoice
                            </h4>
                            <a href="{{ route('customer.invoices') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="mdi mdi-arrow-left"></i> Back to My Invoices
                            </a>
                        </div>

                        <div id="global-alert-container" style="min-height: 10px; margin-bottom: 15px;"></div>

                        <form action="{{ route('customer.invoices.store') }}" method="POST" id="invoiceForm" novalidate>
                            @csrf
                           
                            @if (isset($invoice))
                                @method('PUT')
                            @endif

                            <input type="hidden" name="total_rows" id="total_rows"
                                value="{{ old('total_rows', isset($invoice) ? count($invoice->products) : 1) }}">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Invoice Number</label>
                                        <input type="text" name="invoice_number" class="form-control"
                                            value="{{ old('invoice_number', $invoice->invoice_number ?? 'INV-' . date('Ymd') . '-' . rand(100, 999)) }}"
                                            readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Invoice Date</label>
                                        <input type="date" name="invoice_date" class="form-control"
                                            value="{{ old('invoice_date', isset($invoice) ? \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') : date('Y-m-d')) }}">
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <h5>Products</h5>
                            <div id="global-alert-container" style="min-height: 10px;"></div>
                            <div id="product-rows">
                                @php
                                    $rowCount = old('total_rows', isset($invoice) ? count($invoice->products) : 1);
                                @endphp

                                @for ($i = 0; $i < $rowCount; $i++)
                                    @php
                                        $oldProductId = old('product_id.' . $i);
                                        $hasOldData = !is_null($oldProductId);
                                        $existingProduct = $invoice->products[$i] ?? null;

                                        $selectedProductId = $hasOldData
                                            ? $oldProductId
                                            : $existingProduct['product_id'] ?? '';
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

                                    <div class="row product-row mb-4 align-items-start" data-row-id="{{ $i + 1 }}">
                                        <div class="col-md-4 d-flex flex-column">
                                            <label class="mb-2">Select Product</label>
                                            <div class="input-group" id="product-group-{{ $i }}">
                                                <select name="product_id[]" class="form-control product-select"
                                                    onchange="updateProductDetails(this)" style="color: #333;">
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

                                                <!-- This span keeps the border structure intact -->
                                                <span class="input-group-text bg-white border-start-0"
                                                    style="padding: 0; width: 0; min-width: 0;"></span>

                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>

                                        <!-- Qty Column -->
                                        <div class="col-md-2 d-flex flex-column">
                                            <label class="mb-2">Qty</label>
                                            <input type="number" name="quantity[]" class="form-control product-quantity"
                                                value="{{ $selectedQty }}" min="1" placeholder="Qty"
                                                oninput="updateProductDetailsFromQuantity(this); clearFieldError(this)">
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <!-- Price Column -->
                                        <div class="col-md-2 d-flex flex-column">
                                            <label class="mb-2">Price (₹)</label>
                                            <input type="number" name="price[]" class="form-control product-price"
                                                placeholder="0.00" step="0.01" value="{{ $selectedPrice }}"
                                                oninput="updateSubtotalFromPrice(this); clearFieldError(this)">
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <!-- Subtotal Column -->
                                        <div class="col-md-2 d-flex flex-column">
                                            <label class="mb-2">Subtotal (₹)</label>
                                            <input type="text" name="subtotal[]"
                                                class="form-control product-subtotal bg-white" placeholder="0.00"
                                                value="{{ $selectedSubtotal }}" readonly
                                                oninput="updateSubtotalFromPrice(this); clearFieldError(this)">
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <!-- ✅ PERFECTLY ALIGNED Remove Button Column -->
                                        <!-- pt-2 exactly matches the mb-2 label, pb-1 matches the input margin -->
                                        <div class="col-md-2 d-flex flex-column pt-2 pb-1">
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

                            <div class="row {{ isset($invoice) ? 'justify-content-end' : '' }}">
                                <div
                                    class="col-md-{{ isset($invoice) ? 4 : 6 }} offset-md-{{ isset($invoice) ? 0 : 6 }}">
                                    <div class="form-group">
                                        <label>Tax Rate (%)</label>
                                        <input type="number" name="tax_rate" class="form-control"
                                            value="{{ old('tax_rate', $invoice->tax_rate ?? 10) }}" step="0.01"
                                            placeholder="Tax Rate" oninput="calculateTotal(); clearFieldError(this)">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="form-group">
                                        <label>Subtotal</label>
                                        <input type="text" name="subtotal_amount" id="subtotal_amount"
                                            class="form-control bg-white" value="{{ old('subtotal_amount', '0.00') }}"
                                            readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Tax Amount</label>
                                        <input type="text" name="tax_amount" id="tax_amount" class="form-control"
                                            value="{{ old('tax_amount', '0.00') }}" style="background: #f8f9fa;">
                                    </div>
                                    <div class="form-group">
                                        <label><strong>Total Amount</strong></label>
                                        <input type="text" name="total_amount" id="total_amount" class="form-control"
                                            value="{{ old('total_amount', '0.00') }}" readonly
                                            style="background: #f8f9fa; font-weight: bold; font-size: 1.2em;">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-content-save"></i>
                                    {{ isset($invoice) ? 'Update Invoice' : 'Create Invoice' }}
                                </button>
                                <a href="{{ route('customer.invoices') }}" class="btn btn-outline-secondary">
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // 1. SETUP CSRF TOKEN
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // 2. FIXED CLEAR FIELD ERROR HELPER
            window.clearFieldError = function(field) {
                // Find the column container
                var $col = $(field).closest('[class*="col-md-"]');

                // Remove red border from the field itself
                $(field).removeClass('is-invalid');
                $(field).removeClass('border-danger');

                // If this is a select box inside an input-group, remove is-invalid AND border-danger from the wrapper too
                if ($(field).is('select')) {
                    var group = $(field).closest('.input-group');
                    group.removeClass('is-invalid');
                    group.removeClass('border-danger');
                }

                // Remove any error message divs inside this column
                $col.find('.invalid-feedback').remove();

                // Additionally, if it's a product row, ensure all errors in that row are cleared
                $(field).closest('.product-row').find('.invalid-feedback').remove();
                $(field).closest('.product-row').find('.is-invalid').removeClass('is-invalid');
                $(field).closest('.product-row').find('.border-danger').removeClass('border-danger');
            };
            // 3. CALCULATE SUBTOTAL FUNCTIONS
            window.updateSubtotalFromPrice = function(input) {
                const row = input.closest('.product-row');
                const quantityInput = row.querySelector('.product-quantity');
                const subtotalInput = row.querySelector('.product-subtotal');
                const price = parseFloat(input.value) || 0;
                const quantity = parseInt(quantityInput.value) || 1;

                // Update subtotal value
                subtotalInput.value = (price * quantity).toFixed(2);

                // ✅ FORCE CLEAR ERRORS ON SUBTOTAL WHEN PRICE CHANGES
                clearFieldError(subtotalInput);
                clearFieldError(input);

                calculateTotal();
            };

            window.updateProductDetailsFromQuantity = function(input) {
                const row = input.closest('.product-row');
                const productSelect = row.querySelector('.product-select');
                const priceInput = row.querySelector('.product-price');
                const subtotalInput = row.querySelector('.product-subtotal');

                if (productSelect.value) {
                    const price = parseFloat(priceInput.value) || 0;
                    const quantity = parseInt(input.value) || 1;
                    subtotalInput.value = (price * quantity).toFixed(2);

                    // ✅ FORCE CLEAR ERRORS ON SUBTOTAL & QTY WHEN QTY CHANGES
                    clearFieldError(subtotalInput);
                    clearFieldError(input);

                    calculateTotal();
                }
            };

            window.updateProductDetails = function(select) {
                const row = select.closest('.product-row');
                const priceInput = row.querySelector('.product-price');
                const subtotalInput = row.querySelector('.product-subtotal');
                const quantityInput = row.querySelector('.product-quantity');
                const option = select.options[select.selectedIndex];

                // Clear the Select dropdown error immediately when changed
                clearFieldError(select);

                if (option.value) {
                    row.classList.remove('hide-fields');
                    const price = parseFloat(option.dataset.price) || 0;
                    priceInput.value = price.toFixed(2);
                    quantityInput.value = 1;

                    // Clear Qty and Price errors
                    clearFieldError(quantityInput);
                    clearFieldError(priceInput);

                    // Update the subtotal
                    updateSubtotalFromPrice(priceInput);
                } else {
                    row.classList.add('hide-fields');
                    priceInput.value = '';
                    quantityInput.value = '';
                    subtotalInput.value = '';
                    calculateTotal();
                }
            };

            // 4. ADD / REMOVE ROWS
            window.addProductRow = function() {
                const row = document.querySelector('.product-row').cloneNode(true);
                const container = document.getElementById('product-rows');

                // Ensure the new row has margin-bottom
                row.classList.add('mb-4');

                // Reset inputs and clear errors in the clone
                row.querySelectorAll('input').forEach(input => {
                    if (input.type !== 'hidden') {
                        input.value = '';
                        input.classList.remove('is-invalid');
                        input.classList.remove('border-danger');
                    }
                });

                // Reset the select
                const select = row.querySelector('.product-select');
                if (select) {
                    select.selectedIndex = 0;
                    select.classList.remove('is-invalid');
                    select.classList.remove('border-danger');
                    select.closest('.input-group').classList.remove('is-invalid');
                    select.closest('.input-group').classList.remove('border-danger');
                }

                // Reset ALL invalid-feedback divs (Clear text, keep the div)
                row.querySelectorAll('.invalid-feedback').forEach(errorDiv => {
                    errorDiv.innerHTML = '';
                });
                row.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                row.querySelectorAll('.border-danger').forEach(el => el.classList.remove('border-danger'));

                // Reset subtotal and hide fields until product is selected
                row.querySelector('.product-subtotal').value = '';
                row.classList.add('hide-fields');

                // Re-attach event listeners
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

                // Append and update total row count
                container.appendChild(row);
                document.getElementById('total_rows').value = parseInt(document.getElementById('total_rows')
                    .value) + 1;
                calculateTotal();
            };
            window.removeProductRow = function(button) {
                const rows = document.querySelectorAll('.product-row');
                const alertContainer = document.getElementById('global-alert-container');

                // Clear any previous errors in the alert container first
                alertContainer.innerHTML = '';

                if (rows.length === 1) {
                    // ✅ CHANGE: Use 'alert-danger' for Red color
                    alertContainer.innerHTML = `
            <div id="last-product-error" class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> You cannot remove the last product. You must have at least one product.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;

                    setTimeout(function() {
                        const errorAlert = document.getElementById('last-product-error');
                        if (errorAlert) {
                            // Use Bootstrap's fade out transition
                            errorAlert.classList.remove('show');
                            // Remove it from DOM completely after the fade finishes (500ms)
                            setTimeout(() => errorAlert.remove(), 500);
                        }
                    }, 1000);

                    return;
                }

                button.closest('.product-row').remove();
                document.getElementById('total_rows').value = parseInt(document.getElementById('total_rows')
                    .value) - 1;
                calculateTotal();
            };

            // 5. CALCULATE TOTALS
            window.calculateTotal = function() {
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
            };

            // 6. AJAX SUBMISSION - NO GLOBAL ALERT, ONLY FIELD ERRORS
            $('#invoiceForm').on('submit', function(e) {
                e.preventDefault();

                // Clear ALL existing errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                $('#global-alert-container').empty();

                var formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#global-alert-container').html(
                            '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            response.message +
                            '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' +
                            '</div>'
                        );
                        setTimeout(function() {
                            window.location.href = "{{ route('customer.invoices') }}";
                        }, 100);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;

                            // ✅ Loop through the exact errors returned by your Controller
                            $.each(errors, function(key, messages) {
                                // Handle array fields (product_id.0, quantity.1, etc.)
                                if (key.includes('.')) {
                                    var parts = key.split('.');
                                    var baseName = parts[0];
                                    var index = parseInt(parts[1]);

                                    var inputs = $('[name="' + baseName + '[]"]');
                                    if (inputs.length > index) {
                                        var input = inputs.eq(index);
                                        if (input.length) {
                                            var col = input.closest(
                                                '[class*="col-md-"]');

                                            // Remove old errors
                                            col.find('.invalid-feedback').remove();

                                            // Apply red border
                                            input.addClass('is-invalid');

                                            // ✅ Display your CUSTOM validation message
                                            input.after(
                                                '<div class="invalid-feedback" style="display:block;">' +
                                                messages[0] + '</div>'
                                            );
                                        }
                                    }
                                } else {
                                    // Handle standard fields (tax_rate, invoice_date, customer_id)
                                    var input = $('[name="' + key + '"]');
                                    if (input.length) {
                                        var col = input.closest('[class*="col-md-"]');

                                        // Remove old errors
                                        col.find('.invalid-feedback').remove();

                                        // Apply red border
                                        input.addClass('is-invalid');

                                        // ✅ Display your CUSTOM validation message
                                        input.after(
                                            '<div class="invalid-feedback" style="display:block;">' +
                                            messages[0] + '</div>'
                                        );
                                    }
                                }
                            });
                        } else {
                            // Fallback for 500 errors
                            var errorMsg = xhr.responseJSON ? xhr.responseJSON.message :
                                'Unknown Server Error';
                            $('#global-alert-container').html(
                                '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                'Error ' + xhr.status + ': ' + errorMsg +
                                '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' +
                                '</div>'
                            );
                        }
                    }
                });
            });

            // Initialize total calculation
            calculateTotal();
        });
    </script>
@endsection
