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
<script src="{{ asset('assets/js/midwife-analytics.js') }}"></script>
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
                    <h5 class="modal-title" id="viewPatientModalLabel">Yearly Patient</h5>
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
                        Total Family Planning
                    </h4>
                    <a href="/family-planning" class="ms-2 d-flex align-items-center">
                        <i class='bx bxs-right-arrow-circle'></i>
                    </a>
                </div>

                <!-- Card Body with Left-aligned Text and Right-aligned Image -->
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 id="totalFamilyPlanning" class="card-title text-primary m-3"></h2> <!-- Left-aligned text -->
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
                    <h5 class="modal-title" id="viewConsultationModalLabel">Yearly Family Planning</h5>
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
                        Total Subdermal Implant
                    </h4>
                    <a href="/implant" class="ms-2 d-flex align-items-center">
                        <i class='bx bxs-right-arrow-circle'></i>
                    </a>
                </div>

                <!-- Card Body with Left-aligned Text and Right-aligned Image -->
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 id="totalImplant" class="card-title text-primary m-3"></h2> <!-- Left-aligned text -->
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
                    <h5 class="modal-title" id="viewDentalModalLabel">Yearly Subdermal Implant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="yearlyDentalChart"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection