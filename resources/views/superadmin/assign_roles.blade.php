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
                            <h4 class="card-title">Assign Roles to Users</h4>
                            <p class="card-description">Super Admin can assign roles to Admins and Customers.</p>

                            <form action="{{ route('superadmin.assign.role') }}" method="POST" class="mt-4">
                                @csrf

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Select User Type</label>
                                            <select name="user_type" id="userType" class="form-control" required>
                                                <option value="">-- Select User Type --</option>
                                                <option value="admin">Admin</option>
                                                <option value="customer">Customer</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Select User</label>
                                            <select name="user_id" id="userId" class="form-control" required>
                                                <option value="">-- Select User --</option>
                                                <!-- Admins will be loaded via JavaScript -->
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Assign Role</label>
                                            <select name="role_id" class="form-control" required>
                                                <option value="">-- Select Role --</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-account-check"></i> Assign Role
                                    </button>
                                </div>
                            </form>

                            <hr>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Admin and Customer data from Laravel
        const admins = @json($admins);
        const customers = @json($customers);

        document.getElementById('userType').addEventListener('change', function() {
            const type = this.value;
            const userSelect = document.getElementById('userId');

            // Clear existing options
            userSelect.innerHTML = '<option value="">-- Select User --</option>';

            let data = [];
            if (type === 'admin') {
                data = admins;
            } else if (type === 'customer') {
                data = customers;
            }

            // Populate users
            data.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                // Show name + email in a clean format
                option.textContent = (user.name || user.fullname) + ' (' + user.email + ')';
                userSelect.appendChild(option);
            });
        });
    </script>
@endsection
