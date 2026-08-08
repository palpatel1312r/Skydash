@extends('Components.customerheader')

@section('content')
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- FILTERS + CREATE BUTTON ROW --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div
                        class="d-flex flex-column flex-sm-row flex-wrap align-items-start align-items-sm-center justify-content-between gap-2 gap-sm-3">

                        {{-- LEFT: Filters --}}
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="text-muted fw-bold small me-1 d-none d-sm-inline">Filter By:</span>

                            {{-- Date Range Dropdown --}}
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm shadow-sm rounded-pill px-3 dropdown-toggle"
                                    type="button" id="dateRangeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-calendar-outline me-1"></i>
                                    <span id="dateRangeLabel">
                                        @php
                                            $label = 'All Time';

                                            // ✅ NEW: If it's a preset (today, this_week, etc.), just show that name
if (
    isset($request) &&
    $request->has('date_range') &&
    $request->date_range !== 'custom'
) {
    $label = ucwords(str_replace('_', ' ', $request->date_range));
}
// If it's custom dates
                                            elseif (
                                                isset($request) &&
                                                $request->has('start_date') &&
                                                $request->has('end_date')
                                            ) {
                                                $start = $request->start_date;
                                                $end = $request->end_date;

                                                // Check if it matches today
                                                $today = date('Y-m-d');
                                                if ($start === $today && $end === $today) {
                                                    $label = 'Today';
                                                }
                                                // Check if single day
                                                elseif ($start === $end) {
                                                    $label = \Carbon\Carbon::parse($start)->format('M d, Y');
                                                }
                                                // Otherwise full range
                                                else {
                                                    $label = $start . ' to ' . $end;
                                                }
                                            }
                                        @endphp
                                        {{ $label }}
                                    </span>
                                </button>
                                <ul class="dropdown-menu shadow-sm" aria-labelledby="dateRangeDropdown"
                                    style="min-width: 200px;">
                                    <li><a class="dropdown-item date-preset-option" href="#" data-range="all">All
                                            Time</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item date-preset-option" href="#"
                                            data-range="today">Today</a></li>
                                    <li><a class="dropdown-item date-preset-option" href="#"
                                            data-range="yesterday">Yesterday</a></li>
                                    <li><a class="dropdown-item date-preset-option" href="#"
                                            data-range="this_week">This Week</a></li>
                                    <li><a class="dropdown-item date-preset-option" href="#"
                                            data-range="last_week">Last Week</a></li>
                                    <li><a class="dropdown-item date-preset-option" href="#"
                                            data-range="this_month">This Month</a></li>
                                    <li><a class="dropdown-item date-preset-option" href="#"
                                            data-range="last_month">Last Month</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                            data-bs-target="#dateRangeModal">Custom Range</a></li>
                                </ul>
                            </div>

                            {{-- Clear Filters Button --}}
                            <a href="{{ route('customer.invoices') }}"
                                class="btn btn-sm shadow-sm rounded-pill px-3 
                               {{ request()->has('customer_id') || request()->has('start_date') || request()->has('end_date') || request()->has('date_range') ? 'btn-outline-danger' : 'btn-outline-dark' }}">
                                <i class="mdi mdi-close me-1"></i> <span class="d-none d-sm-inline">Clear</span>
                            </a>
                        </div>

                        {{-- RIGHT: Create Button --}}
                        <a href="{{ route('customer.invoices.create') }}" class="btn btn-primary shadow px-3 px-sm-4 py-2">
                            <i class="mdi mdi-plus me-1"></i><span class="d-none d-sm-inline">Create </span>Invoice
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div
                                class="d-flex flex-wrap align-items-center justify-content-between p-3 p-sm-4 pb-3 border-bottom">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle text-primary">
                                        <i class="mdi mdi-file-document-outline" style="font-size: 24px;"></i>
                                    </div>
                                    <div>
                                        <h4 class="card-title mb-0 fw-bold text-dark">My Invoices</h4>
                                        <small class="text-muted">Manage your invoices</small>
                                    </div>
                                </div>
                                <div class="input-group" style="max-width: 320px; min-width: 200px;">
                                    <span class="input-group-text bg-light border-end-0 py-2 px-3">
                                        <i class="mdi mdi-magnify text-muted"
                                            style="font-size: 1.3rem; line-height: 1;"></i>
                                    </span>
                                    <input id="dtSearch" class="form-control bg-light border-start-0 py-2 px-3"
                                        placeholder="Search invoices..." style="font-size: 1rem;">
                                </div>
                            </div>

                            {{-- TABLE --}}
                            <div class="table-responsive">
                                <table class="table table-striped table-borderless" id="invoiceTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Invoice No</th>
                                            <th>Products</th>
                                            <th>Qty</th>
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
                                                <td><strong>₹{{ number_format($item->total_amount, 2) }}</strong></td>
                                                <td>{{ \Carbon\Carbon::parse($item->invoice_date)->format('M d, Y') }}</td>
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
                                                <td colspan="7" class="text-center py-4">No invoices found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- FOOTER: Custom DataTables Footer --}}
                        <div class="card-footer bg-white border-top py-3 px-3 px-sm-4">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2"
                                id="dtCustomFooter">
                                <div id="lengthContainer" class="order-1 order-sm-1"></div>
                                <div id="paginationContainer" class="order-3 order-sm-2"></div>
                                <div id="infoContainer" class="text-center text-sm-end order-2 order-sm-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- CUSTOM DATE RANGE MODAL --}}
    <div class="modal fade" id="dateRangeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 720px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header border-bottom bg-light">
                    <h5 class="modal-title fw-bold">Select Custom Range</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 p-sm-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <button type="button" class="btn btn-sm btn-light rounded-circle cal-nav" id="calPrev"
                            style="width: 32px; height: 32px;">
                            <i class="mdi mdi-chevron-left"></i>
                        </button>
                        <div class="d-flex gap-2 gap-sm-5">
                            <strong id="month1Label" class="fs-6 text-dark">January 2026</strong>
                            <strong id="month2Label" class="fs-6 text-dark d-none d-sm-block">February 2026</strong>
                        </div>
                        <button type="button" class="btn btn-sm btn-light rounded-circle cal-nav" id="calNext"
                            style="width: 32px; height: 32px;">
                            <i class="mdi mdi-chevron-right"></i>
                        </button>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-3 gap-sm-4 flex-grow-1">
                        {{-- Calendar 1 --}}
                        <div class="flex-grow-1">
                            <div class="d-flex text-center small text-muted mb-2 fw-semibold">
                                <div class="flex-grow-1 py-1">Su</div>
                                <div class="flex-grow-1 py-1">Mo</div>
                                <div class="flex-grow-1 py-1">Tu</div>
                                <div class="flex-grow-1 py-1">We</div>
                                <div class="flex-grow-1 py-1">Th</div>
                                <div class="flex-grow-1 py-1">Fr</div>
                                <div class="flex-grow-1 py-1">Sa</div>
                            </div>
                            <div id="calendar1" class="calendar-grid"></div>
                        </div>
                        {{-- Calendar 2 --}}
                        <div class="flex-grow-1 d-none d-sm-block">
                            <div class="d-flex text-center small text-muted mb-2 fw-semibold">
                                <div class="flex-grow-1 py-1">Su</div>
                                <div class="flex-grow-1 py-1">Mo</div>
                                <div class="flex-grow-1 py-1">Tu</div>
                                <div class="flex-grow-1 py-1">We</div>
                                <div class="flex-grow-1 py-1">Th</div>
                                <div class="flex-grow-1 py-1">Fr</div>
                                <div class="flex-grow-1 py-1">Sa</div>
                            </div>
                            <div id="calendar2" class="calendar-grid"></div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3 pt-3 border-top">
                        <button type="button" class="btn btn-light btn-sm px-3 rounded-pill"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="applyDateRange"
                            class="btn btn-primary btn-sm px-4 rounded-pill fw-semibold">Apply dates</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- View Invoice Modals --}}
    @foreach ($invoices as $item)
        <div class="modal fade" id="viewInvoiceModal{{ $item->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title"><strong>Invoice #{{ $item->invoice_number }}</strong></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6>Customer Information</h6>
                                <p><strong>Name:</strong> {{ $item->customer_name }}<br>
                                    <strong>Email:</strong> {{ $item->customer_email }}<br>
                                    <strong>Phone:</strong> {{ $item->customer_phone ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="col-md-6 text-end">
                                <h6>Invoice Information</h6>
                                <p><strong>Invoice #:</strong> {{ $item->invoice_number }}<br>
                                    <strong>Date:</strong>
                                    {{ \Carbon\Carbon::parse($item->invoice_date)->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th class="text-end">Quantity</th>
                                        <th class="text-end">Price Per Item</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($item->products as $index => $product)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $product['product_name'] }}</td>
                                            <td class="text-end">{{ $product['quantity'] }}</td>
                                            <td class="text-end">₹{{ number_format($product['price'], 2) }}</td>
                                            <td class="text-end">₹{{ number_format($product['subtotal'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                        <td class="text-end">₹{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Tax ({{ $item->tax_rate }}%)
                                                :</strong></td>
                                        <td class="text-end">₹{{ number_format($item->tax_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Grand Total:</strong></td>
                                        <td class="text-end"><strong>₹{{ number_format($item->total_amount, 2) }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" onclick="window.print()"><i
                                class="mdi mdi-printer"></i> Print</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <style>
        #dateRangeDropdown:hover {
            background-color: #1e2124 !important;
        }


        /* ===== CUSTOM CALENDAR ===== */
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
        }

        .cal-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.82rem;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.15s ease;
            border: 2px solid transparent;
            color: #212529;
            font-weight: 500;
        }

        .cal-day:hover:not(.empty):not(.disabled) {
            background: #e9ecef;
        }

        .cal-day.other-month {
            color: #ced4da;
            pointer-events: none;
        }

        .cal-day.in-range {
            background: #e7f1ff;
            color: #0d6efd;
            border-radius: 0;
        }

        .cal-day.in-range.start-date {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        .cal-day.in-range.end-date {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        .cal-day.selected-start,
        .cal-day.selected-end {
            background: #0d6efd !important;
            color: #fff !important;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(13, 110, 253, 0.35);
        }

        .cal-day.selected-start {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .cal-day.selected-end {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .cal-day.selected-both {
            border-radius: 8px !important;
        }

        .cal-day.empty {
            pointer-events: none;
        }

        .cal-nav {
            transition: all 0.2s;
        }

        .cal-nav:hover {
            background: #e9ecef !important;
        }

        #invoiceTable {
            width: 100% !important;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 800px;
        }

        #invoiceTable thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
            white-space: nowrap;
        }

        #invoiceTable tbody tr:hover {
            background-color: #f8f9fa !important;
            transition: 0.2s;
        }

        #invoiceTable tbody td {
            vertical-align: middle;
            font-size: 0.875rem;
        }

        .badge-info {
            background-color: #e3f2fd;
            color: #0d6efd;
            padding: 4px 8px;
            margin: 2px;
            font-weight: 500;
            border-radius: 4px;
        }

        .dataTables_filter {
            display: none !important;
        }

        #dtCustomFooter {
            min-height: 40px;
        }

        #lengthContainer .dataTables_length {
            float: none !important;
            margin: 0 !important;
        }

        #lengthContainer .dataTables_length label {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            margin: 0 !important;
            color: #6c757d !important;
            font-size: 0.875rem !important;
            font-weight: 500;
        }

        #lengthContainer .dataTables_length select {
            border-radius: 50px !important;
            border: 1px solid #dee2e6 !important;
            background-color: #f8f9fa !important;
            padding: 0.25rem 2rem 0.25rem 0.75rem !important;
            font-size: 0.875rem !important;
            cursor: pointer !important;
            color: #495057 !important;
            font-weight: 500;
            appearance: auto;
        }

        #paginationContainer .dataTables_paginate {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 4px !important;
            flex-wrap: wrap !important;
        }

        #paginationContainer .dataTables_paginate .paginate_button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 38px;
            height: 38px;
            border-radius: 10px !important;
            border: 1px solid #e9ecef !important;
            background: #ffffff !important;
            color: #495057 !important;
            font-size: 0.875rem !important;
            transition: all 0.2s ease !important;
        }

        #paginationContainer .dataTables_paginate .paginate_button.current {
            background: #121314 !important;
            border-color: #b7b8ba !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.28) !important;
        }

        #infoContainer .dataTables_info {
            float: none !important;
            text-align: right !important;
            color: #6c757d;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* RESPONSIVE */
        @media (max-width: 767.98px) {
            #invoiceTable {
                min-width: 750px;
            }

            .content-wrapper {
                padding: 1rem !important;
            }
        }

        @media (max-width: 575.98px) {
            #invoiceTable {
                min-width: 700px;
            }

            #dtCustomFooter {
                flex-direction: column !important;
                gap: 10px !important;
            }
        }
    </style>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            var urlParams = new URLSearchParams(window.location.search);

            // ===================== DATATABLES =====================
            // ✅ FIX: Check if DataTable is already initialized
            if ($.fn.DataTable.isDataTable('#invoiceTable')) {
                $('#invoiceTable').DataTable().destroy();
            }

            var table = $('#invoiceTable').DataTable({
                responsive: true,
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
                        targets: [2, 6]
                    },
                    {
                        searchable: false,
                        targets: [2, 6]
                    },
                    {
                        className: 'text-center',
                        targets: [3]
                    }
                ],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"></div>',
                    search: "",
                    lengthMenu: "Show _MENU_",
                    info: "Showing _START_ to _END_ of _TOTAL_ invoices",
                    infoEmpty: "Showing 0 to 0 of 0 invoices",
                    zeroRecords: "No matching invoices found",
                    paginate: {
                        first: '<i class="mdi mdi-chevron-double-left"></i>',
                        previous: '<i class="mdi mdi-chevron-left"></i>',
                        next: '<i class="mdi mdi-chevron-right"></i>',
                        last: '<i class="mdi mdi-chevron-double-right"></i>'
                    }
                },
                // ✅ FIX: Ensure table is fully initialized before moving controls
                initComplete: function() {
                    // Move controls to custom footer after initialization
                    setTimeout(function() {
                        $('#lengthContainer').append($('.dataTables_length'));
                        $('#paginationContainer').append($('.dataTables_paginate'));
                        $('#infoContainer').append($('.dataTables_info'));
                    }, 100);
                }
            });

            // ✅ FIX: Double-check that controls are moved (fallback)
            setTimeout(function() {
                if ($('#lengthContainer').is(':empty')) {
                    $('#lengthContainer').append($('.dataTables_length'));
                    $('#paginationContainer').append($('.dataTables_paginate'));
                    $('#infoContainer').append($('.dataTables_info'));
                }
            }, 500);

            // ========================================
            // ✅ PERSISTENT SEARCH FUNCTIONALITY
            // ========================================
            var searchInput = $('#dtSearch');
            var searchParam = searchInput.data('search-param');

            // 1. If there's a search term in the URL, apply it to DataTable immediately
            if (searchParam && searchParam.trim() !== '') {
                table.search(searchParam).draw();
                console.log('✅ Applied initial search: ' + searchParam);
            }

            // 2. Search as user types (with debounce)
            var searchTimeout;
            searchInput.on('keyup', function() {
                clearTimeout(searchTimeout);
                var value = $(this).val().trim();

                searchTimeout = setTimeout(function() {
                    // Update the URL with the search term (without reloading the page)
                    var url = new URL(window.location.href);
                    if (value !== '') {
                        url.searchParams.set('search', value);
                    } else {
                        url.searchParams.delete('search');
                    }
                    // Update the URL in the browser without reloading
                    window.history.replaceState({}, '', url.toString());

                    // Apply search to DataTable
                    table.search(value).draw();
                    console.log('✅ Applied search: ' + value);
                }, 300);
            });

            // 3. Handle "Clear Filters" button to also clear the search
            $('.btn-outline-danger, .btn-outline-dark').on('click', function(e) {
                // If this is the Clear button, also clear the search input and URL param
                if ($(this).hasClass('btn-outline-danger') || $(this).text().includes('Clear')) {
                    searchInput.val('');
                    var url = new URL(window.location.href);
                    url.searchParams.delete('search');
                    window.history.replaceState({}, '', url.toString());
                    table.search('').draw();
                    console.log('✅ Cleared search');
                }
            });

            // ===================== DATE RANGE FILTER =====================
            var dateRangeParam = urlParams.get('date_range');
            var startDateParam = urlParams.get('start_date');
            var endDateParam = urlParams.get('end_date');

            // Set date range label on page load
            function updateDateRangeLabel() {
                var start = urlParams.get('start_date');
                var end = urlParams.get('end_date');

                if (start && end) {
                    // Get today's date in YYYY-MM-DD format
                    var today = new Date();
                    var todayStr = today.getFullYear() + '-' +
                        String(today.getMonth() + 1).padStart(2, '0') + '-' +
                        String(today.getDate()).padStart(2, '0');

                    // If start and end are the same as today
                    if (start === todayStr && end === todayStr) {
                        $('#dateRangeLabel').text('Today');
                    }
                    // If start and end are the same (just a single day)
                    else if (start === end) {
                        var d = new Date(start + 'T00:00:00');
                        var options = {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        };
                        $('#dateRangeLabel').text(d.toLocaleDateString('en-US', options));
                    }
                    // Otherwise, show the full range
                    else {
                        $('#dateRangeLabel').text(start + ' to ' + end);
                    }
                } else {
                    // Fallback
                    $('#dateRangeLabel').text('All Time');
                }
            }

            // Only run the label update if the URL does NOT have a 'date_range' parameter
            if (!urlParams.has('date_range')) {
                updateDateRangeLabel();
            }

            // Handle Preset Clicks (Today, Yesterday, etc.)
            $('.date-preset-option').on('click', function(e) {
                e.preventDefault();
                var range = $(this).data('range');
                var url = new URL(window.location.href);

                // Clear old dates
                url.searchParams.delete('start_date');
                url.searchParams.delete('end_date');
                url.searchParams.delete('page');

                // Helper to format date as YYYY-MM-DD
                function formatDate(d) {
                    return d.getFullYear() + '-' +
                        String(d.getMonth() + 1).padStart(2, '0') + '-' +
                        String(d.getDate()).padStart(2, '0');
                }

                var now = new Date();
                var start = new Date(now);
                var end = new Date(now);
                var label = 'All Time';

                if (range !== 'all') {
                    switch (range) {
                        case 'today':
                            start = end = now;
                            label = 'Today';
                            break;
                        case 'yesterday':
                            start = new Date(now);
                            start.setDate(now.getDate() - 1);
                            end = start;
                            label = 'Yesterday';
                            break;
                        case 'this_week':
                            start = new Date(now);
                            start.setDate(now.getDate() - now.getDay());
                            label = 'This Week';
                            break;
                        case 'last_week':
                            start = new Date(now);
                            start.setDate(now.getDate() - now.getDay() - 7);
                            end = new Date(start);
                            end.setDate(start.getDate() + 6);
                            label = 'Last Week';
                            break;
                        case 'this_month':
                            start = new Date(now.getFullYear(), now.getMonth(), 1);
                            end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                            label = 'This Month';
                            break;
                        case 'last_month':
                            start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                            end = new Date(now.getFullYear(), now.getMonth(), 0);
                            label = 'Last Month';
                            break;
                    }

                    // Update Label Immediately
                    $('#dateRangeLabel').text(label);

                    // Set URL parameters
                    url.searchParams.set('start_date', formatDate(start));
                    url.searchParams.set('end_date', formatDate(end));

                    // Set the date_range param so the page knows it's a preset
                    url.searchParams.set('date_range', range);
                } else {
                    $('#dateRangeLabel').text('All Time');
                }

                window.location.href = url.toString(); // Reload page
            });

            // ===================== CUSTOM DATE RANGE MODAL LOGIC =====================
            const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];

            let currentMonth = new Date();
            currentMonth.setDate(1);
            let selectedStart = null;
            let selectedEnd = null;

            // Parse existing URL params for custom dates
            if (startDateParam && endDateParam) {
                selectedStart = new Date(startDateParam + 'T00:00:00');
                selectedEnd = new Date(endDateParam + 'T00:00:00');
                currentMonth = new Date(selectedStart);
                currentMonth.setDate(1);
            }

            function formatDate(d) {
                var year = d.getFullYear();
                var month = String(d.getMonth() + 1).padStart(2, '0');
                var day = String(d.getDate()).padStart(2, '0');
                return year + '-' + month + '-' + day;
            }

            function isSameDay(d1, d2) {
                return d1 && d2 &&
                    d1.getFullYear() === d2.getFullYear() &&
                    d1.getMonth() === d2.getMonth() &&
                    d1.getDate() === d2.getDate();
            }

            function isBetween(target, d1, d2) {
                if (!d1 || !d2) return false;
                const start = d1 < d2 ? d1 : d2;
                const end = d1 < d2 ? d2 : d1;
                const t = new Date(target.getFullYear(), target.getMonth(), target.getDate());
                return t > start && t < end;
            }

            function renderCalendar() {
                const m1 = new Date(currentMonth);
                const m2 = new Date(currentMonth);
                m2.setMonth(m2.getMonth() + 1);

                $('#month1Label').text(monthNames[m1.getMonth()] + ' ' + m1.getFullYear());
                $('#month2Label').text(monthNames[m2.getMonth()] + ' ' + m2.getFullYear());

                renderMonthGrid('calendar1', m1);
                renderMonthGrid('calendar2', m2);
            }

            function renderMonthGrid(containerId, date) {
                const year = date.getFullYear();
                const month = date.getMonth();
                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const grid = $('#' + containerId);
                grid.empty();

                // Empty cells before start
                for (let i = 0; i < firstDay; i++) {
                    grid.append('<div class="cal-day empty"></div>');
                }

                for (let d = 1; d <= daysInMonth; d++) {
                    const cellDate = new Date(year, month, d);
                    let classes = 'cal-day';
                    let inRange = false;
                    let isStart = false;
                    let isEnd = false;

                    if (selectedStart && selectedEnd) {
                        isStart = isSameDay(cellDate, selectedStart);
                        isEnd = isSameDay(cellDate, selectedEnd);
                        inRange = isBetween(cellDate, selectedStart, selectedEnd);
                    } else if (selectedStart && !selectedEnd) {
                        isStart = isSameDay(cellDate, selectedStart);
                    }

                    if (isStart && isEnd) {
                        classes += ' selected-both selected-start selected-end';
                    } else if (isStart) {
                        classes += ' selected-start';
                        if (selectedEnd) classes += ' in-range';
                    } else if (isEnd) {
                        classes += ' selected-end in-range';
                    } else if (inRange) {
                        classes += ' in-range';
                    }

                    const btn = $('<div class="' + classes + '">' + d + '</div>');
                    btn.on('click', function() {
                        onDayClick(cellDate);
                    });
                    grid.append(btn);
                }

                // Fill remaining to complete 6 rows (42 cells)
                const totalCells = firstDay + daysInMonth;
                const remaining = 42 - totalCells;
                for (let i = 0; i < remaining; i++) {
                    grid.append('<div class="cal-day empty"></div>');
                }
            }

            function onDayClick(date) {
                if (!selectedStart || (selectedStart && selectedEnd)) {
                    selectedStart = date;
                    selectedEnd = null;
                } else {
                    if (date < selectedStart) {
                        selectedEnd = selectedStart;
                        selectedStart = date;
                    } else {
                        selectedEnd = date;
                    }
                }
                renderCalendar();
            }

            // Calendar navigation
            $('#calPrev').on('click', function() {
                currentMonth.setMonth(currentMonth.getMonth() - 1);
                renderCalendar();
            });

            $('#calNext').on('click', function() {
                currentMonth.setMonth(currentMonth.getMonth() + 1);
                renderCalendar();
            });

            // Apply custom date range
            $('#applyDateRange').on('click', function() {
                if (selectedStart && selectedEnd) {
                    var url = new URL(window.location.href);
                    url.searchParams.delete('date_range');
                    url.searchParams.set('start_date', formatDate(selectedStart));
                    url.searchParams.set('end_date', formatDate(selectedEnd));
                    url.searchParams.delete('page');
                    window.location.href = url.toString();
                } else if (selectedStart && !selectedEnd) {
                    var url = new URL(window.location.href);
                    url.searchParams.delete('date_range');
                    url.searchParams.set('start_date', formatDate(selectedStart));
                    url.searchParams.set('end_date', formatDate(selectedStart));
                    url.searchParams.delete('page');
                    window.location.href = url.toString();
                } else {
                    alert('Please select a date range.');
                }
            });

            // Sync calendar with URL params when modal opens
            $('#dateRangeModal').on('shown.bs.modal', function() {
                const urlParams = new URLSearchParams(window.location.search);
                const startParam = urlParams.get('start_date');
                const endParam = urlParams.get('end_date');

                if (startParam && endParam) {
                    selectedStart = new Date(startParam + 'T00:00:00');
                    selectedEnd = new Date(endParam + 'T00:00:00');
                    currentMonth = new Date(selectedStart);
                    currentMonth.setDate(1);
                } else {
                    selectedStart = null;
                    selectedEnd = null;
                    currentMonth = new Date();
                    currentMonth.setDate(1);
                }
                renderCalendar();
            });

            // Initialize calendar
            renderCalendar();

            // ===================== AUTO-DISMISS ALERTS =====================
            setTimeout(function() {
                document.querySelectorAll('.alert').forEach(alert => {
                    alert.style.transition = 'opacity 1s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.style.display = 'none', 500);
                });
            }, 50);

            // ✅ FIX: Open browser console (F12) to see if this logs
            console.log('✅ DataTable initialized. Search should work now.');
        });
    </script>
@endsection
