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
                                    <i
                                        class="mdi mdi-{{ isset($product) ? 'package-variant-closed' : 'package-plus' }} text-primary"></i>
                                    {{ isset($product) ? 'Update Product' : 'Add New Product' }}
                                </h4>
                                <a href="{{ route('products') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="mdi mdi-arrow-left"></i> Back to Products
                                </a>
                            </div>

                            <form
                                action="{{ isset($invoice) ? route('admin.invoices.update', $invoice->id) : route('admin.invoices.store') }}"
                                method="POST" id="invoiceForm" novalidate>
                                @csrf
                                @if (isset($invoice))
                                    @method('PUT')
                                @endif

                                <input type="hidden" name="total_rows" id="total_rows"
                                    value="{{ old('total_rows', isset($invoice) ? count($invoice->products) : 1) }}">

                                <div id="global-alert-container" style="min-height: 10px;"></div>

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
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Select Customer</label>
                                    <select name="customer_id" id="customerSelect" class="form-control"
                                        style="color: #333; background-color: #ffffff !important;">
                                        <option value="">-- Select Customer --</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ old('customer_id', $invoice->customer_id ?? '') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->fullname }} ({{ $customer->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <hr>

                                <h5>Products</h5>
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

                                        <div class="row product-row align-items-end pr-0"
                                            data-row-id="{{ $i + 1 }}">
                                            {{-- Product Dropdown --}}
                                            <div class="col-md-4">
                                                <label>Select Product</label>
                                                <select name="product_id[]" class="form-control product-select"
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
                                                <div class="invalid-feedback"></div>
                                            </div>

                                            {{-- Qty --}}
                                            <div class="col-md-2">
                                                <label>Qty</label>
                                                <input type="number" name="quantity[]"
                                                    class="form-control product-quantity" value="{{ $selectedQty }}"
                                                    min="1" placeholder="Qty"
                                                    oninput="updateProductDetailsFromQuantity(this); clearFieldError(this)">
                                                <div class="invalid-feedback"></div>
                                            </div>

                                            {{-- Price --}}
                                            <div class="col-md-2">
                                                <label>Price (₹)</label>
                                                <input type="number" name="price[]" class="form-control product-price"
                                                    placeholder="0.00" step="0.01" value="{{ $selectedPrice }}"
                                                    style="background-color: #ffffff !important;"
                                                    oninput="updateSubtotalFromPrice(this); clearFieldError(this)">
                                                <div class="invalid-feedback"></div>
                                            </div>

                                            {{-- Subtotal --}}
                                            <div class="col-md-2">
                                                <label>Subtotal (₹)</label>
                                                <input type="text" name="subtotal[]"
                                                    class="form-control product-subtotal" placeholder="0.00"
                                                    value="{{ $selectedSubtotal }}" readonly
                                                    oninput="updateSubtotalFromPrice(this)">
                                                <div class="invalid-feedback"></div>
                                            </div>

                                            {{-- Remove Button --}}
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

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save"></i>
                                        {{ isset($invoice) ? 'Update Invoice' : 'Create Invoice' }}
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
        .form-control.is-invalid {
            border-color: #dc3545 !important;
        }

        .is-invalid~.invalid-feedback {
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
                $(field).removeClass('is-invalid');
                var $col = $(field).closest('[class*="col-md-"]');
                $col.find('.invalid-feedback').remove();
            };

            // 3. CALCULATION FUNCTIONS (Preserved from your original code)
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
                    quantityInput.value = 1;
                    clearFieldError(quantityInput);
                    updateSubtotalFromPrice(priceInput);
                } else {
                    row.classList.add('hide-fields');
                    priceInput.value = '';
                    quantityInput.value = '';
                    subtotalInput.value = '';
                    calculateTotal();
                }
            };

            window.addProductRow = function() {
                const row = document.querySelector('.product-row').cloneNode(true);
                const container = document.getElementById('product-rows');
                row.querySelectorAll('input').forEach(input => {
                    if (input.type !== 'hidden') {
                        input.value = '';
                        input.classList.remove('is-invalid');
                    }
                });
                row.querySelector('.product-subtotal').value = '';
                row.querySelector('.product-quantity').value = '';
                const select = row.querySelector('.product-select');
                if (select) select.selectedIndex = 0;
                row.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                row.querySelectorAll('.invalid-feedback').forEach(errorDiv => errorDiv.remove());
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
                document.getElementById('total_rows').value = parseInt(document.getElementById('total_rows')
                    .value) + 1;
                calculateTotal();
            };

            window.removeProductRow = function(button) {
                const rows = document.querySelectorAll('.product-row');
                if (rows.length === 1) {
                    alert("You cannot remove the last product. You must have at least one product.");
                    return;
                }
                button.closest('.product-row').remove();
                document.getElementById('total_rows').value = parseInt(document.getElementById('total_rows')
                    .value) - 1;
                calculateTotal();
            };

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

            // 4. AJAX SUBMISSION
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
                            window.location.href = "{{ route('invoices.index') }}";
                        }, 1500);
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
                                            col.append(
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
                                        col.append(
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

            // Initial calculation
            calculateTotal();
        });
    </script>
@endsection
