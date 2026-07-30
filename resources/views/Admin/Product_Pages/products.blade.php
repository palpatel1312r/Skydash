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

            {{-- FILTERS & CREATE BUTTON ROW --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                        {{-- LEFT: Filters (Kept for structure, remove if not needed) --}}
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="text-muted fw-bold small me-1">Filter By:</span>
                            {{-- Add your custom filters here if you expand later --}}
                        </div>

                        {{-- RIGHT: Create Button --}}
                        <a href="{{ route('products.create') }}" class="btn btn-primary shadow px-4 py-2">
                            <i class="mdi mdi-plus me-1"></i> Add New Product
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">

                            {{-- HEADER: Title + Show Entries + Search --}}
                            <div
                                class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-3 border-bottom">

                                {{-- LEFT: Title, Stats & Show Entries --}}
                                <div class="d-flex flex-wrap align-items-center gap-3">
                                    {{-- Title --}}
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle text-primary">
                                            <i class="mdi mdi-package-variant-closed" style="font-size: 24px;"></i>
                                        </div>
                                        <div>
                                            <h4 class="card-title mb-0 fw-bold text-dark">Product List</h4>
                                            <small class="text-muted">Manage your products</small>
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
                                            placeholder="Search products...">
                                    </div>
                                </div>
                            </div>

                            {{-- TABLE --}}
                            @if ($products->isEmpty())
                                <div class="text-center py-5">
                                    <p class="text-muted">No products found. Click "Add New Product" to create one.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped table-borderless" id="productTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Title</th>
                                                <th>Image</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                                <th>Category</th>
                                                <th>Type</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($products as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->title }}</td>
                                                    <td>
                                                        @if ($item->image)
                                                            @if (Str::startsWith($item->image, ['http://', 'https://']))
                                                                <img src="{{ $item->image }}" alt="Product Image"
                                                                    class="img-thumbnail"
                                                                    style="width: 80px; height: 80px; object-fit: cover;">
                                                            @else
                                                                <img src="{{ asset($item->image) }}" alt="Product Image"
                                                                    class="img-thumbnail"
                                                                    style="width: 80px; height: 80px; object-fit: cover;">
                                                            @endif
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
                                                            <span class="badge badge-warning">{{ $item->quantity }} in stock
                                                                (Low)
                                                            </span>
                                                        @else
                                                            <span class="badge badge-danger">Out of Stock</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-success">{{ $item->category }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info">{{ $item->type }}</span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('products.edit', $item->id) }}"
                                                            class="btn btn-primary btn-sm">
                                                            <i class="mdi mdi-pencil"></i> Update
                                                        </a>
                                                        <a href="#" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $item->id }})">
                                                            <i class="mdi mdi-delete"></i> Delete
                                                        </a>
                                                        <form id="delete-form-{{ $item->id }}"
                                                            action="{{ route('admin.products.delete', $item->id) }}"
                                                            method="GET" style="display: none;">
                                                            @csrf
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- content-wrapper ends -->
@endsection

@section('scripts')
    {{-- Include DataTables CSS and JS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {

            // ===================== DATATABLES =====================
            var table = $('#productTable').DataTable({
                responsive: true,
                processing: true,
                serverSide: false, // Client-side processing
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
                        targets: [2, 7] // Disable sorting on Image and Actions
                    },
                    {
                        searchable: false,
                        targets: [2, 7] // Disable search on Image and Actions
                    },
                    {
                        className: 'text-center',
                        targets: [4] // Center align Quantity
                    }
                ],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    search: "",
                    searchPlaceholder: "Search products...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ products",
                    infoEmpty: "Showing 0 to 0 of 0 products",
                    infoFiltered: "(filtered from _MAX_ total products)",
                    zeroRecords: "No matching products found",
                    emptyTable: "No products available",
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
            }, 50);

            window.confirmDelete = function(productId) {
                if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                    document.getElementById('delete-form-' + productId).submit();
                }
            };
        });
    </script>
@endsection
