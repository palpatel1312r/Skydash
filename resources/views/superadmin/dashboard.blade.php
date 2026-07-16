@extends('components.adminheader')

@section('content')
    <!-- partial -->

    <div class="main-panel">
        <div class="content-wrapper">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Welcome Super Admin</h3>
                            <h6 class="font-weight-normal mb-0">You have full control over the system.</h6>
                        </div>
                        <div class="col-12 col-xl-4">
                            <div class="justify-content-end d-flex">
                                <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                                    <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button"
                                        id="dropdownMenuDate2" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="true">
                                        <i class="mdi mdi-calendar"></i> Today
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Super Admin Dashboard</h4>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white p-3">
                                        <h3>Admins</h3>
                                        <h1>{{ \App\Models\Admin::where('role', 'Admin')->count() }}</h1>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white p-3">
                                        <h3>Customers</h3>
                                        <h1>{{ \App\Models\Customer::count() }}</h1>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white p-3">
                                        <h3>Products</h3>
                                        <h1>{{ \App\Models\Product::count() }}</h1>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white p-3">
                                        <h3>Invoices</h3>
                                        <h1>{{ \App\Models\Invoice::count() }}</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
    </div>
@endsection
