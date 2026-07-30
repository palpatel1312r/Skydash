@extends('Components.customerheader')

@section('content')
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">

                </div>
            </div>

                      {{-- ✅ FILTER ROW --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="text-muted fw-bold small me-1">Filter By:</span>

                            <button type="button" id="dateRangeBtn"
                                class="btn btn-outline-secondary btn-sm shadow-sm rounded-pill px-3" data-bs-toggle="modal"
                                data-bs-target="#dateRangeModal">
                                <i class="mdi mdi-calendar-outline me-1"></i>
                                <span id="dateRangeLabel">All Time</span>
                            </button>

                            {{-- ✅ FIX: Change route to customer.invoices --}}
                            <a href="{{ route('customer.invoices') }}"
                                class="btn btn-sm shadow-sm rounded-pill px-3 
                               {{ request()->has('start_date') || request()->has('end_date') ? 'btn-outline-danger' : 'btn-outline-secondary' }}">
                                <i class="mdi mdi-close me-1"></i> Clear
                            </a>
                        </div>

                        {{-- ✅ FIX: Change route to customer.invoices.create --}}
                        <a href="{{ route('customer.invoices.create') }}" class="btn btn-primary shadow px-8 py-2 md-12">
                            <i class="mdi mdi-plus me-1"></i>Create New Invoice
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">

                            {{-- ✅ HEADER: Title + Show Entries + Search --}}
                            <div
                                class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-3 border-bottom">

                                {{-- LEFT: Title, Stats & Show Entries --}}
                                <div class="d-flex flex-wrap align-items-center gap-3">
                                    {{-- Title --}}
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle text-primary">
                                            <i class="mdi mdi-file-document-outline" style="font-size: 24px;"></i>
                                        </div>
                                        <div>
                                            <h4 class="card-title mb-0 fw-bold text-dark">My Invoices</h4>
                                            <small class="text-muted">Manage your invoices</small>
                                        </div>
                                    </div>

                                    {{-- Show Entries Dropdown --}}
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted small">Show</span>
                                        <select id="dtLength" class="form-select form-select-sm shadow-sm"
                                            style="width: 70px;">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="-1">All</option>
                                        </select>
                                        <span class="text-muted small">Rows</span>
                                    </div>
                                </div>

                                {{-- RIGHT: Search Bar --}}
                                <div class="d-flex align-items-center">
                                    <div class="input-group input-group-sm shadow-sm rounded" style="width: 250px;">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="mdi mdi-magnify text-muted"></i>
                                        </span>
                                        <input type="text" id="dtSearch" class="form-control border-start-0 bg-white"
                                            placeholder="Search invoices...">
                                    </div>
                                </div>
                            </div>

                            {{-- ✅ TABLE --}}
                            <div class="table-responsive">
                                <table class="table table-striped table-borderless" id="invoiceTable">
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
    </div>
    <div class="modal fade" id="dateRangeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 720px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-body p-0">
                    <div class="d-flex" style="min-height: 420px;">

                        {{-- LEFT SIDEBAR: Presets --}}
                        <div class="p-3 border-end" style="width: 190px; background: #f8f9fa;">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3"
                                style="font-size: 0.7rem; letter-spacing: 0.05em;">Quick Select</h6>
                            <div class="preset-list d-flex flex-column gap-1">
                                <button type="button" class="preset-btn w-100 text-start btn btn-sm"
                                    data-range="today">Today</button>
                                <button type="button" class="preset-btn w-100 text-start btn btn-sm"
                                    data-range="yesterday">Yesterday</button>
                                <button type="button" class="preset-btn w-100 text-start btn btn-sm"
                                    data-range="last7">Past 7 days</button>
                                <button type="button" class="preset-btn w-100 text-start btn btn-sm"
                                    data-range="last30">Past 30 days</button>
                                <button type="button" class="preset-btn w-100 text-start btn btn-sm"
                                    data-range="thisMonth">This month</button>
                                <button type="button" class="preset-btn w-100 text-start btn btn-sm"
                                    data-range="lastMonth">Previous month</button>
                                <button type="button" class="preset-btn w-100 text-start btn btn-sm"
                                    data-range="thisYear">This year</button>
                                <div class="border-top my-2"></div>
                                <button type="button" class="preset-btn w-100 text-start btn btn-sm active"
                                    data-range="custom">Custom range</button>
                            </div>
                        </div>

                        {{-- RIGHT: Dual Calendar --}}
                        <div class="flex-grow-1 p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <button type="button" class="btn btn-sm btn-light rounded-circle cal-nav" id="calPrev"
                                    style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-chevron-left"></i>
                                </button>
                                <div class="d-flex gap-5">
                                    <strong id="month1Label" class="fs-6 text-dark">January 2026</strong>
                                    <strong id="month2Label" class="fs-6 text-dark">February 2026</strong>
                                </div>
                                <button type="button" class="btn btn-sm btn-light rounded-circle cal-nav" id="calNext"
                                    style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-chevron-right"></i>
                                </button>
                            </div>

                            <div class="d-flex gap-4 flex-grow-1">
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
        </div>
    </div>

    {{-- View Invoice Modals --}}
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
        /* Table Styling */
        #invoiceTable {
            width: 100% !important;
        }

        #invoiceTable tbody tr:hover {
            background-color: #f8f9fa !important;
            transition: background-color 0.3s ease;
        }

        #invoiceTable tbody tr td {
            vertical-align: middle;
        }

        .badge-info {
            background-color: #e3f2fd;
            color: #0d6efd;
            padding: 4px 8px;
            margin: 2px;
            font-weight: 500;
            border-radius: 4px;
        }

        /* ✅ Date Range Picker Styles */
        .preset-btn {
            color: #495057;
            background: transparent;
            border: none;
            padding: 7px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .preset-btn:hover {
            background: #e9ecef;
            color: #212529;
        }

        .preset-btn.active {
            background: #0d6efd;
            color: #fff;
            box-shadow: 0 2px 6px rgba(13, 110, 253, 0.25);
        }

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

        #dateRangeBtn {
            border-color: #dee2e6;
            font-weight: 500;
        }

        #dateRangeBtn:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
        }

        /* ✅ Centered Pagination Fix */
        .dataTables_wrapper .dataTables_paginate {
            float: none !important;
            text-align: center !important;
            padding-top: 1em !important;
        }

        .dataTables_wrapper .dataTables_info {
            float: none !important;
            text-align: center !important;
            padding-top: 0.5em !important;
            padding-bottom: 0.5em !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            margin: 0 2px !important;
            display: inline-block !important;
        }
    </style>

    <!-- Include DataTables CSS and JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {

            // ===================== DATATABLES =====================
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
                        targets: [2, 9]
                    },
                    {
                        searchable: false,
                        targets: [2, 9]
                    },
                    {
                        className: 'text-center',
                        targets: [3]
                    }
                ],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    search: "",
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
                dom: '<"row"<"col-12"t>>' +
                    '<"row"<"col-12 text-center"i>>' +
                    '<"row"<"col-12 text-center"p>>',
                drawCallback: function() {
                    $('.dataTables_paginate .paginate_button').addClass('btn btn-sm');
                }
            });

            // Sync custom dropdowns with DataTables
            $('#dtLength').on('change', function() {
                table.page.len($(this).val()).draw();
            });
            $('#dtSearch').on('keyup', function() {
                table.search($(this).val()).draw();
            });

            // ===================== CUSTOM DATE RANGE PICKER =====================
            const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August",
                "September", "October", "November", "December"
            ];
            let currentMonth = new Date();
            currentMonth.setDate(1);
            let selectedStart = null;
            let selectedEnd = null;
            let activePreset = 'custom';

            // Parse existing URL params
            var urlParams = new URLSearchParams(window.location.search);
            const existingStart = urlParams.get('start_date');
            const existingEnd = urlParams.get('end_date');

            if (existingStart && existingEnd) {
                selectedStart = new Date(existingStart + 'T00:00:00');
                selectedEnd = new Date(existingEnd + 'T00:00:00');
                currentMonth = new Date(selectedStart);
                currentMonth.setDate(1);
                updateButtonLabelFromDates();
            }

            function formatDate(d) {
                var year = d.getFullYear();
                var month = String(d.getMonth() + 1).padStart(2, '0');
                var day = String(d.getDate()).padStart(2, '0');
                return year + '-' + month + '-' + day;
            }

            function formatDisplay(d) {
                return monthNames[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
            }

            function isSameDay(d1, d2) {
                return d1 && d2 && d1.getFullYear() === d2.getFullYear() && d1.getMonth() === d2.getMonth() && d1
                    .getDate() === d2.getDate();
            }

            function isBetween(target, d1, d2) {
                if (!d1 || !d2) return false;
                const start = d1 < d2 ? d1 : d2;
                const end = d1 < d2 ? d2 : d1;
                const t = new Date(target.getFullYear(), target.getMonth(), target.getDate());
                return t > start && t < end;
            }

            function getPresetRange(range) {
                const now = new Date();
                const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                let start, end;
                switch (range) {
                    case 'today':
                        start = new Date(today);
                        end = new Date(today);
                        break;
                    case 'yesterday':
                        start = new Date(today);
                        start.setDate(start.getDate() - 1);
                        end = new Date(start);
                        break;
                    case 'last7':
                        end = new Date(today);
                        start = new Date(today);
                        start.setDate(start.getDate() - 6);
                        break;
                    case 'last30':
                        end = new Date(today);
                        start = new Date(today);
                        start.setDate(start.getDate() - 29);
                        break;
                    case 'thisMonth':
                        start = new Date(today.getFullYear(), today.getMonth(), 1);
                        end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                        break;
                    case 'lastMonth':
                        start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                        end = new Date(today.getFullYear(), today.getMonth(), 0);
                        break;
                    case 'thisYear':
                        start = new Date(today.getFullYear(), 0, 1);
                        end = new Date(today.getFullYear(), 11, 31);
                        break;
                    default:
                        return null;
                }
                return {
                    start,
                    end
                };
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

                const totalCells = firstDay + daysInMonth;
                const remaining = 42 - totalCells;
                for (let i = 0; i < remaining; i++) {
                    grid.append('<div class="cal-day empty"></div>');
                }
            }

            function onDayClick(date) {
                activePreset = 'custom';
                $('.preset-btn').removeClass('active');
                $('.preset-btn[data-range="custom"]').addClass('active');

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

            function applyPreset(range) {
                const r = getPresetRange(range);
                if (!r) return;
                selectedStart = r.start;
                selectedEnd = r.end;
                activePreset = range;
                renderCalendar();
            }

            function updateButtonLabelFromDates() {
                if (!selectedStart || !selectedEnd) {
                    $('#dateRangeLabel').text('All Time');
                    return;
                }
                const s = formatDate(selectedStart);
                const e = formatDate(selectedEnd);
                const now = new Date();
                const today = formatDate(new Date(now.getFullYear(), now.getMonth(), now.getDate()));
                const yesterday = formatDate(new Date(now.getFullYear(), now.getMonth(), now.getDate() - 1));

                if (s === today && e === today) $('#dateRangeLabel').text('Today');
                else if (s === yesterday && e === yesterday) $('#dateRangeLabel').text('Yesterday');
                else if (s === e) $('#dateRangeLabel').text(formatDisplay(selectedStart));
                else $('#dateRangeLabel').text(formatDisplay(selectedStart) + ' - ' + formatDisplay(selectedEnd));
            }

            $('#calPrev').on('click', function() {
                currentMonth.setMonth(currentMonth.getMonth() - 1);
                renderCalendar();
            });
            $('#calNext').on('click', function() {
                currentMonth.setMonth(currentMonth.getMonth() + 1);
                renderCalendar();
            });

            $('.preset-btn').on('click', function() {
                const range = $(this).data('range');
                $('.preset-btn').removeClass('active');
                $(this).addClass('active');
                if (range === 'custom') {
                    activePreset = 'custom';
                } else {
                    applyPreset(range);
                }
            });

            $('#applyDateRange').on('click', function() {
                if (!selectedStart || !selectedEnd) {
                    if (activePreset !== 'custom') {
                        const r = getPresetRange(activePreset);
                        if (r) {
                            selectedStart = r.start;
                            selectedEnd = r.end;
                        }
                    }
                }

                if (selectedStart && selectedEnd) {
                    var url = new URL(window.location.href);
                    url.searchParams.set('date_range', 'custom');
                    url.searchParams.set('start_date', formatDate(selectedStart));
                    url.searchParams.set('end_date', formatDate(selectedEnd));
                    window.location.href = url.toString();
                } else {
                    $('#dateRangeModal').modal('hide');
                }
            });

            $(document).on('click', 'a[href*="customer.invoices"]', function() {
                selectedStart = null;
                selectedEnd = null;
                activePreset = 'custom';
            });

            $('#dateRangeModal').on('shown.bs.modal', function() {
                const urlParams = new URLSearchParams(window.location.search);
                const startParam = urlParams.get('start_date');
                const endParam = urlParams.get('end_date');

                if (startParam && endParam) {
                    selectedStart = new Date(startParam + 'T00:00:00');
                    selectedEnd = new Date(endParam + 'T00:00:00');
                    currentMonth = new Date(selectedStart);
                    currentMonth.setDate(1);
                    renderCalendar();
                }
            });

            renderCalendar();
            updateButtonLabelFromDates();

            // ===================== DELETE & ALERTS =====================
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
        });
    </script>
@endsection
