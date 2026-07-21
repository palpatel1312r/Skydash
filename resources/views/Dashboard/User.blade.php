@extends('components.adminheader')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            {{-- Alert Messages --}}
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

            {{-- User List Table --}}
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            {{-- REDIRECT TO USER FORM PAGE --}}
                            <a href="{{ route('admin.user.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Add New User
                            </a>

                            <br><br>
                            <h4 class="card-title">User List</h4>
                            <br>

                            <div class="table-responsive">
                                <table class="table table-striped table-borderless" id="userTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>E-mail</th>
                                            <th>User Type</th>
                                            <th>Role</th>
                                            <th>Created At</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($users as $user)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $user['name'] }}</td>
                                                <td>{{ $user['email'] }}</td>

                                                <td>
                                                    @if ($user['guard'] == 'customer')
                                                        <span class="badge badge-success">Customer</span>
                                                    @elseif ($user['role_name'] == 'Super Admin')
                                                        <span class="badge badge-danger">Super Admin</span>
                                                    @else
                                                        <span class="badge badge-warning text-dark">Admin</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge badge-primary">{{ $user['role_name'] }}</span>
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($user['created_at'])->format('M d, Y') }}</td>
                                                <td class="text-center">
                                                    {{-- ✅ UPDATED: PURE LINK TO EDIT PAGE --}}
                                                    <a href="{{ route('admin.user.edit', ['id' => $user['id'], 'guard' => $user['guard']]) }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="mdi mdi-pencil"></i> Update
                                                    </a>

                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="confirmDeleteUser('{{ $user['id'] }}', '{{ $user['guard'] }}')">
                                                        <i class="mdi mdi-delete"></i> Delete
                                                    </button>

                                                    <form id="delete-user-form-{{ $user['id'] }}-{{ $user['guard'] }}"
                                                        action="{{ route('admin.user.destroy', ['id' => $user['id'], 'guard' => $user['guard']]) }}"
                                                        method="POST" style="display: none;">
                                                        @csrf @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No users found.</td>
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

    <style>
        .form-control.is-invalid {
            border-color: #dc3545 !important;
        }

        .is-invalid~.invalid-feedback {
            display: block !important;
        }
    </style>

    <script>
        function confirmDeleteUser(userId, guard) {
            if (confirm('Are you sure you want to delete this user?')) {
                document.getElementById('delete-user-form-' + userId + '-' + guard).submit();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.querySelectorAll('.alert').forEach(alert => {
                    alert.style.transition = 'opacity 1s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.style.display = 'none', 500);
                });
            }, 5000);
        });
    </script>
@endsection
