@extends('components.adminheader')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
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
                                        class="mdi mdi-{{ isset($invoice) ? 'file-document-edit-outline' : 'file-document-outline' }} text-primary"></i>
                                    {{ isset($invoice) ? 'Update Invoice #' . $invoice->invoice_number : 'Create New Invoice' }}
                                </h4>
                                <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="mdi mdi-arrow-left"></i> Back to Invoices
                                </a>
                            </div>

                            {{-- DYNAMIC FORM ACTION & METHOD --}}
                            <form
                                action="{{ isset($invoice) ? route('admin.invoices.update', $invoice->id) : route('admin.invoices.store') }}"
                                method="POST" id="invoiceForm" novalidate>
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
                                            <input type="date" name="invoice_date"
                                                class="form-control @error('invoice_date') is-invalid @enderror"
                                                value="{{ old('invoice_date', isset($invoice) ? \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') : date('Y-m-d')) }}">
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
                                                {{ old('customer_id', $invoice->customer_id ?? '') == $customer->id ? 'selected' : '' }}>
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

                                            {{-- Product Dropdown (Width 4) --}}
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

                                            {{-- Qty (Width 2) --}}
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

                                            {{-- Price (Width 2) --}}
                                            <div class="col-md-2">
                                                <label>Price (₹)</label>
                                                {{-- ✅ FIX: Added inline PHP to remove d-none if a product is pre-selected --}}
                                                <input type="number" name="price[]"
                                                    class="form-control product-price @error('price.' . $i) is-invalid @enderror"
                                                    placeholder="0.00" step="0.01" value="{{ $selectedPrice }}"
                                                    style="background-color: #ffffff !important;"
                                                    oninput="updateSubtotalFromPrice(this); clearFieldError(this)">
                                                @error('price.' . $i)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Subtotal (Width 2) --}}
                                            <div class="col-md-2">
                                                <label>Subtotal (₹)</label>
                                                {{-- ✅ FIX: Added inline PHP to remove d-none if a product is pre-selected --}}
                                                <input type="text" name="subtotal[]"
                                                    class="form-control product-subtotal @error('subtotal.' . $i) is-invalid @enderror"
                                                    placeholder="0.00" value="{{ $selectedSubtotal }}"
                                                    aria-label="Subtotal (Calculated)"
                                                    oninput="updateSubtotalFromPrice(this)">
                                                @error('subtotal.' . $i)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Remove Button (Width 2) --}}
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
                                            <input type="number" name="tax_rate"
                                                class="form-control @error('tax_rate') is-invalid @enderror"
                                                value="{{ old('tax_rate', $invoice->tax_rate ?? 10) }}" step="0.01"
                                                placeholder="Tax Rate" oninput="calculateTotal(); clearFieldError(this)">
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

        .btn-danger.remove-row:disabled,
        .btn-danger.remove-row.disabled {
            opacity: 1 !important;
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: #fff !important;
        }

        /* Error styles - using Bootstrap classes */
        .is-invalid {
            border-color: #dc3545 !important;
            border-width: 1px !important;
            border-style: solid !important;
            padding-right: calc(1.5em + 0.75rem) !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.1h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right calc(0.375em + 0.1875rem) center !important;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
        }

        /* Show invalid feedback - using Bootstrap classes */
        .invalid-feedback {
            display: block !important;
            color: #dc3545 !important;
            font-size: 80% !important;
            margin-top: 0.25rem !important;
            width: 100% !important;
        }

        /* Product row styling */
        .product-row {
            display: flex;
            align-items: flex-start !important;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .product-row .col-md-4,
        .product-row .col-md-2 {
            min-height: 85px !important;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            position: relative;
            padding-bottom: 5px;
        }

        .product-row .col-md-2.d-flex {
            min-height: 85px !important;
            align-items: flex-end !important;
            justify-content: flex-end !important;
            padding-bottom: 0 !important;
        }

        .product-row .invalid-feedback {
            position: relative;
            bottom: auto;
            left: auto;
            right: auto;
            margin-top: 0.25rem;
            font-size: 75%;
        }

        /* Form group styling */
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-group .invalid-feedback {
            position: relative;
            bottom: auto;
            left: auto;
            right: auto;
            margin-top: 0.25rem;
        }

        /* Alert messages */
        .alert {
            margin-bottom: 20px;
        }

        /* Fix Remove button alignment */
        .product-row .col-md-2.d-flex {
            min-height: 79px !important;
            padding-bottom: 5px !important;
        }

        .product-row .col-md-2.d-flex .btn {
            margin-bottom: 0 !important;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // CSRF token setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // ---------- Clear Field Error Helper ----------
            window.clearFieldError = function(field) {
                $(field).removeClass('is-invalid border-danger');
                $(field).css('border-color', '');
                $(field).css('background-image', '');

                $(field).nextAll('.invalid-feedback').remove();

                if ($(field).is('select')) {
                    $(field).closest('.col-md-4').find('.invalid-feedback').remove();
                    $(field).closest('.col-md-2').find('.invalid-feedback').remove();
                }

                var $col = $(field).closest('[class*="col-md-"]');
                if ($col.length) {
                    $col.find('.invalid-feedback').remove();
                }

                var $formGroup = $(field).closest('.form-group');
                if ($formGroup.length) {
                    $formGroup.find('.invalid-feedback').remove();
                }

                var $row = $(field).closest('.product-row');
                if ($row.length) {
                    $row.find('.invalid-feedback').remove();
                    $row.find('.is-invalid').removeClass('is-invalid');
                    $row.find('.border-danger').removeClass('border-danger');
                }

                if ($(field).attr('id') === 'customerSelect') {
                    $('#customerSelect').removeClass('is-invalid');
                    $('#customerSelect').nextAll('.invalid-feedback').remove();
                    $('#customerSelect').closest('.form-group').find('.invalid-feedback').remove();
                }
            };

            // ---------- Calculation Functions ----------
            window.updateSubtotalFromPrice = function(input) {
                var row = $(input).closest('.product-row')[0];
                if (!row) return;

                var quantityInput = row.querySelector('.product-quantity');
                var subtotalInput = row.querySelector('.product-subtotal');
                var price = parseFloat(input.value) || 0;
                var quantity = parseInt(quantityInput.value) || 1;

                if (subtotalInput) {
                    subtotalInput.value = (price * quantity).toFixed(2);
                }

                clearFieldError(input);
                if (quantityInput) clearFieldError(quantityInput);
                if (subtotalInput) clearFieldError(subtotalInput);

                calculateTotal();
            };

            window.updateProductDetailsFromQuantity = function(input) {
                var row = $(input).closest('.product-row')[0];
                if (!row) return;

                var productSelect = row.querySelector('.product-select');
                var priceInput = row.querySelector('.product-price');
                var subtotalInput = row.querySelector('.product-subtotal');

                if (productSelect && productSelect.value) {
                    var price = parseFloat(priceInput.value) || 0;
                    var quantity = parseInt(input.value) || 1;
                    if (subtotalInput) {
                        subtotalInput.value = (price * quantity).toFixed(2);
                    }

                    clearFieldError(input);
                    if (subtotalInput) clearFieldError(subtotalInput);
                    calculateTotal();
                }
            };

            window.updateProductDetails = function(select) {
                var row = $(select).closest('.product-row')[0];
                if (!row) return;

                var priceInput = row.querySelector('.product-price');
                var subtotalInput = row.querySelector('.product-subtotal');
                var quantityInput = row.querySelector('.product-quantity');
                var option = select.options[select.selectedIndex];

                clearFieldError(select);

                if (option && option.value) {
                    // ✅ Show the fields using Bootstrap d-none class
                    $(priceInput).removeClass('d-none');
                    $(quantityInput).removeClass('d-none');
                    $(subtotalInput).removeClass('d-none');
                    $(priceInput).closest('.col-md-2').find('label').css('opacity', '1');
                    $(quantityInput).closest('.col-md-2').find('label').css('opacity', '1');
                    $(subtotalInput).closest('.col-md-2').find('label').css('opacity', '1');

                    var price = parseFloat(option.dataset.price) || 0;
                    if (priceInput) priceInput.value = price.toFixed(2);
                    if (quantityInput) quantityInput.value = 1;

                    if (quantityInput) clearFieldError(quantityInput);
                    if (priceInput) clearFieldError(priceInput);

                    if (priceInput) updateSubtotalFromPrice(priceInput);
                } else {
                    // Hide the fields if they deselect
                    $(priceInput).addClass('d-none');
                    $(quantityInput).addClass('d-none');
                    $(subtotalInput).addClass('d-none');
                    $(priceInput).closest('.col-md-2').find('label').css('opacity', '0.5');
                    $(quantityInput).closest('.col-md-2').find('label').css('opacity', '0.5');
                    $(subtotalInput).closest('.col-md-2').find('label').css('opacity', '0.5');

                    if (priceInput) priceInput.value = '';
                    if (quantityInput) quantityInput.value = '';
                    if (subtotalInput) subtotalInput.value = '';
                    calculateTotal();
                }
            };

            // ---------- Add / Remove Rows ----------
            window.addProductRow = function() {
                var firstRow = $('.product-row').first();
                if (!firstRow.length) return;

                var row = firstRow.clone();
                var container = $('#product-rows');
                var rowCount = $('.product-row').length;
                row.attr('data-row-id', rowCount + 1);

                row.find('input').each(function() {
                    if ($(this).attr('type') !== 'hidden') {
                        $(this).val('');
                        $(this).removeClass('is-invalid border-danger');
                        $(this).css('border-color', '');
                        $(this).css('background-image', '');
                    }
                });

                var select = row.find('.product-select');
                if (select.length) {
                    select.val('');
                    select.removeClass('is-invalid border-danger');
                    select.css('border-color', '');
                    select.css('background-image', '');
                }

                row.find('.invalid-feedback').remove();

                var subtotalInput = row.find('.product-subtotal');
                if (subtotalInput.length) {
                    subtotalInput.val('');
                }


                select.off('change').on('change', function() {
                    updateProductDetails(this);
                });

                var qtyInput = row.find('.product-quantity');
                qtyInput.off('input').on('input', function() {
                    updateProductDetailsFromQuantity(this);
                });

                var priceInput = row.find('.product-price');
                priceInput.off('input').on('input', function() {
                    updateSubtotalFromPrice(this);
                });

                row.find('input, select').off('blur').on('blur', function() {
                    if ($(this).val() !== '') {
                        clearFieldError(this);
                    }
                });

                container.append(row);
                $('#total_rows').val(parseInt($('#total_rows').val()) + 1);
                calculateTotal();
            };

            window.removeProductRow = function(button) {
                var rows = $('.product-row');
                var alertContainer = $('#global-alert-container');
                alertContainer.empty();

                if (rows.length === 1) {
                    alertContainer.html(`
                        <div id="last-product-error" class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> You cannot remove the last product. You must have at least one product.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `);
                    setTimeout(function() {
                        var errorAlert = $('#last-product-error');
                        if (errorAlert.length) {
                            errorAlert.removeClass('show');
                            setTimeout(function() {
                                errorAlert.remove();
                            }, 500);
                        }
                    }, 300);
                    return;
                }

                $(button).closest('.product-row').remove();
                $('#total_rows').val(parseInt($('#total_rows').val()) - 1);
                calculateTotal();
            };

            // ---------- Calculate Totals ----------
            window.calculateTotal = function() {
                var rows = document.querySelectorAll('.product-row');
                var subtotal = 0;
                rows.forEach(function(row) {
                    var subtotalInput = row.querySelector('.product-subtotal');
                    if (subtotalInput && subtotalInput.value !== '') {
                        subtotal += parseFloat(subtotalInput.value) || 0;
                    }
                });
                var taxRate = parseFloat(document.querySelector('input[name="tax_rate"]').value) || 0;
                var taxAmount = subtotal * (taxRate / 100);
                var total = subtotal + taxAmount;
                document.getElementById('subtotal_amount').value = subtotal.toFixed(2);
                document.getElementById('tax_amount').value = taxAmount.toFixed(2);
                document.getElementById('total_amount').value = total.toFixed(2);
            };

            // ---------- Form Submission ----------
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
                        setTimeout(function() {
                            window.location.href = "{{ route('invoices.index') }}";
                        }, 1500);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $('.is-invalid').removeClass('is-invalid');
                            $('.invalid-feedback').remove();

                            $.each(errors, function(key, messages) {
                                if (key.includes('.')) {
                                    var parts = key.split('.');
                                    var baseName = parts[0];
                                    var index = parseInt(parts[1]);

                                    var inputs = $('[name="' + baseName + '[]"]');
                                    if (inputs.length > index) {
                                        var input = inputs.eq(index);
                                        if (input.length) {

                                            // ✅ CRITICAL FIX: ONLY ADD ERROR BORDER IF THE INPUT IS VISIBLE
                                            var isHidden = input.hasClass('d-none');
                                            if (!isHidden) {
                                                input.addClass('is-invalid');
                                            }

                                            input.nextAll('.invalid-feedback').remove();
                                            input.closest('[class*="col-md-"]').append(
                                                '<div class="invalid-feedback" style="display:block;">' +
                                                messages[0] + '</div>'
                                            );
                                        }
                                    }
                                } else {
                                    var input = $('[name="' + key + '"]');
                                    if (input.length) {
                                        input.addClass('is-invalid');
                                        input.parent().find('.invalid-feedback')
                                            .remove();
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

            // ---------- Initialization ----------
            calculateTotal();

            $('#customerSelect').on('change', function() {
                clearFieldError(this);
            });

            $(document).on('input', 'input, select', function() {
                if ($(this).val() !== '' && $(this).hasClass('is-invalid')) {
                    clearFieldError(this);
                }
            });

            $(document).on('blur', 'input, select', function() {
                if ($(this).val() !== '' && $(this).hasClass('is-invalid')) {
                    clearFieldError(this);
                }
            });

            $(document).on('change', 'select', function() {
                if ($(this).val() !== '' && $(this).hasClass('is-invalid')) {
                    clearFieldError(this);
                }
            });
        });
    </script>
@endsection
