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

                            <form
                                action="{{ isset($customer) ? route('admin.customers.update') : route('admin.customers.store') }}"
                                method="POST" id="customerForm" novalidate>
                                @csrf
                                @if (isset($customer))
                                    @method('PUT')
                                    <input type="hidden" name="id" value="{{ $customer->id }}">
                                @endif

                                <div id="global-alert-container" style="min-height: 10px;"></div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Full Name</label>
                                            <input type="text" name="fullname" id="fullname" class="form-control"
                                                value="{{ old('fullname', $customer->fullname ?? '') }}"
                                                placeholder="Enter customer's full name">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" name="email" id="email" class="form-control"
                                                value="{{ old('email', $customer->email ?? '') }}"
                                                placeholder="Enter customer's email address">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="status" id="status" class="form-select">
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
                                            <div class="invalid-feedback"></div>
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
                        }, 10);
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
                                    // Append error message
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
