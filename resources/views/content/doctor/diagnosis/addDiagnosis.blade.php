@extends('layouts/contentNavbarLayout')

@section('title', 'Diagnosis')

@section('page-script')
<script src="{{ asset('assets/js/consultation_diagnosis.js') }}"></script>
<script src="{{ asset('assets/error_trapping/consultation_diagnosis.js') }}"></script>
<script src="{{ asset('assets/js/consultation_diagnosis_view.js') }}"></script>

@endsection

@section('content')

<style>
.diagnosisType-box,
.diagnosisTypes-box,
.medicinesType-box {
    border: 1px solid #ccc;
    background-color: white;
    position: absolute;
    width: 100%;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
}

.diagnosisType-box div,
.diagnosisTypes-box div,
.medicinesType-box div {
    padding: 10px;
    cursor: pointer;
}

.diagnosisTypes-box div:hover,
.diagnosisType-box div:hover {
    background-color: #f0f0f0;
}

.medicinesType-box {
    border: 1px solid #ccc;
    background-color: white;
    position: absolute;
    width: 100%;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    padding: 5px;
}

.medicinesType-item {
    display: flex;
    align-items: center;
    padding: 5px 0;
}

.medicinesType-item input[type="checkbox"] {
    margin-right: 10px;
}

.card-body {
    position: relative;
    /* Position relative to manage child elements */
}

.chief-complaints-container {
    flex: 1;
    /* Allow the container to grow */
    min-width: 150px;
    /* Ensure it has a minimum width */
    overflow-wrap: break-word;
    /* Break long words if necessary */
}

.chief-complaints {
    white-space: normal;
    /* Allow normal wrapping */
    display: inline;
    /* Ensure it displays inline */
}

