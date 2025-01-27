@extends('layouts/contentNavbarLayout')

@section('title', 'Account settings - Account')

@section('page-script')
<script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>

@if(session('swal'))
<script>
swal({
    title: "{{ session('swal.title') }}",
    text: "{{ session('swal.text') }}",
    icon: "{{ session('swal.icon') }}",
});
</script>
@endif

@if(session('activeTab') == 'changePassword')
<script>
// Switch to the "Change Password" tab after form submission
var changePasswordTab = new bootstrap.Tab(document.getElementById('changePassword-tab'));
changePasswordTab.show();
</script>
@endif
@endsection

@section('content')

<div class="card mb-4">
    <div class="container mt-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5>Account Profile</h5>
            <!-- Breadcrumb Section -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="@if(Auth::check()) 
            @switch(Auth::user()->role)
                @case('Admin')
                    {{ url('/admin-dashboard') }}
                    @break
                @case('Doctor')
                    {{ url('/doctor-dashboard') }}
                    @break
                @case('Dentist')
                    {{ url('/dentist-dashboard') }}
                    @break
                @case('Nurse')
                    {{ url('/nurse-dashboard') }}
                    @break
                @case('Midwife')
                    {{ url('/midwife-dashboard') }}
                    @break
                @case('Staff')
                    {{ url('/staff-dashboard') }}
                    @break
                @case('Patient')
                    {{ url('/patient-dashboard') }}
                    @break
                @default
                    {{ url('/') }}
            @endswitch
        @else
            {{ url('/') }}
        @endif">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page" id="breadcrumb-title">
                        Profile
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header" style="border-bottom: 2px solid #ddd;">
        <ul class="nav nav-tabs" id="accountTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active bg-white" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab"
                    aria-controls="profile" aria-selected="true" data-breadcrumb="Home / Profile">
                    <i class="fas fa-user me-2"></i> Profile
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link bg-white" id="changePassword-tab" data-bs-toggle="tab" href="#changePassword"
                    role="tab" aria-controls="changePassword" aria-selected="false"
                    data-breadcrumb="Home / Change Password">
                    <i class="fas fa-lock me-2"></i> Change Password
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body">

        <!-- Tabs Content -->
        <div class="tab-content" id="accountTabsContent">
            <!-- Personal Details Tab -->
            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <form id="UpdateProfile" action="{{ route('update-nurse') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card" style="border: 2px solid #ddd;">
                        <div class="card-header mb-4" style="border-bottom: 2px solid #ddd;">
                            <h5>Profile Details</h5>
                        </div>
                        <div class="card-body" style="border-bottom: 2px solid #ddd;">
                            <!-- Update Profile Section -->
                            <div class="col-md-6 mb-4">
                                <div class="d-flex align-items-center gap-4">
                                    <img src="{{ asset($user->profile->profile_picture ?? 'assets/img/avatars/avatar.png') }}"
                                        alt="User Avatar" class="rounded" height="100" width="100" id="profileAvatar" />
                                    <div class="button-wrapper">
                                        <label for="profileUpload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                            <span class="d-none d-sm-block">Upload new photo</span>
                                            <i class="bx bx-upload d-block d-sm-none"></i>
                                            <input type="file" id="profileUpload" class="account-file-input"
                                                name="profile_picture" hidden accept="image/png, image/jpeg" />
                                        </label>
                                        <button type="button" class="btn btn-outline-secondary account-image-reset mb-4"
                                            id="resetProfile">
                                            <i class="bx bx-reset d-block d-sm-none"></i>
                                            <span class="d-none d-sm-block">Reset</span>
                                        </button>
                                        <p class="text-muted mb-0">Allowed JPG, GIF, or PNG.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5>My Account</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="emailUpdate" class="form-label">Email</label>
                                    <input name="email" type="email" id="emailUpdate" class="form-control"
                                        placeholder="xxxx@xxx.xx" value="{{ $user->email }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nameUpdate" class="form-label">Username</label>
                                    <input name="username" type="text" id="nameUpdate" class="form-control"
                                        placeholder="Enter Username" value="{{ $user->username }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="statusUpdate" class="form-label">Status</label>
                                    <select name="status" class="form-select" id="statusUpdate" required>
                                        <option value="Active" {{ $user->status == 'Active' ? 'selected' : '' }}>
                                            Active
                                        </option>
                                        <option value="Inactive" {{ $user->status == 'Inactive' ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <span class="tf-icons bx bxs-save me-1"></span>Update Profile
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="tab-pane fade" id="changePassword" role="tabpanel" aria-labelledby="changePassword-tab">
                <form action="{{ route('change-password') }}" method="POST">
                    @csrf
                    <div class="card" style="border: 2px solid #ddd;">
                        <div class="card-header mb-4" style="border-bottom: 2px solid #ddd;">
                            <h5>Change Password</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input name="password" type="password" id="password" class="form-control"
                                        placeholder="Enter New Password" required>

                                    <!-- Show error message for password -->
                                    @error('password')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input name="confirm_password" type="password" id="confirm_password"
                                        class="form-control" placeholder="Confirm New Password" required>

                                    <!-- Show error message for confirm_password -->
                                    @error('confirm_password')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-primary">
                                <span class="tf-icons bx bxs-save me-1"></span>Update Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 
<div class="card">
    <h5 class="card-header">Delete Account</h5>
    <div class="card-body">
        <div class="mb-3 col-12 mb-0">
            <div class="alert alert-warning">
                <h6 class="alert-heading fw-medium mb-1">Are you sure you want to delete your account?</h6>
                <p class="mb-0">Once you delete your account, there is no going back. Please be certain.</p>
            </div>
        </div>
        <form id="formAccountDeactivation" onsubmit="return false">
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="accountActivation" id="accountActivation" />
                <label class="form-check-label" for="accountActivation">I confirm my account
                    deactivation</label>
            </div>
            <button type="submit" class="btn btn-danger deactivate-account">Deactivate Account</button>
        </form>
    </div>
</div> -->
@endsection