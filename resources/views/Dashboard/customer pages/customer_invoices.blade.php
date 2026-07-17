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
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <p class="card-title mb-0">My Invoices</p>
                                <span class="badge badge-info">Total: {{ $invoices->count() }} invoices</span>
                            </div>

                            @if ($invoices->isEmpty())
                                <div class="text-center py-5">
                                    <i class="mdi mdi-file-document-outline" style="font-size: 64px; color: #ddd;"></i>
                                    <h4 class="mt-3 text-muted">No invoices found</h4>
                                    <p class="text-muted">You don't have any invoices yet.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped table-borderless" id="customerInvoiceTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Invoice No</th>
                                                <th>Date</th>
                                                <th>Products</th>
                                                <th>Subtotal</th>
                                                <th>Tax</th>
                                                <th>Total Amount</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($invoices as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td><strong>{{ $item->invoice_number }}</strong></td>
                                                    <td>{{ \Carbon\Carbon::parse($item->invoice_date)->format('M d, Y') }}
                                                    </td>
                                                    <td>
                                                        @foreach ($item->products as $product)
                                                            <span
                                                                class="badge badge-info mb-1">{{ $product['product_name'] }}</span>
                                                        @endforeach
                                                    </td>
                                                    <td>₹{{ number_format($item->subtotal, 2) }}</td>
                                                    <td>₹{{ number_format($item->tax_amount, 2) }}</td>
                                                    <td>
                                                        <strong>₹{{ number_format($item->total_amount, 2) }}</strong>
                                                    </td>
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
                                                        <button type="button" class="btn btn-info btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#viewInvoiceModal{{ $item->id }}">
                                                            <i class="mdi mdi-eye"></i> View
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">No invoices found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            @endif
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
                                        <strong>Name:</strong> {{ $item->customer_name }}<br>
                                        <strong>Email:</strong> {{ $item->customer_email }}<br>
                                        <strong>Phone:</strong> {{ $item->customer_phone ?? 'N/A' }}
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
                                            <th class="text-right">Price</th>
                                            <th class="text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item->products as $index => $product)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $product['product_name'] }}</td>
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
                                                    ({{ $item->tax_rate }}%) :</strong></td>
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
                            <button type="button" class="btn btn-success" onclick="window.print()">
                                <i class="mdi mdi-printer"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        // Initialize DataTable for customer invoices
        $(document).ready(function() {
            $('#customerInvoiceTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "order": [[0, 'desc']], // ✅ FIXED: removed extra spaces & used single quotes
                "language": {
                    "emptyTable": "No invoices found"
                }
            });

            // Auto-hide alerts after 5 seconds
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