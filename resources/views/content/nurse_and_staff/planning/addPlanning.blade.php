@extends('layouts/contentNavbarLayout')

@section('title', 'Family Planning')

@section('page-script')
<!--<script src="{{ asset('assets/js/ui-modals.js') }}"></script>-->
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

<div class="card">
    <div class="container">
        <form id="addPlanningForm">
            @csrf
            <div id=" error-message" class="alert alert-danger" style="display: none;">
                <!-- Error message will be displayed here -->
            </div>

            <div class="row mt-4">
                <div class="col-md-8">
                    <h4 class="title" id="exampleModalLabel3">Family Planning Enrolment Record</h4>

                </div>

                <div class="col-md-4 d-flex align-items-end justify-content-end">
                    <h5>
                        <a href="/family-planning" class="form-text">
                            <span class="bx bxs-chevron-left"></span>Back
                        </a>
                    </h5>
                </div>
            </div>

            <!-- Last Name & Suffix -->
            <div class="row g-2">
                <div class="col mb-3">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input name="lastName" type="text" id="lastName" class="form-control" placeholder="Enter Last Name"
                        required>
                </div>
                <div class="col mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <input name="firstName" type="text" id="firstName" class="form-control"
                        placeholder="Enter First Name" required>
                </div>
            </div>
            <!-- / Last Name & Suffix -->

            <!-- First Name & Mother's Name -->
            <div class="row g-2">
                <div class="col mb-3">
                    <label for="middleName" class="form-label">Middle Name</label>
                    <input name="middleName" type="text" id="middleName" class="form-control"
                        placeholder="Enter Middle Name">
                </div>
                <div class="col mb-3">
                    <label for="suffixName" class="form-label">Suffix</label>
                    <input name="suffixName" type="text" id="suffixName" class="form-control"
                        placeholder="Enter Suffix (e.g. Jr., Sr., II, III)">
                </div>

            </div>
            <!--/ First Name & Mother's Name -->

            <!-- Gender & Barangay -->
            <div class="row g-2">
                <div class="col mb-3">
                    <div style="width: 100%;">
                        <label for="selectGender" class="form-label">Gender</label>
                        <select name="gender" class="form-select form-control" id="selectGender"
                            aria-label="Default select example" required>
                            <option value="" selected disabled>Select Gender</option>
                            <!-- populate the options usin AJAX -->
                        </select>
                    </div>
                </div>
                <div class="col mb-3">
                    <label for="birthDate" class="form-label">Birth Date</label>
                    <input name="birthDate" id="birthDate" type="date" class="form-control" required>
                </div>
            </div>
            <!--/ Gender & Barangay -->

            <!--/ Birth Date & Municipality -->

            <!-- Birth Place and Province -->
            <div class="row g-2">
                <div class="col-md-6 mb-3">
                    <label for="selectBarangay" class="form-label">Barangay</label>
                    <select name="barangay" class="form-select form-control" id="selectBarangay"
                        aria-label="Default select example" required>
                        <option value="" selected disabled>Select Barangay</option>
                        <!-- diri mo populate ra ang data gamit ang AJAX -->
                    </select>
                </div>
                <div class="col mb-3">
                    <label for="dswdNhtsRadioGroup" class="form-label">SDWD NHTS?</label>
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
            <!--/ Birth Place and Province  -->

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
        </form>
    </div>
</div>
@endsection