@extends('components.adminheader')

@section('content')
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
                            <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Create New Invoice
                            </a>
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
                                            <th>Quntity</th>
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
                                                    {{ $item->customer_name }}
                                                    <br>
                                                    <small class="text-muted">{{ $item->customer_email }}</small>
                                                </td>
                                                <td>
                                                    @foreach ($item->products as $product)
                                                        <span class="badge badge-info">{{ $product['product_name'] }}</span>
                                                    @endforeach
                                                </td>
                                                <td class="text-center">
                                                    <strong>
                                                        {{ collect($item->products)->sum('quantity') }}
                                                    </strong>
                                                </td>
                                                <td>₹{{ number_format($item->subtotal, 2) }}</td>
                                                <td>₹{{ number_format($item->subtotal, 2) }}</td>
                                                <td>₹{{ number_format($item->tax_amount, 2) }}</td>
                                                <td>
                                                    <strong>₹{{ number_format($item->total_amount, 2) }}</strong>
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($item->invoice_date)->format('M d, Y') }}</td>
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
                                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                                        data-target="#viewInvoiceModal{{ $item->id }}">
                                                        <i class="mdi mdi-eye"></i> View
                                                    </button>

                                                    <!-- Change Status Dropdown -->
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

                                                    <button type="button" class="btn btn-danger"
                                                        onclick="confirmDelete('{{ $item->id }}')"
                                                        style="padding: 6px 12px; font-size: 14px; display: inline-flex; align-items: center; gap: 5px;">
                                                        <i class="mdi mdi-delete" style="font-size: 16px;"></i> Delete
                                                    </button>

                                                    <form id="delete-form-{{ $item->id }}"
                                                        action="{{ route('invoices.destroy', $item->id) }}" method="POST"
                                                        style="display: none;">
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
                                        <strong>Date:</strong>
                                        {{ \Carbon\Carbon::parse($item->invoice_date)->format('M d, Y') }}<br>
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
                                            <th>Quantity</th> <!-- ✅ Fixed typo -->
                                            <th class="text-right">Price</th>
                                            <th class="text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item->products as $index => $product)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $product['product_name'] }}</td>
                                                <td class="text-right">x{{ $product['quantity'] ?? 1 }}</td>
                                                <td class="text-right">₹{{ number_format($product['price'], 2) }}</td>
                                                <td class="text-right">₹{{ number_format($product['subtotal'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                                            <td class="text-right">₹{{ number_format($item->subtotal, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Tax
                                                    ({{ $item->tax_rate }}%)
                                                    :</strong></td>
                                            <td class="text-right">₹{{ number_format($item->tax_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Grand Total:</strong></td>
                                            <td class="text-right">
                                                <strong>₹{{ number_format($item->total_amount, 2) }}</strong>
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

        {{-- <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2024 <a
                        href="#" target="_blank">Skydash</a>. All rights reserved.</span>
                <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i
                        class="ti-heart text-danger ml-1"></i></span>
            </div>
        </footer> --}}
    </div>

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
                subtotalInput.value = price || 0;
            } else {
                priceInput.value = '';
                subtotalInput.value = '';
            }

            calculateTotal();
        }

        // Calculate Overall Total
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

        // ==========================================
        // ✅ NEW: Disable Products until Customer is selected
        // ==========================================
        document.addEventListener('DOMContentLoaded', function() {
            const customerSelect = document.querySelector('select[name="customer_id"]');
            const productRows = document.getElementById('product-rows');
            const addProductBtn = document.querySelector('button[onclick="addProductRow()"]');

            // Function to toggle product section
            function toggleProductSection() {
                const selected = customerSelect.value;
                if (selected === '') {
                    // If no customer selected, disable everything
                    productRows.style.opacity = '0.5';
                    productRows.style.pointerEvents = 'none';
                    if (addProductBtn) addProductBtn.disabled = true;
                } else {
                    // If customer selected, enable everything
                    productRows.style.opacity = '1';
                    productRows.style.pointerEvents = 'auto';
                    if (addProductBtn) addProductBtn.disabled = false;
                }
            }

            // Run the check on page load (in case a user is pre-selected)
            toggleProductSection();

            // Run the check whenever the Customer dropdown changes
            customerSelect.addEventListener('change', toggleProductSection);
        });

        // ==========================================
        // ✅ Auto-hide alerts after 5 seconds
        // ==========================================
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                });
            }, 5000);
        });
    </script>
@endsection
