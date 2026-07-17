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

                            <form action="{{ route('products.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" value="{{ $product->id }}">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input type="text" name="title"
                                                class="form-control @error('title') is-invalid @enderror"
                                                value="{{ old('title', $product->title) }}">
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Price (₹)</label>
                                            <input type="number" name="price"
                                                class="form-control @error('price') is-invalid @enderror"
                                                value="{{ old('price', $product->price) }}" step="0.01">
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Quantity</label>
                                            <input type="number" name="quantity"
                                                class="form-control @error('quantity') is-invalid @enderror"
                                                value="{{ old('quantity', $product->quantity) }}">
                                            @error('quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select name="category"
                                                class="form-control @error('category') is-invalid @enderror">
                                                <option value="">Select Category</option>
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
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Type</label>
                                            <select name="type" class="form-control @error('type') is-invalid @enderror">
                                                <option value="">Select Type</option>
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
                                                <div class="invalid-feedback">{{ $message }}</div>
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
                                            <input type="file" name="image"
                                                class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                            <small class="text-muted">Leave empty to keep current image</small>
                                            @error('image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
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
@endsection
