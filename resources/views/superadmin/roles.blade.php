@extends('components.adminheader')

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
                                                            action="{{ route('superadmin.roles.destroy', $role->id) }}"
                                                            method="POST" style="display:none;">
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

                {{-- Add Role Modal (Bootstrap 5) --}}
                <div class="modal fade" id="addRoleModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title"><i class="mdi mdi-plus-circle-outline"></i> Add Role</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <form action="{{ route('superadmin.roles.store') }}" method="POST" novalidate>
                                @csrf
                                <div class="modal-body p-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Role Name</label>
                                        <input type="text" name="name"
                                            class="form-control form-control-lg @error('name') is-invalid @enderror"
                                            placeholder="e.g. Manager, Editor, Support" value="{{ old('name') }}">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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

                {{-- Edit Role Modals (Bootstrap 5) --}}
                @foreach ($roles as $role)
                    <div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-secondary text-white">
                                    <h5 class="modal-title"><i class="mdi mdi-pencil-outline"></i> Edit Role</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                </div>
                                <form action="{{ route('superadmin.roles.update', $role->id) }}" method="POST" novalidate>
                                    @csrf @method('PUT')
                                    <div class="modal-body p-4">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Role Name</label>
                                            <input type="text" name="name" value="{{ $role->name }}"
                                                class="form-control form-control-lg @error('name') is-invalid @enderror"
                                                oninput="removeFieldError(this)" onfocus="removeFieldError(this)">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-secondary">Update Role</button>
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

    <script>
        // Delete confirmation
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this role?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // ✅ FUNCTION: Instantly remove the red border & error message
            function removeFieldError(field) {
                field.classList.remove('is-invalid');
                const formGroup = field.closest('.form-group');
                if (formGroup) {
                    const errorDiv = formGroup.querySelector('.invalid-feedback');
                    if (errorDiv) {
                        errorDiv.remove(); // Permanently delete the error from DOM
                    }
                }
            }

            // ✅ LOOP: Attach event listeners to all inputs inside modals
            document.querySelectorAll('.modal input, .modal select').forEach(field => {
                field.addEventListener('input', function() {
                    if (this.value.trim() !== '') {
                        removeFieldError(this);
                    }
                });
                field.addEventListener('change', function() {
                    if (this.value !== '') {
                        removeFieldError(this);
                    }
                });
                field.addEventListener('focus', function() {
                    removeFieldError(this);
                });
            });

            // ✅ RE-OPEN MODAL IF PAGE RELOADS WITH ERRORS
            if (document.querySelector('.is-invalid')) {
                const invalidInput = document.querySelector('.is-invalid');
                const modalElement = invalidInput.closest('.modal');
                if (modalElement) {
                    $(modalElement).modal('show');
                }
            }
        });
    </script>
@endsection
