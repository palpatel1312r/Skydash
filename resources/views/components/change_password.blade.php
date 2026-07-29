@extends('Components.adminheader')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div id="global-alert-container" style="min-height: 10px;"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">
                                    <i class="mdi mdi-lock text-primary"></i> Change Password
                                </h4>
                            </div>

                            <div class="row">
                                <div class="col-md-8 offset-md-2">
                                    <form id="changePasswordForm">
                                        @csrf

                                        {{-- Current Password --}}
                                        <div class="form-group">
                                            <label for="current_password">Current Password</label>
                                            <div class="input-group">
                                                <input type="password" name="current_password" id="current_password"
                                                    class="form-control" placeholder="Enter current password">
                                                <div class="input-group-append">
                                                    <span class="input-group-text toggle-password"
                                                        data-target="#current_password" style="cursor: pointer;">
                                                        <i class="mdi mdi-eye"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="invalid-feedback d-none"></div>
                                        </div>

                                        {{-- New Password --}}
                                        <div class="form-group">
                                            <label for="new_password">New Password</label>
                                            <div class="input-group">
                                                <input type="password" name="new_password" id="new_password"
                                                    class="form-control"
                                                    placeholder="Enter new password (min 4 characters)">
                                                <div class="input-group-append">
                                                    <span class="input-group-text toggle-password"
                                                        data-target="#new_password" style="cursor: pointer;">
                                                        <i class="mdi mdi-eye"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="invalid-feedback d-none"></div>
                                        </div>

                                        {{-- Confirm New Password --}}
                                        <div class="form-group">
                                            <label for="new_password_confirmation">Confirm New Password</label>
                                            <div class="input-group">
                                                <input type="password" name="new_password_confirmation"
                                                    id="new_password_confirmation" class="form-control"
                                                    placeholder="Confirm new password">
                                                <div class="input-group-append">
                                                    <span class="input-group-text toggle-password"
                                                        data-target="#new_password_confirmation" style="cursor: pointer;">
                                                        <i class="mdi mdi-eye"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="invalid-feedback d-none"></div>
                                        </div>

                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="mdi mdi-content-save"></i> Update Password
                                            </button>
                                        </div>
                                    </form>
                                </div>
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

        .invalid-feedback.d-none {
            display: none !important;
        }

        .invalid-feedback {
            display: block !important;
            color: #dc3545 !important;
            font-size: 80% !important;
            margin-top: 0.25rem !important;
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

            // 2. EYE TOGGLE VISIBILITY
            const togglePasswordButtons = document.querySelectorAll('.toggle-password');
            togglePasswordButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const input = document.querySelector(targetId);
                    const icon = this.querySelector('i');

                    if (input) {
                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.classList.remove('mdi-eye');
                            icon.classList.add('mdi-eye-off');
                        } else {
                            input.type = 'password';
                            icon.classList.remove('mdi-eye-off');
                            icon.classList.add('mdi-eye');
                        }
                    }
                });
            });

            // 3. CLEAR ERROR HELPER
            window.clearFieldError = function(field) {
                $(field).removeClass('is-invalid');
                // Hide the error div directly after the input group
                $(field).closest('.input-group').next('.invalid-feedback').addClass('d-none').html('');
            };

            // 4. LIVE CLEARING
            $('#changePasswordForm input').on('input', function() {
                if ($(this).val().trim() !== '') {
                    clearFieldError(this);
                }
            });

            // ✅ 5. AUTO-FOCUS TO CONFIRM PASSWORD (Press Enter to Jump)
            $('#current_password').on('keydown', function(e) {
                // 1. Check if the key pressed is "Enter"
                if (e.key === 'Enter') {
                    e.preventDefault(); // Prevent accidental form submission
                    $('#new_password').focus(); // Move cursor to New Password
                }
            });

            $('#new_password').on('keydown', function(e) {
                // 2. Check if the key pressed is "Enter"
                if (e.key === 'Enter') {
                    e.preventDefault(); // Prevent accidental form submission
                    $('#new_password_confirmation').focus(); // Move cursor to Confirm Password
                }
            });

            // 6. AJAX SUBMISSION
            $('#changePasswordForm').on('submit', function(e) {
                e.preventDefault();

                // Clear existing errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').addClass('d-none').html('');
                $('#global-alert-container').empty();

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.password.update') }}",
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
                        // Redirect to login after 2 seconds
                        setTimeout(function() {
                            window.location.href = "{{ route('login') }}";
                        }, 200);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;

                            // Loop through errors and display them
                            $.each(errors, function(key, messages) {
                                var errorMessage = messages[0].toLowerCase();

                                // ✅ IMPROVED LOGIC:
                                var inputName = key;

                                // 1. If the key is 'new_password' AND the message is about confirmation or matching
                                if (key === 'new_password' && (errorMessage.includes(
                                        'confirmation') || errorMessage.includes(
                                        'match'))) {
                                    inputName = 'new_password_confirmation';
                                }

                                // 2. If the key is 'new_password' AND the message asks to "confirm" (empty confirm field)
                                else if (key === 'new_password' && errorMessage
                                    .includes('confirm')) {
                                    inputName = 'new_password_confirmation';
                                }

                                var input = $('[name="' + inputName + '"]');
                                if (input.length) {
                                    // Remove old errors
                                    input.removeClass('is-invalid');
                                    input.closest('.input-group').next(
                                            '.invalid-feedback').removeClass('d-none')
                                        .html(messages[0]);

                                    // Apply red border
                                    input.addClass('is-invalid');
                                } else {
                                    // Fallback for general errors
                                    $('#global-alert-container').append(
                                        '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                        messages[0] +
                                        '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' +
                                        '</div>'
                                    );
                                }
                            });
                        } else {
                            // Server error (500, etc.)
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
