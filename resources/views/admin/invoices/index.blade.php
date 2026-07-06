<x-adminheader />

<!-- partial -->
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
                        <!-- Button to Open the Modal -->
                        <button type="button" class="btn btn-primary" data-toggle="modal"
                            data-target="#addInvoiceModal">
                            <i class="mdi mdi-plus"></i> Create New Invoice
                        </button>

                        <!-- Add Invoice Modal -->
                        <div class="modal fade" id="addInvoiceModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Create New Invoice</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form action="{{ route('invoices.store') }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Invoice Number:</label>
                                                    <input type="text" name="invoice_number"
                                                        class="form-control mb-2"
                                                        value="INV-{{ date('Ymd') }}-{{ rand(100, 999) }}" readonly
                                                        required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Invoice Date:</label>
                                                    <input type="date" name="invoice_date" class="form-control mb-2"
                                                        value="{{ date('Y-m-d') }}" required>
                                                </div>
                                            </div>

                                            <!-- Customer Dropdown -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>Select Customer:</label>
                                                    <select name="customer_id" class="form-control mb-2" required>
                                                        <option value="">-- Select Customer --</option>
                                                        @foreach ($customers as $customer)
                                                            <option value="{{ $customer->id }}">
                                                                {{ $customer->fullname }} ({{ $customer->email }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <hr>
                                            <h5>Products</h5>
                                            <div id="product-rows">
                                                <div class="row product-row">
                                                    <div class="col-md-5">
                                                        <label>Select Product:</label>
                                                        <select name="product_id[]" class="form-control product-select"
                                                            onchange="updateProductDetails(this)" required>
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
                                                    <div class="col-md-3">
                                                        <label>Price (₹):</label>
                                                        <input type="number" name="price[]"
                                                            class="form-control product-price" placeholder="Price"
                                                            step="0.01" readonly style="background: #f8f9fa;">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label>Subtotal (₹):</label>
                                                        <input type="text" name="subtotal[]"
                                                            class="form-control product-subtotal" readonly
                                                            style="background: #f8f9fa;">
                                                    </div>
                                                    <div class="col-md-1" style="display: flex; align-items: flex-end;">
                                                        <button type="button" class="btn btn-danger btn-sm remove-row"
                                                            onclick="removeProductRow(this)">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-success btn-sm mt-2"
                                                onclick="addProductRow()">
                                                <i class="mdi mdi-plus"></i> Add More Product
                                            </button>

                                            <hr>
                                            <div class="row">
                                                <div class="col-md-6 offset-md-6">
                                                    <div class="form-group">
                                                        <label>Tax Rate (%):</label>
                                                        <input type="number" name="tax_rate" class="form-control"
                                                            value="10" step="0.01" oninput="calculateTotal()"
                                                            placeholder="Tax Rate">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Subtotal:</label>
                                                        <input type="text" name="subtotal_amount"
                                                            id="subtotal_amount" class="form-control" readonly
                                                            style="background: #f8f9fa;">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Tax Amount:</label>
                                                        <input type="text" name="tax_amount" id="tax_amount"
                                                            class="form-control" readonly
                                                            style="background: #f8f9fa;">
                                                    </div>
                                                    <div class="form-group">
                                                        <label><strong>Total Amount:</strong></label>
                                                        <input type="text" name="total_amount" id="total_amount"
                                                            class="form-control" readonly
                                                            style="background: #f8f9fa; font-weight: bold; font-size: 1.2em;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Create Invoice</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <br><br>
                        <p class="card-title mb-0">Invoice List</p>
                        <div class="table-responsive">
                            <table class="table table-striped table-borderless" id="invoiceTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Invoice No</th>
                                        <th>Customer</th>
                                        <th>Products</th>
                                        <th>Subtotal</th>
                                        <th>Tax</th>
                                        <th>Grand Total</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($invoices as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $item->invoice_number }}</strong></td>
                                            <td>
                                                @if ($item->customer)
                                                    {{ $item->customer->fullname }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @foreach ($item->products as $product)
                                                    <span
                                                        class="badge badge-info">{{ $product['product_name'] }}</span>
                                                @endforeach
                                            </td>
                                            <td>₹{{ number_format($item->subtotal, 2) }}</td>
                                            <td>₹{{ number_format($item->tax_amount, 2) }}</td>
                                            <td>
                                                <strong>₹{{ number_format($item->total_amount, 2) }}</strong>
                                            </td>
                                            <td>{{ $item->invoice_date->format('M d, Y') }}</td>
                                            <td>
                                                @if ($item->status == 'Paid')
                                                    <label class="badge badge-success">Paid</label>
                                                @elseif ($item->status == 'Unpaid')
                                                    <label class="badge badge-warning">Unpaid</label>
                                                @elseif ($item->status == 'Cancelled')
                                                    <label class="badge badge-danger">Cancelled</label>
                                                @else
                                                    <label class="badge badge-info">{{ $item->status }}</label>
                                                @endif
                                            </td>
                                            <td>
                                                <!-- View Invoice Button -->
                                                <button type="button" class="btn btn-info btn-sm"
                                                    data-toggle="modal"
                                                    data-target="#viewInvoiceModal{{ $item->id }}">
                                                    <i class="mdi mdi-eye"></i> View
                                                </button>

                                                <!-- Change Status -->
                                                <div class="btn-group" role="group">
                                                    <button type="button"
                                                        class="btn btn-sm btn-secondary dropdown-toggle"
                                                        data-toggle="dropdown">
                                                        Status
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item"
                                                            href="{{ route('invoices.status', ['id' => $item->id, 'status' => 'Paid']) }}">
                                                            <span class="badge badge-success">Paid</span>
                                                        </a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('invoices.status', ['id' => $item->id, 'status' => 'Unpaid']) }}">
                                                            <span class="badge badge-warning">Unpaid</span>
                                                        </a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('invoices.status', ['id' => $item->id, 'status' => 'Cancelled']) }}">
                                                            <span class="badge badge-danger">Cancelled</span>
                                                        </a>
                                                    </div>
                                                </div>

                                                <!-- Delete Button -->
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="confirmDelete('{{ $item->id }}')">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>

                                                <!-- Delete Form -->
                                                <form id="delete-form-{{ $item->id }}"
                                                    action="{{ route('invoices.destroy', $item->id) }}"
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">No invoices found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- content-wrapper ends -->

    <!-- View Invoice Modals -->
    @foreach ($invoices as $item)
        <div class="modal fade" id="viewInvoiceModal{{ $item->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background: #f8f9fa;">
                        <h5 class="modal-title">
                            <strong>Invoice #{{ $item->invoice_number }}</strong>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6>Customer Information</h6>
                                <p>
                                    <strong>Name:</strong>
                                    @if ($item->customer)
                                        {{ $item->customer->fullname }}
                                    @else
                                        N/A
                                    @endif
                                    <br>
                                    <strong>Email:</strong>
                                    @if ($item->customer)
                                        {{ $item->customer->email }}
                                    @else
                                        N/A
                                    @endif
                                    <br>
                                    <strong>Phone:</strong>
                                    @if ($item->customer)
                                        {{ $item->customer->phone ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 text-right">
                                <h6>Invoice Information</h6>
                                <p>
                                    <strong>Invoice #:</strong> {{ $item->invoice_number }}<br>
                                    <strong>Date:</strong> {{ $item->invoice_date->format('M d, Y') }}<br>
                                    <strong>Status:</strong>
                                    @if ($item->status == 'Paid')
                                        <span class="badge badge-success">Paid</span>
                                    @elseif ($item->status == 'Unpaid')
                                        <span class="badge badge-warning">Unpaid</span>
                                    @else
                                        <span class="badge badge-danger">{{ $item->status }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th class="text-right">Price</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($item->products as $index => $product)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $product['product_name'] }}</td>
                                            <td class="text-right">${{ number_format($product['price'], 2) }}</td>
                                            <td class="text-right">${{ number_format($product['subtotal'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                                        <td class="text-right">${{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Tax
                                                ({{ $item->tax_rate }}%):</strong></td>
                                        <td class="text-right">${{ number_format($item->tax_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Grand Total:</strong></td>
                                        <td class="text-right">
                                            <strong>${{ number_format($item->total_amount, 2) }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <x-adminfooter />

    <script>
        // Add Product Row
        function addProductRow() {
            const row = document.querySelector('.product-row').cloneNode(true);
            const container = document.getElementById('product-rows');

            // Clear input values
            row.querySelectorAll('input').forEach(input => input.value = '');
            row.querySelector('.product-subtotal').value = '';

            // Reset select dropdown
            const select = row.querySelector('.product-select');
            if (select) {
                select.selectedIndex = 0;
            }

            container.appendChild(row);
            updateRowNumbers();
        }

        // Remove Product Row
        function removeProductRow(button) {
            const rows = document.querySelectorAll('.product-row');
            if (rows.length > 1) {
                button.closest('.product-row').remove();
                updateRowNumbers();
                calculateTotal();
            }
        }

        // Update Row Numbers
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

        // Update Product Details when dropdown changes
        function updateProductDetails(select) {
            const row = select.closest('.product-row');
            const priceInput = row.querySelector('.product-price');
            const subtotalInput = row.querySelector('.product-subtotal');
            const selectedOption = select.options[select.selectedIndex];

            if (selectedOption && selectedOption.value) {
                const price = selectedOption.getAttribute('data-price');
                priceInput.value = price || 0;
                // Subtotal = price (since no quantity)
                subtotalInput.value = price || 0;
            } else {
                priceInput.value = '';
                subtotalInput.value = '';
            }

            calculateTotal();
        }

        // Calculate Overall Total (no quantity needed)
        function calculateTotal() {
            const rows = document.querySelectorAll('.product-row');
            let subtotal = 0;

            rows.forEach(row => {
                const subtotalInput = row.querySelector('.product-subtotal');
                if (subtotalInput) {
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

        // Confirm Delete
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this invoice?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }

        // Auto-calculate on tax rate change
        document.querySelector('input[name="tax_rate"]')?.addEventListener('input', calculateTotal);

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (document.querySelector('#addInvoiceModal')) {
                document.querySelector('#addInvoiceModal').addEventListener('show.bs.modal', function() {
                    setTimeout(calculateTotal, 100);
                });
            }
        });
    </script>
</div>
