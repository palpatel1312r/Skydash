@extends('Components.adminheader')

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


                            {{-- 3. NEW: Category Filter --}}
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm shadow-sm rounded-pill px-3 dropdown-toggle"
                                    type="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-tag-outline me-1"></i>
                                    <span id="categoryLabel">
                                        @if (isset($request) && $request->has('category'))
                                            {{ $request->category }}
                                        @else
                                            All Categories
                                        @endif
                                    </span>
                                </button>
                                <ul class="dropdown-menu shadow-sm" aria-labelledby="categoryDropdown"
                                    style="min-width: 200px; max-height: 300px; overflow-y: auto;">
                                    <li><a class="dropdown-item category-option" href="#" data-cat="">All
                                            Categories</a></li>
                                    @foreach ($categories as $category)
                                        <li><a class="dropdown-item category-option" href="#"
                                                data-cat="{{ $category }}">{{ $category }}</a></li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- 4. NEW: Type Filter --}}
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm shadow-sm rounded-pill px-3 dropdown-toggle"
                                    type="button" id="typeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-shape-outline me-1"></i>
                                    <span id="typeLabel">
                                        @if (isset($request) && $request->has('type'))
                                            {{ $request->type }}
                                        @else
                                            All Types
                                        @endif
                                    </span>
                                </button>
                                <ul class="dropdown-menu shadow-sm" aria-labelledby="typeDropdown"
                                    style="min-width: 200px; max-height: 300px; overflow-y: auto;">
                                    <li><a class="dropdown-item type-option" href="#" data-type="">All Types</a>
                                    </li>
                                    @foreach ($types as $type)
                                        <li><a class="dropdown-item type-option" href="#"
                                                data-type="{{ $type }}">{{ $type }}</a></li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- 5. Clear Filters Button --}}
                            <a href="{{ route('products') }}"
                                class="btn btn-sm shadow-sm rounded-pill px-3 
                   {{ request()->has('category') || request()->has('type') || request()->has('customer_id') || request()->has('date_range') ? 'btn-outline-danger' : 'btn-outline-dark' }}">
                                <i class="mdi mdi-close me-1"></i> <span class="d-none d-sm-inline">Clear</span>
                            </a>
                        </div>

                        {{-- RIGHT: Create Button --}}
                        <a href="{{ route('products.create') }}" class="btn btn-primary shadow px-3 px-sm-4 py-2">
                            <i class="mdi mdi-plus me-1"></i><span class="d-none d-sm-inline">Create New </span>Product
                        </a>
                    </div>
                </div>
            </div>

            {{-- MAIN CARD --}}
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            {{-- HEADER ROW --}}
                            <div
                                class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between p-3 p-sm-4 pb-3 border-bottom">
                                <!-- Left: Title -->
                                <div class="d-flex align-items-center gap-3 mb-3 mb-md-0">
                                    <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0"
                                        style="width:48px;height:48px;">
                                        <i class="mdi mdi-package-variant-closed fs-3"></i>
                                    </div>
                                    <div>
                                        <h4 class="card-title mb-0 fw-bold">Product List</h4>
                                        <small class="text-muted">Manage your products</small>
                                    </div>
                                </div>

                                <!-- Right: Search -->
                                <div class="input-group input-group-sm w-100" style="max-width:250px;">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="mdi mdi-magnify text-muted"></i>
                                    </span>
                                    <input id="dtSearch" class="form-control bg-light border-start-0"
                                        placeholder="Search products...">
                                </div>
                            </div>

                            {{-- TABLE --}}
                            <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">
                                <table class="table table-striped table-borderless" id="productTable">
                                    <thead>
                                        <tr>
                                            <th class="text-nowrap">#</th>
                                            <th class="text-nowrap">Title</th>
                                            <th class="text-nowrap">Image</th>
                                            <th class="text-nowrap">Price</th>
                                            <th class="text-nowrap text-center">Qty</th>
                                            <th class="text-nowrap">Category</th>
                                            <th class="text-nowrap">Type</th>
                                            <th class="text-nowrap">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($products as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><strong>{{ $item->title }}</strong></td>
                                                <td style="cursor: pointer;">
                                                    @if ($item->image)
                                                        @php
                                                            $imgSrc = Str::startsWith($item->image, [
                                                                'http://',
                                                                'https://',
                                                            ])
                                                                ? $item->image
                                                                : asset($item->image);
                                                        @endphp
                                                        <img src="{{ $imgSrc }}" alt="Product Image"
                                                            class="img-thumbnail"
                                                            style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                                            onclick="openProductModal(
    {{ $item->id }}, 
    '{{ addslashes($item->title) }}', 
    `{{ addslashes($item->description) }}`, 
    '{{ $item->price }}', 
   '{{ $item->mrp ?? 0 }}', 
    '{{ $item->category }}', 
    '{{ $item->quantity }}', 
    '{{ $item->type }}',
    '{{ $item->image }}', 
    '{{ route('products.edit', $item->id) }}'
)">
                                                    @else
                                                        <span class="text-muted">No image</span>
                                                    @endif

                                                </td>
                                                <td>₹{{ number_format($item->price, 2) }}</td>
                                                <td>
                                                    @if ($item->quantity > 10)
                                                        <span class="badge badge-success">{{ $item->quantity }} in
                                                            stock</span>
                                                    @elseif($item->quantity > 0 && $item->quantity <= 10)
                                                        <span class="badge badge-warning">{{ $item->quantity }} in
                                                            stock
                                                            (Low)
                                                        </span>
                                                    @else
                                                        <span class="badge badge-danger">Out of Stock</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-success text-nowrap">{{ $item->category }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info text-nowrap">{{ $item->type }}</span>
                                                </td>
                                                <td>
                                                    <div
                                                        class="d-flex align-items-center gap-1 flex-nowrap action-buttons">
                                                        <a href="{{ route('products.edit', $item->id) }}"
                                                            class="btn btn-primary btn-sm">
                                                            <i class="mdi mdi-pencil me-1"></i> <span
                                                                class="d-none d-lg-inline">Update</span>
                                                        </a>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $item->id }})">
                                                            <i class="mdi mdi-delete me-1"></i> <span
                                                                class="d-none d-lg-inline">Delete</span>
                                                        </button>
                                                    </div>
                                                    <form id="delete-form-{{ $item->id }}"
                                                        action="{{ route('admin.products.delete', $item->id) }}"
                                                        method="GET" style="display: none;">
                                                        @csrf
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4">No products found.</td>
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

    {{-- 🛒 DYNAMIC PRODUCT DETAIL MODAL (Amazon/Flipkart Style) --}}

    <div class="modal fade" id="productDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 750px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header border-bottom bg-white px-4 py-3">
                    <h5 class="modal-title fw-bold text-truncate" id="modalProductTitle" style="max-width: 85%;">
                        Product Title</h5>
                    {{-- Use button with data-bs-dismiss -- NO href, NO page reload --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        {{-- Left: Main Image & Thumbnails --}}
                        {{-- Left: Main Image Only --}}
                        <div class="col-md-5">
                            <div class="main-image-wrapper bg-light rounded-3 d-flex align-items-center justify-content-center p-3 mb-3 border"
                                style="height: 280px;">
                                <img id="modalMainImage" src="" alt="Product Image" class="img-fluid"
                                    style="max-height: 100%; max-width: 100%; object-fit: contain;">
                            </div>
                        </div>

                        {{-- Right: Details --}}
                        <div class="col-md-7">
                            <h3 class="fw-bold mb-2 text-dark" id="modalProductName">Product Name</h3>

                            <div class="d-flex align-items-baseline gap-3 mb-3">
                                <h4 class="text-primary fw-bold mb-0" id="modalProductPrice">₹0.00</h4>
                                <span class="text-danger text-decoration-line-through small"
                                    id="modalProductMrp">₹0.00</span>
                            </div>

                            <div class="mb-3">
                                <span
                                    class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3 py-2"
                                    id="modalProductStatus">
                                    <i class="mdi mdi-check-circle me-1"></i> In Stock
                                </span>
                                <span
                                    class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-3 py-2 ms-2"
                                    id="modalProductCategory">
                                    Category
                                </span>
                            </div>

                            <p class="text-muted small mb-3" id="modalProductDesc" style="line-height: 1.6;">
                                Product description will appear here...
                            </p>

                            <div class="d-grid gap-2 mt-3">
                                <a href="#" id="modalEditBtn" class="btn btn-primary py-2">
                                    <i class="mdi mdi-pencil me-2"></i> Edit This Product
                                </a>
                                {{-- Use button with data-bs-dismiss -- NO href, NO page reload --}}
                                <button type="button" class="btn btn-light py-2" data-bs-dismiss="modal">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .dropdown-menu {
            z-index: 1090 !important;
            max-height: 300px;
            overflow-y: auto;
            margin-top: 8px !important;
        }

        #productTable {
            width: 100% !important;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 900px;
        }

        #productTable thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e9ecef;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        #productTable tbody tr {
            transition: background-color 0.2s ease;
        }

        #productTable tbody tr:hover {
            background-color: #f8f9fa !important;
        }

        #productTable tbody td {
            vertical-align: middle;
            font-size: 0.875rem;
        }

        .badge-info {
            background-color: #e3f2fd;
            color: #0d6efd;
            font-weight: 500;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
            font-weight: 500;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
            font-weight: 500;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: 500;
        }

        #productTable td:last-child {
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

        #lengthContainer .dataTables_length select:focus {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15) !important;
            outline: none;
        }

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

        #typeDropdown:hover,
        #categoryDropdown:hover {
            background: #3d3f41;
            border-color: #f8f3f3;
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

        /* Calendar CSS */
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

        /* Responsive */
        @media (max-width: 1199.98px) {
            #productTable {
                min-width: 850px;
            }
        }

        @media (max-width: 991.98px) {
            #productTable {
                min-width: 800px;
            }
        }

        @media (max-width: 767.98px) {
            .content-wrapper {
                padding: 1rem !important;
            }

            #productTable {
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

        @media (max-width: 575.98px) {
            .content-wrapper {
                padding: 0.75rem !important;
            }

            #productTable {
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
            var table = $('#productTable').DataTable({
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
                        targets: [2, 7]
                    },
                    {
                        searchable: false,
                        targets: [2, 7]
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
                    zeroRecords: "No matching products found",
                    emptyTable: "No products available",
                    paginate: {
                        first: '<i class="mdi mdi-chevron-double-left"></i>',
                        previous: '<i class="mdi mdi-chevron-left"></i>',
                        next: '<i class="mdi mdi-chevron-right"></i>',
                        last: '<i class="mdi mdi-chevron-double-right"></i>'
                    }
                }
            });

            // ✅ MOVE CONTROLS TO CUSTOM BOOTSTRAP FOOTER
            $('#lengthContainer').append($('.dataTables_length'));
            $('#paginationContainer').append($('.dataTables_paginate'));
            $('#infoContainer').append($('.dataTables_info'));

            // Keep Search working
            $('#dtSearch').on('keyup', function() {
                table.search($(this).val()).draw();
            });

            // ===================== GET URL PARAMETERS =====================
            var urlParams = new URLSearchParams(window.location.search);

            // ===================== CATEGORY FILTER =====================
            var categoryParam = urlParams.get('category');
            if (categoryParam) {
                $('#categoryLabel').text(categoryParam);
            }

            // ===================== CATEGORY FILTER (AJAX) =====================
            $('.category-option').on('click', function(e) {
                e.preventDefault();
                var cat = $(this).data('cat');
                var url = new URL(window.location.href);

                // 1. Get current category from URL
                var currentCat = url.searchParams.get('category');

                // 2. If clicking the same category that's already active, STOP!
                if (currentCat === cat) {
                    return;
                }

                url.searchParams.delete('page');
                if (cat === '') {
                    url.searchParams.delete('category');
                    $('#categoryLabel').text('All Categories');
                } else {
                    url.searchParams.set('category', cat);
                    $('#categoryLabel').text(cat);
                }

                // Finally reload
                window.location.href = url.toString();
            });
            // ===================== TYPE FILTER =====================
            var typeParam = urlParams.get('type');
            if (typeParam) {
                $('#typeLabel').text(typeParam);
            }

            $('.type-option').on('click', function(e) {
                e.preventDefault();
                var type = $(this).data('type');
                var url = new URL(window.location.href);
                url.searchParams.delete('page');

                if (type === '') {
                    url.searchParams.delete('type');
                    $('#typeLabel').text('All Types');
                } else {
                    url.searchParams.set('type', type);
                    $('#typeLabel').text(type);
                }
                window.location.href = url.toString();
            });

            // ===================== DELETE CONFIRMATION =====================
            window.confirmDelete = function(id) {
                if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
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

        // ===================== PRODUCT MODAL =====================
        window.openProductModal = function(id, title, description, price, mrp, category, qty, type, image, editUrl) {
            // Set all the data using vanilla JS
            document.getElementById('modalProductTitle').textContent = title;
            document.getElementById('modalProductName').textContent = title;
            document.getElementById('modalProductDesc').textContent = description || 'No description available.';

            // ✅ Calculate and display the MRP (40% higher)
            var mrp = parseFloat(price) * 1.4;
            document.getElementById('modalProductMrp').textContent = '₹' + mrp.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            // ✅ Format the main price
            document.getElementById('modalProductPrice').textContent = '₹' + parseFloat(price).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            document.getElementById('modalProductCategory').textContent = category;
            document.getElementById('modalProductStatus').textContent = qty > 0 ? 'In Stock' : 'Out of Stock';

            // Handle image
            var imgSrc = image && (image.startsWith('http://') || image.startsWith('https://')) ? image : (image ?
                '{{ asset('') }}' + image : '');
            var mainImage = document.getElementById('modalMainImage');
            mainImage.src = imgSrc;
            mainImage.onerror = function() {
                this.src = 'https://via.placeholder.com/400x300?text=No+Image';
            };

            // Set edit button
            document.getElementById('modalEditBtn').href = editUrl;

            // Use jQuery to open modal
            $('#productDetailModal').modal('show');
        };
    </script>
@endsection
