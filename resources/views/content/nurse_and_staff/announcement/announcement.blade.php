@extends('layouts/contentNavbarLayout')

@section('title', 'Announcements')

@section('page-script')
<script src="{{ asset('assets/js/announcement.js') }}"></script>
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

<div class="card mb-4">
    <div class="container mt-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5>Announcements</h5>
            <!-- Breadcrumb Section -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ Auth::check() ? url(lcfirst(Auth::user()->role) . '-dashboard') : url('/') }}">
                            Home
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Announcements
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>


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
            <h5 class="mb-0">List of Announcements</h5>
            <a href="#" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
                <span class="fa-solid fa-bullhorn me-1"></span>Add
            </a>

        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-3 d-flex align-items-center">
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
            <table id="tableAnnouncement" class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Location</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <!-- ang AJAX rani -->
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
<!--/ Large Modal for Individual Treatment Record  -->

<!-- Modal for Adding Announcement -->
<div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="addAnnouncementModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="addAnnouncementForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAnnouncementModalLabel">Add Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="announcementTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="announcementTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="announcementContent" class="form-label">Content</label>
                        <textarea class="form-control" id="announcementContent" name="content" rows="3"
                            required></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="mb-3">
                            <label for="selectBarangay" class="form-label">Barangay</label>
                            <select name="location" class="form-select form-control" id="selectBarangay"
                                aria-label="Default select example" required>
                                <option value="" selected disabled>Select Barangay</option>
                                <!-- diri mo populate ra ang data gamit ang AJAX -->
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="announcementDate" class="form-label">Date</label>
                        <input type="date" class="form-control" id="announcementDate" name="date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="saveAnnouncementBtn"><i
                            class="fa-solid fa-floppy-disk me-1"></i> Save Announcement</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection