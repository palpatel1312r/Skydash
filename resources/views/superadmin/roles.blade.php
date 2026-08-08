@extends('Components.superadminheader')
@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            {{-- Header --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1 font-weight-bold">
                                    <i class="mdi mdi-shield-account-outline text-primary mr-2"></i>Manage Roles
                                </h4>
                                <p class="mb-0 text-muted small">Create, edit and manage system roles</p>
                            </div>
                            @php $currentUser = auth()->guard('admin')->user(); @endphp
                            @if ($currentUser->role_id == 1)
                                <button type="button" class="btn btn-primary btn-sm shadow-sm px-3" data-bs-toggle="modal"
                                    data-bs-target="#addRoleModal">
                                    <i class="mdi mdi-plus"></i> New Role
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if ($currentUser->role_id == 1)
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-0">
                                {{-- HEADER: Title + Search --}}
                                <div
                                    class="d-flex flex-wrap align-items-center justify-content-between p-3 p-sm-4 pb-3 border-bottom">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle text-primary">
                                            <i class="mdi mdi-shield-account-outline" style="font-size: 24px;"></i>
                                        </div>
                                        <div>
                                            <h4 class="card-title mb-0 fw-bold text-dark">System Roles</h4>
                                            <small class="text-muted">Manage access levels</small>
                                        </div>
                                    </div>
                                    <div class="input-group" style="max-width: 320px; min-width: 200px;">
                                        <span class="input-group-text bg-light border-end-0 py-2 px-3">
                                            <i class="mdi mdi-magnify text-muted"
                                                style="font-size: 1.3rem; line-height: 1;"></i>
                                        </span>
                                        <input id="dtSearch" class="form-control bg-light border-start-0 py-2 px-3"
                                            placeholder="Search Roles..." data-search-param="{{ request('search') ?? '' }}"
                                            style="font-size: 1rem;">
                                    </div>
                                </div>

                                {{-- TABLE --}}
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 w-100" id="rolesTable">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="border-0 px-4 py-3" style="width:6%">#</th>
                                                <th class="border-0 px-4 py-3" style="width:28%">Role Name</th>
                                                <th class="border-0 px-4 py-3" style="width:22%">Created At</th>
                                                <th class="border-0 px-4 py-3 text-end" style="width:22%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($roles as $role)
                                                <tr>
                                                    <td class="px-4 py-3 text-muted">{{ $loop->iteration }}</td>
                                                    <td class="px-4 py-3">
                                                        <span class="badge px-3 py-2 shadow-sm text-white"
                                                            style="background: linear-gradient(135deg,#4e73df,#224abe); font-size:0.875rem;">
                                                            <i class="mdi mdi-shield-account me-1"></i>{{ $role->name }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 text-muted"
                                                        data-order="{{ $role->created_at ? $role->created_at->timestamp : 0 }}">
                                                        <i class="mdi mdi-calendar-clock mr-1"></i>
                                                        {{ $role->created_at ? $role->created_at->format('d M Y, h:i A') : '—' }}
                                                    </td>
                                                    <td class="px-4 py-3 text-end">
                                                        <div class="d-flex justify-content-end align-items-center gap-1">

                                                            {{-- ✅ FIXED EDIT BUTTON --}}
                                                            <button type="button"
                                                                class="btn btn-outline-primary btn-sm p-0 d-flex align-items-center justify-content-center shadow-sm"
                                                                style="width: 40px; height: 40px; border-radius: 6px;"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editRoleModal{{ $role->id }}"
                                                                title="Edit">
                                                                <i class="mdi mdi-pencil"
                                                                    style="font-size: 1.5rem; line-height: 1; margin: 0;"></i>
                                                            </button>

                                                            {{-- ✅ FIXED DELETE BUTTON --}}
                                                            <button type="button"
                                                                class="btn btn-outline-danger btn-sm p-0 d-flex align-items-center justify-content-center shadow-sm"
                                                                style="width: 40px; height: 40px; border-radius: 6px;"
                                                                onclick="confirmDelete({{ $role->id }})" title="Delete">
                                                                <i class="mdi mdi-delete"
                                                                    style="font-size: 1.5rem; line-height: 1; margin: 0;"></i>
                                                            </button>

                                                        </div>

                                                        <form id="delete-form-{{ $role->id }}"
                                                            action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                                            class="d-none">
                                                            @csrf @method('DELETE')
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- FOOTER --}}
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

                {{-- Add Role Modal --}}
                <div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg" style="border-radius:12px;">
                            <div class="modal-header bg-primary text-white" style="border-radius:12px 12px 0 0;">
                                <h5 class="modal-title"><i class="mdi mdi-plus-circle-outline me-1"></i> Add Role</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form id="addRoleForm">
                                @csrf
                                <div class="modal-body p-4">
                                    <div id="addRoleAlert" class="mb-2"></div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Role Name</label>
                                        <input type="text" name="name" id="addRoleName"
                                            class="form-control form-control-lg"
                                            placeholder="e.g. Manager, Editor, Support">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Role</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Edit Role Modals --}}
                @foreach ($roles as $role)
                    <div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg" style="border-radius:12px;">
                                <div class="modal-header bg-secondary text-white" style="border-radius:12px 12px 0 0;">
                                    <h5 class="modal-title"><i class="mdi mdi-pencil-outline me-1"></i> Edit Role</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form class="editRoleForm" data-role-id="{{ $role->id }}">
                                    @csrf @method('PUT')
                                    <div class="modal-body p-4">
                                        <div id="editRoleAlert{{ $role->id }}" class="mb-2"></div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Role Name</label>
                                            <input type="text" name="name" value="{{ $role->name }}"
                                                class="form-control form-control-lg role-name-input">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Update Role</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="alert alert-danger border-0 shadow-sm">
                    <i class="mdi mdi-lock me-2"></i> Access Denied: Only Super Admin can manage roles.
                </div>
            @endif
        </div>
    </div>

    <style>
        .form-control.is-invalid {
            border-color: #dc3545 !important;
        }

        .is-invalid~.invalid-feedback {
            display: block !important;
        }

        .modal-content {
            border-radius: 12px;
        }

        .table>tbody>tr:hover {
            background-color: #f8f9fc;
        }

        #dtCustomFooter {
            min-height: 40px;
        }

        #rolesTable thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
        }

        .dataTables_filter {
            display: none !important;
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

        @media (max-width: 575.98px) {
            #dtCustomFooter {
                flex-direction: column !important;
                gap: 10px !important;
            }
        }
    </style>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this role?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if ($.fn.DataTable.isDataTable('#rolesTable')) {
                $('#rolesTable').DataTable().destroy();
            }

            var table = $('#rolesTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                order: [
                    [2, 'desc']
                ],
                columnDefs: [{
                    targets: [0, 3],
                    orderable: false
                }],
                language: {
                    search: "",
                    lengthMenu: "Show _MENU_",
                    info: "Showing _START_ to _END_ of _TOTAL_ roles",
                    infoEmpty: "Showing 0 to 0 of 0 roles",
                    zeroRecords: "No matching roles found",
                    paginate: {
                        first: '<i class="mdi mdi-chevron-double-left"></i>',
                        previous: '<i class="mdi mdi-chevron-left"></i>',
                        next: '<i class="mdi mdi-chevron-right"></i>',
                        last: '<i class="mdi mdi-chevron-double-right"></i>'
                    }
                },
                initComplete: function() {
                    setTimeout(function() {
                        $('#lengthContainer').append($('.dataTables_length'));
                        $('#paginationContainer').append($('.dataTables_paginate'));
                        $('#infoContainer').append($('.dataTables_info'));
                    }, 100);
                }
            });

            setTimeout(function() {
                if ($('#lengthContainer').is(':empty')) {
                    $('#lengthContainer').append($('.dataTables_length'));
                    $('#paginationContainer').append($('.dataTables_paginate'));
                    $('#infoContainer').append($('.dataTables_info'));
                }
            }, 500);

            // Persistent search
            var searchInput = $('#dtSearch');
            var searchParam = searchInput.data('search-param');
            if (searchParam && searchParam.trim() !== '') {
                table.search(searchParam).draw();
            }

            var searchTimeout;
            searchInput.on('keyup', function() {
                clearTimeout(searchTimeout);
                var value = $(this).val().trim();
                searchTimeout = setTimeout(function() {
                    var url = new URL(window.location.href);
                    if (value !== '') {
                        url.searchParams.set('search', value);
                    } else {
                        url.searchParams.delete('search');
                    }
                    window.history.replaceState({}, '', url.toString());
                    table.search(value).draw();
                }, 300);
            });

            // Clear field errors
            window.clearFieldError = function(field) {
                $(field).removeClass('is-invalid');
                $(field).closest('.mb-3, .form-group').find('.invalid-feedback').remove();
            };

            $('.modal input').on('input', function() {
                if ($(this).val().trim() !== '') clearFieldError(this);
            });

            // Add Role
            $('#addRoleForm').on('submit', function(e) {
                e.preventDefault();
                $('#addRoleAlert').empty();
                clearFieldError($('#addRoleName'));
                $.ajax({
                    url: "{{ route('roles.store') }}",
                    type: 'POST',
                    data: new FormData(this),
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        $('#addRoleAlert').html('<div class="alert alert-success">' + res
                            .message + '</div>');
                        setTimeout(() => location.reload(), 600);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, function(key, messages) {
                                var input = $('[name="' + key + '"]', '#addRoleForm');
                                input.addClass('is-invalid');
                                input.closest('.mb-3, .form-group').append(
                                    '<div class="invalid-feedback" style="display:block;">' +
                                    messages[0] + '</div>');
                            });
                        } else {
                            $('#addRoleAlert').html('<div class="alert alert-danger">Error: ' +
                                xhr.status + '</div>');
                        }
                    }
                });
            });

            // Edit Role
            $('.editRoleForm').on('submit', function(e) {
                e.preventDefault();
                var roleId = $(this).data('role-id');
                var alertDiv = $('#editRoleAlert' + roleId);
                alertDiv.empty();
                clearFieldError($(this).find('.role-name-input'));
                $.ajax({
                    url: "{{ url('/roles') }}/" + roleId,
                    type: 'POST',
                    data: new FormData(this),
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function() {
                        setTimeout(() => location.reload(), 600);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, function(key, messages) {
                                var input = $('[name="' + key + '"]',
                                    '.editRoleForm[data-role-id="' + roleId + '"]');
                                input.addClass('is-invalid');
                                input.closest('.mb-3, .form-group').append(
                                    '<div class="invalid-feedback" style="display:block;">' +
                                    messages[0] + '</div>');
                            });
                        } else {
                            alertDiv.html('<div class="alert alert-danger">Error: ' + xhr
                                .status + '</div>');
                        }
                    }
                });
            });
        });
    </script>
@endsection
