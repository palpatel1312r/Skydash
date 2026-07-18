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
                                                <div class="invalid-feedback d-block" style="display: none;">
                                                    {{ $message }}</div>
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
                                                <div class="invalid-feedback d-block" style="display: none;">
                                                    {{ $message }}</div>
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
                                                <div class="invalid-feedback d-block" style="display: none;">
                                                    {{ $message }}</div>
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

    {{-- ✅ REMOVED THE CSS BLOCK ENTIRELY --}}

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

            // 2. Show/Hide Password Toggle
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

            // 3. FORCE ERRORS TO SHOW ON PAGE LOAD
            const allInputs = document.querySelectorAll('input[type="password"]');
            allInputs.forEach(input => {
                if (input.classList.contains('is-invalid')) {
                    const errorDiv = input.closest('.form-group').querySelector(
                        '.invalid-feedback.d-block');
                    if (errorDiv) {
                        errorDiv.classList.remove('hidden-error'); // Just in case
                    }
                }
            });

            // 4. ✅ FINAL FIX: Clear errors by adding a class instead of changing style
            allInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const errorDiv = this.closest('.form-group').querySelector(
                        '.invalid-feedback.d-block');
                    if (errorDiv) {
                        // Add a custom class to hide it. This overrides Bootstrap.
                        errorDiv.classList.add('hidden-error');
                    }
                    this.classList.remove('is-invalid');
                });
            });
        });
    </script>

    {{-- ✅ Add this tiny CSS block right above the script --}}
    <style>
        /* This custom class completely hides the error, even against Bootstrap's !important */
        .hidden-error {
            display: none !important;
        }
    </style>
@endsection
