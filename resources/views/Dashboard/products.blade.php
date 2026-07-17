@extends('components.adminheader')

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
                            {{-- ✅ New Add Button --}}
                            <a href="{{ route('products.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Add New Product
                            </a>

                            <br><br>
                            <p class="card-title mb-0">Product List</p>
                            <div class="table-responsive">
                                <table class="table table-striped table-borderless" id="productTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Image</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Category</th>
                                            <th>Type</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 0; @endphp
                                        @foreach ($products as $item)
                                            @php $i++; @endphp
                                            <tr>
                                                <td>{{ $i }}</td>
                                                <td>{{ $item->title }}</td>
                                                <td>
                                                    @if ($item->image)
                                                        @if (Str::startsWith($item->image, ['http://', 'https://']))
                                                            <img src="{{ $item->image }}" alt="Product Image"
                                                                class="img-thumbnail"
                                                                style="width: 80px; height: 80px; object-fit: cover;">
                                                        @else
                                                            <img src="{{ asset($item->image) }}" alt="Product Image"
                                                                class="img-thumbnail"
                                                                style="width: 80px; height: 80px; object-fit: cover;">
                                                        @endif
                                                    @else
                                                        <span class="text-muted">No image</span>
                                                    @endif
                                                </td>
                                                <td>₹{{ number_format($item->price, 2) }}</td>
                                                <td>
                                                    @if ($item->quantity > 10)
                                                        <span class="badge badge-success">{{ $item->quantity }} in
                                                            stock</span>
                                                    @elseif($item->quantity > 0 && $item->quantity <= 10)
                                                        <span class="badge badge-warning">{{ $item->quantity }} in stock
                                                            (Low)</span>
                                                    @else
                                                        <span class="badge badge-danger">Out of Stock</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge badge-success">{{ $item->category }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">{{ $item->type }}</span>
                                                </td>
                                                <td>
                                                    {{-- ✅ Update Link --}}
                                                    <a href="{{ route('products.edit', $item->id) }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="mdi mdi-pencil"></i> Edit
                                                    </a>

                                                    {{-- Delete Button --}}
                                                    <a href="#" class="btn btn-danger btn-sm"
                                                        onclick="confirmDelete({{ $item->id }})">
                                                        <i class="mdi mdi-delete"></i> Delete
                                                    </a>

                                                    <form id="delete-form-{{ $item->id }}"
                                                        action="{{ route('admin.products.delete', $item->id) }}"
                                                        method="GET" style="display: none;">
                                                        @csrf
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if ($products->isEmpty())
                                <div class="text-center py-5">
                                    <p class="text-muted">No products found. Click "Add New Product" to create one.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->

        <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2024 <a
                        href="#" target="_blank">Skydash</a>. All rights reserved.</span>
                <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i
                        class="ti-heart text-danger ml-1"></i></span>
            </div>
        </footer>
    </div>

    <script>
        // Confirm Delete
        function confirmDelete(productId) {
            if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                document.getElementById('delete-form-' + productId).submit();
            }
            return false;
        }

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                });
            }, 5000);
        });
    </script>
@endsection
