@extends('layouts/contentNavbarLayout')

@section('title', 'Treatments')

@section('page-script')
    <script src="{{ asset('assets/js/ui-modals.js') }}"></script>
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
    </style>

    <div class="content-wrapper">
        <!-- Striped Rows -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 mt-2">List of Deceased Patients</h5>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Date of Death</th>
                            <th>Cause of Death</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0" id="deceasedPatientsTableBody">
                        <!-- AJAX will populate this tbody -->
                    </tbody>
                </table>
            </div>
        </div>
        <!--/ Striped Rows -->
    </div>

    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/deceased-patients.js') }}"></script>
@endsection
