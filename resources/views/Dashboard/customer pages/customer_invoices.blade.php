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

                            {{-- ✅ ATTRACTIVE HEADER WITH TITLE, FILTERS, AND CREATE BUTTON --}}
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

                                {{-- LEFT: Title & Total Count Badge --}}
                                <div class="d-flex align-items-center gap-2">
                                    <h4 class="card-title mb-0">
                                        <i class="mdi mdi-file-document-outline text-primary"></i> My Invoices
                                    </h4>
                                    {{-- <span class="badge badge-primary badge-pill px-3 py-2 shadow-sm"
                                        style="font-size: 0.85rem;">
                                        {{ $invoices->total() }} Total
                                    </span> --}}
                                </div>

                                <div class="d-flex align-items gap-2 flex-wrap">

                                    {{-- Customer Filter --}}
                                    <div class="input-group input-group-sm" style="width: 180px;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text border-right-0 text-primary bg-light">
                                                <i class="mdi mdi-account-outline"></i>
                                            </span>
                                        </div>
                                        <select id="filterCustomer"
                                            class="form-control border-left-0 shadow-sm bg-light text-dark font-weight-bold">
                                            <option value="">All Customers</option>
                                            @if (isset($customers))
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}"
                                                        {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                                        {{ $customer->fullname }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    {{-- Time Filter --}}
                                    <div class="input-group input-group-sm" style="width: 150px;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text border-right-0 text-primary bg-light">
                                                <i class="mdi mdi-calendar-clock"></i>
                                            </span>
                                        </div>
                                        <select id="filterTime"
                                            class="form-control border-left-0 shadow-sm bg-light text-dark font-weight-bold">
                                            <option value="">All Time</option>
                                            <option value="today" {{ request('time') == 'today' ? 'selected' : '' }}>Today
                                            </option>
                                            <option value="1_week" {{ request('time') == '1_week' ? 'selected' : '' }}>1
                                                Week</option>
                                            <option value="2_week" {{ request('time') == '2_week' ? 'selected' : '' }}>2
                                                Weeks</option>
                                            <option value="this_month"
                                                {{ request('time') == 'this_month' ? 'selected' : '' }}>This Month</option>
                                            <option value="last_month"
                                                {{ request('time') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- RIGHT: Create Button with Soft shadow --}}
                                <div>
                                    <a href="{{ route('customer.invoices.create') }}"
                                        class="btn btn-primary btn-sm shadow-sm ">
                                        <i class="mdi mdi-plus"></i> Create Invoice
                                    </a>
                                </div>
                            </div>

                            @if ($invoices->isEmpty())
                                <div class="text-center py-5">
                                    <i class="mdi mdi-file-document-outline" style="font-size: 64px; color: #ddd;"></i>
                                    <h4 class="mt-3 text-muted">No invoices found</h4>
                                    <p class="text-muted">You don't have any invoices yet.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped table-borderless">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Invoice No</th>
                                                <th>Date</th>
                                                <th>Products</th>
                                                <th>Price</th>
                                                <th>Subtotal</th>
                                                <th>Tax</th>
                                                <th>Total Amount</th>
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

                            {{-- ✅ PAGINATION --}}
                            @if ($invoices->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $invoices->appends(request()->query())->links('pagination::bootstrap-4') }}
                                </div>
                            @endif


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


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
                                        <td colspan="3" class="text-right"><strong>Tax ({{ $item->tax_rate }}%)
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
                        <button type="button" class="btn btn-success" onclick="window.print()">
                            <i class="mdi mdi-printer"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    </div>
    <style>
        /* FORCE BOOTSTRAP 4 PAGINATION STYLE */
        .pagination {
            display: flex !important;
            justify-content: center !important;
            margin-top: 20px !important;
            border-radius: 4px !important;
        }

        .pagination .page-item .page-link {
            color: #0d6efd !important;
            background-color: #fff !important;
            border: 1px solid #dee2e6 !important;
            padding: 0.5rem 0.75rem !important;
            margin-left: -1px !important;
        }

        .pagination .page-item.active .page-link {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #fff !important;
            z-index: 3 !important;
        }

        .pagination .page-item:first-child .page-link {
            border-top-left-radius: 4px !important;
            border-bottom-left-radius: 4px !important;
        }

        .pagination .page-item:last-child .page-link {
            border-top-right-radius: 4px !important;
            border-bottom-right-radius: 4px !important;
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d !important;
            background-color: #fff !important;
            border-color: #dee2e6 !important;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {

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
            }, 500);

            // ✅ FILTERS REDIRECT LOGIC
            $('#filterCustomer, #filterTime').on('change', function() {
                var customer = $('#filterCustomer').val();
                var time = $('#filterTime').val();

                var url = new URL(window.location.href);

                if (customer) {
                    url.searchParams.set('customer_id', customer);
                } else {
                    url.searchParams.delete('customer_id');
                }

                if (time) {
                    url.searchParams.set('time', time);
                } else {
                    url.searchParams.delete('time');
                }

                window.location.href = url.toString();
            });
        });
    </script>
@endsection
