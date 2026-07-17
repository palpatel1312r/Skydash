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
                            @php
                                $currentUser = auth()->guard('admin')->user();
                                $roles = \App\Models\Role::all();
                            @endphp

                            {{-- SUPER ADMIN CONTROLS --}}
                            @if ($currentUser->role_id == 2) {{-- Assuming Superadmin is ID 2 --}}
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAdminModal">
                                    <i class="mdi mdi-plus"></i> Add New Admin
                                </button>
                            @endif

                            <br><br>
                            <p class="card-title mb-0">Admin List</p>
                            <div class="table-responsive">
                                <table class="table table-striped table-borderless">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($admins as $admin)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $admin->name }}</td>
                                                <td>{{ $admin->email }}</td>
                                                <td>
                                                    <span class="badge badge-primary">
                                                        {{ $admin->role ? $admin->role->name : 'No Role' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($admin->status == 'Active')
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Blocked</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($currentUser->role_id == 2)
                                                        <button type="button" class="btn btn-primary btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#updateAdminModal{{ $admin->id }}">
                                                            <i class="mdi mdi-pencil"></i> Edit
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $admin->id }})">
                                                            <i class="mdi mdi-delete"></i> Delete
                                                        </button>
                                                        <form id="delete-form-{{ $admin->id }}"
                                                            action="{{ route('superadmin.admins.delete', $admin->id) }}"
                                                            method="POST" style="display:none;">
                                                            @csrf @method('DELETE')
                                                        </form>
                                                    @else
                                                        <span class="badge badge-secondary">View Only</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ONLY SUPER ADMIN SEES MODALS --}}
    @if ($currentUser->role_id == 2)
        <!-- Add Admin Modal -->
        <div class="modal fade" id="addAdminModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add New Admin</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form action="{{ route('superadmin.admins.add') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" >
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" >
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" >
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <select name="role_id" class="form-control">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="Active">Active</option>
                                    <option value="Blocked">Blocked</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Modals -->
        @foreach ($admins as $admin)
            <div class="modal fade" id="updateAdminModal{{ $admin->id }}">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Update Admin</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <form action="{{ route('superadmin.admins.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $admin->id }}">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" value="{{ $admin->name }}" class="form-control" >
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" value="{{ $admin->email }}" class="form-control" >
                                </div>
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="Enter new password">
                                </div>
                                <div class="form-group">
                                    <label>Role</label>
                                    <select name="role_id" class="form-control">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" {{ $admin->role_id == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="Active" {{ $admin->status == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Blocked" {{ $admin->status == 'Blocked' ? 'selected' : '' }}>Blocked</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this admin?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
@endsection