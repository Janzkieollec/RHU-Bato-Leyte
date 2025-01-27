@extends('layouts/contentNavbarLayout')

@section('title', 'Family Planning')

@section('page-script')
<script src="{{ asset('assets/js/planning.js') }}"></script>
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
                    <a href="/family-planning" class="form-text">
                        <span class="bx bxs-chevron-left"></span>Back
                    </a>
                </h5>
            </div>
        </div>
        <div class="row">
            <ul class="list-unstyled mb-2 mt-2">
                <div class="row g-2">
                    <div class="col-md-6">
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
                        <li class="d-flex align-items-center mb-1"><i class="bx bx-male"></i>
                            <span class="text-bold-600 mx-2">Gender:</span> <span class="text-primary" id="gender">
                                {{ $patient->gender }}</span>
                        </li>

                    </div>
                    <div class="col-md-6">
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
                <h6 class="text-muted text-uppercase">View Family Planning Records</h6>
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
            <table id="tableViewPlanning" class="table table-striped">
                <thead class="text-uppercase">
                    <tr>
                        <th>Date</th>
                        <th>Family Method Used</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if($planningRecords->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center">No family planning record in the system</td>
                    </tr>
                    @else
                    @foreach($planningRecords as $planningRecord)
                    <tr>
                        <td>{{ $planningRecord->created_at->format('F j, Y') }}</td>
                        <td>{{ $planningRecord->fp_method_used }}</td>
                        <td>{{ $planningRecord->quantity }}</td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection