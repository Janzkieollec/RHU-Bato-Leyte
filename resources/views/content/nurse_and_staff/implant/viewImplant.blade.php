@extends('layouts/contentNavbarLayout')

@section('title', 'Progestin Subdermal Implant Insertion')

@section('page-script')
<script src="{{ asset('assets/js/implant.js') }}"></script>
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <h5 class="mt-2">
            <a href="/implant" class="form-text float-end">
                <span class="bx bxs-chevron-left"></span>Back
            </a>
        </h5>

        <h5 class="text-muted text-uppercase">Patient Information</h6>

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
                                <span class="text-bold-600 mx-2">Contact:</span>
                                <span class="text-primary" id="Contact">{{ $patient->contact}}</span>
                            </li>
                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-cake-candles"></i>
                                <span class="text-bold-600 mx-2">Birth Date:</span>
                                <span class="text-primary" id="Contact">{{ $patient->birth_date}}</span>
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
                <h6 class="text-muted text-uppercase">View Progestin Subdermal Implant Insertion Records</h6>
            </div>
            <div class="col-md-4 d-flex align-items-center justify-content-end">
                <div class="mx-2">
                    <input type="date" id="datePickerInput" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="table-responsive text-nowrap mb-3">
            <table id="tableViewPlanning" class="table table-striped">
                <thead class="text-uppercase">
                    <tr>
                        <th>Date Insertion</th>
                        <th>No. of Children</th>
                        <th>Name of Provider</th>
                        <th>FP Unmet Method Used</th>
                        <th>Family Method Used</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if($implantRecords->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center">No progestin subdermal implant insertion records in the
                            system</td>
                    </tr>
                    @else
                    @foreach($implantRecords as $implantRecord)
                    <tr>
                        <td>{{ $implantRecord->formattedDate}}</td>
                        <td>{{ $implantRecord->no_of_children}}</td>
                        <td>{{ $implantRecord->type_of_provider}} {{ $implantRecord->name_of_provider}}</td>
                        <td>{{ $implantRecord->fp_unmet_method_used}}</td>
                        <td>{{ $implantRecord->previous_fp_method }}</td>
                        <td>{{ $implantRecord->quantity }}</td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection