<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Skydash Admin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="Dashboard/vendors/feather/feather.css">
    <link rel="stylesheet" href="Dashboard/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="Dashboard/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="Dashboard/css/vertical-layout-light/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="Dashboard/images/favicon.png" />
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo">
                                <img src="Dashboard/images/logo.svg" alt="logo">
                            </div>
                            <h4>Hello! let's get started</h4>
                            <br>
                            <form method="POST" action="{{ route('PostLogin') }}">
                                @csrf
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                
                                <div class="form-group">
                                    <input type="email" name="email" class="form-control form-control-lg"
                                        placeholder="Email" required>

                                </div>
                                <div class="form-group">

                                    <input type="password" name="password" class="form-control form-control-lg"
                                        placeholder="Password" required>
                          
                                </div>

                                <div class="mt-3">
                                    <button type="submit"
                                        class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"
                                        href="{{ route('PostLogin') }}">
                                        LOGIN
                                    </button>
                                </div>

                                <div class="my-2 d-flex justify-content-between align-items-center">

                                    <a href="#" class="auth-link text-black">Forgot password?</a>
                                </div>

                                <div class="text-center mt-4 font-weight-light">
                                    Don't have an account? <a href="{{ URL::to('getRegister') }}"
                                        class="text-primary">Register</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="Dashboard/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="Dashboard/js/off-canvas.js"></script>
    <script src="Dashboard/js/hoverable-collapse.js"></script>
    <script src="Dashboard/js/template.js"></script>
    <script src="Dashboard/js/settings.js"></script>
    <script src="Dashboard/js/todolist.js"></script>
    <!-- endinject -->
</body>

</html>
<adminfooter />
