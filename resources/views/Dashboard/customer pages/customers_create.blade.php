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

                            <form action="{{ route('admin.customers.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Full Name</label>
                                            {{-- ✅ Added value="{{ old('fullname') }}" to retain input --}}
                                            <input type="text" name="fullname"
                                                class="form-control @error('fullname') is-invalid @enderror"
                                                value="{{ old('fullname') }}">
                                            @error('fullname')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email</label>
                                            {{-- ✅ Added value="{{ old('email') }}" to retain input --}}
                                            <input type="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email') }}">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Role</label>
                                            <select name="role_id"
                                                class="form-select @error('role_id') is-invalid @enderror">
                                                <option value="">Select role</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}"
                                                        {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('role_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="status"
                                                class="form-select @error('status') is-invalid @enderror">
                                                <option value="">Select Status</option>
                                                <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="Blocked" {{ old('status') == 'Blocked' ? 'selected' : '' }}>
                                                    Blocked</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
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
            // 1. Clear errors when typing in text inputs (text, email)
            document.querySelectorAll('input[type="text"], input[type="email"]').forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                    const errorDiv = this.closest('.form-group').querySelector('.invalid-feedback');
                    if (errorDiv) {
                        errorDiv.style.display = 'none';
                    }
                });
            });

            // 2. Clear errors when changing dropdown selections
            document.querySelectorAll('select').forEach(select => {
                select.addEventListener('change', function() {
                    this.classList.remove('is-invalid');
                    const errorDiv = this.closest('.form-group').querySelector('.invalid-feedback');
                    if (errorDiv) {
                        errorDiv.style.display = 'none';
                    }
                });
            });
        });
    </script>
@endsection
