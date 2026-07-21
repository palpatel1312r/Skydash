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

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Full Name:</label>
                                            <input type="text" name="name" id="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name', $user['name'] ?? '') }}"
                                                placeholder="Enter full name">
                                            @error('name')
                                                <div class="invalid-feedback" id="name-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email Address:</label>
                                            <input type="email" name="email" id="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email', $user['email'] ?? '') }}"
                                                placeholder="Enter email address">
                                            @error('email')
                                                <div class="invalid-feedback" id="email-error">{{ $message }}</div>
                                            @enderror
                                            @if (isset($user))
                                                <small class="text-muted">Email cannot be changed.</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Password
                                                {{ isset($user) ? '(Leave blank to keep current)' : '' }}:</label>
                                            <input type="password" name="password" id="passwordField"
                                                class="form-control @error('password') is-invalid @enderror"
                                                placeholder="{{ isset($user) ? 'Enter new password or leave empty' : 'Min 4 characters' }}"
                                                onkeydown="if(event.key === 'Enter'){ event.preventDefault(); document.getElementById('confirmPasswordField').focus(); }">
                                            @error('password')
                                                <div class="invalid-feedback" id="password-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Confirm Password:</label>
                                            <input type="password" name="password_confirmation" id="confirmPasswordField"
                                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                                placeholder="Confirm password">
                                            @error('password_confirmation')
                                                <div class="invalid-feedback" id="password_confirmation-error">
                                                    {{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Select User Type:</label>
                                            <select name="user_type" id="user_type_select"
                                                class="form-control @error('user_type') is-invalid @enderror"
                                                {{ isset($user) ? 'disabled' : 'required' }}>
                                                <option value="">Select User Type</option>
                                                <option value="super_admin"
                                                    {{ old('user_type', $user['guard'] ?? '') == 'admin' && ($user['role_name'] ?? '') == 'Super Admin' ? 'selected' : '' }}>
                                                    Super Admin</option>
                                                <option value="admin"
                                                    {{ old('user_type', $user['guard'] ?? '') == 'admin' && ($user['role_name'] ?? '') != 'Super Admin' ? 'selected' : '' }}>
                                                    Admin</option>
                                                <option value="customer"
                                                    {{ old('user_type', $user['guard'] ?? '') == 'customer' ? 'selected' : '' }}>
                                                    Customer</option>
                                            </select>
                                            @error('user_type')
                                                <div class="invalid-feedback" id="user_type-error">{{ $message }}</div>
                                            @enderror
                                            @if (isset($user))
                                                <small class="text-muted">User Type cannot be changed.</small>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6" id="role_selection_div" style="display: none;">
                                        <div class="form-group">
                                            <label>Select Role:</label>
                                            <select name="role_id" id="role_id_select"
                                                class="form-control @error('role_id') is-invalid @enderror" required>
                                                <option value="">Select Role</option>
                                                @foreach (\App\Models\Role::all() as $role)
                                                    <option value="{{ $role->id }}"
                                                        {{ old('role_id', $user['role_id'] ?? '') == $role->id ? 'selected' : '' }}>
                                                        {{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('role_id')
                                                <div class="invalid-feedback" id="role_id-error">{{ $message }}</div>
                                            @enderror
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Exact logic from Customer page to completely remove errors
            function removeFieldError(field) {
                // 1. Remove the is-invalid class from the input
                field.classList.remove('is-invalid');

                // 2. Find and completely remove the error message div from the DOM
                const formGroup = field.closest('.form-group');
                if (formGroup) {
                    const errorDiv = formGroup.querySelector('.invalid-feedback');
                    if (errorDiv) {
                        errorDiv.remove(); // Delete the error entirely
                    }
                }
            }

            // Handle all input fields
            document.querySelectorAll('input, select').forEach(field => {
                // For text, email, and password inputs - trigger on input
                if (field.type === 'text' || field.type === 'email' || field.type === 'password') {
                    field.addEventListener('input', function() {
                        if (this.value.trim() !== '') {
                            removeFieldError(this);
                        }
                    });
                }

                // For select dropdowns - trigger on change
                if (field.tagName === 'SELECT') {
                    field.addEventListener('change', function() {
                        if (this.value !== '') {
                            removeFieldError(this);
                        }
                    });
                }
            });

            // Force clear errors when any field gets focus
            document.querySelectorAll('input, select').forEach(field => {
                field.addEventListener('focus', function() {
                    removeFieldError(this);
                });
            });

            // Toggle Role dropdown
            const userTypeSelect = document.getElementById('user_type_select');
            const roleSelectionDiv = document.getElementById('role_selection_div');
            const roleSelect = document.getElementById('role_id_select');

            if (userTypeSelect) {
                // Force check the value when the page first loads
                if (userTypeSelect.value === 'customer') {
                    roleSelectionDiv.style.display = 'block';
                    roleSelect.setAttribute('required', 'required');
                } else {
                    roleSelectionDiv.style.display = 'none';
                    roleSelect.removeAttribute('required');
                    roleSelect.value = '';
                }

                // Listen for changes
                userTypeSelect.addEventListener('change', function() {
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
        });
    </script>
@endsection
