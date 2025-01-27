@extends('layouts/contentNavbarLayout')

@section('title', 'Account settings - Account')

@section('page-script')
<script src="{{ asset('assets/js/pages-account-settings-account.js') }}"></script>
@if(session('swal'))
<script>
swal({
    title: "{{ session('swal.title') }}",
    text: "{{ session('swal.text') }}",
    icon: "{{ session('swal.icon') }}",
});
</script>
@endif

@if(session('activeTab') == 'myAccount')
<script>
// Switch to the "My Account" tab if session has the activeTab value
var myAccountTab = new bootstrap.Tab(document.getElementById('myAccount-tab'));
myAccountTab.show();
</script>
@endif


@if(session('activeTab') == 'changePassword')
<script>
// Switch to the "Change Password" tab after form submission
var changePasswordTab = new bootstrap.Tab(document.getElementById('changePassword-tab'));
changePasswordTab.show();
</script>
@endif

@if(session('activeTab') == 'settings')
<script>
// Switch to the "Change Password" tab after form submission
var settingTab = new bootstrap.Tab(document.getElementById('settings-tab'));
settingTab.show();
</script>
@endif

@endsection

@section('content')
<style>
.nav-link.active {
    font-weight: bold;
    /* Optional: makes the active tab text bold */
}
</style>

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
                        @if(session('activeTab') == 'myAccount')
                        My Account
                        @elseif(session('activeTab') == 'changePassword')
                        Change Password
                        @elseif(session('activeTab') == 'settings')
                        Settings
                        @else
                        Profile
                        @endif
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="border-bottom: 2px solid #ddd;">
        <ul class="nav nav-tabs" id="accountTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active bg-white" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab"
                    aria-controls="profile" aria-selected="true" data-breadcrumb="Home / Profile">
                    <i class="fas fa-user me-2"></i> Profile
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link bg-white" id="myAccount-tab" data-bs-toggle="tab" href="#myAccount" role="tab"
                    aria-controls="myAccount" aria-selected="false" data-breadcrumb="Home / My Account">
                    <i class="fa-regular fa-file-lines me-2"></i> My Account
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link bg-white" id="changePassword-tab" data-bs-toggle="tab" href="#changePassword"
                    role="tab" aria-controls="changePassword" aria-selected="false"
                    data-breadcrumb="Home / Change Password">
                    <i class="fas fa-lock me-2"></i> Change Password
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link bg-white" id="settings-tab" data-bs-toggle="tab" href="#settings" role="tab"
                    aria-controls="settings" aria-selected="false" data-breadcrumb="Home / Settings">
                    <i class="fas fa-cogs me-2"></i> Settings
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body">

        <!-- Tabs Content -->
        <div class="tab-content" id="accountTabsContent">
            <!-- Personal Details Tab -->
            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <form id="UpdateProfile" action="{{ route('update-profile') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Update Profile Section -->
                        <div class="col-md-6 mb-4">
                            <div class="card" style="border: 2px solid #ddd;">
                                <div class="card-header mb-4" style="border-bottom: 2px solid #ddd;">
                                    <h5>Update Profile</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-4">
                                        <img src="{{ asset($user->profile->profile_picture ?? 'assets/img/avatars/avatar.png') }}"
                                            alt="User Avatar" class="rounded" height="100" width="100"
                                            id="profileAvatar" />
                                        <div class="button-wrapper">
                                            <label for="profileUpload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                                <span class="d-none d-sm-block">Upload new photo</span>
                                                <i class="bx bx-upload d-block d-sm-none"></i>
                                                <input type="file" id="profileUpload" class="account-file-input"
                                                    name="profile_picture" hidden accept="image/png, image/jpeg" />
                                            </label>
                                            <button type="button"
                                                class="btn btn-outline-secondary account-image-reset mb-4"
                                                id="resetProfile">
                                                <i class="bx bx-reset d-block d-sm-none"></i>
                                                <span class="d-none d-sm-block">Reset</span>
                                            </button>
                                            <p class="text-muted mb-0">Allowed JPG, GIF, or PNG.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Update Municipal Logo Section -->
                        <div class="col-md-6 mb-4">
                            <div class="card" style="border: 2px solid #ddd;">
                                <div class="card-header mb-4" style="border-bottom: 2px solid #ddd;">
                                    <h5>Update Municipal Logo</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-4">
                                        <img src="{{ asset($user->profile->municipal_logo ?? 'assets/img/favicon/rhu-logo.ico') }}"
                                            alt="Municipal Logo" class="rounded" height="100" width="100"
                                            id="municipalLogo" />
                                        <div class="button-wrapper">
                                            <label for="municipalLogoUpload" class="btn btn-primary me-2 mb-4"
                                                tabindex="0">
                                                <span class="d-none d-sm-block">Upload new logo</span>
                                                <i class="bx bx-upload d-block d-sm-none"></i>
                                                <input type="file" id="municipalLogoUpload" class="account-file-input"
                                                    name="municipal_logo" hidden accept="image/png, image/jpeg" />
                                            </label>
                                            <button type="button"
                                                class="btn btn-outline-secondary account-image-reset mb-4"
                                                id="resetMunicipalLogo">
                                                <i class="bx bx-reset d-block d-sm-none"></i>
                                                <span class="d-none d-sm-block">Reset</span>
                                            </button>
                                            <p class="text-muted mb-0">Allowed JPG, GIF, or PNG.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Update Landing Page Picture Section -->
                        <div class="col-md-6 mb-4">
                            <div class="card" style="border: 2px solid #ddd;">
                                <div class="card-header mb-4" style="border-bottom: 2px solid #ddd;">
                                    <h5>Update Picture in Landing Page</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-4">
                                        <img src="{{ asset($user->profile->landing_page_picture ?? 'assets/img/favicon/banner.png') }}"
                                            alt="Landing Page Picture" class="rounded" height="100" width="100"
                                            id="landingPagePicture" />
                                        <div class="button-wrapper">
                                            <label for="landingPageUpload" class="btn btn-primary me-2 mb-4"
                                                tabindex="0">
                                                <span class="d-none d-sm-block">Upload new picture</span>
                                                <i class="bx bx-upload d-block d-sm-none"></i>
                                                <input type="file" id="landingPageUpload" class="account-file-input"
                                                    name="landing_page_picture" hidden accept="image/png, image/jpeg" />
                                            </label>
                                            <button type="button"
                                                class="btn btn-outline-secondary account-image-reset mb-4"
                                                id="resetLandingPage">
                                                <i class="bx bx-reset d-block d-sm-none"></i>
                                                <span class="d-none d-sm-block">Reset</span>
                                            </button>
                                            <p class="text-muted mb-0">Allowed JPG, GIF, or PNG.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <span class="tf-icons bx bxs-save me-1"></span>Update Profile
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- My Account Tab -->
            <div class="tab-pane fade" id="myAccount" role="tabpanel" aria-labelledby="myAccount-tab">
                <form action="{{ route('update-account') }}" method="POST">
                    @csrf
                    <div class="card" style="border: 2px solid #ddd;">
                        <div class="card-header mb-4" style="border-bottom: 2px solid #ddd;">
                            <h5>My Account</h5>
                        </div>
                        <div class="card-body">
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
                                        <option value="Active" {{ $user->status == 'Active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="Inactive" {{ $user->status == 'Inactive' ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-primary">
                                <span class="tf-icons bx bxs-save me-1"></span>Update Account
                            </button>
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

            <!-- Settings Tab -->
            <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                <form action="{{ route('update-setting') }}" method="POST">
                    @csrf
                    <div class="card" style="border: 2px solid #ddd;">
                        <div class="card-header mb-4" style="border-bottom: 2px solid #ddd;">
                            <h5>Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="col mb-3">
                                <label for="selectRegion" class="form-label">Region</label>
                                <select name="region" class="form-select form-control" id="selectRegion"
                                    aria-label="Default select example">
                                    <option value="" selected disabled>Select Region</option>
                                    <!-- diri mo populate ra ang data gamit ang AJAX -->
                                    @foreach($regions as $region)
                                    <option value="{{ $region->regCode }}"
                                        {{ $userProfile->region_id == $region->regCode ? 'selected' : '' }}>
                                        {{ $region->regDesc }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col mb-3">
                                <label for="selectProvince" class="form-label">Province</label>
                                <select name="province" class="form-select form-control" id="selectProvince"
                                    aria-label="Default select example">
                                    <option value="" selected disabled>Select Province</option>
                                    <!-- diri mo populate ra ang data gamit ang AJAX -->
                                    @foreach($provinces as $province)
                                    <option value="{{ $province->provCode }}"
                                        {{ $userProfile->province_id == $province->provCode ? 'selected' : '' }}>
                                        {{ $province->provDesc }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col mb-3">
                                <label for="selectMunicipality" class="form-label">City/Municipality</label>
                                <select name="municipality" class="form-select form-control" id="selectMunicipality"
                                    aria-label="Default select example">
                                    <option value="" selected disabled>Select City/Municipality</option>
                                    <!-- diri mo populate ra ang data gamit ang AJAX -->
                                    @foreach($municipalities as $municipality)
                                    <option value="{{ $municipality->citymunCode }}"
                                        {{ $userProfile->municipality_id == $municipality->citymunCode ? 'selected' : '' }}>
                                        {{ $municipality->citymunDesc }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-primary">
                                <span class="tf-icons bx bxs-save me-1"></span>Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection