<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SkyDash Login</title>

    <!-- CSS Assets -->
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
                            <div class="brand-logo text-center">
                                <img src="{{ asset('Dashboard/images/logo.svg') }}" alt="logo"
                                    style="max-width: 150px;">
                            </div>
                            <h4 class="text-center">Hello! let's get started</h4>
                            <br>

                            {{-- @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif --}}

                            <!-- Single Login Form -->
                            <form method="POST" action="{{ route('login.auto') }}">
                                @csrf

                                <div class="form-group">
                                    {{-- Added: @error('email') is-invalid @enderror inside the class --}}
                                    <input type="email" name="email"
                                        class="form-control form-control-lg @error('email') is-invalid @enderror"
                                        placeholder="Email" value="{{ old('email') }}">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    {{-- Added: @error('password') is-invalid @enderror inside the class --}}
                                    <input type="password" name="password"
                                        class="form-control form-control-lg @error('password') is-invalid @enderror"
                                        placeholder="Password">
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mt-3">
                                    <button type="submit"
                                        class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                                        LOGIN
                                    </button>
                                </div>

                                <div class="my-2 d-flex justify-content-between align-items-center">
                                    <a href="#" class="auth-link text-black">Forgot password?</a>
                                </div>

                                <div class="text-center mt-4 font-weight-light">
                                    Don't have an account?
                                    <a href="{{ route('register') }}" class="text-primary">Register</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('Dashboard/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('Dashboard/js/off-canvas.js') }}"></script>
    <script src="{{ asset('Dashboard/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('Dashboard/js/template.js') }}"></script>
    <script src="{{ asset('Dashboard/js/settings.js') }}"></script>
    <script src="{{ asset('Dashboard/js/todolist.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]').forEach(
                input => {
                    input.addEventListener('input', function() {
                        this.classList.remove('is-invalid');
                        const errorMessage = this.parentElement.querySelector('span.text-danger');
                        if (errorMessage) {
                            errorMessage.style.display = 'none';
                        }
                    });
                });
        });
    </script>
</body>

</html>
