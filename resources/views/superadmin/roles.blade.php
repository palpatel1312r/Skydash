@extends('Components.superadminheader')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            {{-- Clean Modern Header --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm bg-white">
                        <div class="card-body py-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-1 font-weight-bold text-dark">
                                    <i class="mdi mdi-shield-account-outline mr-2 text-primary"></i> Manage Roles
                                </h4>
                                <p class="mb-0 text-muted small">Create and manage user roles for the system.</p>
                            </div>
                            <div>
                                @php $currentUser = auth()->guard('admin')->user(); @endphp
                                @if ($currentUser->role_id == 1)
                                    <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal"
                                        data-target="#addRoleModal">
                                        <i class="mdi mdi-plus"></i> New Role
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Table Content --}}
            @if ($currentUser->role_id == 1)
                <div class="row">
                    <div class="col-md-12 grid-margin stretch-card">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="border-0 px-4" style="width: 10%;">#</th>
                                                <th class="border-0 px-4" style="width: 70%;">Role Name</th>
                                                <th class="border-0 px-4 text-right" style="width: 20%;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($roles as $role)
                                                <tr>
                                                    <td class="px-4 text-muted">{{ $loop->iteration }}</td>
                                                    <td class="px-4">
                                                        {{-- Modern Gradient Pills --}}
                                                        <span class="badge badge-primary px-3 py-2 rounded-pill shadow-sm"
                                                            style="font-size: 14px; font-weight: 500; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                                                            {{ $role->name }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 text-right">
                                                        {{-- Modern Icon Buttons --}}
                                                        <button type="button"
                                                            class="btn btn-outline-secondary btn-sm rounded-circle p-2 mr-1 shadow-sm"
                                                            data-toggle="modal"
                                                            data-target="#editRoleModal{{ $role->id }}" title="Edit">
                                                            <i class="mdi mdi-pencil" style="font-size: 16px;"></i>
                                                        </button>
                                                        <button type="button"
                                                            class="btn btn-outline-danger btn-sm rounded-circle p-2 shadow-sm"
                                                            onclick="confirmDelete({{ $role->id }})" title="Delete">
                                                            <i class="mdi mdi-delete" style="font-size: 16px;"></i>
                                                        </button>
                                                        <form id="delete-form-{{ $role->id }}"
                                                            action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                                            style="display:none;">
                                                            @csrf @method('DELETE')
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Add Role Modal (Bootstrap 5 - AJAX Enabled) --}}
                <div class="modal fade" id="addRoleModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title"><i class="mdi mdi-plus-circle-outline"></i> Add Role</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <form id="addRoleForm">
                                @csrf
                                <div class="modal-body p-4">
                                    <div id="addRoleAlert" style="min-height: 10px; margin-bottom: 10px;"></div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Role Name</label>
                                        <input type="text" name="name" id="addRoleName"
                                            class="form-control form-control-lg"
                                            placeholder="e.g. Manager, Editor, Support">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Role</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Edit Role Modals (Bootstrap 5 - AJAX Enabled) --}}
                @foreach ($roles as $role)
                    <div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-secondary text-white">
                                    <h5 class="modal-title"><i class="mdi mdi-pencil-outline"></i> Edit Role</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                </div>
                                <form class="editRoleForm" data-role-id="{{ $role->id }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body p-4">
                                        <div id="editRoleAlert{{ $role->id }}"
                                            style="min-height: 10px; margin-bottom: 10px;"></div>
                                        <div class="form-group">
                                            <label class="font-weight-bold">Role Name</label>
                                            <input type="text" name="name" value="{{ $role->name }}"
                                                class="form-control form-control-lg role-name-input">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Cancel</button>
                                        {{-- ✅ FIXED: Changed to btn-primary --}}
                                        <button type="submit" class="btn btn-primary">Update Role</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger border-0 shadow-sm">
                            <i class="mdi mdi-lock mr-2"></i> Access Denied: Only Super Admin can manage roles.
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Custom CSS tweaks for Bootstrap 5 compatibility --}}
    <style>
        /* Force Laravel validation errors to show under the input */
        .form-control.is-invalid {
            border-color: #dc3545 !important;
        }

        .is-invalid~.invalid-feedback {
            display: block !important;
        }

        /* Minor rounded tweaks for modals */
        .modal-content {
            border-radius: 12px;
        }

        .modal-header {
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Delete confirmation
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this role?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }

        $(document).ready(function() {
            // 1. SETUP CSRF TOKEN
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // 2. CLEAR FIELD ERROR HELPER
            window.clearFieldError = function(field) {
                $(field).removeClass('is-invalid');
                $(field).closest('.form-group').find('.invalid-feedback').remove();
            };

            // 3. LIVE CLEARING
            $('.modal input').on('input', function() {
                if ($(this).val().trim() !== '') {
                    clearFieldError(this);
                }
            });

            // 4. AJAX SUBMISSION FOR ADD ROLE
            $('#addRoleForm').on('submit', function(e) {
                e.preventDefault();

                $('#addRoleAlert').empty();
                clearFieldError($('#addRoleName'));

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('roles.store') }}",
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#addRoleAlert').html(
                            '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            response.message +
                            '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' +
                            '</div>'
                        );
                        setTimeout(function() {
                            location.reload(); // Reload to show new role in table
                        }, 10);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, messages) {
                                var input = $('[name="' + key + '"]', '#addRoleForm');
                                if (input.length) {
                                    input.addClass('is-invalid');
                                    input.closest('.form-group').append(
                                        '<div class="invalid-feedback" style="display:block;">' +
                                        messages[0] + '</div>'
                                    );
                                } else {
                                    $('#addRoleAlert').html(
                                        '<div class="alert alert-danger">' +
                                        messages[0] + '</div>'
                                    );
                                }
                            });
                        } else {
                            $('#addRoleAlert').html(
                                '<div class="alert alert-danger">Error: ' + xhr.status +
                                '</div>'
                            );
                        }
                    }
                });
            });

            // 5. AJAX SUBMISSION FOR EDIT ROLE
            $('.editRoleForm').on('submit', function(e) {
                e.preventDefault();

                var roleId = $(this).data('role-id');
                var alertDiv = $('#editRoleAlert' + roleId);
                alertDiv.empty();
                clearFieldError($(this).find('.role-name-input'));

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ url('/roles') }}/" + roleId,
                    type: 'POST', // Laravel uses POST for form submissions with PUT method spoofing
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        alertDiv.html(
                            // '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            // response.message +
                            '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' +
                            '</div>'
                        );
                        setTimeout(function() {
                            location
                        .reload(); // Reload to show updated role name in table
                        }, 10);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, messages) {
                                var input = $('[name="' + key + '"]',
                                    '.editRoleForm[data-role-id="' + roleId + '"]');
                                if (input.length) {
                                    input.addClass('is-invalid');
                                    input.closest('.form-group').append(
                                        '<div class="invalid-feedback" style="display:block;">' +
                                        messages[0] + '</div>'
                                    );
                                } else {
                                    alertDiv.html(
                                        '<div class="alert alert-danger">' +
                                        messages[0] + '</div>'
                                    );
                                }
                            });
                        } else {
                            alertDiv.html(
                                '<div class="alert alert-danger">Error: ' + xhr.status +
                                '</div>'
                            );
                        }
                    }
                });
            });
        });
    </script>
@endsection
