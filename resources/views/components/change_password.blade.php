@extends('components.adminheader')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">
                                    <i class="mdi mdi-lock text-primary"></i> Change Password
                                </h4>
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="mdi mdi-arrow-left"></i> Back to Dashboard
                                </a>
                            </div>

                            <div class="row">
                                <div class="col-md-8 offset-md-2">
                                    <form id="changePasswordForm" method="POST"
                                        action="{{ route('admin.password.update') }}">
                                        @csrf

                                        {{-- Current Password --}}
                                        <div class="form-group">
                                            <label for="current_password">Current Password</label>
                                            <div class="input-group">
                                                <input type="password" name="current_password" id="current_password"
                                                    class="form-control @error('current_password') is-invalid @enderror"
                                                    placeholder="Enter current password">
                                                <div class="input-group-append">
                                                    <span class="input-group-text toggle-password"
                                                        data-target="#current_password" style="cursor: pointer;">
                                                        <i class="mdi mdi-eye"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            @error('current_password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- New Password --}}
                                        <div class="form-group">
                                            <label for="new_password">New Password</label>
                                            <div class="input-group">
                                                <input type="password" name="new_password" id="new_password"
                                                    class="form-control @error('new_password') is-invalid @enderror"
                                                    placeholder="Enter new password (min 4 characters)">
                                                <div class="input-group-append">
                                                    <span class="input-group-text toggle-password"
                                                        data-target="#new_password" style="cursor: pointer;">
                                                        <i class="mdi mdi-eye"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            @error('new_password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Confirm New Password --}}
                                        <div class="form-group">
                                            <label for="new_password_confirmation">Confirm New Password</label>
                                            <div class="input-group">
                                                <input type="password" name="new_password_confirmation"
                                                    id="new_password_confirmation"
                                                    class="form-control @error('new_password') is-invalid @enderror"
                                                    placeholder="Confirm new password">
                                                <div class="input-group-append">
                                                    <span class="input-group-text toggle-password"
                                                        data-target="#new_password_confirmation" style="cursor: pointer;">
                                                        <i class="mdi mdi-eye"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            @error('new_password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="mdi mdi-content-save"></i> Update Password
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* KEEP ONLY THE MINIMUM CSS REQUIRED FOR LARAVEL ERRORS TO SHOW */
        .form-control.is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback.d-block {
            display: block !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const currentPasswordInput = document.getElementById('current_password');
            const newPasswordInput = document.getElementById('new_password');
            const confirmPasswordInput = document.getElementById('new_password_confirmation');

            // 1. Jump to next field on "Enter" key
            currentPasswordInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    newPasswordInput.focus();
                }
            });

            newPasswordInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    confirmPasswordInput.focus();
                }
            });

            // 2. Toggle password visibility
            const togglePasswordButtons = document.querySelectorAll('.toggle-password');
            togglePasswordButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const input = document.querySelector(targetId);
                    const icon = this.querySelector('i');

                    if (input) {
                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.classList.remove('mdi-eye');
                            icon.classList.add('mdi-eye-off');
                        } else {
                            input.type = 'password';
                            icon.classList.remove('mdi-eye-off');
                            icon.classList.add('mdi-eye');
                        }
                    }
                });
            });

            // 3. ✅ PURE BOOTSTRAP FIX: Hide errors using Bootstrap's d-none class
            const allInputs = document.querySelectorAll('input[type="password"]');
            allInputs.forEach(input => {
                input.addEventListener('input', function() {
                    // Remove the red border from the input
                    this.classList.remove('is-invalid');

                    // Find the parent input-group div
                    const inputGroup = this.closest('.input-group');
                    if (inputGroup) {
                        // Find the error div which is the NEXT element after the input-group
                        const errorDiv = inputGroup.nextElementSibling;
                        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                            // ✅ PURE BOOTSTRAP: Use Bootstrap's d-none utility class to hide it
                            errorDiv.classList.add('d-none');
                        }
                    }
                });
            });
        });
    </script>
@endsection
