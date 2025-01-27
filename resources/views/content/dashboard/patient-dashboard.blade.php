@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
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
<script src="{{ asset('assets/js/patients-consultation-analytics.js') }}"></script>
<script src="{{ asset('assets/js/patients-consultation-diagnosis-analytics.js') }}"></script>
<script src="{{ asset('assets/js/patients-dental-analytics.js') }}"></script>
<script src="{{ asset('assets/js/patients-dental-diagnosis-analytics.js') }}"></script>
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
    justify-content: space-between;
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

<div class="card mt-4">
    <!-- Tab Navigation -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="consultation-tab" data-bs-toggle="tab" href="#consultation" role="tab"
                aria-controls="consultation" aria-selected="true">Consultation Analytics</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="dental-tab" data-bs-toggle="tab" href="#dental" role="tab" aria-controls="dental"
                aria-selected="false">Dental Analytics</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="consultation-diagnosis-tab" data-bs-toggle="tab" href="#consultation-diagnosis"
                role="tab" aria-controls="consultation-diagnosis" aria-selected="false">Consultation Diagnois
                Analytics</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="dental-diagnosis-tab" data-bs-toggle="tab" href="#dental-diagnosis" role="tab"
                aria-controls="dental-diagnosis" aria-selected="false">Dental Diagnosis Analytics</a>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <!-- Consultation Analytics Tab -->
        <div class="tab-pane fade show active" id="consultation" role="tabpanel" aria-labelledby="consultation-tab">
            <div class="row mb-3">
                <div class="col-md-6 d-flex align-items-center">
                    <h5 class="card-title text-dark me-2 mb-0">My Consultation Analytics</h5>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="startMonth" class="form-label">Start Month</label>
                    <select id="startMonth" class="form-select mb-3">
                        <option value="">Select Start Month</option>
                        @foreach (range(1, 12) as $month)
                        <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="endMonth" class="form-label">End Month</label>
                    <select id="endMonth" class="form-select mb-3">
                        <option value="">Select End Month</option>
                        @foreach (range(1, 12) as $month)
                        <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="diagnosisInput" class="form-label">Year</label>
                    <select id="yearSelect" class="form-select mb-3">
                        <option value="">Select Year</option>
                        @for ($i = 2019; $i <= date('Y'); $i++) <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                    </select>
                </div>
                <div class="col-md-3 mb-4">
                    <label for="diagnosisInput" class="form-label">Select Symptoms</label>
                    <select name="chief_complaint" class="form-select form-control me-2 mb-0" id="selectChiefComplaint"
                        aria-label="Default select example" required>
                        <option value="" selected disabled>Select Symptoms</option>
                    </select>
                </div>
            </div>
            <div class="h-1000" id="patientsChart"></div> <!-- This div will hold the chart -->
        </div>

        <!-- Dental Analytics Tab -->
        <div class="tab-pane fade" id="dental" role="tabpanel" aria-labelledby="dental-tab">
            <div class="row mb-3">
                <div class="col-md-6 d-flex align-items-center">
                    <h5 class="card-title text-dark me-2 mb-0">My Dental Analytics</h5>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="dentalStartMonth" class="form-label">Start Month</label>
                    <select id="dentalStartMonth" class="form-select mb-3" required>
                        <option value="">Select Start Month</option>
                        @foreach (range(1, 12) as $month)
                        <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="dentalEndMonth" class="form-label">End Month</label>
                    <select id="dentalEndMonth" class="form-select mb-3" required>
                        <option value="">Select End Month</option>
                        @foreach (range(1, 12) as $month)
                        <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="dentalYearSelect" class="form-label">Year</label>
                    <select id="dentalYearSelect" class="form-select mb-3" required>
                        <option value="">Select Year</option>
                        @for ($i = 2019; $i <= date('Y'); $i++) <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="selectDentalSymptoms" class="form-label">Select Symptoms</label>
                    <select name="dental_symptoms" class="form-select" id="selectDentalSymptoms" required>
                        <option value="" selected disabled>Select Symptoms</option>
                    </select>
                </div>
            </div>
            <div class="h-1000" id="dentalChart"></div> <!-- This div will hold the chart -->
        </div>

        <div class="tab-pane fade" id="consultation-diagnosis" role="tabpanel"
            aria-labelledby="consultation-diagnosis-tab">
            <div class="row mb-3">
                <div class="col-md-6 d-flex align-items-center">
                    <h5 class="card-title text-dark me-2 mb-0">My Consultation Analytics</h5>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="ConsultationDiagnosisStartMonth" class="form-label">Start Month</label>
                    <select id="ConsultationDiagnosisStartMonth" class="form-select mb-3" required>
                        <option value="">Select Start Month</option>
                        @foreach (range(1, 12) as $month)
                        <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="ConsultationDiagnosisEndMonth" class="form-label">End Month</label>
                    <select id="ConsultationDiagnosisEndMonth" class="form-select mb-3" required>
                        <option value="">Select End Month</option>
                        @foreach (range(1, 12) as $month)
                        <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="ConsultationDiagnosisYearSelect" class="form-label">Year</label>
                    <select id="ConsultationDiagnosisYearSelect" class="form-select mb-3" required>
                        <option value="">Select Year</option>
                        @for ($i = 2019; $i <= date('Y'); $i++) <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="ConsultationDiagnosisSelectDiagnosis" class="form-label">Select Symptoms</label>
                    <select class="form-select" id="ConsultationDiagnosisSelectDiagnosis" required>
                        <option value="" selected disabled>Select Symptoms</option>
                    </select>
                </div>
            </div>
            <div class="h-1000" id="consultationDiagnosis"></div> <!-- This div will hold the chart -->
        </div>

        <div class="tab-pane fade" id="dental-diagnosis" role="tabpanel" aria-labelledby="dental-diagnosis-tab">
            <div class="row mb-3">
                <div class="col-md-6 d-flex align-items-center">
                    <h5 class="card-title text-dark me-2 mb-0">My Consultation Analytics</h5>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="DentalDiagnosisStartMonth" class="form-label">Start Month</label>
                    <select id="DentalDiagnosisStartMonth" class="form-select mb-3" required>
                        <option value="">Select Start Month</option>
                        @foreach (range(1, 12) as $month)
                        <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="DentalDiagnosisEndMonth" class="form-label">End Month</label>
                    <select id="DentalDiagnosisEndMonth" class="form-select mb-3" required>
                        <option value="">Select End Month</option>
                        @foreach (range(1, 12) as $month)
                        <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="DentalDiagnosisYearSelect" class="form-label">Year</label>
                    <select id="DentalDiagnosisYearSelect" class="form-select mb-3" required>
                        <option value="">Select Year</option>
                        @for ($i = 2019; $i <= date('Y'); $i++) <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="DentalDiagnosisSelectDiagnosis" class="form-label">Select Symptoms</label>
                    <select class="form-select" id="DentalDiagnosisSelectDiagnosis" required>
                        <option value="" selected disabled>Select Symptoms</option>
                    </select>
                </div>
            </div>
            <div class="h-1000" id="dentalDiagnosis"></div> <!-- This div will hold the chart -->
        </div>
    </div>
</div>
@endsection