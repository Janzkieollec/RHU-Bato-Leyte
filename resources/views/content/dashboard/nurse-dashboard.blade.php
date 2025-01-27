@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
<!-- Include Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<!-- Include Leaflet JS -->

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
function updateRunningTime() {
    const timeElement = document.getElementById('runningTime');
    const now = new Date();
    const options = {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    };
    timeElement.textContent = now.toLocaleTimeString('en-US', options);
}

// Initialize the running time and update it every second
updateRunningTime();
setInterval(updateRunningTime, 1000);
</script>
@endsection

@section('page-script')
<script src="{{ asset('assets/js/nurse-analytics.js') }}"></script>
@endsection

@section('content')
<style>
/* Add this CSS to your stylesheet */

.hover-card {
    position: relative;
    transition: box-shadow 0.3s ease, transform 0.3s ease;
    border-radius: 8px;
    overflow: hidden;
}

.hover-card:before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 0;
    background-color: #adc178;
    transition: height 0.3s ease;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
}

.hover-card:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    transform: translateY(-5px);
}

.hover-card:hover:before {
    height: 4px;
}


#weeklyCalendar {
    display: flex;
    flex-wrap: wrap;
    /* Ensures items wrap on smaller screens */
    gap: 10px;
    /* Adds spacing between items */
}

.day {
    text-align: center;
    padding: 10px;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    flex: 1;
    margin: 0 5px;
    background-color: #f9f9f9;
}

.today {
    background-color: #ffeb3b;
    /* Highlight color for today */
    font-weight: bold;
}

/* Media queries for smaller screens */
@media (max-width: 768px) {
    .day {
        flex: 0 0 48%;
        /* 2 items per row */
    }
}

@media (max-width: 576px) {
    .day {
        flex: 0 0 100%;
        /* Stacks items */
    }
}

.floating-effect {
    /* Adds a shadow below each feature for a floating effect */
    filter: drop-shadow(0px 0px 10px rgba(0, 0, 0, 0.4));
    /* Optional: slight blur for more realism */
    opacity: 1;
}

#map {
    width: 100%;
    height: 600px;
    /* Adjust height as needed */
}
</style>

<div class="col mb-4">
    <div class="card h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h4 id="todayDate" class="text-secondary"></h4>
                <h4 id="runningTime" class="text-muted float-end"></h4>
            </div>
            <h5 class="card-title text-dark">Weekly Overview</h5>
            <div id="weeklyCalendar" class="d-flex justify-content-between"></div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Column for Total Patients -->
    <div class="col-lg-4 mb-4">
        <div class="card hover-card h-100">
            <div class="d-flex flex-column">
                <!-- Card Header with Title and Aligned Elements -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 d-flex align-items-center">
                        Total Patients
                    </h4>
                    <a href="/patients" class="ms-2 d-flex align-items-center">
                        <i class='bx bxs-right-arrow-circle'></i>
                    </a>
                </div>

                <!-- Card Body with Left-aligned Text and Right-aligned Image -->
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 id="totalPatient" class="card-title text-primary m-3"></h2> <!-- Left-aligned text -->
                    <img src="{{ asset('assets/img/illustrations/total-patients.png') }}" alt="View Badge User"
                        class="img-fluid" style="max-width: 30%; height: auto;"> <!-- Right-aligned image -->
                </div>

                <!-- Card Footer with View Details Button -->
                <div class="card-footer">
                    <button type="button" class="btn btn-link p-0" data-bs-toggle="modal"
                        data-bs-target="#viewPatientModal">
                        View Details
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="viewPatientModal" tabindex="-1" aria-labelledby="viewPatientModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewPatientModalLabel">Yearly Patients</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="yearlyPatientChart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card hover-card h-100">
            <div class="d-flex flex-column">
                <!-- Card Header with Title -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 d-flex align-items-center">
                        Total Consultation
                    </h4>
                    <a href="/consultations" class="ms-2 d-flex align-items-center">
                        <i class='bx bxs-right-arrow-circle'></i>
                    </a>
                </div>

                <!-- Card Body with Left-aligned Text and Right-aligned Image -->
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 id="totalConsultation" class="card-title text-primary m-3"></h2> <!-- Left-aligned text -->
                    <img src="{{ asset('assets/img/illustrations/total-consultation.png') }}" alt="View Badge User"
                        class="img-fluid" style="max-width: 30%; height: auto;"> <!-- Right-aligned image -->
                </div>

                <!-- Card Footer with View Details Button -->
                <div class="card-footer">
                    <button type="button" class="btn btn-link p-0" data-bs-toggle="modal"
                        data-bs-target="#viewConsultationModal">
                        View Details
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal for Yearly Graph -->
    <div class="modal fade" id="viewConsultationModal" tabindex="-1" aria-labelledby="viewConsultationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewConsultationModalLabel">Yearly Consultation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="yearlyConsultationChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Column for Patients Added Today -->
    <div class="col-lg-4 mb-4 order-0">

        <div class="card hover-card h-100">
            <div class="d-flex flex-column">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 d-flex align-items-center">
                        Total Dental
                    </h4>
                    <a href="/dentals" class="ms-2 d-flex align-items-center">
                        <i class='bx bxs-right-arrow-circle'></i>
                    </a>
                </div>

                <!-- Card Body with Left-aligned Text and Right-aligned Image -->
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 id="totalDental" class="card-title text-primary m-3"></h2> <!-- Left-aligned text -->
                    <img src="{{ asset('assets/img/illustrations/treatment.png') }}" alt="View Badge User"
                        class="img-fluid" style="max-width: 30%; height: auto;"> <!-- Right-aligned image -->
                </div>
                <!-- Card Footer with View Details Button -->
                <div class="card-footer">
                    <button type="button" class="btn btn-link p-0" data-bs-toggle="modal"
                        data-bs-target="#viewDentalModal">
                        View Details
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewDentalModal" tabindex="-1" aria-labelledby="viewDentalModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewDentalModalLabel">Yearly Patients</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="yearlyDentalChart"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tab Navigation -->
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="diagnosis-tab" data-bs-toggle="tab" href="#diagnosis" role="tab"
            aria-controls="diagnosis" aria-selected="false">Diagnosis Analytics</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="consultation-tab" data-bs-toggle="tab" href="#consultation" role="tab"
            aria-controls="consultation" aria-selected="true">Consultation Analytics</a>
    </li>
