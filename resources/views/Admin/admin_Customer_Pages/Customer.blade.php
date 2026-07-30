@extends('Components.adminheader')

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
                            <!-- ✅ Fixed: Flexbox Header with Title Left, Button Right -->
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                                <h4 class="card-title mb-0">Customer List</h4>

                                <a href="{{ route('admin.customers.create') }}" class="btn btn-primary shadow px-4 py-2">
                                    <i class="mdi mdi-plus me-1"></i> Add New Customer
                                </a>
                            </div>

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
                                                    @elseif ($item->status == 'Inactive')
                                                        <label class="badge badge-warning">Inactive</label>
                                                    @else
                                                        <label class="badge badge-warning">{{ $item->status }}</label>
                                                    @endif
                                                </td>
                                                <td>
                                                    <!-- Added d-inline-block to align horizontally -->
                                                    <a href="{{ route('admin.customers.edit', $item->id) }}"
                                                        class="btn btn-primary btn-sm d-inline-block">
                                                        <i class="mdi mdi-pencil"></i> Update
                                                    </a>

                                                    <!-- Added d-inline-block to the form as well -->
                                                    <form action="{{ route('admin.customers.delete', $item->id) }}"
                                                        method="POST" class="d-inline-block"
                                                        onsubmit="return confirm('Are you sure you want to delete this customer? This action cannot be undone.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="mdi mdi-delete"></i> Delete
                                                        </button>
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
                                                                                class="form-control">
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label>Email:</label>
                                                                            <input type="email" name="email"
                                                                                value="{{ $item->email }}"
                                                                                class="form-control">
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label>Role:</label>
                                                                            <select name="role_id" class="form-control">
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
                                                                            <select name="status" class="form-select">
                                                                                <option value="Active"
                                                                                    {{ $item->status == 'Active' ? 'selected' : '' }}>
                                                                                    Active</option>
                                                                                <option value="Inactive"
                                                                                    {{ $item->status == 'Inactive' ? 'selected' : '' }}>
                                                                                    Inactive</option>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // 1. SETUP CSRF TOKEN
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // 2. CLEAR FIELD ERROR HELPER
            window.clearFieldError = function(field) {
                var $col = $(field).closest('.form-group');
                $col.find('.invalid-feedback').remove();
                $(field).removeClass('is-invalid');
            };

            // 3. LIVE CLEARING (Input/Change events)
            $('#customerForm input, #customerForm select').on('input change', function() {
                if ($(this).val().trim() !== '') {
                    clearFieldError(this);
                }
            });

            // 4. AJAX SUBMISSION
            $('#customerForm').on('submit', function(e) {
                e.preventDefault();

                // Clear ALL existing errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                $('#global-alert-container').empty();

                var formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Show success alert
                        $('#global-alert-container').html(
                            '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            response.message +
                            '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' +
                            '</div>'
                        );
                        // Redirect to index after 1.5 seconds
                        setTimeout(function() {
                            window.location.href =
                                "{{ route('admin.customers.index') }}";
                        }, 1500);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;

                            // Loop through errors and display them
                            $.each(errors, function(key, messages) {
                                var input = $('[name="' + key + '"]');
                                if (input.length) {
                                    var formGroup = input.closest('.form-group');
                                    formGroup.find('.invalid-feedback').remove();
                                    input.addClass('is-invalid');
                                    // Append error message right under the input
                                    formGroup.append(
                                        '<div class="invalid-feedback" style="display:block;">' +
                                        messages[0] + '</div>'
                                    );
                                } else {
                                    // If field not found, show in global alert
                                    $('#global-alert-container').append(
                                        '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                        messages[0] +
                                        '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' +
                                        '</div>'
                                    );
                                }
                            });
                        } else {
                            // Server error (500, 404, etc.)
                            var errorMsg = xhr.responseJSON ? xhr.responseJSON.message :
                                'Unknown Server Error';
                            $('#global-alert-container').html(
                                '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                'Error ' + xhr.status + ': ' + errorMsg +
                                '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' +
                                '</div>'
                            );
                        }
                    }
                });
            });
        });
    </script>
@endsection
