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
                                        class="mdi mdi-{{ isset($product) ? 'package-variant-closed' : 'package-plus' }} text-primary"></i>
                                    {{ isset($product) ? 'Update Product' : 'Add New Product' }}
                                </h4>
                                <a href="{{ route('products') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="mdi mdi-arrow-left"></i> Back to Products
                                </a>
                            </div>
                            <form action="{{ isset($product) ? route('products.update') : route('products.add') }}"
                                method="POST" enctype="multipart/form-data" id="productForm">
                                @csrf
                                @if (isset($product))
                                    @method('PUT')
                                    <input type="hidden" name="id" value="{{ $product->id }}">
                                @endif

                                <div id="global-alert-container" style="min-height: 10px;"></div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input type="text" name="title" id="title" class="form-control"
                                                value="{{ old('title', $product->title ?? '') }}"
                                                placeholder="Enter product title">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Price (₹)</label>
                                            <input type="number" name="price" id="price" class="form-control"
                                                value="{{ old('price', $product->price ?? '') }}" step="0.01"
                                                placeholder="0.00">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Quantity</label>
                                            <input type="number" name="quantity" id="quantity" class="form-control"
                                                value="{{ old('quantity', $product->quantity ?? '') }}"
                                                placeholder="Enter stock quantity">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select name="category" id="category" class="form-select">
                                                <option value=""
                                                    {{ empty(old('category', $product->category ?? '')) ? 'selected' : '' }}
                                                    disabled>
                                                    Select Category
                                                </option>
                                                <option value="Accessories"
                                                    {{ old('category', $product->category ?? '') == 'Accessories' ? 'selected' : '' }}>
                                                    Accessories
                                                </option>
                                                <option value="Shoes"
                                                    {{ old('category', $product->category ?? '') == 'Shoes' ? 'selected' : '' }}>
                                                    Shoes
                                                </option>
                                                <option value="Clothes"
                                                    {{ old('category', $product->category ?? '') == 'Clothes' ? 'selected' : '' }}>
                                                    Clothes
                                                </option>
                                                <option value="Electronics"
                                                    {{ old('category', $product->category ?? '') == 'Electronics' ? 'selected' : '' }}>
                                                    Electronics
                                                </option>
                                                <option value="Home"
                                                    {{ old('category', $product->category ?? '') == 'Home' ? 'selected' : '' }}>
                                                    Home & Living
                                                </option>
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Type</label>
                                            <select name="type" id="type" class="form-select">
                                                <option value=""
                                                    {{ empty(old('type', $product->type ?? '')) ? 'selected' : '' }}
                                                    disabled>
                                                    Select Type
                                                </option>
                                                <option value="Best Sellers"
                                                    {{ old('type', $product->type ?? '') == 'Best Sellers' ? 'selected' : '' }}>
                                                    Best Sellers
                                                </option>
                                                <option value="New Arrivals"
                                                    {{ old('type', $product->type ?? '') == 'New Arrivals' ? 'selected' : '' }}>
                                                    New Arrivals
                                                </option>
                                                <option value="Sale"
                                                    {{ old('type', $product->type ?? '') == 'Sale' ? 'selected' : '' }}>
                                                    Sale
                                                </option>
                                                <option value="Featured"
                                                    {{ old('type', $product->type ?? '') == 'Featured' ? 'selected' : '' }}>
                                                    Featured
                                                </option>
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Image</label>
                                            @if (isset($product) && $product->image)
                                                <div class="mb-2">
                                                    <img src="{{ asset($product->image) }}" alt="Current Image"
                                                        width="100" class="img-thumbnail">
                                                </div>
                                            @endif
                                            <input type="file" name="image" id="image" class="form-control"
                                                accept="image/*">
                                            <small class="text-muted">Max size: 2MB (JPG, PNG,
                                                GIF){{ isset($product) ? ' - Leave empty to keep current image' : '' }}</small>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" id="description" class="form-control" rows="3"
                                        placeholder="Enter product description">{{ old('description', $product->description ?? '') }}</textarea>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save"></i>
                                        {{ isset($product) ? 'Update Product' : 'Save Product' }}
                                    </button>
                                    <a href="{{ route('products') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="mdi mdi-arrow-left"></i> Back to Products
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
        .form-control.is-invalid {
            border-color: #dc3545 !important;
        }

        .is-invalid~.invalid-feedback {
            display: block !important;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

            // 3. LIVE CLEARING (Input/Change/Focus/Blur events)
            $('#productForm input, #productForm select, #productForm textarea').on('input change focus blur',
                function() {
                    if ($(this).val().trim() !== '' || $(this).is('select')) {
                        clearFieldError(this);
                    }
                });

            // 4. AJAX SUBMISSION
            $('#productForm').on('submit', function(e) {
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
                        // Show success message
                        $('#global-alert-container').html(
                            '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            response.message +
                            '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' +
                            '</div>'
                        );
                        // Redirect after 1.5 seconds
                        setTimeout(function() {
                            window.location.href = "{{ route('products') }}";
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
