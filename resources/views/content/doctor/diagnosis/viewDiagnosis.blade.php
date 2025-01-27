@extends('layouts/contentNavbarLayout')

@section('title', 'Diagnosis Records')

@section('page-script')
<script src="{{ asset('assets/js/consultation_diagnosis_view.js') }}"></script>

@endsection

@section('content')


<div class="card">
    <div class="card-body text-nowrap mr-2">
        <div class="row">
            <div class="col-md-8">
                <h6 class="text-muted text-uppercase">Patient Information</h6>
            </div>

            <div class="col-md-4 d-flex align-items-end justify-content-end">
                <h5>
                    <a href="/patients-consultation-diagnosis" class="form-text">
                        <span class="bx bxs-chevron-left"></span>Back
                    </a>
                </h5>
            </div>
        </div>
        <div class="row">
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
                            <span class="text-primary" id="addressView">{{ $patient->barangay_name }},
                                {{ $patient->municipality_name }}, {{ $patient->province_name }}</span>
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

<div class="card mt-4">
    <div class="container-wrapper mt-3 mb-2">
        <div class="row m-2">
            <div class="col-md-8 mt-2">
                <h6 class="text-muted text-uppercase">View Diagnosis Records</h6>
            </div>
            <div class="col-md-6 d-flex align-items-center">
                <label for="pageSize" class="me-2 mb-0">Show</label>
                <select id="pageSize" class="form-select" style="width: auto; display: inline-block;">
                    <option value="10">10</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <label class="ms-2 mb-0">entries</label>
            </div>
            <div class="col-md-6 d-flex align-items-center justify-content-end">
                <div class="mx-2">
                    <input type="date" id="datePickerInput" class="form-control">
                </div>
            </div>
        </div>
    </div>

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
    <div class="row mb-3">
        <div class="col-md-11 d-flex justify-content-end">
            <nav aria-label="Page navigation">
                <ul class="pagination" id="pagination">
                    <!-- Pagination links will be dynamically added here -->
                </ul>
            </nav>
        </div>
    </div>
</div>
@endsection