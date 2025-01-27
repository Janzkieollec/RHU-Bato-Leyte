@extends('layouts/contentNavbarLayout')

@section('title', 'Family Planning')

@section('page-script')
<script src="{{ asset('assets/js/planning.js') }}"></script>
@endsection

@section('content')

<style>
#dswdNhtsRadioGroup {
    margin-top: 0px;
    border: 1px solid #ced4da;
    border-radius: 5px;
    padding: 6px;
    display: flex;
    align-items: center;
    justify-content: center;

}

#dswdNhtsRadioGroup {
    margin-right: 1px;
}

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


<div id="loadingSpinner" class="loading-spinner" style="display: none;">
    <div class="dot-container">
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>
</div>

<div class="card mb-4">
    <div class="container mt-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5>Family Planning</h5>
            <!-- Breadcrumb Section -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"> <a
                            href="{{ Auth::check() ? url(lcfirst(Auth::user()->role) . '-dashboard') : url('/') }}">
                            Home
                        </a></li>
                    <li class="breadcrumb-item active" aria-current="page" id="breadcrumb-title">
                        Family Planning
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>


<form id="addNewPlanning">
    @csrf
    <div class="card">
        <div class="card-body text-nowrap mr-2">
            <div class="row">
                <div class="col-md-8">
                    <h6 class="text-muted text-uppercase">Patient Information</h6>
                </div>

                <div class="col-md-4 d-flex align-items-end justify-content-end">
                    <h5>
                        <a href="/family-planning" class="form-text">
                            <span class="bx bxs-chevron-left"></span>Back
                        </a>
                    </h5>
                </div>
            </div>
            <div class="row">
                <ul class="list-unstyled mb-2 mt-2">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <li class="d-flex align-items-center mb-1"><i class="bx bx-user"></i>
                                <span class="text-bold-600 mx-2">Full Name:</span> <span class="text-primary"
                                    id="fullNameView">{{ $planning->first_name}} {{ $planning->middle_name}}
                                    {{ $planning->last_name}}</span>
                            </li>
                            <li class="d-flex align-items-center mb-1"><i class="bx bx-male"></i>
                                <span class="text-bold-600 mx-2">Gender:</span> <span class="text-primary" id="gender">
                                    {{ $planning->gender->gender_name }}</span>
                            </li>

                        </div>
                        <div class="col-md-6">
                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-cake-candles"></i>
                                <span class="text-bold-600 mx-2">Birth Date:</span>
                                <span class="text-primary" id="birthDateView">{{ $planning->birth_date}}</span>
                            </li>

                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-arrow-up-9-1"></i>
                                <span class="text-bold-600 mx-2">Age: </span>
                                <span class="text-primary" id="ageView">{{ $age }}</span>
                            </li>
                        </div>
                    </div>
                </ul>
                <input type="hidden" name="id" id="planning_id" value="{{ $encryptedId }}">
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="container mt-3 mb-2">
            <div class="row">
                <div class="col-md-8 mt-2">
                    <h6 class="text-muted text-uppercase">Add New Family Planning Records</h6>
                </div>
                <div class="col-md-4 d-flex align-items-center justify-content-end">
                </div>
            </div>

            <div class="row g-2">
                <div class="col-md-6 mb-4">
                    <label for="selectBarangay" class="form-label">Barangay</label>
                    <select name="barangay" class="form-select form-control" id="selectBarangay"
                        aria-label="Default select example" required>
                        <option value="" selected disabled>Select Barangay</option>
                        <!-- diri mo populate ra ang data gamit ang AJAX -->
                    </select>
                </div>
                <div class="col mb-4">
                    <label for="dswdNhtsRadioGroup" class="form-label">DSWD NHTS?</label>
                    <div id="dswdNhtsRadioGroup">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="dswdNhts" id="dswdNhtsYes" value="1"
                                required>
                            <label class="form-check-label" for="dswdNhtsYes">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="dswdNhts" id="dswdNhtsNo" value="0"
                                required>
                            <label class="form-check-label" for="dswdNhtsNo">No</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <label for="chiefComplaints" class="form-label">Family Planning Method Used</label>
                    <div class="form-check d-flex align-items-center mb-2">
                        <input type="checkbox" id="dmpType" name="methodUsedType[]" value="DMPA"
                            class="form-check-input me-2">
                        <label for="dmpType" class="form-check-label">DMPA</label>
                        <input type="number" id="dossage" name="dossage" class="form-control ms-3"
                            placeholder="Number of dossage" style="width: auto; display: none;">
                    </div>
                    <div class="form-check d-flex align-items-center mb-2">
                        <input type="checkbox" id="pillsType1" name="methodUsedType[]" value="Pills-COC"
                            class="form-check-input me-2">
                        <label for="pillsType1" class="form-check-label">Pills-COC</label>
                        <input type="number" id="pack" name="pack" class="form-control ms-3"
                            placeholder="Number of packs" style="width: auto; display: none;">
                    </div>
                    <div class="form-check d-flex align-items-center mb-2">
                        <input type="checkbox" id="pillsType2" name="methodUsedType[]" value="Pills-POP"
                            class="form-check-input me-2">
                        <label for="pillsType2" class="form-check-label">Pills-POP</label>
                        <input type="number" id="packs" name="packs" class="form-control ms-3"
                            placeholder="Number of packs" style="width: auto; display: none;">
                    </div>
                    <div class="form-check d-flex align-items-center mb-2">
                        <input type="checkbox" id="condomType" name="methodUsedType[]" value="Condoms"
                            class="form-check-input me-2">
                        <label for="condomType" class="form-check-label">Condoms</label>
                        <input type="number" id="condom-packs" name="condom-packs" class="form-control ms-3"
                            placeholder="Number of packs" style="width: auto; display: none;">
                    </div>
                </div>
            </div>

            <div class="text-end mt-5 mb-3">
                <button type="submit" class="btn btn-primary">
                    <span class="tf-icons bx bxs-save me-1"></span>Save New Patient
                </button>
            </div>
        </div>
    </div>
</form>
@endsection