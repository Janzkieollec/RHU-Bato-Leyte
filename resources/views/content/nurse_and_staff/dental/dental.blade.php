@extends('layouts/contentNavbarLayout')

@section('title', 'Dental Records')

@section('page-script')
<script src="{{ asset('assets/js/dental.js') }}"></script>
@endsection

@section('content')

<style>
.custom-shadow {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.custom-shadow:hover {
    border: 2px solid #84a98c;
    /* Change this to your desired border color */
    box-shadow: 0 0 15px rgba(202, 210, 197, 0.5);
    /* Change the color and intensity as needed */
}

.modal-xxl {
    max-width: 90%;
}

.table-shadow {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.card.shadow-sm {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3) !important;
}
</style>

<div class="card mb-4">
    <div class="container mt-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5>Dental</h5>
            <!-- Breadcrumb Section -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ Auth::check() ? url(lcfirst(Auth::user()->role) . '-dashboard') : url('/') }}">
                            Home
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Dental
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
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">List of Dental Records</h5>
                <div>
                    @if ($patientLimits)
                    <span id="patient-limit" class="badge bg-primary ms-2">
                        {{ $patientLimits->current_patients }}/{{ $patientLimits->max_patients }} Patients
                    </span>
                    @endif


                </div>
            </div>
        </div>
        <div class="container mb-3">
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
                <div class="col-md-6 d-flex justify-content-end align-items-center">
                    <label for="searchInput" class="me-2 mb-0">Search</label>
                    <input class="form-control" type="search" placeholder="Search Patient Family Number"
                        id="searchInput" style="max-width: 260px;" />
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap mb-4">
            <table id="tableDental" class="table table-striped">
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
<!--/ Large Modal for Individual Treatment Record  -->


<!-- Modal -->
<div class="modal" id="sendMessageModal" tabindex="-1" aria-labelledby="sendMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendMessageModalLabel">Send Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="sendMessageForm">
                    @csrf
                    <div class="mb-3">
                        <label for="contact" class="form-label">Contact</label>
                        <input type="text" name="contact" class="form-control" id="contact" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" name="message" id="message" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection