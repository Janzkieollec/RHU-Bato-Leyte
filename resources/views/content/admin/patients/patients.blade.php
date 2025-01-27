@extends('layouts/contentNavbarLayout')

@section('title', 'Patients')

@section('page-script')
<script src="{{ asset('assets/js/patients.js') }}"></script>
@endsection

@section('content')


<div class="card mb-4">
    <div class="container mt-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5>Patients</h5>
            <!-- Breadcrumb Section -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"> <a
                            href="{{ Auth::check() ? url(lcfirst(Auth::user()->role) . '-dashboard') : url('/') }}">
                            Home
                        </a></li>
                    <li class="breadcrumb-item active" aria-current="page" id="breadcrumb-title">
                        Patients
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="content-wrapper">
    <!-- Striped Rows -->
    <div class="card">
        <div class="card-header">

            @if(Auth::user()->role !== 'Admin')
            <a href="/patient/add-patient" class="btn btn-primary float-end">
                <span class="tf-icons bx bx-user-plus me-1"></span>Add Patient
            </a>
            @endif

            <h5 class="mb-0 mt-2">List of Patients</h5>
        </div>
        <div class="container">
            <div class="row">
                <!-- Left Section: Show Entries -->
                <div class="col-md-6 mb-3 d-flex align-items-center">
                    <label for="pageSize" class="me-2 mb-0">Show</label>
                    <select id="pageSize" class="form-select" style="width: auto; display: inline-block;">
                        <option value="10">10</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <label class="ms-2 mb-0">entries</label>
                </div>

                <!-- Right Section: Search -->
                <div class="col-md-6 mb-3 d-flex justify-content-end align-items-center">
                    <label for="searchInput" class="me-2 mb-0">Search</label>
                    <input class="form-control" type="search" placeholder="Search Patient Family Number"
                        id="searchInput" style="max-width: 260px;" />
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap mb-4">
            <table id="tablePatient" class="table table-striped">
                <thead>
                    <tr>
                        <th>Family Number</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Birth Date</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <!-- AJAX-->
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
    <!--/ Striped Rows -->
</div>

<!--/ Large Modal for add Patients -->

<!-- Large Modal for Viewing Patient Info -->
<div class="modal fade" id="viewPatientInformation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-4" style="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);">
                    <div class="card-body text-nowrap mr-2">
                        <h5 class="modal-title" id="exampleModalLabel1">PATIENT INFORMATION</h5>
                    </div>
                </div>
                <div class="card mb-3" style="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);">
                    <div class="card-body text-nowrap mr-2">
                        <ul class="list-unstyled mb-2 mt-2 mb-3">

                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-people-group"></i>
                                <span class="text-bold-600 mx-2">Family Number:</span>
                                <span class="text-primary" id="familyNumberView"></span>
                            </li>

                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-user-tie"></i>
                                <span class="text-bold-600 mx-2">Full Name:</span>
                                <span class="text-primary" id="fullNameView"></span>
                            </li>

                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-location-dot"></i>
                                <span class="text-bold-600 mx-2">Address: </span>
                                <span class="text-primary" id="addressView"></span>
                            </li>

                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-venus-mars"></i>
                                <span class="text-bold-600 mx-2">Sex:</span>
                                <span class="text-primary" id="genderView"></span>
                            </li>

                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-cake-candles"></i>
                                <span class="text-bold-600 mx-2">Birth Date:</span>
                                <span class="text-primary" id="birthDateView"></span>
                            </li>

                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-cake-candles"></i>
                                <span class="text-bold-600 mx-2">Contact Number:</span>
                                <span class="text-primary" id="contactView"></span>
                            </li>

                            <li class="d-flex align-items-center mb-1">
                                <i class="fa-solid fa-arrow-up-9-1"></i>
                                <span class="text-bold-600 mx-2">Age: </span>
                                <span class="text-primary" id="ageView"></span>
                            </li>


                        </ul>
                        <input type="hidden" name="id" class="patient_id" />
                    </div>
                </div>

            </div>

            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>
<!--/ Large Modal for Updating Patients -->

<div class="modal fade" id="getPatient" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addPatientAccount">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add Patient Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="error-message" class="alert alert-danger" style="display: none;">
                        <!-- Error message will be displayed here -->
                    </div>
                    <!-- Card for Patient Information (Full Name) -->
                    <div class="card mb-4" style="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);">
                        <div class="card-body text-nowrap mr-2">
                            <h6 class="text-muted text-uppercase">Patient Information</h6>
                            <ul class="list-unstyled mb-2 mt-2 mb-3">
                                <li class="d-flex align-items-center mb-1">
                                    <i class="bx bx-user"></i>
                                    <span class="text-bold-600 mx-2">Full Name:</span>
                                    <span class="text-primary" id="fullNameViews"></span>
                                </li>
                            </ul>
                            <input type="hidden" name="id" id="patient_id">
                        </div>
                    </div>

                    <div class="card mb-4" style="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);">
                        <div class="card-body text-nowrap mr-2">
                            <h6 class="text-muted text-uppercase">Create Account</h6>
                            <div class="row g-2">
                                <div class="col mb-3">
                                    <label for="nameUpdate" class="form-label">Userame</label>
                                    <input name="username" type="text" id="nameUpdate" class="form-control"
                                        placeholder="Enter Name">
                                </div>
                                <div class="col mb-3">
                                    <label for="emailBasic" class="form-label">Email</label>
                                    <input name="email" type="email" id="emailBasic" class="form-control"
                                        placeholder="xxxx@xxx.xx">
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="Enter Password">
                                </div>
                                <div class="col mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" name="confirm_password" id="confirm_password"
                                        class="form-control" placeholder="Confirm Password">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="savePatientAccount" type="submit" class="btn btn-primary">
                        <span class="tf-icons bx bxs-save me-1"></span>Save Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection