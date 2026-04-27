<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Register | Space Pedia</title>

    <!-- Bootstrap & Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('Dashboard/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard/css/vertical-layout-light/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('Dashboard/images/favicon.png') }}" />
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo text-center mb-3">
                                <img src="{{ asset('Dashboard/images/logo.svg') }}" alt="logo" width="100">
                            </div>

                            <h4>Create a new account</h4>
                            <h6 class="font-weight-light mb-4">It only takes a few steps</h6>

                            <!-- ✅ Register Form -->
                            <form class="pt-3" method="POST" action="{{ route('PostRegister') }}">
                                @csrf

                                <!-- fullname -->
                                <div class="form-group">
                                    <input type="text" name="fullname" class="form-control form-control-lg"
                                        placeholder="Enter your fullname" value="{{ old('fullname') }}" required>
                                    @error('fullname')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="form-group">
                                    <input type="email" name="email" class="form-control form-control-lg"
                                        placeholder="Enter your email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control form-control-lg"
                                        placeholder="Enter your password" required>
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="form-group">
                                    <input type="password" name="password_confirmation"
                                        class="form-control form-control-lg" placeholder="Confirm your password"
                                        required>
                                </div>

                                <!-- ✅ Submit Button -->
                                <div class="mt-3">
                                    <button type="submit"
                                        class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                                        SIGN UP
                                    </button>
                                </div>

                                <div class="text-center mt-4 font-weight-light">
                                    Already have an account?
                                    <a href="{{ route('getLogin') }}" class="text-primary">Login</a>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Files -->
    <script src="{{ asset('Dashboard/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('Dashboard/js/off-canvas.js') }}"></script>
    <script src="{{ asset('Dashboard/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('Dashboard/js/template.js') }}"></script>
    <script src="{{ asset('Dashboard/js/settings.js') }}"></script>
    <script src="{{ asset('Dashboard/js/todolist.js') }}"></script>
</body>

</html>
