@extends('Components.adminheader')

@section('content')
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-12 grid-margin">
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

                            {{-- Customer Filter --}}
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm shadow-sm rounded-pill px-3 dropdown-toggle"
                                    type="button" id="customerDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-account-outline me-1"></i>
                                    <span id="customerLabel">All Customers</span>
                                </button>
                                <ul class="dropdown-menu shadow-sm" aria-labelledby="customerDropdown"
                                    style="min-width: 200px; max-height: 300px; overflow-y: auto;">
                                    <li><a class="dropdown-item customer-option" href="#" data-id="">All
                                            Customers</a></li>
                                    @foreach ($customers as $customer)
                                        <li><a class="dropdown-item customer-option" href="#"
                                                data-id="{{ $customer->id }}">{{ $customer->fullname }}</a></li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- Date Range Dropdown --}}
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm shadow-sm rounded-pill px-3 dropdown-toggle"
                                    type="button" id="dateRangeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-calendar-outline me-1"></i>
                                    {{-- <span id="dateRangeLabel">All Time</span> --}}

                                    <span id="dateRangeLabel">
                                        @php
                                            $label = 'All Time';
                                            // Use the passed $request variable from the controller
                                            if (
                                                isset($request) &&
                                                $request->has('date_range') &&
                                                $request->date_range !== 'custom'
                                            ) {
                                                $label = ucwords(str_replace('_', ' ', $request->date_range));
                                            } elseif (
                                                isset($request) &&
                                                $request->has('start_date') &&
                                                $request->has('end_date')
                                            ) {
                                                $label = $request->start_date . ' to ' . $request->end_date;
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
                            <a href="{{ route('invoices.index') }}"
                                class="btn btn-sm shadow-sm rounded-pill px-3 
                               {{ request()->has('customer_id') || request()->has('start_date') || request()->has('end_date') || request()->has('date_range') ? 'btn-outline-danger' : 'btn-outline-secondary' }}">
                                <i class="mdi mdi-close me-1"></i> <span class="d-none d-sm-inline">Clear</span>
                            </a>
                        </div>

                        {{-- RIGHT: Create Button --}}
                        <a href="{{ route('invoices.create') }}" class="btn btn-primary shadow px-3 px-sm-4 py-2">
                            <i class="mdi mdi-plus me-1"></i><span class="d-none d-sm-inline">Create New </span>Invoice
                        </a>
                    </div>
                </div>
            </div>

            {{-- MAIN CARD --}}
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            {{-- HEADER ROW --}}
                            <div
                                class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between p-3 p-sm-4 pb-3 border-bottom">
                                <!-- Left: Title -->
                                <div class="d-flex align-items-center gap-3 mb-3 mb-md-0">
                                    <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0"
                                        style="width:48px;height:48px;">
                                        <i class="mdi mdi-file-document-outline fs-3"></i>
                                    </div>
                                    <div>
                                        <h4 class="card-title mb-0 fw-bold">Invoice List</h4>
                                        <small class="text-muted">Manage your invoices</small>
                                    </div>
                                </div>

                                <!-- Right: Search -->
                                <div class="input-group input-group-sm w-100" style="max-width:250px;">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="mdi mdi-magnify text-muted"></i>
                                    </span>
                                    <input id="dtSearch" class="form-control bg-light border-start-0"
                                        placeholder="Search invoices">
                                </div>
                            </div>

                            {{-- TABLE --}}
                            <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">
                                <table class="table table-striped table-borderless" id="invoiceTable">
                                    <thead>
                                        <tr>
                                            <th class="text-nowrap">#</th>
                                            <th class="text-nowrap">Invoice No</th>
                                            <th class="text-nowrap">Customer</th>
                                            <th class="text-nowrap">Products</th>
                                            <th class="text-nowrap text-center">Qty</th>
                                            <th class="text-nowrap">Price</th>
                                            <th class="text-nowrap">Subtotal</th>
                                            <th class="text-nowrap">Tax</th>
                                            <th class="text-nowrap">Grand Total</th>
                                            <th class="text-nowrap">Date</th>
                                            <th class="text-nowrap">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($invoices as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><strong>{{ $item->invoice_number }}</strong></td>
                                                <td>
                                                    <div class="text-nowrap">{{ $item->customer_name }}</div>
                                                    <small
                                                        class="text-muted text-nowrap">{{ $item->customer_email }}</small>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach ($item->products as $product)
                                                            <span class="badge badge-info text-nowrap">
                                                                {{ $product['product_name'] }}
                                                                @if (isset($product['quantity']) && $product['quantity'] > 1)
                                                                    (x{{ $product['quantity'] }})
                                                                @endif
                                                            </span>
                                                        @endforeach
                                                    </div>
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
                                                    <div
                                                        class="d-flex align-items-center gap-1 flex-nowrap action-buttons">
                                                        <button type="button" class="btn btn-info btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#viewInvoiceModal{{ $item->id }}">
                                                            <i class="mdi mdi-eye me-1"></i> <span
                                                                class="d-none d-lg-inline">View</span>
                                                        </button>
                                                        <a href="{{ route('invoices.edit', $item->id) }}"
                                                            class="btn btn-primary btn-sm">
                                                            <i class="mdi mdi-pencil me-1"></i> <span
                                                                class="d-none d-lg-inline">Update</span>
                                                        </a>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete('{{ $item->id }}')">
                                                            <i class="mdi mdi-delete me-1"></i> <span
                                                                class="d-none d-lg-inline">Delete</span>
                                                        </button>
                                                    </div>
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
                                                <td colspan="11" class="text-center py-4">No invoices found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- FOOTER --}}
                        <div class="card-footer bg-white border-top py-3 px-3 px-sm-4">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 gap-sm-3"
                                id="dtCustomFooter">

                                {{-- LEFT: Show entries --}}
                                <div id="lengthContainer" class="mb-2 mb-sm-0 order-1 order-sm-1"></div>

                                {{-- CENTER: Pagination --}}
                                <div id="paginationContainer" class="order-3 order-sm-2"></div>

                                {{-- RIGHT: Info text --}}
                                <div id="infoContainer" class="text-center text-sm-end order-2 order-sm-3"></div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- content-wrapper ends -->

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
        <div class="modal fade" id="viewInvoiceModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header" style="background: #f8f9fa;">
                        <h5 class="modal-title">
                            <strong>Invoice #{{ $item->invoice_number }}</strong>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <h6>Customer Information</h6>
                                <p class="mb-0">
                                    <strong>Name:</strong> {{ $item->customer->fullname ?? 'N/A' }}<br>
                                    <strong>Email:</strong> {{ $item->customer->email ?? 'N/A' }}<br>
                                    <strong>Phone:</strong> {{ $item->customer->phone ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="col-sm-6 text-start text-sm-end">
                                <h6>Invoice Information</h6>
                                <p class="mb-0">
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
                                        <th class="text-center">Quantity</th>
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
                                            <td class="text-center">x{{ $product['quantity'] ?? 1 }}</td>
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
                                        <td colspan="3" class="text-end"><strong>Tax
                                                ({{ $item->tax_rate }}%)
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

    <style>
        .dropdown-menu {
            z-index: 1090 !important;
            max-height: 300px;
            overflow-y: auto;
            margin-top: 8px !important;
        }

        /* ===== TABLE STYLING ===== */
        #invoiceTable {
            width: 100% !important;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 900px;
        }

        #invoiceTable thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e9ecef;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        #invoiceTable tbody tr {
            transition: background-color 0.2s ease;
        }

        #invoiceTable tbody tr:hover {
            background-color: #f8f9fa !important;
        }

        #invoiceTable tbody tr:last-child {
            border-bottom: none !important;
        }

        #invoiceTable tbody td {
            vertical-align: middle;
            font-size: 0.875rem;
        }

        /* ===== BADGE STYLING ===== */
        .badge-info {
            background-color: #e3f2fd;
            color: #0d6efd;
            font-weight: 500;
        }

        /* ===== ACTION BUTTONS ===== */
        #invoiceTable td:last-child {
            min-width: 140px;
        }

        .action-buttons .btn-sm {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.75rem !important;
            white-space: nowrap !important;
        }

        .action-buttons .btn-sm i {
            font-size: 16px !important;
        }

        /* ✅ HIDE DEFAULT DATATABLES SEARCH BAR */
        .dataTables_filter {
            display: none !important;
        }

        /* ===== FILTER BUTTONS ===== */
        #customerDropdown,
        #dateRangeDropdown {
            border-color: #dee2e6;
            font-weight: 500;
        }

        #customerDropdown:hover,
        #dateRangeDropdown:hover {
            background: #3d3f41;
            border-color: #f8f3f3;
        }

        /* ===== DATA TABLES FOOTER ===== */
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

        #lengthContainer .dataTables_length select:focus {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15) !important;
            outline: none;
        }

        /* ===== PAGINATION: Soft Pill ===== */
        #paginationContainer .dataTables_paginate {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 4px !important;
            float: none !important;
            margin: 0 !important;
            padding: 0 !important;
            flex-wrap: wrap !important;
        }

        #paginationContainer .dataTables_paginate .paginate_button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 38px;
            height: 38px;
            line-height: 1;
            text-align: center;
            border-radius: 10px !important;
            border: 1px solid #e9ecef !important;
            background: #ffffff !important;
            color: #495057 !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
            padding: 0 10px !important;
            box-sizing: border-box !important;
        }

        #paginationContainer .dataTables_paginate .paginate_button:hover:not(.disabled):not(.current) {
            background: #f8f9fa !important;
            border-color: #dee2e6 !important;
            color: #070708 !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        }

        #paginationContainer .dataTables_paginate .paginate_button.current {
            background: #121314 !important;
            border-color: #b7b8ba !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.28) !important;
            transform: translateY(-1px);
        }

        #paginationContainer .dataTables_paginate .paginate_button.disabled {
            color: #ced4da !important;
            background: #f8f9fa !important;
            border-color: #e9ecef !important;
            cursor: not-allowed !important;
            opacity: 0.7 !important;
        }

        #paginationContainer .dataTables_paginate .paginate_button:active:not(.disabled) {
            transform: translateY(0);
            box-shadow: none !important;
        }

        #infoContainer .dataTables_info {
            float: none !important;
            text-align: right !important;
            margin: 0 !important;
            padding: 0 !important;
            color: #6c757d;
            font-size: 0.875rem;
            font-weight: 500;
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

        /* ===== RESPONSIVE BREAKPOINTS ===== */

        /* Large tablets and small desktops */
        @media (max-width: 1199.98px) {
            #invoiceTable {
                min-width: 850px;
            }
        }

        /* Tablets */
        @media (max-width: 991.98px) {
            #invoiceTable {
                min-width: 800px;
            }
        }

        /* Mobile landscape and below */
        @media (max-width: 767.98px) {
            .content-wrapper {
                padding: 1rem !important;
            }

            #invoiceTable {
                min-width: 750px;
            }

            #dtCustomFooter {
                gap: 12px !important;
            }

            .action-buttons .btn-sm span {
                display: none !important;
            }

            .action-buttons .btn-sm {
                padding: 0.25rem 0.4rem !important;
            }

            .action-buttons .btn-sm i {
                margin-right: 0 !important;
            }
        }

        /* Small mobile */
        @media (max-width: 575.98px) {
            .content-wrapper {
                padding: 0.75rem !important;
            }

            #invoiceTable {
                min-width: 700px;
            }

            .card-body {
                padding: 0.75rem !important;
            }

            .action-buttons {
                gap: 2px !important;
            }

            .action-buttons .btn-sm {
                padding: 0.2rem 0.35rem !important;
                font-size: 0.7rem !important;
            }

            .action-buttons .btn-sm i {
                font-size: 14px !important;
            }

            #dtCustomFooter {
                text-align: center;
                flex-direction: column !important;
                gap: 10px !important;
            }

            #lengthContainer,
            #paginationContainer,
            #infoContainer {
                width: 100%;
                display: flex !important;
                justify-content: center !important;
            }

            #infoContainer .dataTables_info {
                text-align: center !important;
            }

            #paginationContainer .dataTables_paginate .paginate_button {
                min-width: 34px;
                height: 34px;
                font-size: 0.8125rem !important;
            }

            .modal-dialog {
                margin: 0.5rem;
            }

            .modal-body {
                padding: 1rem !important;
            }
        }
    </style>

    <!-- DataTables CSS & JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // ===================== DATA TABLE INITIALIZATION =====================
            var table = $('#invoiceTable').DataTable({
                responsive: true,
                processing: true,
                serverSide: false,
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
                    },
                    {
                        searchable: false,
                        targets: [3, 10]
                    },
                    {
                        className: 'text-center',
                        targets: [4]
                    }
                ],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden"></span></div>',
                    search: "",
                    lengthMenu: "Show _MENU_",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    zeroRecords: "No matching invoices found",
                    emptyTable: "No invoices available",
                    paginate: {
                        first: '<i class="mdi mdi-chevron-double-left"></i>',
                        previous: '<i class="mdi mdi-chevron-left"></i>',
                        next: '<i class="mdi mdi-chevron-right"></i>',
                        last: '<i class="mdi mdi-chevron-double-right"></i>'
                    }
                },
                drawCallback: function() {}
            });

            // ✅ MOVE CONTROLS TO CUSTOM BOOTSTRAP FOOTER
            $('#lengthContainer').append($('.dataTables_length'));
            $('#paginationContainer').append($('.dataTables_paginate'));
            $('#infoContainer').append($('.dataTables_info'));

            // Keep Search working
            $('#dtSearch').on('keyup', function() {
                table.search($(this).val()).draw();
            });

            // ===================== CUSTOMER FILTER =====================
            var urlParams = new URLSearchParams(window.location.search);

            // Set customer label on page load
            var customerId = urlParams.get('customer_id');
            if (customerId) {
                var customerName = $('.customer-option[data-id="' + customerId + '"]').text();
                if (customerName) {
                    $('#customerLabel').text(customerName);
                }
            }

            // Handle customer selection
            $('.customer-option').on('click', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var name = $(this).text();
                var url = new URL(window.location.href);

                url.searchParams.delete('page');

                if (id === '') {
                    url.searchParams.delete('customer_id');
                    $('#customerLabel').text('All Customers'); // Update DOM
                } else {
                    url.searchParams.set('customer_id', id);
                    $('#customerLabel').text(name); // Update DOM
                }

                window.location.href = url.toString(); // Reload
            });
            // ===================== DATE RANGE FILTER =====================
            var dateRangeParam = urlParams.get('date_range');
            var startDateParam = urlParams.get('start_date');
            var endDateParam = urlParams.get('end_date');

            // Set date range label on page load
            function updateDateRangeLabel() {
                if (dateRangeParam && dateRangeParam !== 'custom') {
                    // It's a preset (today, this_week, etc.)
                    var label = dateRangeParam.replace(/_/g, ' ');
                    label = label.charAt(0).toUpperCase() + label.slice(1);
                    $('#dateRangeLabel').text(label);
                } else if (startDateParam && endDateParam) {
                    // It's a custom range
                    $('#dateRangeLabel').text(startDateParam + ' to ' + endDateParam);
                } else {
                    // Fallback (No filters applied)
                    $('#dateRangeLabel').text('All Time');
                }
            }
            updateDateRangeLabel(); // Call on page load
            updateDateRangeLabel();

            // Handle Preset Clicks (Today, Yesterday, etc.)
            $('.date-preset-option').on('click', function(e) {
                e.preventDefault();
                var range = $(this).data('range');
                var url = new URL(window.location.href);

                // Clear out any old custom dates
                url.searchParams.delete('start_date');
                url.searchParams.delete('end_date');
                url.searchParams.delete('page');

                if (range === 'all') {
                    url.searchParams.delete('date_range');
                    $('#dateRangeLabel').text('All Time'); // Update DOM immediately
                } else {
                    url.searchParams.set('date_range', range);

                    // Format label correctly
                    var label = range.replace(/_/g, ' ');
                    label = label.charAt(0).toUpperCase() + label.slice(1);

                    // 🛑 CRITICAL FIX: Force the DOM to update before reload!
                    $('#dateRangeLabel').text(label);
                }

                // Reload the page to apply the filter
                window.location.href = url.toString();
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
                    url.searchParams.set('date_range', 'custom');
                    url.searchParams.set('start_date', formatDate(selectedStart));
                    url.searchParams.set('end_date', formatDate(selectedEnd));
                    url.searchParams.delete('page');
                    window.location.href = url.toString();
                } else if (selectedStart && !selectedEnd) {
                    var url = new URL(window.location.href);
                    url.searchParams.set('date_range', 'custom');
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

            // ===================== DELETE CONFIRMATION =====================
            window.confirmDelete = function(id) {
                if (confirm('Are you sure you want to delete this invoice?')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            };

            // ===================== AUTO-DISMISS ALERTS =====================
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                });
            }, 50);
        });
    </script>
@endsection