</ul>

<div class="card mb-4">
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="diagnosis" role="tabpanel" aria-labelledby="diagnosis-tab">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title text-dark mb-3">Diagnosis Analytics</h5>
                </div>

                <div class="row mb-3 order-0">
                    <div class="row mb-3 order-0">
                        <div class="col-md-4">
                            <label for="diagnosisstartMonth" class="form-label">Start Month</label>
                            <select id="diagnosisstartMonth" class="form-select mb-3">
                                <option value="">Select Start Month</option>
                                @foreach (range(1, 12) as $month)
                                <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="diagnosisendMonth" class="form-label">End Month</label>
                            <select id="diagnosisendMonth" class="form-select mb-3">
                                <option value="">Select End Month</option>
                                @foreach (range(1, 12) as $month)
                                <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="diagnosisInput" class="form-label">Year</label>
                            <select id="diagnosisyearSelect" class="form-select mb-3">
                                <option value="">Select Year</option>
                                @for ($i = 2019; $i <= date('Y'); $i++) <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="diagnosisInput" class="form-label">Barangay</label>
                            <select id="diagnosisselectBarangay" class="form-select mb-3">
                                <option value="">Select Barangay</option>
                                <!-- Barangay options will be populated by JS -->
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="diagnosisInput" class="form-label">Disease</label>
                            <select id="diagnosisSelect" class="form-select mb-3">
                                <option value="">Select Disease</option>
                                <!-- Diagnosis options will be populated by JS -->
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div id="diagnosisChart" class="flex-grow-1"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="consultation" role="tabpanel" aria-labelledby="consultation-tab">
            <div class="row mb-3">
                <div class="col-md-6 d-flex align-items-center">
                    <h5 class="card-title text-dark me-2 mb-0">Consultation Analytics</h5>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="startMonth" class="form-label">Start Month</label>
                    <select id="startMonth" class="form-select mb-3">
                        <option value="">Select Start Month</option>
                        @foreach (range(1, 12) as $month)
                        <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="endMonth" class="form-label">End Month</label>
                    <select id="endMonth" class="form-select mb-3">
                        <option value="">Select End Month</option>
                        @foreach (range(1, 12) as $month)
                        <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="diagnosisInput" class="form-label">Year</label>
                    <select id="yearSelect" class="form-select mb-3">
                        <option value="">Select Year</option>
                        @for ($i = 2019; $i <= date('Y'); $i++) <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="diagnosisInput" class="form-label">Barangay</label>
                    <select id="selectBarangay" class="form-select mb-3">
                        <option value="">Select Barangay</option>
                        <!-- Barangay options will be populated by JS -->
                    </select>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="diagnosisInput" class="form-label">Select Symptoms</label>
                    <select name="chief_complaint" class="form-select form-control me-2 mb-0" id="selectChiefComplaint"
                        aria-label="Default select example" required>
                        <option value="" selected disabled>Select Symptoms</option>
                    </select>
                </div>
            </div>


            <div class="h-1000" id="patientsChart"></div> <!-- This div will hold the chart -->
        </div>
    </div>
</div>

<!-- Hidden Content (Initially visible in Diagnosis Tab) -->
<div id="consultationContent" class="consultation-content">
    <!-- First row with map -->
    <div class="row mb-3">
        <div class="col mb-3 order-0">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-dark">Bato, Leyte Location</h5>
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second row with charts -->
    <div class="row mb-3">
        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-dark"><span id="ageDistTitle">Age Distribution of Patients</span></h5>
                    <div id="ageDistributionChartContainer">
                        <div id="ageDistributionChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title text-dark">Gender Distribution</h5>
                    <div id="genderDistributionChart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col mb-3 order-0">
        <div class="card h-100">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title text-dark mb-3">Top 10 Cases Diagnosis Analytics</h5>
                <div class="h-1000" id="diagnosiTop10"></div>
            </div>
        </div>
    </div>
</div>


<script>
// Use Bootstrap's tab events to control visibility
var consultationTab = document.getElementById('consultation-tab');
var diagnosisTab = document.getElementById('diagnosis-tab');
var consultationContent = document.getElementById('consultationContent');

// Hide content when Consultation Analytics tab is active
consultationTab.addEventListener('click', function() {
    consultationContent.style.display = 'none';
});

// Show content when Diagnosis Analytics tab is active
diagnosisTab.addEventListener('click', function() {
    consultationContent.style.display = 'block';
});
</script>

@endsection