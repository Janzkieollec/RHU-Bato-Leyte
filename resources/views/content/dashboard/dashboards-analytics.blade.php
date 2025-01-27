@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection

@section('page-script')
<script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
@endsection

@section('content')
<style>
/* Add this CSS to your stylesheet */

.hover-card {
    position: relative;
    transition: box-shadow 0.3s ease, transform 0.3s ease;
    border-radius: 8px;
    /* Ensure the card has rounded corners */
    overflow: hidden;
    /* Ensure the pseudo-element is clipped to the card's rounded corners */
}

.hover-card:before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 0;
    background-color: #adc178;
    /* Violet color */
    transition: height 0.3s ease;
    border-bottom-left-radius: 8px;
    /* Match the card's border radius */
    border-bottom-right-radius: 8px;
    /* Match the card's border radius */
}

.hover-card:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    transform: translateY(-5px);
}

.hover-card:hover:before {
    height: 4px;
    /* Adjust the height of the bottom border */
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
</style>
<div class="col mb-4">
    <div class="card h-100">
        <div class="card-body">
            <h4 id="todayDate" class="text-secondary"></h4>
            <h5 class="card-title text-dark">Weekly Overview</h5>
            <div id="weeklyCalendar" class="d-flex justify-content-between"></div>
        </div>
    </div>
</div>
<div class="row">
    <!-- Column for Total Patients -->
    <div class="col-lg-3 mb-4 order-0">
        <a href="/patients" class="card-link">
            <div class="card hover-card h-100">
                <div class="d-flex align-items-center row h-100">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-dark">Total Patients</h5>
                            <h4 id="totalPatient" class="card-title text-primary"></h4>
                        </div>
                    </div>
                    <div class="col-sm-5 d-flex align-items-center justify-content-center">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/total-patients.png') }}" alt="View Badge User"
                                class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 mb-4 order-0">
        <a href="/users" class="card-link">
            <div class="card hover-card h-100">
                <div class="d-flex align-items-center row h-100">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-dark">Total Nurse</h5>
                            <h4 id="totalNurse" class="card-title text-primary"></h4>
                        </div>
                    </div>
                    <div class="col-sm-5 d-flex align-items-center justify-content-center">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/total-patients.png') }}" alt="View Badge User"
                                class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 mb-4 order-0">
        <a href="/users" class="card-link">
            <div class="card hover-card h-100">
                <div class="d-flex align-items-center row h-100">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-dark">Total Staff</h5>
                            <h4 id="totalStaff" class="card-title text-primary"></h4>
                        </div>
                    </div>
                    <div class="col-sm-5 d-flex align-items-center justify-content-center">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/total-patients.png') }}" alt="View Badge User"
                                class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 mb-4 order-0">
        <a href="/users" class="card-link">
            <div class="card hover-card h-100">
                <div class="d-flex align-items-center row h-100">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-dark">Total Midwife</h5>
                            <h4 id="totalMidwife" class="card-title text-primary"></h4>
                        </div>
                    </div>
                    <div class="col-sm-5 d-flex align-items-center justify-content-center">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/total-patients.png') }}" alt="View Badge User"
                                class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 mb-4 order-0">
        <a href="/users" class="card-link">
            <div class="card hover-card h-100">
                <div class="d-flex align-items-center row h-100">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-dark">Total Doctor</h5>
                            <h4 id="totalDoctor" class="card-title text-primary"></h4>
                        </div>
                    </div>
                    <div class="col-sm-5 d-flex align-items-center justify-content-center">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/total-patients.png') }}" alt="View Badge User"
                                class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 mb-4 order-0">
        <a href="/users" class="card-link">
            <div class="card hover-card h-100">
                <div class="d-flex align-items-center row h-100">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-dark">Total Dentist</h5>
                            <h4 id="totalDentist" class="card-title text-primary"></h4>
                        </div>
                    </div>
                    <div class="col-sm-5 d-flex align-items-center justify-content-center">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/total-patients.png') }}" alt="View Badge User"
                                class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 mb-4 order-0">
        <a href="/population" class="card-link">
            <!-- Add href here -->
            <div class="card hover-card h-100">
                <div class="d-flex align-items-center row h-100">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-dark">Total Population</h5>
                            <h4 id="totalPopulation" class="card-title text-primary"></h4>
                        </div>
                    </div>
                    <div class="col-sm-5 d-flex align-items-center justify-content-center">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/population.png') }}" alt="View Badge User"
                                class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </a> <!-- Close the anchor tag here -->
    </div>
</div>
@endsection