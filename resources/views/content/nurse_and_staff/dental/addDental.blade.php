@extends('layouts/contentNavbarLayout')

@section('title', 'Dental')

@section('page-script')
<script src="{{ asset('assets/js/dental.js') }}"></script>
<script src="{{ asset('assets/error_trapping/vitalsign.js') }}"></script>
<script src="{{ asset('assets/error_trapping/dental.js') }}"></script>
<script>
document.getElementById('emergencyPurposeToggle').addEventListener('change', function() {
    const emergencySection = document.getElementById('emergencyPurposeSection');
    if (this.checked) {
        emergencySection.style.display = 'block';
    } else {
        emergencySection.style.display = 'none';
    }
});
</script>
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
            <h5>Consultation</h5>
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
                            href="{{ Auth::check() && (lcfirst(Auth::user()->role) === 'nurse' || lcfirst(Auth::user()->role) === 'staff') ? url('/dentals') : url('/') }}">
                            Dental
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Add Dental Records
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<form id="dentalForm">
    @csrf

    <div id="loadingSpinner" class="loading-spinner" style="display: none;">
        <div class="dot-container">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div>
    <div class="card">
        <div class="card-body text-nowrap mr-2">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="modal-title" id="exampleModalLabel3">Add New Dental Record</h4>
                </div>

                <div class="col-md-4 d-flex align-items-end justify-content-end">
                    <h5>
                        <a href="/dentals" class="form-text">
                            <span class="bx bxs-chevron-left"></span>Back
                        </a>
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <div class="card  mt-4">
        <div class="card-body text-nowrap mr-2">
            <div class="row mb-3">
                <h6 class="text-muted text-uppercase">Patient Information</h6>
                <ul class="list-unstyled mb-2 mt-2">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-people-group"></i>
                                <span class="text-bold-600 mx-2">Family Number:</span>
                                <span class="text-primary" id="familyNumberView">{{ $patient->family_number }}</span>

                            </li>
                            <li class="d-flex align-items-center mb-1"><i class="bx bx-user"></i>
                                <span class="text-bold-600 mx-2">Full Name:</span> <span class="text-primary"
                                    id="fullNameView">{{ $patient->first_name}} {{ $patient->middle_name}}
                                    {{ $patient->last_name}}</span>
                            </li>
                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-location-dot"></i>
                                <span class="text-bold-600 mx-2">Address: </span>
                                <span class="text-primary" id="barangayView">{{ $patient->barangay_name }}
                                </span>,
                                <span class="text-primary mx-1"> {{ $patient->municipality_name }},
                                    {{ $patient->province_name }}
                                </span>
                            </li>
                        </div>
                        <div class="col-md-6">
                            <li class="d-flex align-items-center mb-1"><i class="bx bx-male"></i>
                                <span class="text-bold-600 mx-2">Gender:</span> <span class="text-primary" id="gender">
                                    {{ $patient->gender->gender_name }}</span>
                            </li>
                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-cake-candles"></i>
                                <span class="text-bold-600 mx-2">Birth Date:</span>
                                <span class="text-primary" id="birthDateView">{{ $patient->birth_date}}</span>
                            </li>

                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-arrow-up-9-1"></i>
                                <span class="text-bold-600 mx-2">Age: </span>
                                <span class="text-primary" id="ageView">{{ $age }}</span>
                            </li>
                        </div>
                    </div>
                </ul>
                <input type="hidden" name="id" id="patient_id" value="{{ $encryptedId }}">
            </div>
        </div>
    </div>

    <div class="card mb-4 mt-4">
        <div class="card-body text-nowrap mr-2">
            <h6 class="text-muted text-uppercase">For CHU / RHU Personnel Only</h6>
            <!-- Mode of Transaction & Date of Consultation -->

            <!-- Birth Place and Province -->
            <div class="row g-2">
                <div class="col mb-3">
                    <label for="bloodPressure" class="form-label">Blood Pressure</label>
                    <span id="bloodPressureAsterisk" class="text-danger">*</span>
                    <small id="bloodPressureFeedback" class="badge rounded-pill me-2"></small>
                    <input name="bloodPressure" type="text" id="bloodPressure" class="form-control"
                        placeholder="Enter Blood Pressure" required>
                </div>

                <div class="col mb-3">
                    <label for="bodyTemperature" class="form-label">Body Temperature</label>
                    <span id="bodyTemperatureAsterisk" class="text-danger">*</span>
                    <small id="bodyTempFeedback" class="badge rounded-pill me-2"> </small>
                    <input name="bodyTemperature" type="text" id="bodyTemperature" class="form-control"
                        placeholder="Enter Body Temperature" required>
                </div>
            </div>
            <!--/ Birth Place and Province  -->

            <div class="row g-2">
                <div class="col mb-3">
                    <label for="height" class="form-label">Height (cm)</label>
                    <span id="heightAsterisk" class="text-danger">*</span>
                    <small id="heightFeedback" class="badge rounded-pill me-2"> </small>
                    <input name="height" type="text" id="height" class="form-control" placeholder="Enter Height"
                        required>
                </div>
                <div class="col mb-3">
                    <label for="weight" class="form-label">Weight (kg)</label>
                    <span id="weightAsterisk" class="text-danger">*</span>
                    <small id="weightFeedback" class="badge rounded-pill me-2"> </small>
                    <input name="weight" type="text" id="weight" class="form-control" placeholder="Enter Weight"
                        required>
                </div>
            </div>
            <!--/ Blood Pressure & Body Temperature -->

            <!--/ Blood Pressure & Body Temperature -->

            <!-- Dental specific fields -->
            <div id="dentalFields" class="row g-2">
                <label for="chiefComplaints" class="form-label">Chief Complaints</label>
                <div class="row">
                    <div class="col mb-3">
                        <div class="form-check d-flex align-items-center mb-2">
                            <input type="checkbox" id="dentalType1" name="chiefComplaintsType[]" value="Toothache"
                                class="form-check-input me-2">
                            <label for="dentalType" class="form-check-label">Toothache</label>
                            <input type="number" id="numberOfDays1" name="numberOfDays1" class="form-control ms-3"
                                placeholder="Enter number of days" style="width: auto; display: none;">
                        </div>
                        <div class="form-check d-flex align-items-center mb-2">
                            <input type="checkbox" id="dentalType2" name="chiefComplaintsType[]"
                                value="Gum Swelling/Bleeding" class="form-check-input me-2">
                            <label for="dentalType" class="form-check-label">Gum Swelling/Bleeding</label>
                            <input type="number" id="numberOfDays2" name="numberOfDays2" class="form-control ms-3"
                                placeholder="Enter number of days" style="width: auto; display: none;">
                        </div>
                        <div class="form-check d-flex align-items-center mb-2">
                            <input type="checkbox" id="dentalType3" name="chiefComplaintsType[]" value="Cavities"
                                class="form-check-input me-2">
                            <label for="dentalType" class="form-check-label">Cavities</label>
                            <input type="number" id="numberOfDays3" name="numberOfDays3" class="form-control ms-3"
                                placeholder="Enter number of days" style="width: auto; display: none;">
                        </div>
                        <div class="form-check d-flex align-items-center mb-2">
                            <input type="checkbox" id="dentalType4" name="chiefComplaintsType[]"
                                value="Broken/Chipped Tooth" class="form-check-input me-2">
                            <label for="dentalType" class="form-check-label">Broken/Chipped Tooth</label>
                            <input type="number" id="numberOfDays4" name="numberOfDays4" class="form-control ms-3"
                                placeholder="Enter number of days" style="width: auto; display: none;">
                        </div>
                        <div class="form-check d-flex align-items-center mb-2">
                            <input type="checkbox" id="dentalType5" name="chiefComplaintsType[]" value="Sensitivity"
                                class="form-check-input me-2">
                            <label for="dentalType" class="form-check-label">Sensitivity</label>
                            <input type="number" id="numberOfDays5" name="numberOfDays5" class="form-control ms-3"
                                placeholder="Enter number of days" style="width: auto; display: none;">
                        </div>
                        <div class="form-check d-flex align-items-center mb-2">
                            <input type="checkbox" id="dentalType6" name="chiefComplaintsType[]" value="Others"
                                class="form-check-input me-2">
                            <label for="dentalType6" class="form-check-label">Others</label>
                        </div>
                        <div id="otherComplaintContainers" style="display: none;">
                            <div class="d-flex align-items-center mb-2">
                                <input type="text" name="otherDental[]" class="form-control ms-3"
                                    placeholder="Chief Complaints" style="width: 160px;">
                                <input type="number" name="otherDentalDays[]" class="form-control mx-3"
                                    placeholder="Number of days" style="width: 160px;">
                                <button type="button" class="btn btn-primary btn-icon rounded-circle custom-rounded-btn"
                                    onclick="addOtherComplaints()">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="col mb-3">
                        <div class="form-check d-flex align-items-center mb-2">
                            <input type="checkbox" id="dentalType7" name="chiefComplaintsType[]"
                                value="Bad Breath (Halitosis)" class="form-check-input me-2">
                            <label for="dentalType" class="form-check-label">Bad Breath (Halitosis)</label>
                            <input type="number" id="numberOfDays7" name="numberOfDays6" class="form-control ms-3"
                                placeholder="Enter number of days" style="width: auto; display: none;">
                        </div>
                        <div class="form-check d-flex align-items-center mb-2">
                            <input type="checkbox" id="dentalType8" name="chiefComplaintsType[]" value="Loose Tooth"
                                class="form-check-input me-2">
                            <label for="dentalType" class="form-check-label">Loose Tooth</label>
                            <input type="number" id="numberOfDays8" name="numberOfDays7" class="form-control ms-3"
                                placeholder="Enter number of days" style="width: auto; display: none;">
                        </div>
                        <div class="form-check d-flex align-items-center mb-2">
                            <input type="checkbox" id="dentalType9" name="chiefComplaintsType[]" value="Jaw Pain"
                                class="form-check-input me-2">
                            <label for="dentalType" class="form-check-label">Jaw Pain</label>
                            <input type="number" id="numberOfDays9" name="numberOfDays8" class="form-control ms-3"
                                placeholder="Enter number of days" style="width: auto; display: none;">
                        </div>
                        <div class="form-check d-flex align-items-center mb-2">
                            <input type="checkbox" id="dentalType10" name="chiefComplaintsType[]"
                                value="Dry Mouth (Xerostomia)" class="form-check-input me-2">
                            <label for="dentalType" class="form-check-label">Dry Mouth (Xerostomia)</label>
                            <input type="number" id="numberOfDays10" name="numberOfDays9" class="form-control ms-3"
                                placeholder="Enter number of days" style="width: auto; display: none;">
                        </div>
                        <div class="form-check d-flex align-items-center mb-2">
                            <input type="checkbox" id="dentalType11" name="chiefComplaintsType[]"
                                value="Discoloration of Teeth" class="form-check-input me-2">
                            <label for="dentalType" class="form-check-label">Discoloration of Teeth</label>
                            <input type="number" id="numberOfDays11" name="numberOfDays10" class="form-control ms-3"
                                placeholder="Enter number of days" style="width: auto; display: none;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Purpose (Optional) -->
            <div class="row g-2">
                <div class="col-12">
                    <div class="form-check mb-2">
                        <input type="checkbox" id="emergencyPurposeToggle" class="form-check-input">
                        <label for="emergencyPurposeToggle" class="form-check-label">
                            Emergency Purpose (Optional)
                        </label>
                    </div>
                    <div id="emergencyPurposeSection" style="display: none;">
                        <div class="row g-2">
                            <div class="col-md-6 mb-3">
                                <div class="mb-3">
                                    <label for="emergencyDetails" class="form-label">Emergency
                                        Details</label>
                                    <textarea name="emergencyDetails" id="emergencyDetails" rows="3"
                                        class="form-control" placeholder="Provide emergency details"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <span id="addNewTreatment" class="tf-icons fa-solid fa-floppy-disk me-1"></span> Save
                </button>
            </div>
        </div>
    </div>
</form>
@endsection