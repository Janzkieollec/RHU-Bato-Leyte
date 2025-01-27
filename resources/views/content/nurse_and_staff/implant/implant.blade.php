@extends('layouts/contentNavbarLayout')

@section('title', 'Progestin Subdermal Implant Insertion.')

@section('page-script')
<script src="{{ asset('assets/js/implant.js') }}"></script>
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


<div id="loadingSpinner" class="loading-spinner" style="display: none;">
    <div class="dot-container">
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>
</div>

<div class="content-wrapper">
    <!-- Striped Rows -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Progestin Subdermal Implant Insertion (Female)</h5>
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
                    <input class="form-control" type="search" placeholder="Search Name" id="searchInput"
                        style="max-width: 260px;" />
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap mb-3">
            <table id="tableImplant" class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Birth Date</th>
                        <th>Address</th>
                        <th>Action</th>
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
<!--/ Large Modal for Individual Treatment Record  -->
@endsection