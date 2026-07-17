@extends('components.adminheader')

@section('content')
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <!-- Button to Open the Modal -->
                            <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Add New User
                            </a>

                            <br><br>
                            <p class="card-title mb-0">Customer List</p>
                            <br>
                            <div class="table-responsive">
                                <table class="table table-striped table-borderless" id="customerTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>E-mail</th>
                                            <th>Role</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                            <th>Update</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($customers as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->fullname }}</td>
                                                <td>{{ $item->email }}</td>
                                                <td>
                                                    {{-- ✅ FIXED: Shows the role name cleanly --}}
                                                    @if ($item->role)
                                                        <span class="badge badge-primary">{{ $item->role->name }}</span>
                                                    @else
                                                        <span class="badge badge-secondary">No Role</span>
                                                    @endif
                                                </td>
                                                <td>{{ $item->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    @if ($item->status == 'Active')
                                                        <label class="badge badge-success">Active</label>
                                                    @elseif ($item->status == 'Blocked')
                                                        <label class="badge badge-danger">Blocked</label>
                                                    @else
                                                        <label class="badge badge-warning">{{ $item->status }}</label>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($item->status == 'Active')
                                                        <a class="btn btn-sm btn-danger"
                                                            href="{{ route('admin.customers.status', ['status' => 'Blocked', 'id' => $item->id]) }}"
                                                            onclick="return confirm('Are you sure you want to block this user?')">
                                                            <i class="mdi mdi-block"></i> Block
                                                        </a>
                                                    @else
                                                        <a class="btn btn-sm btn-success"
                                                            href="{{ route('admin.customers.status', ['status' => 'Active', 'id' => $item->id]) }}"
                                                            onclick="return confirm('Are you sure you want to unblock this user?')">
                                                            <i class="mdi mdi-check"></i> Unblock
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.customers.edit', $item->id) }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="mdi mdi-pencil"></i> Update
                                                    </a>

                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="confirmDelete('{{ $item->id }}')">
                                                        <i class="mdi mdi-delete"></i> Delete
                                                    </button>

                                                    <form id="delete-form-{{ $item->id }}"
                                                        action="{{ route('admin.customers.delete', $item->id) }}"
                                                        method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>

                                                    <div class="modal fade" id="updateModal{{ $item->id }}"
                                                        tabindex="-1" role="dialog">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">Update Customer</h4>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal">&times;</button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form action="{{ route('admin.customers.update') }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="id"
                                                                            value="{{ $item->id }}">

                                                                        <div class="form-group">
                                                                            <label>Name:</label>
                                                                            <input type="text" name="fullname"
                                                                                value="{{ $item->fullname }}"
                                                                                class="form-control" >
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label>Email:</label>
                                                                            <input type="email" name="email"
                                                                                value="{{ $item->email }}"
                                                                                class="form-control" >
                                                                        </div>

                                                                
                                                                        <div class="form-group">
                                                                            <label>Role:</label>
                                                                            <select name="role_id" class="form-control"
                                                                                >
                                                                                <option value="">Select Role</option>
                                                                                @foreach ($roles as $role)
                                                                                    <option value="{{ $role->id }}"
                                                                                        {{ $item->role_id == $role->id ? 'selected' : '' }}>
                                                                                        {{ $role->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label>Status:</label>
                                                                            <select name="status" class="form-control"
                                                                                >
                                                                                <option value="Active"
                                                                                    {{ $item->status == 'Active' ? 'selected' : '' }}>
                                                                                    Active</option>
                                                                                <option value="Blocked"
                                                                                    {{ $item->status == 'Blocked' ? 'selected' : '' }}>
                                                                                    Blocked</option>
                                                                            </select>
                                                                        </div>

                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary"
                                                                                data-dismiss="modal">Close</button>
                                                                            <button type="submit"
                                                                                class="btn btn-success">Save
                                                                                Changes</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No customers found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function confirmDelete(customerId) {
            if (confirm('Are you sure you want to delete this customer? This action cannot be undone.')) {
                document.getElementById('delete-form-' + customerId).submit();
            }
            return false;
        }

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    alert.style.transition = 'opacity 1s ease';
                    alert.style.opacity = '1';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                });
            }, 5000);
        });
    </script>
@endsection
