@extends('layouts/contentNavbarLayout')

@section('title', 'Logs')

@section('page-script')
<script src="{{ asset('assets/js/logs.js') }}"></script>
@endsection

@section('content')

<div class="card mb-4">
    <div class="container mt-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5>Logs</h5>
            <!-- Breadcrumb Section -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ Auth::check() ? url(lcfirst(Auth::user()->role) . '-dashboard') : url('/') }}">
                            Home
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page" id="breadcrumb-title">
                        Logs
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="content-wrapper">
    <!-- Striped Rows -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">List of Logs</h5>
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
            </div>
        </div>
        <div class="table-responsive text-nowrap mb-4">
            <table id="tableLogs" class="table table-striped">
                <thead>
                    <tr>
                        <th>Description</th>
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
@endsection