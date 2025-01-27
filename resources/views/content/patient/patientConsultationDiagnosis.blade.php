@extends('layouts/contentNavbarLayout')

@section('title', 'Diagnosis Records')

@section('page-script')
<script src="{{ asset('assets/js/patient_consultation_diagnosis.js') }}"></script>
@endsection

@section('content')
<div class="card">
    <div class="container-wrapper mt-3">
        <div class="row m-2 mb-3">
            <div class="col-md-8">
                <h5 class="modal-title" id="exampleModalLabel3"> Diagnosis Records</h5>
            </div>

            <div class="col-md-4 d-flex align-items-end justify-content-end">
            </div>
        </div>
        <div class="container mb-3">
            <div class="row">
                <!-- Left Section: Show Entries -->
                <div class="col-md-8 d-flex align-items-center">
                    <label for="pageSize" class="me-2 mb-0">Show</label>
                    <select id="pageSize" class="form-select" style="width: auto; display: inline-block;">
                        <option value="10">10</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <label class="ms-2 mb-0">entries</label>
                </div>

                <div class="col-md-4 d-flex align-items-center justify-content-end">
                    <div class="mx-2">
                        <input type="date" id="datePickerInput" class="form-control">
                    </div>
                </div>
            </div>
        </div>
        <div class="content-wrapper mt-4">
            <div class="table-responsive text-nowrap mb-4">
                <table id="tableViewDiagnosis" class="table table-striped table-shadow">
                    <thead>
                        <tr>
                            <th>Date of Diagnosis</th>
                            <th>Diagnosis Name</th>
                            <th>Description / Recommendation</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">

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
    </div>
</div>
@endsection