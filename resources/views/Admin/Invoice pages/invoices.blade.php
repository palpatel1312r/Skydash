@extends('Components.adminheader')

@section('content')
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    {{-- Alert code --}}
                </div>
            </div>

            {{-- ✅ EXTERNAL TOP RIGHT HEADER --}}
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('invoices.create') }}" class="btn btn-primary shadow px-4 py-2">
                    <i class="mdi mdi-plus me-1"></i>Create New Invoice
                </a>
            </div>

            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">

                            <div
                                class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-3 border-bottom">

                                {{-- LEFT: Title & Stats --}}
                                <div class="d-flex align-items-center gap-3 mb-2 mb-md-0">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle text-primary">
                                        <i class="mdi mdi-file-document-outline" style="font-size: 24px;"></i>
                                    </div>
                                    <div>
                                        <h4 class="card-title mb-0 fw-bold text-dark">Invoice List</h4>
                                        <small class="text-muted">Manage your invoices</small>
                                    </div>
                                </div>

                                {{-- ✅ FIXED FILTERS SECTION --}}
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <div>
                                        <h4 class="card-title mr-2 mt-3 fw-bold text-dark">Filter By</h4>
                                    </div>

                                    {{-- Customer Filter --}}
                                    <div class="input-group input-group-sm shadow rounded" style="width: 180px;">
                                        <span class="input-group-text bg-primary text-white border-primary px-3">
                                            <i class="mdi mdi-account-outline"></i>
                                        </span>
                                        <select id="filterCustomer"
                                            class="form-select border-start-0 border-primary bg-white text-dark fw-bold">
                                            <option value="">All Customers</option>
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}"
                                                    {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->fullname }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
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
                                        <a href="{{ route('invoices.index') }}"
                                            class="btn btn-outline-danger btn-sm shadow-sm rounded-pill px-3"
                                            title="Clear Filters">
                                            <i class="mdi mdi-close me-1"></i> Clear
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- TABLE --}}

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
                                                        <span class="badge badge-info">
                                                            {{ $product['product_name'] }}
                                                            @if (isset($product['quantity']) && $product['quantity'] > 1)
                                                                (x{{ $product['quantity'] }})
                                                            @endif
                                                        </span>
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
                                                <td>{{ \Carbon\Carbon::parse($item->invoice_date)->format('M d, Y') }}</td>
                                                <td>
                                                    <!-- View Invoice Button -->
                                                    <button type="button" class="btn btn-info btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#viewInvoiceModal{{ $item->id }}">
                                                        <i class="mdi mdi-eye"></i> View
                                                    </button>

                                                    <!-- Update Button -->
                                                    <a href="{{ route('invoices.edit', $item->id) }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="mdi mdi-pencil"></i> Update
                                                    </a>

                                                    <!-- Delete Button -->
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="confirmDelete('{{ $item->id }}')">
                                                        <i class="mdi mdi-delete"></i> Delete
                                                    </button>

                                                    <form id="delete-form-{{ $item->id }}"
                                                        action="{{ route('admin.invoices.destroy', $item->id) }}"
                                                        method="POST" style="display: none;">
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

                            {{-- REMOVE THIS PAGINATION SECTION --}}
                            {{-- @if ($invoices->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $invoices->appends(request()->query())->links() }}
    </div>
@endif --}}

                            {{-- ✅ BOOTSTRAP 5 PAGINATION --}}
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6>Customer Information</h6>
                                <p>
                                    <strong>Name:</strong> {{ $item->customer->fullname ?? 'N/A' }}<br>
                                    <strong>Email:</strong> {{ $item->customer->email ?? 'N/A' }}<br>
                                    <strong>Phone:</strong> {{ $item->customer->phone ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="col-md-6 text-end">
                                <h6>Invoice Information</h6>
                                <p>
                                    <strong>Invoice #:</strong> {{ $item->invoice_number }}<br>
                                    <strong>Date:</strong>
                                    {{ \Carbon\Carbon::parse($item->invoice_date)->format('M d, Y') }}<br>
                                    <strong>Status:</strong>
                                    @if ($item->status == 'Paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif ($item->status == 'Unpaid')
                                        <span class="badge bg-warning">Unpaid</span>
                                    @else
                                        <span class="badge bg-danger">{{ $item->status }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($item->products as $index => $product)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                {{ $product['product_name'] }}
                                                @if (isset($product['quantity']) && $product['quantity'] > 1)
                                                    <strong>(x{{ $product['quantity'] }})</strong>
                                                @endif
                                            </td>
                                            <td class="text-end">x{{ $product['quantity'] ?? 1 }}</td>
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
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    </div>

    <style>
        /* DataTables Custom Styling */
        .dataTables_wrapper .dataTables_filter {
            float: right;
            text-align: right;
            margin-bottom: 15px;
        }

        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            border: 1px solid #e2e8f0;
            min-width: 200px;
        }

        .dataTables_wrapper .dataTables_length {
            float: left;
            margin-bottom: 15px;
        }

        .dataTables_wrapper .dataTables_length select {
            padding: 0.5rem 2rem 0.5rem 1rem;
            border-radius: 50px;
            border: 1px solid #e2e8f0;
            background-color: #fff;
            min-width: 80px;
        }

        .dataTables_wrapper .dataTables_info {
            clear: both;
            float: left;
            padding-top: 0.755em;
            color: #6c757d;
        }

        .dataTables_wrapper .dataTables_paginate {
            float: right;
            text-align: right;
            padding-top: 0.25em;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            box-sizing: border-box;
            display: inline-block;
            min-width: 1.5em;
            padding: 0.5em 1em;
            margin-left: 2px;
            text-align: center;
            text-decoration: none !important;
            cursor: pointer;
            color: #4a4a4a !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 50px !important;
            font-weight: 600 !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(145deg, #0d6efd 0%, #0dcaf0 100%) !important;
            border-color: #0d6efd !important;
            color: #ffffff !important;
            box-shadow: 0 6px 18px rgba(13, 110, 253, 0.4) !important;
            transform: scale(1.05) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #0d6efd !important;
            color: #ffffff !important;
            border-color: #0d6efd !important;
            transform: translateY(-3px) !important;
            box-shadow: 0 6px 15px rgba(13, 110, 253, 0.3) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            color: #adb5bd !important;
            background: #f8f9fa !important;
            border-color: #dee2e6 !important;
            box-shadow: none !important;
            transform: none !important;
            cursor: not-allowed !important;
        }

        /* Table styling */
        #invoiceTable {
            width: 100% !important;
        }

        #invoiceTable tbody tr:hover {
            background-color: #f8f9fa !important;
            transition: background-color 0.3s ease;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dataTables_wrapper .dataTables_filter {
                float: left;
                width: 100%;
                text-align: left;
                margin-top: 10px;
            }

            .dataTables_wrapper .dataTables_filter input {
                width: 100%;
                min-width: unset;
            }

            .dataTables_wrapper .dataTables_length {
                width: 100%;
            }

            .dataTables_wrapper .dataTables_info {
                float: none;
                text-align: center;
            }

            .dataTables_wrapper .dataTables_paginate {
                float: none;
                text-align: center;
                margin-top: 10px;
            }
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            if ($.fn.DataTable.isDataTable('#invoiceTable')) {
                $('#invoiceTable').DataTable().destroy();
            }

            var table = $('#invoiceTable').DataTable({
                responsive: true,
                processing: true,
                serverSide: false, // Set to true if you want server-side processing
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                order: [
                    [0, 'asc']
                ],
                columnDefs: [{
                        orderable: false,
                        targets: [3, 10]
                    }, // Disable sorting on Products and Actions columns
                    {
                        searchable: false,
                        targets: [3, 10]
                    } // Disable search on Products and Actions columns
                ],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    search: "_INPUT_",
                    searchPlaceholder: "Search invoices...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ invoices",
                    infoEmpty: "Showing 0 to 0 of 0 invoices",
                    infoFiltered: "(filtered from _MAX_ total invoices)",
                    zeroRecords: "No matching invoices found",
                    emptyTable: "No invoices available",
                    paginate: {
                        first: '<i class="mdi mdi-chevron-double-left"></i>',
                        previous: '<i class="mdi mdi-chevron-left"></i>',
                        next: '<i class="mdi mdi-chevron-right"></i>',
                        last: '<i class="mdi mdi-chevron-double-right"></i>'
                    }
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                drawCallback: function() {
                    // Re-initialize any Bootstrap tooltips or popovers if needed
                    // Handle any custom styling after draw
                }
            });

            // Apply existing filters to DataTable
            var filterCustomer = $('#filterCustomer').val();
            var dateRange = $('#dateRange').val();

            // If there are server-side filters, reload the page
            // Since you're using server-side filters, we keep the existing redirect logic
            // and the DataTable will just display the filtered results

            // Optional: Add custom styling to DataTables elements
            $('#invoiceTable_wrapper .dataTables_filter input').addClass('form-control form-control-sm');
            $('#invoiceTable_wrapper .dataTables_length select').addClass('form-select form-select-sm');

            // Auto-hide alerts after 5 seconds (existing code)
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

            // Show/Hide Custom Date Inputs when 'Custom Range' is selected
            $('#dateRange').on('change', function() {
                if ($(this).val() === 'custom') {
                    $('#customDateContainer').fadeIn(200);
                } else {
                    $('#customDateContainer').hide();
                    $('#customStartDate').val('');
                    $('#customEndDate').val('');
                }
            });

            // Trigger the check on page load
            if ($('#dateRange').val() === 'custom') {
                $('#customDateContainer').show();
            }

            // Filter handlers (existing code)
            $('#filterCustomer, #dateRange, #customStartDate, #customEndDate').on('change', function() {
                var customer = $('#filterCustomer').val();
                var dateRange = $('#dateRange').val();
                var startDate = $('#customStartDate').val();
                var endDate = $('#customEndDate').val();

                $('#global-alert-container').empty();

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
                        return;
                    } else {
                        $('#customStartDate').removeClass('is-invalid');
                        $('#customEndDate').removeClass('is-invalid');
                    }
                }

                var url = new URL(window.location.href);

                if (customer) {
                    url.searchParams.set('customer_id', customer);
                } else {
                    url.searchParams.delete('customer_id');
                }

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

            // Confirm Delete function
            window.confirmDelete = function(id) {
                if (confirm('Are you sure you want to delete this invoice?')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            };
        });
    </script>
@endsection
