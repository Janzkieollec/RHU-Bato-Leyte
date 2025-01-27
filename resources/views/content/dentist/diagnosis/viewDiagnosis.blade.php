@extends('layouts/contentNavbarLayout')

@section('title', 'Dental Records')

@section('page-script')

@endsection

@section('content')

<style>
.table-container {
    max-height: 400px;
    overflow-y: auto;
    display: block;
}

.table-container table {
    width: 100%;
    border-collapse: collapse;
}

.table-container th,
.table-container td {
    text-align: left;
    padding: 12px;
}

.table-container thead th {
    background-color: #f2f2f2;
    position: sticky;
    top: 0;
    z-index: 1;
    font-size: 0.9rem;
}

tr:nth-child(even) {
    background-color: #fbfbfb;
}
</style>

<div class="card">
    <div class="card-body text-nowrap mr-2">
        <div class="row">
            <div class="col-md-8">
                <h6 class="text-muted text-uppercase">Patient Information</h6>
            </div>

            <div class="col-md-4 d-flex align-items-end justify-content-end">
                <h5>
                    <a href="/patients-dental-diagnosis" class="form-text">
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
                <h6 class="text-muted text-uppercase">View Dental Records</h6>
            </div>
            <div class="col-md-4 d-flex align-items-center justify-content-end">
                <div class="mx-2">
                    <input type="date" id="datePickerInput" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="table-container">
            <table id="tableViewConsultation">
                <thead class="text-uppercase">
                    <tr>
                        <th>Date of Consultation</th>
                        <th>Diagnosis</th>
                        <th>Description / Recommendation</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if($dentalDiagnosisRecords->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center">No dental record in the system</td>
                    </tr>
                    @else
                    @foreach($dentalDiagnosisRecords as $dentalDiagnosisRecord)
                    <tr>
                        <td>{{ $dentalDiagnosisRecord->created_at->format('F j, Y') }}</td>
                        <td>{{ $dentalDiagnosisRecord->diagnosis_name }}</td>
                        <td>{{ $dentalDiagnosisRecord->description }}</td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection