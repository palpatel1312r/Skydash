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
                                <h4 class="card-title">
                                    <i class="mdi mdi-account-edit-outline text-primary"></i> Update Customer
                                </h4>
                                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="mdi mdi-arrow-left"></i> Back to Customers
                                </a>
                            </div>

                            <form action="{{ route('admin.customers.update') }}" method="POST" id="customerUpdateForm">
                                @csrf
                                <input type="hidden" name="id" value="{{ $customer->id }}">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Full Name</label>
                                            <input type="text" name="fullname" id="fullname"
                                                class="form-control @error('fullname') is-invalid @enderror"
                                                value="{{ old('fullname', $customer->fullname) }}"
                                                placeholder="Enter customer's full name">
                                            @error('fullname')
                                                <div class="invalid-feedback" id="fullname-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" name="email" id="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email', $customer->email) }}"
                                                placeholder="Enter customer's email address">
                                            @error('email')
                                                <div class="invalid-feedback" id="email-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="status" id="status"
                                                class="form-select @error('status') is-invalid @enderror">
                                                <option value="" disabled
                                                    {{ old('status', $customer->status) == '' ? 'selected' : '' }}>
                                                    Select Status
                                                </option>
                                                <option value="Active"
                                                    {{ old('status', $customer->status) == 'Active' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="Inactive"
                                                    {{ old('status', $customer->status) == 'Inactive' ? 'selected' : '' }}>
                                                    Inactive</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback" id="status-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-success">
                                        <i class="mdi mdi-content-save"></i> Update Customer
                                    </button>
                                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
                                        <i class="mdi mdi-arrow-left"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <style>
        .sidebar-dark .sidebar .nav .nav-item.active>a.nav-link,
        .sidebar-dark .sidebar .nav .nav-item.active>a.nav-link:hover,
        .sidebar-dark .sidebar .nav .nav-item.active>a.nav-link:focus {
            background: #2c3e7d !important;
            color: #ffffff !important;
            border-radius: 4px;
        }

        /* Ensure the icon and text turn white */
        .sidebar-dark .sidebar .nav .nav-item.active>a.nav-link i,
        .sidebar-dark .sidebar .nav .nav-item.active>a.nav-link .menu-title {
            color: #ffffff !important;
        }

        /* ✅ ALTERNATIVE LIGHT MODE FIX */
        .sidebar-light .sidebar .nav .nav-item.active>a.nav-link,
        .sidebar-light .sidebar .nav .nav-item.active>a.nav-link:hover {
            background: #5c73f2 !important;
            color: #0d6efd !important;
        }

        .sidebar-light .sidebar .nav .nav-item.active>a.nav-link i,
        .sidebar-light .sidebar .nav .nav-item.active>a.nav-link .menu-title {
            color: #0d6efd !important;
        }

        /* Fix for the navbar toggler */
        .navbar-toggler:focus,
        .navbar-toggler:active,
        .navbar-toggler:hover {
            outline: none !important;
            box-shadow: none !important;
        }

        /* ✅ CRITICAL FIX: Stop the template from defaulting to all-active when session is empty */
        .sidebar .nav .nav-item:not(.active)>.nav-link {
            background: transparent !important;
            color: inherit !important;
        }
    </style> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to completely remove error styling and messages
            function removeFieldError(field) {
                // Remove the is-invalid class
                field.classList.remove('is-invalid');

                // Find and remove the error message div
                const formGroup = field.closest('.form-group');
                if (formGroup) {
                    const errorDiv = formGroup.querySelector('.invalid-feedback');
                    if (errorDiv) {
                        // Hide the error div
                        errorDiv.style.display = 'none';
                        errorDiv.style.visibility = 'hidden';
                        errorDiv.textContent = '';
                    }
                }
            }

            // Handle all input fields
            document.querySelectorAll('input, select').forEach(field => {
                // For text and email inputs - trigger on input
                if (field.type === 'text' || field.type === 'email') {
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
        });
    </script>
@endsection
