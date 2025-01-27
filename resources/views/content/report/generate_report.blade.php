@extends('layouts/contentNavbarLayout')

@section('title', 'Generate Reports')

@section('page-script')
<script src="{{ asset('assets/js/reports.js') }}"></script>
<script src="{{ asset('assets/error_trapping/reports.js') }}"></script>

@endsection

@section('content')

<style>
/* Container for the spinner */
.loading-spinner {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: rgba(0, 0, 0, 0.3);
    /* semi-transparent background */
    z-index: 9999;
    /* Ensure it's on top */
}

/* Dot container (all dots in a row) */
.dot-container {
    display: flex;
    justify-content: space-between;
    width: 70px;
    /* Width of the container holding the dots */
}

/* Individual dot styles */
.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #3498db;
    opacity: 0;
    animation: dot-chase 1.5s infinite;
}

/* Keyframes for the dot chase effect */
@keyframes dot-chase {
    0% {
        opacity: 0;
        transform: translateY(0);
    }

    30% {
        opacity: 1;
        transform: translateY(-10px);
        /* Bounce up */
    }

    60% {
        opacity: 1;
        transform: translateY(0);
    }

    100% {
        opacity: 0;
        transform: translateY(0);
    }
}

/* Stagger the animation for each dot */
.dot:nth-child(1) {
    animation-delay: 0s;
    /* First dot starts immediately */
}

.dot:nth-child(2) {
    animation-delay: 0.3s;
    /* Second dot starts after 0.3s */
}

.dot:nth-child(3) {
    animation-delay: 0.6s;
    /* Third dot starts after 0.6s */
}
</style>

<div class="card mb-4">
    <div class="container mt-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5>Generate Reports</h5>
            <!-- Breadcrumb Section -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ Auth::check() ? url(lcfirst(Auth::user()->role) . '-dashboard') : url('/') }}">
                            Home
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Generate Reports
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="card">
    <div class="container-wrapper mt-3">
        <div id="loadingSpinner" class="loading-spinner" style="display: none;">
            <div class="dot-container">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
        </div>

        <form id="formReport">
            @csrf
            <div class="row m-2 mb-3">
                <div class="col-md-8">
                    <h5 class="modal-title" id="exampleModalLabel3">List of Generate Reports</h5>
                </div>

                <div class="col-md-4 d-flex align-items-end justify-content-end">
                    <button type="submit" class="btn btn-primary" id="generateReportButton"><span
                            class="tf-icons bx bxs-file me-1"></span> Generate Report</button>
                </div>
            </div>
            <!-- Filter Section -->
            <div class="container">
                <div class="row">
                    <div class="col-md-10 mb-3 d-flex align-items-center">
                        <label for="reportType" class="me-2 mb-0">Select Report Type:</label>
                        <select id="reportType" class="form-select" style="width: 200px;">
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="annual">Annually</option>
                        </select>

                        <div class="mx-2"></div>

                        <label for="selectType" class="me-2 mb-0">Select Month:</label>
                        <select id="selectType" class="form-select" style="width: 200px;">
                            <!-- Options will be dynamically populated -->
                        </select>

                        <div class="mx-2"></div>

                        <label for="selectYear" class="me-2 mb-0">Select Year:</label>
                        <select id="selectYear" class="form-select" style="width: 200px;">
                            <option value="">Select Year</option>
                            @for ($i = 2019; $i <= date('Y'); $i++) <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                        </select>
                    </div>
                </div>
            </div>
        </form>

        <div class="container">
            <div class="row mb-3">
                <div class="col-md-6 d-flex align-items-center">
                    <label for="pageSize" class="me-2 mb-0">Show</label>
                    <select id="pageSize" class="form-select" style="width: auto; display: inline-block;">
                        <option value="10">10</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <label class="ms-2 mb-0">entries</label>
                </div>
            </div>
        </div>

        <!-- Report Table Section -->
        <div class="content-wrapper mt-4">
            <div class="table-responsive text-nowrap mb-4">
                <table id="tableImportPatient" class="table table-striped table-shadow">
                    <thead>
                        <tr>
                            <th>Report ID</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Actions</th>
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