/* Responsive behavior */
@media (max-width: 600px) {
    .chief-complaints-container {
        width: 100%;
        /* Full width on small screens */
        margin-top: 5px;
        /* Space above to separate from label */
    }

    .chief-complaints {
        display: block;
        /* Stack items vertically if needed */
    }
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

<form id="DiagnosisForm">
    @csrf

    <div class="row d-flex">
        <div class="col-md-6 d-flex">
            <div class="card mb-4 flex-fill">
                <div class="card-body text-nowrap mr-2">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-muted text-uppercase">Patient Information</h6>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-2 mt-2">
                        <div class="row g-2">
                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-people-group"></i>
                                <span class="text-bold-600 mx-2">Family Number:</span>
                                <span class="text-primary" id="familyNumberView">{{ $patient->family_number }}</span>
                            </li>
                            <li class="d-flex align-items-center mb-1"><i class="bx bx-user"></i>
                                <span class="text-bold-600 mx-2">Full Name:</span>
                                <span class="text-primary" id="fullNameView">{{ $patient->first_name}}
                                    {{ $patient->middle_name}} {{ $patient->last_name}}
                                    {{ $patient->suffix_name}}</span>
                            </li>
                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-location-dot"></i>
                                <span class="text-bold-600 mx-2">Address:</span>
                                <span class="text-primary" id="barangayView">{{ $patient->barangay_name }}</span>,
                                <span class="text-primary mx-1">{{ $patient->municipality_name }},
                                    {{ $patient->province_name }}</span>
                            </li>
                            <li class="d-flex align-items-center mb-1"><i class="bx bx-male"></i>
                                <span class="text-bold-600 mx-2">Gender:</span>
                                <span class="text-primary" id="gender">{{ $patient->gender->gender_name }}</span>
                            </li>
                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-cake-candles"></i>
                                <span class="text-bold-600 mx-2">Birth Date:</span>
                                <span class="text-primary" id="birthDateView">{{ $patient->birth_date}}</span>
                            </li>
                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-arrow-up-9-1"></i>
                                <span class="text-bold-600 mx-2">Age:</span>
                                <span class="text-primary" id="ageView">{{ $age }}</span>
                            </li>
                        </div>
                    </ul>
                    <input type="hidden" name="id" id="patient_id" value="{{ $encryptedId }}">
                </div>
            </div>
        </div>

        <div class="col-md-6 d-flex">
            <div class="card mb-4 flex-fill">
                <div class="card-body text-nowrap mr-2 h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Patient Details Header on the left -->
                        <h6 class="text-muted text-uppercase mb-0">Patient Details</h6>

                        <!-- View Record Button on the right -->
                        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#viewRecordModal">
                            <i class="fa-solid fa-eye me-1"></i> View Record
                        </a>
                    </div>

                    <ul class="list-unstyled mb-2 mt-2">
                        <div class="row g-2">
                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-droplet"></i>
                                <span class="text-bold-600 mx-2">Blood Pressure:</span>
                                <span class="text-primary">{{ $consultations->first()->blood_pressure ?? 'N/A' }}</span>
                            </li>
                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-temperature-three-quarters"></i>
                                <span class="text-bold-600 mx-2">Body Temperature:</span>
                                <span
                                    class="text-primary">{{ $consultations->first()->body_temperature ?? 'N/A' }}</span>
                            </li>
                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-ruler-vertical"></i>
                                <span class="text-bold-600 mx-2">Height:</span>
                                <span class="text-primary">{{ $consultations->first()->height ?? 'N/A' }} cm</span>
                            </li>
                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-weight-scale"></i>
                                <span class="text-bold-600 mx-2">Weight:</span>
                                <span class="text-primary">{{ $consultations->first()->weight ?? 'N/A' }} kg</span>
                            </li>
                            <li class="d-flex align-items-start mb-1 flex-wrap">
                                <i class="fa-solid fa-rectangle-list"></i>
                                <span class="text-bold-600 mx-2">Chief Complaints:</span>
                                <div class="chief-complaints-container flex-grow-1">
                                    <span class="text-primary chief-complaints">
                                        @foreach ($consultations as $consultation)
                                        {{ $consultation->chief_complaints }}
                                        ({{ $consultation->number_of_days }} days)
                                        {{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    </span>
                                </div>
                            </li>
                        </div>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Modal for View Record -->
        <div class="modal fade" id="viewRecordModal" tabindex="-1" aria-labelledby="viewRecordModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewRecordModalLabel">Patient Record</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive text-nowrap mb-3">
                            <table id="tableViewConsultation" class="table table-striped">
                                <thead class="text-uppercase">
                                    <tr>
                                        <th>Date</th>
                                        <th>Blood Pressure</th>
                                        <th>Body Temperature</th>
                                        <th>Height</th>
                                        <th>Weight</th>
                                        <th>Chief Complaints</th>
                                        <th>Diagnosis</th>
                                        <th>Description / Recommendation</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">

                                </tbody>
                            </table>
                        </div>
                        <div class="container">
                            <!-- Pagination Section -->
                            <div class="row mb-3">
                                <div class="col-12 d-flex justify-content-center justify-content-sm-end">
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination pagination-sm" id="pagination">
                                            <!-- Pagination links will be dynamically added here -->
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="row d-flex">
        <div class="col d-flex">
            <div class="card mb-4 flex-fill">
                <div class="card-body text-nowrap mr-2 h-100">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-muted text-uppercase">Diagnosis</h6>
                        </div>
                        <div class="col-md-4 d-flex align-items-end justify-content-end">
                            <h5>
                                <a href="/doctor-patients" class="form-text">
                                    <span class="bx bxs-chevron-left"></span>Back
                                </a>
                            </h5>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6 mb-3">
                            <label for="diagnosisInput" class="form-label">Diagnosis</label>
                            <div class="input-group">
                                <div style="width: 100%; position: relative;">
                                    <input type="text" class="form-control diagnosisInput"
                                        placeholder="Select or enter a diagnosis" autocomplete="off" name="diagnosis[]"
                                        required>
                                    <input type="hidden" class="diagnosis_id" name="diagnosis_id[]">
                                    <div class="diagnosisType diagnosisType-box" style="display: none;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5 mb-3">
                            <label for="icdCode" class="form-label">ICD-10 Code</label>
                            <input type="text" class="form-control" id="icdCode" placeholder="Enter ICD-10 code"
                                name="icdCode[]" required>
                        </div>

                        <div class="col-md-1 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-primary btn-icon rounded-circle custom-rounded-btn"
                                onclick="addDiagnosisFields()">+</button>
                        </div>

                        <div id="additionalDiagnoses"></div>

                        <div class="col-md-6 mb-3">
                            <label for="medicinesInput" class="form-label">Prescribe Medicines</label>
                            <div style="width: 100%; position: relative;">
                                <input type="text" class="form-control medicinesInput"
                                    placeholder="Select or enter a medicine" autocomplete="off" name="medicines[]"
                                    required>
                                <input type="hidden" class="medicine_id" name="medicine_id[]">
                                <div id="medicinesType" class="medicinesType-box" style="display: none;"></div>
                            </div>
                        </div>

                        <div class="col-md-5 mb-3">
                            <label for="medicineTypeInput" class="form-label">Type of Medicine</label>
                            <div style="width: 100%; position: relative;">
                                <input type="text" class="form-control medicineTypeInput"
                                    placeholder="Enter type of medicine (e.g., capsule, tablet, liquid)"
                                    id="medicinesInput" autocomplete="off" name="medicineType[]" required>
                                <input type="hidden" class="medicineType_id" name="medicineType_id[]">
                                <div id="medicineTypeBox" class="medicineType-box" style="display: none;"></div>
                            </div>
                        </div>

                        <div class="col-md-1 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-primary btn-icon rounded-circle custom-rounded-btn"
                                onclick="addMedicineFields()">+</button>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="dosageInput" class="form-label">Dosage</label>
                            <div style="width: 100%; position: relative;">
                                <input type="text" class="form-control dosageInput"
                                    placeholder="Enter type of dosage (e.g., mg)" id="dosageInput" autocomplete="off"
                                    name="dosage[]" required>
                            </div>
                        </div>

                        <div class="col-md-5 mb-3">
                            <label for="quantityInput" class="form-label">Quantity</label>
                            <div style="width: 100%; position: relative;">
                                <input type="text" class="form-control quantityInput" placeholder="Enter quantity"
                                    id="quantityInput" autocomplete="off" name="quantity[]" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="frequencyInput" class="form-label">Frequency</label>
                            <select name="frequency[]" class="form-select form-control" id="frequencyInput"
                                aria-label="Default select example" required>
                                <option value="" selected disabled>Select Frequency</option>
                                <option value="Once a day">Once a day</option>
                                <option value="Twice a day">Twice a day</option>
                            </select>
                        </div>

                        <div class="col-md-5 mb-3">
                            <label for="durationInput" class="form-label">Duration</label>
                            <select name="duration[]" class="form-select form-control" id="durationInput"
                                aria-label="Default select example" required>
                                <option value="" selected disabled>Select Duration</option>
                                <option value="Weekly">Weekly</option>
                                <option value="Monthly">Monthly</option>
                            </select>
                        </div>

                        <div id="additionalMedicines"></div>

                        <div class="col-md-6">
                            <label for="selectDescription" class="form-label">Description /
                                Recommendation</label>
                            <textarea class="form-control" name="description" id="selectDescription" rows="3"
                                placeholder="Enter description or recommendation"></textarea>
                        </div>

                        <!-- Follow-up Date -->
                        <div class="col-md-5 mb-3">
                            <label for="followUpDate" class="form-label">Follow-up Date</label>
                            <input type="date" class="form-control" id="followUpDate" name="followUpDate">
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <span id="addNewTreatment" class="tf-icons fa-solid fa-floppy-disk me-1"></span> Add
                            Diagnosis
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection