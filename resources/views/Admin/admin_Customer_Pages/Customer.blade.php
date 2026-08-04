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

                            {{-- Role Filter --}}
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm shadow-sm rounded-pill px-3 dropdown-toggle"
                                    type="button" id="roleDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-shield-account-outline me-1"></i>
                                    <span id="roleLabel">
                                        @if (isset($request) && $request->has('role'))
                                            {{ $roles->firstWhere('id', $request->role)->name ?? 'All Roles' }}
                                        @else
                                            All Roles
                                        @endif
                                    </span>
                                </button>
                                <ul class="dropdown-menu shadow-sm" aria-labelledby="roleDropdown"
                                    style="min-width: 200px; max-height: 300px; overflow-y: auto;">
                                    <li><a class="dropdown-item role-option" href="#" data-id="">All Roles</a>
                                    </li>
                                    @foreach ($roles as $role)
                                        <li><a class="dropdown-item role-option" href="#"
                                                data-id="{{ $role->id }}">{{ $role->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- Status Filter --}}
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm shadow-sm rounded-pill px-3 dropdown-toggle"
                                    type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-check-circle-outline me-1"></i>
                                    <span id="statusLabel">
                                        @if (isset($request) && $request->has('status'))
                                            {{ $request->status }}
                                        @else
                                            All Statuses
                                        @endif
                                    </span>
                                </button>
                                <ul class="dropdown-menu shadow-sm" aria-labelledby="statusDropdown"
                                    style="min-width: 200px; max-height: 300px; overflow-y: auto;">
                                    <li><a class="dropdown-item status-option" href="#" data-status="">All
                                            Statuses</a></li>
                                    @foreach ($statuses as $status)
                                        <li><a class="dropdown-item status-option" href="#"
                                                data-status="{{ $status }}">{{ $status }}</a></li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- Clear Filters Button --}}
                            <a href="{{ route('admin.customers.index') }}"
                                class="btn btn-sm shadow-sm rounded-pill px-3 
                               {{ request()->has('role') || request()->has('status') ? 'btn-outline-danger' : 'btn-outline-secondary' }}">
                                <i class="mdi mdi-close me-1"></i> <span class="d-none d-sm-inline">Clear</span>
                            </a>
                        </div>

                        {{-- RIGHT: Create Button --}}
                        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary shadow px-3 px-sm-4 py-2">
                            <i class="mdi mdi-plus me-1"></i><span class="d-none d-sm-inline">Create New </span>Customer
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
                                        <i class="mdi mdi-account-multiple fs-3"></i>
                                    </div>
                                    <div>
                                        <h4 class="card-title mb-0 fw-bold">Customer List</h4>
                                        <small class="text-muted">Manage your customers</small>
                                    </div>
                                </div>

                                <!-- Right: Search -->
                                <div class="input-group input-group-sm w-100" style="max-width:250px;">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="mdi mdi-magnify text-muted"></i>
                                    </span>
                                    <input id="dtSearch" class="form-control bg-light border-start-0"
                                        placeholder="Search customers...">
                                </div>
                            </div>

                            {{-- TABLE --}}
                            <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">
                                <table class="table table-striped table-borderless" id="customerTable">
                                    <thead>
                                        <tr>
                                            <th class="text-nowrap">#</th>
                                            <th class="text-nowrap">Name</th>
                                            <th class="text-nowrap">E-mail</th>
                                            <th class="text-nowrap">Role</th>
                                            <th class="text-nowrap">Created At</th>
                                            <th class="text-nowrap">Status</th>
                                            <th class="text-nowrap text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($customers as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><strong>{{ $item->fullname }}</strong></td>
                                                <td>{{ $item->email }}</td>
                                                <td>
                                                    @if ($item->role)
                                                        <span class="badge badge-primary">{{ $item->role->name }}</span>
                                                    @else
                                                        <span class="badge badge-secondary">No Role</span>
                                                    @endif
                                                </td>
                                                <td>{{ $item->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    @if ($item->status == 'Active')
                                                        <label class="badge badge-success">Active</label>
                                                    @elseif ($item->status == 'Inactive')
                                                        <label class="badge badge-warning">Inactive</label>
                                                    @else
                                                        <label class="badge badge-warning">{{ $item->status }}</label>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div
                                                        class="d-flex align-items-center justify-content-center gap-1 flex-nowrap action-buttons">
                                                        <button type="button" class="btn btn-primary btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#updateModal{{ $item->id }}">
                                                            <i class="mdi mdi-pencil me-1"></i> <span
                                                                class="d-none d-lg-inline">Update</span>
                                                        </button>

                                                        <form action="{{ route('admin.customers.delete', $item->id) }}"
                                                            method="POST" class="d-inline-block"
                                                            onsubmit="return confirm('Are you sure you want to delete this customer? This action cannot be undone.');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="mdi mdi-delete me-1"></i> <span
                                                                    class="d-none d-lg-inline">Delete</span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4">No customers found.</td>
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

    {{-- CUSTOMER UPDATE MODALS --}}
    @foreach ($customers as $item)
        <div class="modal fade" id="updateModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-light border-bottom">
                        <h5 class="modal-title fw-bold">Update Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.customers.update') }}" method="POST" class="updateCustomerForm">
                            @csrf
                            <input type="hidden" name="id" value="{{ $item->id }}">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Name:</label>
                                <input type="text" name="fullname" value="{{ $item->fullname }}"
                                    class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email:</label>
                                <input type="email" name="email" value="{{ $item->email }}" class="form-control"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Role:</label>
                                <select name="role_id" class="form-select">
                                    <option value="">Select Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ $item->role_id == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status:</label>
                                <select name="status" class="form-select">
                                    <option value="Active" {{ $item->status == 'Active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="Inactive" {{ $item->status == 'Inactive' ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <button type="button" class="btn btn-light btn-sm"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold">Save
                                    Changes</button>
                            </div>
                        </form>
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

        #customerTable {
            width: 100% !important;
            min-width: 900px;
        }

        #customerTable thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        #customerTable tbody tr:hover {
            background-color: #f8f9fa !important;
        }

        #customerTable tbody td {
            vertical-align: middle;
            font-size: 0.875rem;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-primary {
            background-color: #cce5ff;
            color: #004085;
        }

        #customerTable td:last-child {
            min-width: 160px;
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

        #paginationContainer .dataTables_paginate .paginate_button:hover:not(.disabled):not(.current) {
            background: #f8f9fa !important;
            border-color: #dee2e6 !important;
        }

        #paginationContainer .dataTables_paginate .paginate_button.current {
            background: #121314 !important;
            border-color: #b7b8ba !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.28) !important;
        }

        #paginationContainer .dataTables_paginate .paginate_button.disabled {
            color: #ced4da !important;
            background: #f8f9fa !important;
            border-color: #e9ecef !important;
            cursor: not-allowed !important;
        }

        #infoContainer .dataTables_info {
            float: none !important;
            text-align: right !important;
            color: #6c757d;
            font-size: 0.875rem;
            font-weight: 500;
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
            // ===================== DATA TABLE =====================
            var table = $('#customerTable').DataTable({
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
                    targets: [6]
                }],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"></div>',
                    search: "",
                    lengthMenu: "Show _MENU_",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    zeroRecords: "No matching customers found",
                    paginate: {
                        first: '<i class="mdi mdi-chevron-double-left"></i>',
                        previous: '<i class="mdi mdi-chevron-left"></i>',
                        next: '<i class="mdi mdi-chevron-right"></i>',
                        last: '<i class="mdi mdi-chevron-double-right"></i>'
                    }
                }
            });

            $('#lengthContainer').append($('.dataTables_length'));
            $('#paginationContainer').append($('.dataTables_paginate'));
            $('#infoContainer').append($('.dataTables_info'));

            $('#dtSearch').on('keyup', function() {
                table.search($(this).val()).draw();
            });

            // ===================== GET URL PARAMETERS =====================
            var urlParams = new URLSearchParams(window.location.search);

            // ===================== ROLE FILTER =====================
            var roleParam = urlParams.get('role');
            if (roleParam) {
                var roleText = $('.role-option[data-id="' + roleParam + '"]').text();
                if (roleText) $('#roleLabel').text(roleText);
            }

            $('.role-option').on('click', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var name = $(this).text();
                var url = new URL(window.location.href);
                url.searchParams.delete('page');

                if (id === '') {
                    url.searchParams.delete('role');
                    $('#roleLabel').text('All Roles');
                } else {
                    url.searchParams.set('role', id);
                    $('#roleLabel').text(name);
                }
                window.location.href = url.toString();
            });

            // ===================== STATUS FILTER =====================
            var statusParam = urlParams.get('status');
            if (statusParam) {
                $('#statusLabel').text(statusParam);
            }

            $('.status-option').on('click', function(e) {
                e.preventDefault();
                var status = $(this).data('status');
                var url = new URL(window.location.href);
                url.searchParams.delete('page');

                if (status === '') {
                    url.searchParams.delete('status');
                    $('#statusLabel').text('All Statuses');
                } else {
                    url.searchParams.set('status', status);
                    $('#statusLabel').text(status);
                }
                window.location.href = url.toString();
            });

            // ===================== UPDATE MODAL HANDLING =====================
            // Intercept the modal form submit to use AJAX
            $('.updateCustomerForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var modal = form.closest('.modal');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        // Close modal and reload page to show updated data
                        modal.modal('hide');
                        location.reload();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            // Clear previous errors
                            form.find('.is-invalid').removeClass('is-invalid');
                            form.find('.invalid-feedback').remove();

                            // Display new errors
                            $.each(errors, function(key, messages) {
                                var input = form.find('[name="' + key + '"]');
                                if (input.length) {
                                    input.addClass('is-invalid');
                                    input.after(
                                        '<div class="invalid-feedback" style="display:block;">' +
                                        messages[0] + '</div>');
                                }
                            });
                        } else {
                            alert('An error occurred while updating the customer.');
                        }
                    }
                });
            });

            // Clear errors when modal is closed
            $('.modal').on('hidden.bs.modal', function() {
                $(this).find('.is-invalid').removeClass('is-invalid');
                $(this).find('.invalid-feedback').remove();
            });

            // ===================== AUTO-DISMISS ALERTS =====================
            setTimeout(function() {
                document.querySelectorAll('.alert').forEach(alert => {
                    alert.style.transition = 'opacity 1s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.style.display = 'none', 500);
                });
            }, 50);
        });
    </script>
@endsection
