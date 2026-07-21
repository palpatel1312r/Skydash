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
                                    <i class="mdi mdi-account-plus-outline text-primary"></i> Add New Customer
                                </h4>
                                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="mdi mdi-arrow-left"></i> Back to Customers
                                </a>
                            </div>

                         <form action="{{ route('admin.customers.store') }}" method="POST" id="customerForm" novalidate>
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Full Name</label>
                                            <input type="text" name="fullname" id="fullname"
                                                class="form-control @error('fullname') is-invalid @enderror"
                                                value="{{ old('fullname') }}" placeholder="Enter customer's full name">
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
                                                value="{{ old('email') }}" placeholder="Enter customer's email address">
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
                                                <option value="">Select Status</option>
                                                <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="Inactive"
                                                    {{ old('status') == 'Inactive' ? 'selected' : '' }}>
                                                    Inactive</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback" id="status-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save"></i> Save Customer
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


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to completely remove error styling and messages
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
