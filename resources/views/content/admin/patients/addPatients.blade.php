@extends('layouts/contentNavbarLayout')

@section('title', 'Patients')

@section('page-script')
<!--<script src="{{ asset('assets/js/ui-modals.js') }}"></script>-->
<script src="{{ asset('assets/js/patients.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initially populate with a random family number (fallback in case no data is returned)
    populateFamilyNumber([generateRandomID()]);

    // Add event listener to the reset button
    document.getElementById('resetFamilyNumber').addEventListener('click', function() {
        populateFamilyNumber([generateRandomID()]);
    });

    // Trigger when the user enters the last name or middle name
    document.getElementById('lastName').addEventListener('input', fetchFamilyNumber);
    document.getElementById('middleName').addEventListener('input', fetchFamilyNumber);

    // Hide asterisks dynamically on input
    const requiredFields = document.querySelectorAll('[required]');
    requiredFields.forEach(function(field) {
        const asterisk = field.closest('.mb-3').querySelector('.text-danger'); // Find the asterisk

        if (asterisk) {
            field.addEventListener('input', function() {
                asterisk.style.display = field.value.trim() ? 'none' : 'inline';
            });
        }
    });
});

// Function to generate a random ID
function generateRandomID() {
    return Math.floor(100000 + Math.random() * 900000);
}

// Function to populate the select dropdown with family numbers
function populateFamilyNumber(familyNumbers) {
    const select = document.getElementById('familyNumber');
    select.innerHTML = ''; // Clear existing options

    if (Array.isArray(familyNumbers)) {
        familyNumbers.forEach(function(familyNumber) {
            const option = document.createElement('option');
            option.value = familyNumber;
            option.textContent = familyNumber;
            select.appendChild(option);
        });
    }
}

// Fetch family numbers from the backend based on last name and middle name
function fetchFamilyNumber() {
    var lastName = document.getElementById('lastName').value;
    var middleName = document.getElementById('middleName').value;

    if (lastName.length > 2 && middleName.length > 2) { // Check that both fields have enough input
        // Send a request to the backend to get family numbers
        fetch(`/get-family-number/${lastName}/${middleName}`)
            .then(response => response.json())
            .then(data => {
                // If no family numbers are returned, use a random one as fallback
                const familyNumbers = data.family_numbers.length > 0 ? data.family_numbers : [generateRandomID()];
                // Populate the family number dropdown with the returned family numbers
                populateFamilyNumber(familyNumbers);
            })
            .catch(error => console.error('Error fetching family numbers:', error));
    }
}
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
                        Add Patients
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="card">
    <div class="container">
        <form id="addPatientForm">
            @csrf
            <div id="error-message" class="alert alert-danger" style="display: none;">
                <!-- Error message will be displayed here -->
            </div>

            <div id="loadingSpinner" class="loading-spinner" style="display: none;">
                <div class="dot-container">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-8">
                    <h4 class="title" id="exampleModalLabel3">Patient Enrolment Record (For New Patients)</h4>

                </div>

                <div class="col-md-4 d-flex align-items-end justify-content-end">
                    <h5>
                        <a href="/patients" class="form-text">
                            <span class="bx bxs-chevron-left"></span>Back
                        </a>
                    </h5>
                </div>
            </div>

            <div class="row g-2">
                <div class="col-md-6 mb-3 position-relative">
                    <label for="familyNumber" class="form-label">Family Number</label>
                    <div class="input-group">
                        <select name="familyNumber" id="familyNumber" class="form-control" readonly>
                            <option value="">Select Family Number</option>
                            <!-- Options will be populated by JavaScript -->
                        </select>
                        <button type="button" class="btn btn-outline-secondary" id="resetFamilyNumber"
                            title="Generate a new Family Number">
                            <span class="tf-icons bx bx-refresh"></span>
                        </button>
                    </div>
                </div>
            </div>


            <!-- Last Name & Suffix -->
            <div class="row g-2">
                <div class="col mb-3">
                    <label for="lastName" class="form-label">Last Name</label>
                    <span id="bloodPressureAsterisk" class="text-danger">*</span>
                    <input name="lastName" type="text" id="lastName" class="form-control" placeholder="Enter Last Name"
                        required>
                </div>
                <div class="col mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <span id="bloodPressureAsterisk" class="text-danger">*</span>
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
                    <label for="birthDate" class="form-label">Birth Date</label>
                    <span id="bloodPressureAsterisk" class="text-danger">*</span>
                    <small id="ageFeedback" class="form-text"></small> <!-- Feedback for age-related validation -->
                    <input name="birthDate" id="birthDate" type="date" class="form-control" required>
                </div>

                <div class="col mb-3">
                    <div style="width: 100%;">
                        <label for="selectGender" class="form-label">Sex</label>
                        <span id="bloodPressureAsterisk" class="text-danger">*</span>
                        <select name="gender" class="form-select form-control" id="selectGender"
                            aria-label="Default select example" required>
                            <option value="" selected disabled>Please Select</option>
                            <!-- populate the options usin AJAX -->
                        </select>
                    </div>
                </div>
            </div>
            <!--/ Gender & Barangay -->

            <!--/ Birth Date & Municipality -->

            <!-- Birth Place and Province -->
            <div class="row g-2">
                <div class="col mb-3">
                    <label for="selectBarangay" class="form-label">Barangay</label>
                    <span id="bloodPressureAsterisk" class="text-danger">*</span>
                    <select name="barangay" class="form-select form-control" id="selectBarangay"
                        aria-label="Default select example" required>
                        <option value="" selected disabled>Select Barangay</option>
                        <!-- diri mo populate ra ang data gamit ang AJAX -->
                    </select>
                </div>
                <div class="col mb-3">
                    <label for="contact" class="form-label">Contact Number</label>
                    <input name="contact" type="contact" id="contact" class="form-control"
                        placeholder="Enter Contact Number (e.g. 09xxxxxxxxx)">
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