@extends('layouts/contentNavbarLayout')

@section('title', 'Diagnosis Records')

@section('page-script')
<script src="{{ asset('assets/js/consultation_diagnosis.js') }}"></script>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Striped Rows -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 mt-2">Diagnosis Records</h5>
        </div>
        <div class="container mb-3">
            <div class="row">
                <div class="col-md-6 d-flex align-items-center">
                    <label for="pageSize" class="me-2 mb-0">Show</label>
                    <select id="pageSize" class="form-select" style="width: auto; display: inline-block;">
                        <option value="10">10</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <label class="ms-2 mb-0">entries</label>
                </div>
                <!-- Right Section: Search -->
                <div class="col-md-6 d-flex justify-content-end align-items-center">
                    <label for="searchInput" class="me-2 mb-0">Search</label>
                    <input class="form-control" type="search" placeholder="Search Patient Family Number"
                        id="searchInput" style="max-width: 260px;" />
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap mb-3">
            <table id="tablePatientDiagnosis" class="table table-striped">
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
                    <!-- ang AJAX rani -->
                </tbody>
            </table>
        </div>
        <!-- Pagination Section -->
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
    <!--/ Striped Rows -->
</div>
@endsection