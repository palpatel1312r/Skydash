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
                                method="POST" novalidate id="userForm">
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

            // 5. AJAX SUBMISSION
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

                            // Loop through errors and display them
                            $.each(errors, function(key, messages) {
                                var input = $('[name="' + key + '"]');
                                if (input.length) {
                                    var formGroup = input.closest('.form-group');
                                    formGroup.find('.invalid-feedback').remove();
                                    input.addClass('is-invalid');
                                    // Append error message right under the input
                                    formGroup.append(
                                        '<div class="invalid-feedback" style="display:block;">' +
                                        messages[0] + '</div>'
                                    );
                                } else {
                                    // If field not found, show in global alert
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
