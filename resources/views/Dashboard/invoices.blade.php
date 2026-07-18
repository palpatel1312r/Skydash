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
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Subtotal</th>
                                            <th>Tax</th>
                                            <th>Grand Total</th>
                                            <th>Date</th>
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
                                                <td>
                                                    @if (isset($item->products[0]))
                                                        ₹{{ number_format($item->products[0]['price'], 2) }}
                                                    @else
                                                        ₹0.00
                                                    @endif
                                                </td>
                                                <td>₹{{ number_format($item->subtotal, 2) }}</td>
                                                <td>₹{{ number_format($item->tax_amount, 2) }}</td>
                                                <td>
                                                    <strong>₹{{ number_format($item->total_amount, 2) }}</strong>
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($item->invoice_date)->format('M d, Y') }}</td>
                                                <td>
                                                    <!-- View Invoice Button -->
                                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                                        data-target="#viewInvoiceModal{{ $item->id }}">
                                                        <i class="mdi mdi-eye"></i> View
                                                    </button>

                                                    {{-- ✅ FIXED: This opens the Edit page (invoices_edit.blade.php) --}}
                                                    <a href="{{ route('invoices.edit', $item->id) }}"
                                                        class="btn btn-warning btn-sm">
                                                        <i class="mdi mdi-pencil"></i> Update
                                                    </a>

                                                    <!-- Delete Button -->
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
                                                <td colspan="11" class="text-center">No invoices found.</td>
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
                                            <th>Quantity</th>
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
    </div>

    <script>
        // Confirm Delete
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this invoice?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }

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
