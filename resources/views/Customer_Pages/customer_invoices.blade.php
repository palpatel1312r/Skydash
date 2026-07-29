@extends('Components.customerheader')
@section('content')
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">
            {{-- ✅ EXTERNAL TOP RIGHT HEADER --}}
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('customer.invoices.create') }}" class="btn btn-primary shadow px-4 py-2">
                    <i class="mdi mdi-plus me-1"></i> Create Invoice
                </a>
            </div>

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

                            {{-- ✅ MODERN HEADER WITH TITLE AND FILTERS --}}
                            <div
                                class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-3 border-bottom">

                                {{-- LEFT: Title --}}
                                <div class="d-flex align-items-center gap-3 mb-2 mb-md-0">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle text-primary">
                                        <i class="mdi mdi-file-document-outline" style="font-size: 24px;"></i>
                                    </div>
                                    <div>
                                        <h4 class="card-title mb-0 fw-bold text-dark">My Invoices</h4>
                                        <small class="text-muted">Manage your invoices</small>
                                    </div>
                                </div>

                                {{-- ✅ FIXED FILTERS SECTION --}}
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <div class="input-group input-group-sm shadow rounded" style="width: 180px;">
                                        <span class="input-group-text bg-primary text-white border-primary px-3">
                                            <i class="mdi mdi-account-outline"></i>
                                        </span>
                                        <select id="dateRange" class="form-select border-0 bg-white text-dark fw-bold">
                                            <option value="">All Time</option>
                                            <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>
                                                Today</option>
                                            <option value="yesterday"
                                                {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>Yesterday
                                            </option>
                                            <option value="this_week"
                                                {{ request('date_range') == 'this_week' ? 'selected' : '' }}>This Week
                                            </option>
                                            <option value="last_week"
                                                {{ request('date_range') == 'last_week' ? 'selected' : '' }}>Last Week
                                            </option>
                                            <option value="this_month"
                                                {{ request('date_range') == 'this_month' ? 'selected' : '' }}>This Month
                                            </option>
                                            <option value="last_month"
                                                {{ request('date_range') == 'last_month' ? 'selected' : '' }}>Last Month
                                            </option>
                                            <option value="custom"
                                                {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range
                                            </option>
                                        </select>
                                    </div>

                                    <div id="customDateContainer" style="display: none;">
                                        <div class="input-group input-group-sm shadow rounded" style="width: 180px;">
                                            <span class="input-group-text bg-primary text-white border-primary px-3">
                                                <i class="mdi mdi-account-outline"></i>
                                            </span>
                                            <input type="date" id="customStartDate"
                                                class="form-control border-0 bg-white text-dark fw-bold shadow-none"
                                                value="{{ request('start_date') }}" style="border-radius: 0;">
                                        </div>

                                        <span class="mx-1 text-primary fw-bold">to</span>

                                        <div class="input-group input-group-sm shadow rounded" style="width: 180px;">
                                            <span class="input-group-text bg-primary text-white border-primary px-3">
                                                <i class="mdi mdi-account-outline"></i>
                                            </span>
                                            <input type="date" id="customEndDate"
                                                class="form-control border-0 bg-white text-dark fw-bold shadow-none"
                                                value="{{ request('end_date') }}" style="border-radius: 0;">
                                        </div>
                                    </div>

                                    {{-- ✅ CLEAR FILTER BUTTON (Pill Shape) --}}
                                    @if (request()->has('customer_id') ||
                                            request()->has('date_range') ||
                                            request()->has('start_date') ||
                                            request()->has('end_date'))
                                        <a href="{{ route('customer.invoices') }}"
                                            class="btn btn-outline-danger btn-sm shadow-sm rounded-pill px-3"
                                            title="Clear Filters">
                                            <i class="mdi mdi-close me-1"></i> Clear
                                        </a>
                                    @endif
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
                                                <th>Products</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                                <th>Subtotal</th>
                                                <th>Tax</th>
                                                <th>Total Amount</th>
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
                                                        @foreach ($item->products as $product)
                                                            <span
                                                                class="badge badge-info mb-1">{{ $product['product_name'] }}</span>
                                                        @endforeach
                                                    </td>
                                                    <td class="text-center">
                                                        <strong>{{ collect($item->products)->sum('quantity') }}</strong>
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
                                                    <td>{{ \Carbon\Carbon::parse($item->invoice_date)->format('M d, Y') }}
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-info btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#viewInvoiceModal{{ $item->id }}">
                                                            <i class="mdi mdi-eye"></i> View
                                                        </button>
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
                            @endif

                            {{-- ✅ PAGINATION --}}
                            @if ($invoices->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $invoices->appends(request()->query())->links() }}
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                            <div class="col-md-6 text-end">
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
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($item->products as $index => $product)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $product['product_name'] }}</td>
                                            <td class="text-end">₹{{ number_format($product['price'], 2) }}</td>
                                            <td class="text-end">₹{{ number_format($product['subtotal'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                        <td class="text-end">₹{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Tax ({{ $item->tax_rate }}%)
                                                :</strong></td>
                                        <td class="text-end">₹{{ number_format($item->tax_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                                        <td class="text-end">
                                            <strong>₹{{ number_format($item->total_amount, 2) }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" onclick="window.print()">
                            <i class="mdi mdi-printer"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <style>
        .filter-input,
        .custom-date-input {
            transition: box-shadow 0.2s ease, transform 0.2s ease;
            min-width: 140px;
            max-width: 100%;
        }

        .filter-input:hover,
        .custom-date-input:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
            transform: translateY(-1px);
        }

        .pagination {
            display: flex !important;
            justify-content: center !important;
            margin-top: 30px !important;
            gap: 6px !important;
            padding-bottom: 20px !important;
        }

        .pagination .page-item {
            margin: 0 !important;
        }

        .pagination .page-link {
            background: #ffffff !important;
            border: 1px solid #e0e0e0 !important;
            border-radius: 50px !important;
            padding: 8px 16px !important;
            font-weight: 500 !important;
            color: #555555 !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02) !important;
            text-decoration: none !important;
        }

        .pagination .page-link:hover {
            background: #f0f7ff !important;
            border-color: #0d6efd !important;
            color: #0d6efd !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15) !important;
            z-index: 2 !important;
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(145deg, #0d6efd 0%, #0dcaf0 100%) !important;
            border-color: transparent !important;
            color: #ffffff !important;
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.45) !important;
            transform: scale(1.05) !important;
        }

        .pagination .page-item.disabled .page-link {
            color: #adb5bd !important;
            background: #ffffff !important;
            border-color: #e0e0e0 !important;
            box-shadow: none !important;
            transform: none !important;
            cursor: not-allowed !important;
            opacity: 0.6 !important;
        }

        @media (max-width: 576px) {
            .pagination .page-link {
                padding: 6px 12px !important;
                font-size: 14px !important;
            }
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

            // 1. Show/Hide Custom Date Inputs when 'Custom Range' is selected
            const dateRangeSelect = document.getElementById('dateRange');
            const customDateContainer = document.getElementById('customDateContainer');

            function toggleCustomDate() {
                if (dateRangeSelect.value === 'custom') {
                    $(customDateContainer).fadeIn(200).removeClass('d-none');
                } else {
                    $(customDateContainer).hide().addClass('d-none');
                    $('#customStartDate').val('');
                    $('#customEndDate').val('');
                }
            }

            // Run toggle on page load
            toggleCustomDate();

            // 2. FILTERS REDIRECT LOGIC (With Date Validation)
            $('#dateRange, #customStartDate, #customEndDate').on('change', function() {
                // ✅ REMOVED customer variable since there is no customer dropdown
                var dateRange = $('#dateRange').val();
                var startDate = $('#customStartDate').val();
                var endDate = $('#customEndDate').val();

                // Clear previous error messages first
                $('#global-alert-container').empty();

                // Validate Custom Dates
                if (dateRange === 'custom' && startDate && endDate) {
                    if (new Date(startDate) > new Date(endDate)) {
                        $('#global-alert-container').html(`
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Start date cannot be after End date. Please adjust your date range.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `);
                        $('#customStartDate').addClass('is-invalid');
                        $('#customEndDate').addClass('is-invalid');
                        return; // stop redirect
                    } else {
                        $('#customStartDate').removeClass('is-invalid');
                        $('#customEndDate').removeClass('is-invalid');
                    }
                }

                var url = new URL(window.location.href);

                // ✅ REMOVED customer search param logic
                url.searchParams.delete('customer_id');

                if (dateRange) {
                    url.searchParams.set('date_range', dateRange);
                } else {
                    url.searchParams.delete('date_range');
                }

                if (dateRange === 'custom') {
                    if (startDate) {
                        url.searchParams.set('start_date', startDate);
                    } else {
                        url.searchParams.delete('start_date');
                    }
                    if (endDate) {
                        url.searchParams.set('end_date', endDate);
                    } else {
                        url.searchParams.delete('end_date');
                    }
                } else {
                    url.searchParams.delete('start_date');
                    url.searchParams.delete('end_date');
                }

                window.location.href = url.toString();
            });
        });
    </script>
@endsection
