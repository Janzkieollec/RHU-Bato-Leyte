@extends('layouts/contentNavbarLayout')

@section('title', 'Patients Queue')

@section('page-script')
<script src="{{ asset('js/app.js') }}"></script>

<script>
$('#setPatientsLimit').on('click', function() {
    const maxPatients = $('#patientsLimit').val();

    if (maxPatients && maxPatients > 0) {

        // Send the limit to the server
        $.ajax({
            url: '/api/set-max-patients', // Ensure this matches your route
            method: 'POST', // Use POST here
            data: {
                maxPatients: maxPatients
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content'), // Include CSRF token if necessary
            },
            success: function(response) {
                alert(response.message);
            },
            error: function(xhr) {
                console.error(xhr.responseJSON?.message || 'An error occurred.');
                alert('Failed to set the limit. Please try again.');
            },
        });
    } else {
        alert("Please enter a valid number of patients.");
    }
});

// Check the current queue size and enforce the limit
Echo.channel('consultation-queueing')
    .listen('.consultation-queueing.added', (event) => {
        // Create new patient object and row
        let newPatient = {
            family_number: event.patient.family_number,
            first_name: event.patient.first_name,
            middle_name: event.patient.middle_name,
            last_name: event.patient.last_name,
            suffix_name: event.patient.suffix_name,
            gender: event.gender,
            birth_date: event.patient.birth_date,
            address: {
                barangay: event.address.barangay,
                municipality: event.address.municipality,
                province: event.address.province,
            },
            emergencyPurposes: event.emergencyPurposes,
            patient_id: event.encryptedPatientId,
        };

        // Create the new row for the patient
        let newRow = `
            <tr data-patient-id="${newPatient.patient_id}">
                <td>${newPatient.family_number}</td>
                <td>${newPatient.first_name} ${newPatient.middle_name ? newPatient.middle_name.charAt(0) + '.' : ''} ${newPatient.last_name} ${newPatient.suffix_name ? newPatient.suffix_name + '.' : ''}</td>
                <td>${newPatient.gender}</td>
                <td>${newPatient.birth_date}</td>
                <td>${newPatient.address.barangay}, ${newPatient.address.municipality}, ${newPatient.address.province}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-primary btn-icon rounded-circle custom-rounded-btn" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-primary btn-icon rounded-circle custom-rounded-btn" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li>
                                <a href="/add-consultation-diagnosis/${newPatient.patient_id}" class="dropdown-item"><i class="fa-solid fa-file-medical"></i> Add Diagnosis</a>
                            </li>
                            <li>
                                <a class="dropdown-item sendMessage" href="#" data-encrypted-id="${newPatient.encrypted_id}">
                                   <i class="fa-solid fa-message"></i>
                                    <span class="ms-2">Send Message</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        `;

        // Append the new patient row to the table
        $('#tablePatient tbody').append(newRow);

        // Include the external JavaScript file after the new row is added
        $.getScript("assets/js/consultation_queue.js")
            .done(function(script, textStatus) {
                console.log("Script loaded: " + textStatus);
            })
            .fail(function(jqxhr, settings, exception) {
                console.error("Failed to load script: " + exception);
            });
    });
</script>


<script src="{{ asset('assets/js/consultation_queue.js') }}"></script>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Striped Rows -->
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6 d-flex align-items-center">
                    <h5 class="mb-0 mt-2">Patients Queue</h5>
                </div>

                <div class="col-md-6 d-flex justify-content-end align-items-center">
                    <label for="patientLimit" class="me-2 mb-0">Set Number of Patients to Accept:</label>
                    <input type="number" id="patientsLimit" class="form-control"
                        style="width: 150px; display: inline-block;" placeholder="Enter number" min="1" max="1000">
                    <button class="btn btn-primary ms-2" id="setPatientsLimit">Set Limit</button>
                </div>
            </div>
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
            <table id="tablePatient" class="table table-striped">
                <thead>
                    <tr>
                        <th>Family Number</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Birth Date</th>
                        <th>Address</th>
                        <th>Emergency</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <!-- ajax -->
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

<!-- Modal -->
<div class="modal fade" id="emergencyModal" tabindex="-1" aria-labelledby="emergencyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emergencyModalLabel">Emergency Purpose</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted" id="emergency">No emergency contact available.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection