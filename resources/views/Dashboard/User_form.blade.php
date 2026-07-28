@extends('components.adminheader')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title mb-0">
                                    @if (isset($user))
                                        Edit User: {{ $user['name'] }}
                                    @else
                                        Add New User
                                    @endif
                                </h4>
                                <a href="{{ route('admin.user.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="mdi mdi-arrow-left"></i> Back to User List
                                </a>
                            </div>

                            <form
                                action="{{ isset($user) ? route('admin.user.update', ['id' => $user['id'], 'guard' => $user['guard']]) : route('admin.user.store') }}"
                                method="POST" novalidate id="userForm" onsubmit="return false;">
                                @csrf
                                @if (isset($user))
                                    @method('PUT')
                                @endif

                                <div id="global-alert-container" style="min-height: 10px;"></div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Full Name:</label>
                                            <input type="text" name="name" id="name" class="form-control"
                                                value="{{ old('name', $user['name'] ?? '') }}"
                                                placeholder="Enter full name">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email Address:</label>
                                            <input type="email" name="email" id="email" class="form-control"
                                                value="{{ old('email', $user['email'] ?? '') }}"
                                                placeholder="Enter email address">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Password
                                                {{ isset($user) ? '(Leave blank to keep current)' : '' }}:</label>
                                            <input type="password" name="password" id="passwordField" class="form-control"
                                                placeholder="{{ isset($user) ? 'Enter new password or leave empty' : 'Min 4 characters' }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Confirm Password:</label>
                                            <input type="password" name="password_confirmation" id="confirmPasswordField"
                                                class="form-control" placeholder="Confirm password">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Select User Type:</label>
                                            <select name="user_type" id="user_type_select" class="form-control">
                                                <option value="">Select User Type</option>
                                                <option value="super_admin"
                                                    {{ ($user['role_name'] ?? '') == 'Super Admin' ? 'selected' : '' }}>
                                                    Super Admin
                                                </option>
                                                <option value="admin"
                                                    {{ ($user['role_name'] ?? '') == 'Admin' ? 'selected' : '' }}>
                                                    Admin
                                                </option>
                                                <option value="customer"
                                                    {{ ($user['guard'] ?? '') == 'customer' ? 'selected' : '' }}>
                                                    Customer
                                                </option>
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6" id="role_selection_div" style="display: none;">
                                        <div class="form-group">
                                            <label>Select Role:</label>
                                            <select name="role_id" id="role_id_select" class="form-control">
                                                <option value="">Select Role</option>
                                                @foreach (\App\Models\Role::all() as $role)
                                                    <option value="{{ $role->id }}"
                                                        {{ old('role_id', $user['role_id'] ?? '') == $role->id ? 'selected' : '' }}>
                                                        {{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 d-flex justify-content-between">
                                    <div>
                                        <button type="submit" class="btn btn-success"><i class="mdi mdi-content-save"></i>
                                            {{ isset($user) ? 'Update User' : 'Save User' }}</button>
                                        <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* 🔥 FORCE OVERRIDE THEME STYLES FOR SELECT ELEMENTS */
        select.form-control.is-invalid,
        select.is-invalid,
        select.form-control.is-invalid:focus,
        select.is-invalid:focus {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.1h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right calc(0.375em + 0.1875rem) center !important;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
            padding-right: calc(1.5em + 0.75rem) !important;
        }

        /* Keep these existing rules */
        .form-control.is-invalid {
            border-color: #dc3545 !important;
        }

        .is-invalid~.invalid-feedback {
            display: block !important;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // 1. SETUP CSRF TOKEN
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // 2. CLEAR FIELD ERROR HELPER
            window.clearFieldError = function(field) {
                var $col = $(field).closest('.form-group');
                $col.find('.invalid-feedback').remove();
                $(field).removeClass('is-invalid');
            };

            // 3. LIVE CLEARING (Input/Change/Focus events)
            $('#userForm input, #userForm select').on('input change focus', function() {
                if ($(this).val().trim() !== '') {
                    clearFieldError(this);
                }
            });

            // 4. TOGGLE ROLE DROPDOWN (Customer/Admin logic)
            const userTypeSelect = document.getElementById('user_type_select');
            const roleSelectionDiv = document.getElementById('role_selection_div');
            const roleSelect = document.getElementById('role_id_select');

            if (userTypeSelect) {
                // Initial check on page load
                if (userTypeSelect.value === 'customer') {
                    roleSelectionDiv.style.display = 'block';
                    roleSelect.setAttribute('required', 'required');
                }

                // Listen for changes
                $(userTypeSelect).on('change', function() {
                    if (this.value === 'customer') {
                        roleSelectionDiv.style.display = 'block';
                        roleSelect.setAttribute('required', 'required');
                    } else {
                        roleSelectionDiv.style.display = 'none';
                        roleSelect.removeAttribute('required');
                        roleSelect.value = '';
                    }
                });
            }

            // 5. AUTO-FOCUS TO CONFIRM PASSWORD (NEW)
            // 5. AUTO-FOCUS TO CONFIRM PASSWORD (Improved)
            $('#passwordField').on('input', function() {
                var passwordVal = $(this).val();
                // Only move focus if:
                // 1. Password is at least 4 characters long
                // 2. Confirm Password field is NOT already focused
                // 3. Confirm Password field is empty (so we don't interrupt typing)
                if (passwordVal.length >= 4 && $('#confirmPasswordField').val() === '') {
                    $('#confirmPasswordField').focus();
                }
            });

            // 6. AJAX SUBMISSION
            $('#userForm').on('submit', function(e) {
                e.preventDefault();

                // Clear ALL existing errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                $('#global-alert-container').empty();

                var formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Show success message
                        $('#global-alert-container').html(
                            '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            response.message +
                            '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' +
                            '</div>'
                        );
                        // Redirect after 1.5 seconds
                        setTimeout(function() {
                            window.location.href = "{{ route('admin.user.index') }}";
                        }, 1500);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;

                            $.each(errors, function(key, messages) {
                                var input = $('[name="' + key + '"]');
                                if (input.length) {
                                    var formGroup = input.closest('.form-group');
                                    formGroup.find('.invalid-feedback').remove();

                                    // ✅ FORCE CLASS ADDITION
                                    input.addClass('is-invalid');

                                    // Append error message
                                    formGroup.append(
                                        '<div class="invalid-feedback" style="display:block;">' +
                                        messages[0] + '</div>'
                                    );
                                } else {
                                    $('#global-alert-container').append(
                                        '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                        messages[0] +
                                        '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' +
                                        '</div>'
                                    );
                                }
                            });
                        } else {
                            // Server error (500, 404, etc.)
                            var errorMsg = xhr.responseJSON ? xhr.responseJSON.message :
                                'Unknown Server Error';
                            $('#global-alert-container').html(
                                '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                'Error ' + xhr.status + ': ' + errorMsg +
                                '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' +
                                '</div>'
                            );
                        }
                    }
                });
            });
        });
    </script>
@endsection
