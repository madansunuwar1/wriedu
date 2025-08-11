@extends('layouts.admin')

@section('content')

<div class="position-relative overflow-hidden">
    <div class="position-relative overflow-hidden rounded-3">
        <img src="{{ asset('assets/images/backgrounds/bg1.jpg') }}" alt="profile-background" class="w-100" style="height: 300px; object-fit:cover; object-position: top-center;">
    </div>
    <div class="card mx-9 mt-n5">
        <div class="card-body pb-0">
            <div class="d-md-flex align-items-center justify-content-between text-center text-md-start">
                <div class="d-md-flex align-items-center">
                    <div class="rounded-circle position-relative mb-9 mb-md-0 d-inline-block">
                        <img src="{{ asset('assets/images/profile/user-1.jpg') }}" 
                             alt="user-avatar" class="img-fluid rounded-circle" width="100" height="100">
                    </div>
                    <div class="ms-0 ms-md-3 mb-9 mb-md-0">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-1">
                            <h4 class="me-7 mb-0 fs-7">{{ Auth::user()->name }} {{ Auth::user()->last }}</h4>
                        </div>
                        <p class="fs-4 mb-1">{{ Auth::user()->email }}</p>
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                            <span class="bg-success p-1 rounded-circle"></span>
                            <h6 class="mb-0 ms-2">Active</h6>
                        </div>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary px-3 shadow-none">
                    Edit Profile
                </a>
            </div>
            
            <ul class="nav nav-pills user-profile-tab mt-4 justify-content-center justify-content-md-start" id="pills-tab" role="tablist">
                <li class="nav-item me-2 me-md-3" role="presentation">
                    <button class="nav-link position-relative rounded-0 active d-flex align-items-center justify-content-center bg-transparent py-6" 
                            id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="true">
                        <i class="ti ti-user-circle me-0 me-md-6 fs-6"></i>
                        <span class="d-none d-md-block">Profile</span>
                    </button>
                </li>
                <li class="nav-item me-2 me-md-3" role="presentation">
                    <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent py-6" 
                            id="pills-settings-tab" data-bs-toggle="pill" data-bs-target="#pills-settings" type="button" role="tab" aria-controls="pills-settings" aria-selected="false">
                        <i class="ti ti-settings me-0 me-md-6 fs-6"></i>
                        <span class="d-none d-md-block">Settings</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="tab-content mx-10" id="pills-tabContent">
    <!-- Profile Tab -->
    <div class="tab-pane fade show active" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
        <div class="row">
            <div class="mx-auto">
                <div class="card">
                    <div class="card-body p-4">
                        <h4 class="fs-6 mb-4">Basic Information</h4>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <div class="form-control bg-light">{{ Auth::user()->name }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <div class="form-control bg-light">{{ Auth::user()->last }}</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <div class="form-control bg-light">{{ Auth::user()->email }}</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Account Created</label>
                            <div class="form-control bg-light">
                                {{ Auth::user()->created_at->format('F j, Y') }} ({{ Auth::user()->created_at->diffForHumans() }})
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Settings Tab -->
    <div class="tab-pane fade" id="pills-settings" role="tabpanel" aria-labelledby="pills-settings-tab" tabindex="0">
        <div class="row">
            <div class="mx-auto">
                <div class="card">
                    <div class="card-body p-4">
                        <h4 class="fs-6 mb-4">Update Profile</h4>
                        
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="last" value="{{ Auth::user()->last }}" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" value="{{ Auth::user()->email }}" required>
                            </div>
                            
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary">
                                    Update Profile
                                </button>
                            </div>
                        </form>
                        
                        <div class="border-top mt-5 pt-4">
                            <h5 class="mb-4">Change Password</h5>
                            <form action="{{ route('password.update') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" name="password_confirmation" required>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        Change Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light {
        background-color: #f8f9fa;
    }
    .form-control.bg-light {
        border: none;
        padding: 0.5rem 0.75rem;
    }
    .nav-pills .nav-link.active {
        color: #4f46e5;
        background-color: rgba(79, 70, 229, 0.1);
    }
    .nav-pills .nav-link {
        color: #6b7280;
    }
</style>
@endsection