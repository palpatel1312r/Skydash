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
                            @php
                                // ✅ DYNAMIC USER DETECTION
                                if(auth()->guard('admin')->check()) {
                                    $user = auth()->guard('admin')->user();
                                    $role = 'Admin';
                                    $updateRoute = route('admin.profile.update');
                                    $dashboardRoute = route('admin.dashboard');
                                    $profileRoute = route('admin.profile');
                                } elseif(auth()->guard('customer')->check()) {
                                    $user = auth()->guard('customer')->user();
                                    $role = 'Customer';
                                    $updateRoute = route('customer.profile.update');
                                    $dashboardRoute = route('customer.dashboard');
                                    $profileRoute = route('customer.profile');
                                } else {
                                    $user = null;
                                    $role = 'Guest';
                                    $updateRoute = '#';
                                    $dashboardRoute = route('login');
                                    $profileRoute = '#';
                                }

                                // Get first letter for avatar
                                $initial = $user ? strtoupper(substr($user->name ?? 'U', 0, 1)) : 'U';
                            @endphp

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title mb-0">
                                    <i class="mdi mdi-account-circle text-primary" style="font-size: 24px;"></i>
                                    My Profile
                                </h4>
                                <span class="badge badge-info">{{ $role }}</span>
                            </div>

                            <div class="row">
                                <!-- Profile Picture Section -->
                                <div class="col-md-4 border-right text-center">
                                    <div class="profile-img-container mb-3">
                                        @if ($user && $user->profile_image)
                                            {{-- If they have a real image, show it --}}
                                            <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile Picture"
                                                id="profilePreview" class="img-fluid rounded-circle shadow-sm"
                                                style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #f0f0f0;">
                                        @else
                                            {{-- If no image, show the Large First Letter Icon --}}
                                            <div
                                                style="
                                                width: 150px; 
                                                height: 150px; 
                                                border-radius: 50%; 
                                                background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
                                                color: white; 
                                                display: flex; 
                                                align-items: center; 
                                                justify-content: center; 
                                                font-weight: bold; 
                                                font-size: 70px;
                                                text-transform: uppercase;
                                                border: 4px solid #f0f0f0;
                                                margin: 0 auto;
                                                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                                            ">
                                                {{ $initial }}
                                            </div>
                                        @endif

                                        <div class="mt-3">
                                            <h5 class="font-weight-bold mb-0">{{ $user->name ?? 'User' }}</h5>
                                            <p class="text-muted small mb-2">{{ $user->email ?? 'user@example.com' }}</p>
                                            <span class="badge badge-primary px-3 py-2">{{ $role }}</span>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- Hidden File Input -->
                                        <input type="file" id="profileImageInput" accept="image/*"
                                            style="display: none;">

                                        <!-- Change Photo Button -->
                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                            onclick="document.getElementById('profileImageInput').click();">
                                            <i class="mdi mdi-camera"></i> Change Photo
                                        </button>
                                    </div>
                                </div>

                                <!-- Profile Details Form -->
                                <div class="col-md-8">
                                    <form action="{{ $updateRoute }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">Full Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control @error('name') is-invalid @enderror"
                                                        id="name" name="name"
                                                        value="{{ old('name', $user->name ?? '') }}"
                                                        placeholder="Enter your full name" required>
                                                    @error('name')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email">Email Address <span
                                                            class="text-danger">*</span></label>
                                                    <input type="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        id="email" name="email"
                                                        value="{{ old('email', $user->email ?? '') }}"
                                                        placeholder="Enter your email" required>
                                                    @error('email')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phone">Phone Number</label>
                                                    <input type="text" class="form-control" id="phone" name="phone"
                                                        value="{{ old('phone', $user->phone ?? '') }}"
                                                        placeholder="Enter your phone number">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="role">Role</label>
                                                    <input type="text" class="form-control" id="role"
                                                        value="{{ $role }}" disabled style="background-color: #f8f9fa;">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter your current address">{{ old('address', $user->address ?? '') }}</textarea>
                                        </div>

                                        <div class="form-group mt-4">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="mdi mdi-content-save"></i> Save Changes
                                            </button>
                                            <a href="{{ $dashboardRoute }}" class="btn btn-outline-secondary">
                                                <i class="mdi mdi-arrow-left"></i> Back to Dashboard
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
    </div>

    <script>
        // Preview the selected image before uploading
        document.getElementById('profileImageInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('profilePreview').src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection