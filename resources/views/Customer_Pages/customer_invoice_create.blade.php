@extends('Components.customerheader')

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
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom-0 pt-3 px-4 pb-0">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="card-title fw-bold mb-0">
                                    <i class="mdi mdi-file-document-outline text-primary me-2"></i> Create New Invoice
                                </h4>
                                <a href="{{ route('customer.invoices') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="mdi mdi-arrow-left"></i> Back to My Invoices
                                </a>
                            </div>
                        </div>

                        <div class="card-body px-4 pt-3">
                            {{-- Global Alert Container --}}
                            <div id="global-alert-container" style="min-height: 15px; margin-bottom: 15px;"></div>

                            <form action="{{ route('customer.invoices.store') }}" method="POST" id="invoiceForm"
                                novalidate>
                                @csrf

                                @if (isset($invoice))
                                    @method('PUT')
                                @endif

                                <input type="hidden" name="total_rows" id="total_rows"
                                    value="{{ isset($cartItems) && $cartItems->count() > 0 ? $cartItems->count() : 1 }}">

                                {{-- Invoice Header Info --}}
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="fw-medium">Invoice Number</label>
                                            <input type="text" class="form-control bg-light"
                                                value="INV-{{ date('Ymd') }}-{{ rand(100, 999) }}" readonly>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="fw-medium">Invoice Date <span class="text-danger">*</span></label>
                                            <input type="date" name="invoice_date" class="form-control"
                                                value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <h5 class="mb-3">Products</h5>
                                <div id="product-rows">
                                    @php
                                        $rows =
                                            isset($cartItems) && $cartItems->count() > 0 ? $cartItems : collect([null]); // one empty row when cart is empty
                                    @endphp

                                    @foreach ($rows as $index => $cartItem)
                                        @php
                                            $selectedProductId =
                                                optional($cartItem)->product_id ?? old('product_id.' . $index);
                                            $selectedQty =
                                                optional($cartItem)->quantity ?? old('quantity.' . $index, '');
                                            $selectedPrice =
                                                optional(optional($cartItem)->product)->price ??
                                                old('price.' . $index, '');
                                            $selectedSubtotal =
                                                $selectedPrice && $selectedQty
                                                    ? number_format($selectedPrice * $selectedQty, 2, '.', '')
                                                    : old('subtotal.' . $index, '');
                                        @endphp

                                        <div class="row product-row mb-4 align-items-start"
                                            data-row-id="{{ $index + 1 }}">
                                            {{-- Product --}}
                                            <div class="col-md-4 d-flex flex-column">
                                                <label class="mb-2">Select Product</label>
                                                <div class="input-group">
                                                    <select name="product_id[]" class="form-select product-select"
                                                        onchange="updateProductDetails(this)">
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
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>

                                            {{-- Qty --}}
                                            <div class="col-md-2 d-flex flex-column">
                                                <label class="mb-2">Qty</label>
                                                <input type="number" name="quantity[]"
                                                    class="form-control product-quantity" value="{{ $selectedQty }}"
                                                    min="1" placeholder="Enter quantity"
                                                    oninput="updateProductDetailsFromQuantity(this); clearFieldError(this)">
                                                <div class="invalid-feedback"></div>
                                            </div>

                                            {{-- Price --}}
                                            <div class="col-md-2 d-flex flex-column">
                                                <label class="mb-2">Price (₹)</label>
                                                <input type="number" name="price[]" class="form-control product-price"
                                                    step="0.01" value="{{ $selectedPrice }}" placeholder="Enter price"
                                                    oninput="updateSubtotalFromPrice(this); clearFieldError(this)">
                                                <div class="invalid-feedback"></div>
                                            </div>

                                            {{-- Subtotal --}}
                                            <div class="col-md-2 d-flex flex-column">
                                                <label class="mb-2">Subtotal (₹)</label>
                                                <input type="text" name="subtotal[]"
                                                    class="form-control product-subtotal bg-white" placeholder="Subtotal"
                                                    value="{{ $selectedSubtotal }}" readonly>
                                                <div class="invalid-feedback"></div>
                                            </div>

                                            {{-- Remove --}}
                                            <div class="col-md-2 d-flex align-items-end justify-content-end pb-3">
                                                <button type="button" class="btn btn-danger btn-sm remove-row"
                                                    onclick="removeProductRow(this)">
                                                    <i class="mdi mdi-delete"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
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
                                                class="form-control bg-white"
                                                value="{{ old('subtotal_amount', '0.00') }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Tax Amount</label>
                                            <input type="text" name="tax_amount" id="tax_amount" class="form-control"
                                                value="{{ old('tax_amount', '0.00') }}" style="background: #f8f9fa;">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Total Amount</strong></label>
                                            <input type="text" name="total_amount" id="total_amount"
                                                class="form-control" value="{{ old('total_amount', '0.00') }}" readonly
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

    <style>
        /* Fix Remove button alignment */
        .product-row .col-md-2.d-flex {
            min-height: 79px !important;
            padding-bottom: 5px !important;
        }

        .product-row .col-md-2.d-flex .btn {
            margin-bottom: 0 !important;
        }

        /* ✅ FIX RED BORDER FOR BOOTSTRAP 5 SELECTS */
        .form-select.is-invalid {
            border-color: #dc3545 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.1h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e') !important;
     background-repeat: no-repeat !important;
                    background-position: right calc(0.75em + 0.1875rem) center !important;
                    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
                    padding-right: calc(1.5em + 0.75rem) !important;
            }

            .form-control.is-invalid {
                border-color: #dc3545 !important;
            }

            .is-invalid ~ .invalid-feedback {
                display: block !important;
            }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // 1. SETUP CSRF TOKEN
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // 2. CLEAR FIELD ERROR HELPER
            window.clearFieldError = function(field) {
                var $col = $(field).closest('[class*="col-md-"]');
                $(field).removeClass('is-invalid border-danger');
                if ($(field).is('select')) {
                    $(field).closest('.input-group').removeClass('is-invalid border-danger');
                }
                $col.find('.invalid-feedback').remove();
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
                subtotalInput.value = (price * quantity).toFixed(2);
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

                clearFieldError(select);

                if (option.value) {
                    row.classList.remove('hide-fields');
                    const price = parseFloat(option.dataset.price) || 0;
                    priceInput.value = price.toFixed(2);

                    // ✅ Fixed part
                    if (parseInt(quantityInput.value) === 0 || quantityInput.value === '') {
                        quantityInput.value = 1;
                    }

                    clearFieldError(quantityInput);
                    clearFieldError(priceInput);
                    updateSubtotalFromPrice(priceInput);
                } else {
                    row.classList.add('hide-fields');
                    priceInput.value = '';
                    // ✅ NEW: Reset quantity to 0 when deselected
                    quantityInput.value = '0';
                    subtotalInput.value = '';
                    calculateTotal();
                }
            };

            // 4. ADD / REMOVE ROWS - RE-WRITTEN TO BE BULLETPROOF
            window.addProductRow = function() {
                // ✅ CRITICAL FIX: Get ALL rows and clone the LAST one to keep the correct structure
                const rows = document.querySelectorAll('.product-row');
                if (rows.length === 0) return;

                // Pick the last row (most recent structure) to clone
                const lastRow = rows[rows.length - 1];
                const row = lastRow.cloneNode(true);
                const container = document.getElementById('product-rows');

                // Ensure the new row has margin-bottom
                row.classList.add('mb-4');

                // Reset ALL inputs in the clone
                row.querySelectorAll('input').forEach(input => {
                    if (input.type !== 'hidden') {
                        input.value = '';
                        input.classList.remove('is-invalid', 'border-danger');
                    }
                });

                // Reset the select dropdown
                const select = row.querySelector('.product-select');
                if (select) {
                    select.selectedIndex = 0;
                    select.classList.remove('is-invalid', 'border-danger');
                    select.closest('.input-group').classList.remove('is-invalid', 'border-danger');
                }

                // Reset ALL invalid-feedback divs
                row.querySelectorAll('.invalid-feedback').forEach(errorDiv => errorDiv.innerHTML = '');
                row.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                row.querySelectorAll('.border-danger').forEach(el => el.classList.remove('border-danger'));

                // Reset subtotal and hide fields until product is selected
                const subtotalInput = row.querySelector('.product-subtotal');
                if (subtotalInput) subtotalInput.value = '';
                row.classList.add('hide-fields');

                // ✅ RE-ATTACH EVENT LISTENERS (Remove old ones first to prevent double-firing)
                const newSelect = row.querySelector('.product-select');
                newSelect.removeEventListener('change', updateProductDetails);
                newSelect.addEventListener('change', function() {
                    updateProductDetails(this);
                });

                const newQtyInput = row.querySelector('.product-quantity');
                newQtyInput.removeEventListener('input', updateProductDetailsFromQuantity);
                newQtyInput.addEventListener('input', function() {
                    updateProductDetailsFromQuantity(this);
                });

                const newPriceInput = row.querySelector('.product-price');
                newPriceInput.removeEventListener('input', updateSubtotalFromPrice);
                newPriceInput.addEventListener('input', function() {
                    updateSubtotalFromPrice(this);
                });

                // ✅ Append the row and ensure total_rows updates correctly
                container.appendChild(row);
                const totalRowsInput = document.getElementById('total_rows');
                totalRowsInput.value = parseInt(totalRowsInput.value) + 1;

                calculateTotal();
            };

            window.removeProductRow = function(button) {
                const rows = document.querySelectorAll('.product-row');
                const alertContainer = document.getElementById('global-alert-container');
                alertContainer.innerHTML = '';

                if (rows.length === 1) {
                    alertContainer.innerHTML = `
                    <div id="last-product-error" class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> You cannot remove the last product. You must have at least one product.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `;
                    setTimeout(() => {
                        const errorAlert = document.getElementById('last-product-error');
                        if (errorAlert) {
                            errorAlert.classList.remove('show');
                            setTimeout(() => errorAlert.remove(), 5000);
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

            // 6. AJAX SUBMISSION
            $('#invoiceForm').on('submit', function(e) {
                e.preventDefault();
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
                        setTimeout(() => {
                            window.location.href = "{{ route('customer.invoices') }}";
                        }, 100);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, messages) {
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
                                            col.find('.invalid-feedback').remove();
                                            input.addClass('is-invalid');
                                            input.after(
                                                '<div class="invalid-feedback" style="display:block;">' +
                                                messages[0] + '</div>'
                                            );
                                        }
                                    }
                                } else {
                                    var input = $('[name="' + key + '"]');
                                    if (input.length) {
                                        var col = input.closest('[class*="col-md-"]');
                                        col.find('.invalid-feedback').remove();
                                        input.addClass('is-invalid');
                                        input.after(
                                            '<div class="invalid-feedback" style="display:block;">' +
                                            messages[0] + '</div>'
                                        );
                                    }
                                }
                            });
                        } else {
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

            calculateTotal();
        });
    </script>
@endsection
