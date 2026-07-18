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
                            <form action="{{ route('admin.invoices.update', $invoice->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                {{-- Invoice Details --}}
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
                                            <input type="date" name="invoice_date" class="form-control"
                                                value="{{ old('invoice_date', $invoice->invoice_date) }}">
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
                                    @forelse ($invoice->products as $index => $productData)
                                        <div class="row product-row align-items-end pr-0"
                                            data-row-id="{{ $loop->iteration }}">
                                            {{-- Product Dropdown --}}
                                            <div class="col-md-5">
                                                <label>Select Product</label>
                                                <select name="product_id[]" class="form-control product-select"
                                                    onchange="updateProductDetails(this)"
                                                    style="color: #333; background-color: #ffffff !important;">
                                                    <option value="">-- Select Product --</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}"
                                                            data-price="{{ $product->price }}"
                                                            data-name="{{ $product->title }}"
                                                            {{ $productData['product_id'] == $product->id ? 'selected' : '' }}>
                                                            {{ $product->title }} -
                                                            ₹{{ number_format($product->price, 2) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-1">
                                                <label>Qty</label>
                                                <input type="number" name="quantity[]"
                                                    class="form-control product-quantity"
                                                    value="{{ old('quantity.' . $index, $productData['quantity']) }}"
                                                    min="1">
                                            </div>

                                            <div class="col-md-2">
                                                <label>Price (₹)</label>
                                                <input type="number" class="form-control product-price" placeholder="Price"
                                                    step="0.01" readonly
                                                    value="{{ old('price.' . $index, $productData['price']) }}"
                                                    style="background-color: #ffffff !important;">
                                            </div>


                                            <div class="col-md-2">
                                                <label>Subtotal (₹)</label>
                                                {{-- ✅ FIXED: Removed 'name="subtotal[]"' --}}
                                                <input type="text" class="form-control product-subtotal" readonly
                                                    value="{{ old('subtotal.' . $index, $productData['subtotal']) }}"
                                                    style="background-color: #ffffff !important;">
                                            </div>

                                            <div class="col-md-2 d-flex flex-row align-items-end justify-content-end"
                                                style="padding-bottom: 5px; gap: 5px;">
                                                <button type="button" class="btn btn-danger btn-sm remove-row"
                                                    onclick="removeProductRow(this)"
                                                    style="height: 38px; font-size: 12px; padding: 0 8px; white-space: nowrap;">
                                                    <i class="mdi mdi-delete" style="font-size: 14px;"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    @empty

                                        <div class="row product-row align-items-end pr-0" data-row-id="1">
                                            <div class="col-md-5">
                                                <label>Select Product</label>
                                                <select name="product_id[]" class="form-control product-select"
                                                    onchange="updateProductDetails(this)"
                                                    style="color: #333; background-color: #ffffff !important;">
                                                    <option value="">-- Select Product --</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}"
                                                            data-price="{{ $product->price }}"
                                                            data-name="{{ $product->title }}">
                                                            {{ $product->title }} -
                                                            ₹{{ number_format($product->price, 2) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-1">
                                                <label>Qty</label>
                                                <input type="number" name="quantity[]"
                                                    class="form-control product-quantity" value="1" min="1">
                                            </div>
                                            <div class="col-md-2">
                                                <label>Price (₹)</label>

                                                <input type="number" class="form-control product-price" placeholder="Price"
                                                    step="0.01" readonly style="background-color: #ffffff !important;">
                                            </div>
                                            <div class="col-md-2">
                                                <label>Subtotal (₹)</label>

                                                <input type="text" class="form-control product-subtotal" readonly
                                                    style="background-color: #ffffff !important;">
                                            </div>
                                            <div class="col-md-2 d-flex flex-row align-items-end justify-content-end"
                                                style="padding-bottom: 5px; gap: 5px;">
                                                <button type="button" class="btn btn-danger btn-sm remove-row"
                                                    onclick="removeProductRow(this)"
                                                    style="height: 38px; font-size: 12px; padding: 0 8px; white-space: nowrap;">
                                                    <i class="mdi mdi-delete" style="font-size: 14px;"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    @endempty
                            </div>

                            <button type="button" class="btn btn-success btn-sm mt-2" onclick="addProductRow()">
                                <i class="mdi mdi-plus"></i> Add More Product
                            </button>

                            <hr>

                            <div class="row">
                                <div class="col-md-6 offset-md-6">
                                    <div class="form-group">
                                        <label>Tax Rate (%)</label>
                                        <input type="number" name="tax_rate" class="form-control"
                                            value="{{ old('tax_rate', $invoice->tax_rate) }}" step="0.01"
                                            oninput="calculateTotal()" placeholder="Tax Rate">
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
    input[readonly].form-control,
    .form-control[readonly] {
        background-color: #ffffff !important;
        border-color: #ced4da !important;
        color: #212529 !important;
        opacity: 1 !important;
        cursor: default !important;
    }

    .form-control.is-invalid {
        background-color: #ffffff !important;
        background-image: none !important;
    }
</style>

<script>
    function addProductRow() {
        const row = document.querySelector('.product-row').cloneNode(true);
        const container = document.getElementById('product-rows');

        row.querySelectorAll('input').forEach(input => input.value = '');
        row.querySelector('.product-subtotal').value = '';
        row.querySelector('.product-quantity').value = 1;

        const select = row.querySelector('.product-select');
        if (select) select.selectedIndex = 0;

        select.addEventListener('change', function() {
            this.classList.remove('is-invalid');
            const errorMsg = document.getElementById('product-error-msg');
            if (errorMsg) errorMsg.remove();
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
        // Calculate total on initial load
        calculateTotal();

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
                const errorMsg = document.getElementById('product-error-msg');
                if (errorMsg) errorMsg.remove();
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
