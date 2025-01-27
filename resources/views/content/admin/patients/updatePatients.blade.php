@extends('layouts/contentNavbarLayout')

@section('title', 'Patients')

@section('page-script')
<script src="{{ asset('assets/js/patients.js') }}"></script>
@endsection

@section('content')
<style>
/* Container for the spinner */
.loading-spinner {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: rgba(0, 0, 0, 0.3);
    /* semi-transparent background */
    z-index: 9999;
    /* Ensure it's on top */
}

/* Dot container (all dots in a row) */
.dot-container {
    display: flex;
    justify-content: space-between;
    width: 70px;
    /* Width of the container holding the dots */
}

/* Individual dot styles */
.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #3498db;
    opacity: 0;
    animation: dot-chase 1.5s infinite;
}

/* Keyframes for the dot chase effect */
@keyframes dot-chase {
    0% {
        opacity: 0;
        transform: translateY(0);
    }

    30% {
        opacity: 1;
        transform: translateY(-10px);
        /* Bounce up */
    }

    60% {
        opacity: 1;
        transform: translateY(0);
    }

    100% {
        opacity: 0;
        transform: translateY(0);
    }
}

/* Stagger the animation for each dot */
.dot:nth-child(1) {
    animation-delay: 0s;
    /* First dot starts immediately */
}

.dot:nth-child(2) {
    animation-delay: 0.3s;
    /* Second dot starts after 0.3s */
}

.dot:nth-child(3) {
    animation-delay: 0.6s;
    /* Third dot starts after 0.6s */
}
</style>


<div class="card mb-4">
    <div class="container mt-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5>Patients</h5>
            <!-- Breadcrumb Section -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ Auth::check() ? url(lcfirst(Auth::user()->role) . '-dashboard') : url('/') }}">
                            Home
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a
                            href="{{ Auth::check() && (lcfirst(Auth::user()->role) === 'nurse' || lcfirst(Auth::user()->role) === 'staff') ? url('/patients') : url('/') }}">
                            Patients
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Update Patients
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header mb-4">
        <div class="row">
            <div class="col-md-8">
                <h5 class="text-muted text-uppercase" id="exampleModalLabel3">Update Patient Information</h5>

            </div>

            <div class="col-md-4 d-flex align-items-end justify-content-end">
                <h5>
                    <a href="/patients" class="form-text">
                        <span class="bx bxs-chevron-left"></span>Back
                    </a>
                </h5>
            </div>
        </div>

        <form id="patientsFormUpdate">
            @csrf
            @method('PUT')

            <div id="loadingSpinner" class="loading-spinner" style="display: none;">
                <div class="dot-container">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
            </div>

            <input type="hidden" name="id" id="patient_id" value="{{ $encryptedPatientId }}">

            <!-- Last Name & Suffix -->
            <div class="row g-2">
                <div class="col mb-3">
                    <label for="lastNameUpdate" class="form-label">Last Name</label>
                    <input name="lastName" type="text" value="{{ $patient->last_name }}" class="form-control"
                        placeholder="Enter Last Name" required>
                </div>
                <div class="col mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <input name="firstName" type="text" value="{{ $patient->first_name }}" class="form-control"
                        placeholder="Enter First Name" required>
                </div>
            </div>
            <!-- / Last Name & Suffix -->

            <!-- Middle Name & Maiden Name -->
            <div class="row g-2">
                <div class="col mb-3">
                    <label for="middleName" class="form-label">Middle Name</label>
                    <input name="middleName" type="text" value="{{ $patient->middle_name }}" class="form-control"
                        placeholder="Enter Middle Name">
                </div>
                <div class="col mb-3">
                    <label for="suffixName" class="form-label">Suffix</label>
                    <input name="suffixName" type="text" value="{{ $patient->suffix_name }}" class="form-control"
                        placeholder="Enter Suffix (e.g. Jr., Sr., II, III)">
                </div>
            </div>
            <!--/ Middle Name & Maiden Name -->

            <!-- Birth Date & Municipality -->
            <div class="row g-2">
                <div class="col mb-3">
                    <label for="birthDate" class="form-label">Birth Date</label>
                    <input name="birthDate" type="date" value="{{ $patient->birth_date }}" class="form-control">
                </div>
                <div class="col mb-3">
                    <div style="width: 100%;">
                        <label for="selectGender" class="form-label">Sex</label>
                        <select name="gender" class="form-select form-control" aria-label="Default select example"
                            required>
                            <option value="" selected disabled>Please Select</option>
                            @foreach($gender as $genders)
                            <option value="{{ $genders->gender_id }}"
                                {{ $patient->gender_id == $genders->gender_id ? 'selected' : '' }}>
                                {{ $genders->gender_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <!--/ Birth Date & Municipality -->


            <!-- Blood Type and Contact Number -->
            <div class="row g-2 mb-4">

                <div class="col mb-3">
                    <label for="selectBarangay" class="form-label">Barangay</label>
                    <select name="barangay_id" class="form-select form-control" aria-label="Default select example">
                        <option value="" selected disabled>Select Barangay</option>
                        @foreach($barangay as $barangays)
                        <option value="{{ $barangays->brgyCode }}"
                            {{ $patient->address->barangay_id == $barangays->brgyCode ? 'selected' : '' }}>
                            {{ $barangays->brgyDesc }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col mb-3">
                    <label for="contact" class="form-label">Contact Number</label>
                    <input name="contact" type="contact" id="contact" class="form-control"
                        value="{{ $patient->contact }}">
                </div>
            </div>
            <!--/ Blood Type and Contact Number -->

            <div class="col mb-3 text-end">
                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">
                    <span id="updatePatientBtn" class="tf-icons fa-solid fa-floppy-disk me-1"></span>Update
                    Patient
                </button>
            </div>
        </form>
    </div>
</div>

@endsection