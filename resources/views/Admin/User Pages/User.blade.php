@extends('Components.adminheader')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            {{-- Alert Messages --}}
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
                                            {{ $allRoles->firstWhere('id', $request->role)->name ?? 'All Roles' }}
                                        @else
                                            All Roles
                                        @endif
                                    </span>
                                </button>
                                <ul class="dropdown-menu shadow-sm" aria-labelledby="roleDropdown"
                                    style="min-width: 200px; max-height: 300px; overflow-y: auto;">
                                    <li><a class="dropdown-item role-option" href="#" data-id="">All Roles</a>
                                    </li>
                                    @foreach ($allRoles as $role)
                                        <li><a class="dropdown-item role-option" href="#"
                                                data-id="{{ $role->id }}">{{ $role->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- User Type Filter --}}
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm shadow-sm rounded-pill px-3 dropdown-toggle"
                                    type="button" id="typeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-account-multiple-outline me-1"></i>
                                    <span id="typeLabel">
                                        @if (isset($request) && $request->has('user_type'))
                                            {{ ucfirst($request->user_type) }}
                                        @else
                                            All Types
                                        @endif
                                    </span>
                                </button>
                                <ul class="dropdown-menu shadow-sm" aria-labelledby="typeDropdown"
                                    style="min-width: 200px; max-height: 300px; overflow-y: auto;">
                                    <li><a class="dropdown-item type-option" href="#" data-type="">All Types</a>
                                    </li>
                                    @foreach ($userTypes as $type)
                                        <li><a class="dropdown-item type-option" href="#"
                                                data-type="{{ $type['value'] }}">{{ $type['label'] }}</a></li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- Clear Filters Button --}}
                            <a href="{{ route('admin.user.index') }}"
                                class="btn btn-sm shadow-sm rounded-pill px-3 
                               {{ request()->has('role') || request()->has('user_type') ? 'btn-outline-danger' : 'btn-outline-secondary' }}">
                                <i class="mdi mdi-close me-1"></i> <span class="d-none d-sm-inline">Clear</span>
                            </a>
                        </div>

                        {{-- RIGHT: Create Button --}}
                        <a href="{{ route('admin.user.create') }}" class="btn btn-primary shadow px-3 px-sm-4 py-2">
                            <i class="mdi mdi-plus me-1"></i><span class="d-none d-sm-inline">Create New </span>User
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
                                        <i class="mdi mdi-account-group fs-3"></i>
                                    </div>
                                    <div>
                                        <h4 class="card-title mb-0 fw-bold">User List</h4>
                                        <small class="text-muted">Manage system users</small>
                                    </div>
                                </div>

                                <!-- Right: Search -->
                                <div class="input-group input-group-sm w-100" style="max-width:250px;">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="mdi mdi-magnify text-muted"></i>
                                    </span>
                                    <input id="dtSearch" class="form-control bg-light border-start-0"
                                        placeholder="Search users...">
                                </div>
                            </div>

                            {{-- TABLE --}}
                            <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">
                                <table class="table table-striped table-borderless" id="userTable">
                                    <thead>
                                        <tr>
                                            <th class="text-nowrap">#</th>
                                            <th class="text-nowrap">Name</th>
                                            <th class="text-nowrap">E-mail</th>
                                            <th class="text-nowrap">User Type</th>
                                            <th class="text-nowrap">Role</th>
                                            <th class="text-nowrap">Created At</th>
                                            <th class="text-nowrap text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($users as $user)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><strong>{{ $user['name'] }}</strong></td>
                                                <td>{{ $user['email'] }}</td>

                                                <td>
                                                    @if ($user['guard'] == 'customer')
                                                        <span class="badge badge-success">Customer</span>
                                                    @elseif ($user['role_name'] == 'Super Admin')
                                                        <span class="badge badge-danger">Super Admin</span>
                                                    @else
                                                        <span class="badge badge-warning text-dark">Admin</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge badge-primary">{{ $user['role_name'] }}</span>
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($user['created_at'])->format('M d, Y') }}</td>
                                                <td class="text-center">
                                                    <div
                                                        class="d-flex align-items-center justify-content-center gap-1 flex-nowrap action-buttons">
                                                        <a href="{{ route('admin.user.edit', ['id' => $user['id'], 'guard' => $user['guard']]) }}"
                                                            class="btn btn-primary btn-sm">
                                                            <i class="mdi mdi-pencil me-1"></i> <span
                                                                class="d-none d-lg-inline">Update</span>
                                                        </a>

                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDeleteUser('{{ $user['id'] }}', '{{ $user['guard'] }}')">
                                                            <i class="mdi mdi-delete me-1"></i> <span
                                                                class="d-none d-lg-inline">Delete</span>
                                                        </button>
                                                    </div>
                                                    <form id="delete-user-form-{{ $user['id'] }}-{{ $user['guard'] }}"
                                                        action="{{ route('admin.user.destroy', ['id' => $user['id'], 'guard' => $user['guard']]) }}"
                                                        method="POST" style="display: none;">
                                                        @csrf @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4">No users found.</td>
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

    <style>
        .dropdown-menu {
            z-index: 1090 !important;
            max-height: 300px;
            overflow-y: auto;
            margin-top: 8px !important;
        }

        #userTable {
            width: 100% !important;
            min-width: 900px;
        }

        #userTable thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        #userTable tbody tr:hover {
            background-color: #f8f9fa !important;
        }

        #userTable tbody td {
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

        #userTable td:last-child {
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
            var table = $('#userTable').DataTable({
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
                }, {
                    searchable: false,
                    targets: [6]
                }],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"></div>',
                    search: "",
                    lengthMenu: "Show _MENU_",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    zeroRecords: "No matching users found",
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

            // ===================== USER TYPE FILTER =====================
            var typeParam = urlParams.get('user_type');
            if (typeParam) {
                $('#typeLabel').text(typeParam.charAt(0).toUpperCase() + typeParam.slice(1));
            }

            $('.type-option').on('click', function(e) {
                e.preventDefault();
                var type = $(this).data('type');
                var url = new URL(window.location.href);
                url.searchParams.delete('page');

                if (type === '') {
                    url.searchParams.delete('user_type');
                    $('#typeLabel').text('All Types');
                } else {
                    url.searchParams.set('user_type', type);
                    $('#typeLabel').text(type.charAt(0).toUpperCase() + type.slice(1));
                }
                window.location.href = url.toString();
            });

            // ===================== DELETE =====================
            window.confirmDeleteUser = function(userId, guard) {
                if (confirm('Are you sure you want to delete this user?')) {
                    document.getElementById('delete-user-form-' + userId + '-' + guard).submit();
                }
            };

            // ===================== AUTO-DISMISS ALERTS =====================
            setTimeout(function() {
                document.querySelectorAll('.alert').forEach(alert => {
                    alert.style.transition = 'opacity 1s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.style.display = 'none', 500);
                });
            }, 5000);
        });
    </script>
@endsection
