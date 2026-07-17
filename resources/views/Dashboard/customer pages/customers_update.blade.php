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

                            <form action="{{ route('admin.customers.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $customer->id }}">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Full Name</label>
                                            <input type="text" name="fullname"
                                                class="form-control @error('fullname') is-invalid @enderror"
                                                value="{{ old('fullname', $customer->fullname) }}">
                                            @error('fullname')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email', $customer->email) }}">
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
                                            {{-- ✅ FIXED: name="role_id" --}}
                                            <select name="role_id"
                                                class="form-select @error('role_id') is-invalid @enderror">
                                                <option value="">Select Role</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}"
                                                        {{ old('role_id', $customer->role_id) == $role->id ? 'selected' : '' }}>
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
                                            {{-- ✅ This one was correct (name="status") --}}
                                            <select name="status"
                                                class="form-select @error('status') is-invalid @enderror">
                                                <option value="">Select Status</option>
                                                <option value="Active"
                                                    {{ old('status', $customer->status) == 'Active' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="Blocked"
                                                    {{ old('status', $customer->status) == 'Blocked' ? 'selected' : '' }}>
                                                    Blocked</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
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
@endsection
