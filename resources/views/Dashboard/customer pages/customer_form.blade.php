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
                                    <i
                                        class="mdi mdi-{{ isset($customer) ? 'account-edit-outline' : 'account-plus-outline' }} text-primary"></i>
                                    {{ isset($customer) ? 'Update Customer' : 'Add New Customer' }}
                                </h4>
                                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="mdi mdi-arrow-left"></i> Back to Customers
                                </a>
                            </div>

                            {{-- DYNAMIC FORM ACTION --}}
                            <form
                                action="{{ isset($customer) ? route('admin.customers.update') : route('admin.customers.store') }}"
                                method="POST" id="customerForm" novalidate>
                                @csrf
                                @if (isset($customer))
                                    @method('PUT')
                                    <input type="hidden" name="id" value="{{ $customer->id }}">
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Full Name</label>
                                            <input type="text" name="fullname" id="fullname"
                                                class="form-control @error('fullname') is-invalid @enderror"
                                                value="{{ old('fullname', $customer->fullname ?? '') }}"
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
                                                value="{{ old('email', $customer->email ?? '') }}"
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
                                                <option value=""
                                                    {{ old('status', $customer->status ?? '') == '' ? 'selected' : '' }}>
                                                    Select Status
                                                </option>
                                                <option value="Active"
                                                    {{ old('status', $customer->status ?? '') == 'Active' ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="Inactive"
                                                    {{ old('status', $customer->status ?? '') == 'Inactive' ? 'selected' : '' }}>
                                                    Inactive
                                                </option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback" id="status-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-{{ isset($customer) ? 'success' : 'primary' }}">
                                        <i class="mdi mdi-content-save"></i>
                                        {{ isset($customer) ? 'Update Customer' : 'Save Customer' }}
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

    <style>
        .sidebar-dark .sidebar .nav .nav-item.active>a.nav-link,
        .sidebar-dark .sidebar .nav .nav-item.active>a.nav-link:hover,
        .sidebar-dark .sidebar .nav .nav-item.active>a.nav-link:focus {
            background: #2c3e7d !important;
            color: #ffffff !important;
            border-radius: 4px;
        }

        .sidebar-dark .sidebar .nav .nav-item.active>a.nav-link i,
        .sidebar-dark .sidebar .nav .nav-item.active>a.nav-link .menu-title {
            color: #ffffff !important;
        }

        .sidebar-light .sidebar .nav .nav-item.active>a.nav-link,
        .sidebar-light .sidebar .nav .nav-item.active>a.nav-link:hover {
            background: #5c73f2 !important;
            color: #0d6efd !important;
        }

        .sidebar-light .sidebar .nav .nav-item.active>a.nav-link i,
        .sidebar-light .sidebar .nav .nav-item.active>a.nav-link .menu-title {
            color: #0d6efd !important;
        }

        .navbar-toggler:focus,
        .navbar-toggler:active,
        .navbar-toggler:hover {
            outline: none !important;
            box-shadow: none !important;
        }

        .form-control.is-invalid {
            border-color: #dc3545 !important;
        }

        .is-invalid~.invalid-feedback {
            display: block !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function removeFieldError(field) {
                field.classList.remove('is-invalid');
                const formGroup = field.closest('.form-group');
                if (formGroup) {
                    const errorDiv = formGroup.querySelector('.invalid-feedback');
                    if (errorDiv) {
                        // Delete the error entirely
                        errorDiv.remove();
                    }
                }
            }

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

            document.querySelectorAll('input, select').forEach(field => {
                field.addEventListener('focus', function() {
                    removeFieldError(this);
                });
            });
        });
    </script>
@endsection
