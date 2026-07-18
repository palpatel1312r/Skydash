@extends('components.adminheader')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Welcome, {{ $customer->fullname ?? 'Customer' }}</h3>
                        <h6 class="font-weight-normal mb-0">Welcome to your customer dashboard.</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Quick Links</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <a href="{{ route('customer.products') }}" class="btn btn-success btn-block">
                                    <i class="mdi mdi-package"></i> View Products
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('customer.invoices') }}" class="btn btn-info btn-block">
                                    <i class="mdi mdi-file-document"></i> My Invoices
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('customer.profile') }}" class="btn btn-warning btn-block">
                                    <i class="mdi mdi-account"></i> My Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection