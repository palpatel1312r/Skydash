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
                                    <i class="mdi mdi-package-plus text-primary"></i> Update Product
                                </h4>
                                <a href="{{ route('products') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="mdi mdi-arrow-left"></i> Back to Products
                                </a>
                            </div>

                            <form action="{{ route('products.update') }}" method="POST" enctype="multipart/form-data"
                                id="productUpdateForm">
                                @csrf
                                <input type="hidden" name="id" value="{{ $product->id }}">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input type="text" name="title" id="title"
                                                class="form-control @error('title') is-invalid @enderror"
                                                value="{{ old('title', $product->title) }}"
                                                placeholder="Enter product title">
                                            @error('title')
                                                <div class="invalid-feedback" id="title-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Price (₹)</label>
                                            <input type="number" name="price" id="price"
                                                class="form-control @error('price') is-invalid @enderror"
                                                value="{{ old('price', $product->price) }}" step="0.01"
                                                placeholder="0.00">
                                            @error('price')
                                                <div class="invalid-feedback" id="price-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Quantity</label>
                                            <input type="number" name="quantity" id="quantity"
                                                class="form-control @error('quantity') is-invalid @enderror"
                                                value="{{ old('quantity', $product->quantity) }}"
                                                placeholder="Enter stock quantity">
                                            @error('quantity')
                                                <div class="invalid-feedback" id="quantity-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select name="category" id="category"
                                                class="form-control @error('category') is-invalid @enderror">
                                                {{-- ✅ PLACEHOLDER FOR DROPDOWN --}}
                                                <option value="" disabled
                                                    {{ old('category', $product->category) == '' ? 'selected' : '' }}>
                                                    Select Category
                                                </option>
                                                <option value="Accessories"
                                                    {{ old('category', $product->category) == 'Accessories' ? 'selected' : '' }}>
                                                    Accessories</option>
                                                <option value="Shoes"
                                                    {{ old('category', $product->category) == 'Shoes' ? 'selected' : '' }}>
                                                    Shoes</option>
                                                <option value="Clothes"
                                                    {{ old('category', $product->category) == 'Clothes' ? 'selected' : '' }}>
                                                    Clothes</option>
                                                <option value="Electronics"
                                                    {{ old('category', $product->category) == 'Electronics' ? 'selected' : '' }}>
                                                    Electronics</option>
                                                <option value="Home"
                                                    {{ old('category', $product->category) == 'Home' ? 'selected' : '' }}>
                                                    Home & Living</option>
                                            </select>
                                            @error('category')
                                                <div class="invalid-feedback" id="category-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Type</label>
                                            <select name="type" id="type"
                                                class="form-control @error('type') is-invalid @enderror">
                                                {{-- ✅ PLACEHOLDER FOR DROPDOWN --}}
                                                <option value="" disabled
                                                    {{ old('type', $product->type) == '' ? 'selected' : '' }}>
                                                    Select Type
                                                </option>
                                                <option value="Best Sellers"
                                                    {{ old('type', $product->type) == 'Best Sellers' ? 'selected' : '' }}>
                                                    Best Sellers</option>
                                                <option value="New Arrivals"
                                                    {{ old('type', $product->type) == 'New Arrivals' ? 'selected' : '' }}>
                                                    New Arrivals</option>
                                                <option value="Sale"
                                                    {{ old('type', $product->type) == 'Sale' ? 'selected' : '' }}>Sale
                                                </option>
                                                <option value="Featured"
                                                    {{ old('type', $product->type) == 'Featured' ? 'selected' : '' }}>
                                                    Featured</option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback" id="type-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Image</label>
                                            @if ($product->image)
                                                <div class="mb-2">
                                                    <img src="{{ asset($product->image) }}" alt="Current Image"
                                                        width="100" class="img-thumbnail">
                                                </div>
                                            @endif
                                            <input type="file" name="image" id="image"
                                                class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                            <small class="text-muted">Leave empty to keep current image</small>
                                            @error('image')
                                                <div class="invalid-feedback" id="image-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                        rows="3" placeholder="Enter product description">{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback" id="description-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save"></i> Update Product
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to completely remove error styling and messages
            function removeFieldError(field) {
                // Remove the is-invalid class
                field.classList.remove('is-invalid');

                // Find and remove the error message div
                const formGroup = field.closest('.form-group');
                if (formGroup) {
                    const errorDiv = formGroup.querySelector('.invalid-feedback');
                    if (errorDiv) {
                        // Hide the error div
                        errorDiv.style.display = 'none';
                        errorDiv.style.visibility = 'hidden';
                        errorDiv.textContent = '';
                    }
                }
            }

            // Handle all input fields
            document.querySelectorAll('input, select, textarea').forEach(field => {
                // For text, email, number, textarea inputs - trigger on input
                if (field.type === 'text' || field.type === 'email' || field.type === 'number' || field
                    .tagName === 'TEXTAREA') {
                    field.addEventListener('input', function() {
                        if (this.value.trim() !== '') {
                            removeFieldError(this);
                        }
                    });
                }

                // For select dropdowns and file inputs - trigger on change
                if (field.tagName === 'SELECT' || field.type === 'file') {
                    field.addEventListener('change', function() {
                        if (this.value !== '') {
                            removeFieldError(this);
                        }
                    });
                }
            });

            // Force clear errors when any field gets focus
            document.querySelectorAll('input, select, textarea').forEach(field => {
                field.addEventListener('focus', function() {
                    removeFieldError(this);
                });
            });
        });
    </script>
@endsection
