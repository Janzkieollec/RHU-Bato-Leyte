@extends('layouts/contentNavbarLayout')

@section('title', 'Users')

@section('page-script')
<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets/js/users.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
@endsection

@section('content')

<div class="card mb-4">
    <div class="container mt-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5>Users</h5>
            <!-- Breadcrumb Section -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"> <a
                            href="{{ Auth::check() ? url(lcfirst(Auth::user()->role) . '-dashboard') : url('/') }}">
                            Home
                        </a></li>
                    <li class="breadcrumb-item active" aria-current="page" id="breadcrumb-title">
                        Users
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Striped Rows -->
<div class="card">
    <div class="card-header">
        <button id="addUserBtn" type="button" class="btn btn-primary float-end" data-bs-toggle="modal"
            data-bs-target="#addNewUserModal">
            <span class="tf-icons bx bx-user-plus me-1"></span>Add User
        </button>
        <h5 class="mb-0 mt-2">List of Users</h5>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                <!-- AJAX -->
            </tbody>
        </table>
    </div>
</div>
<!--/ Striped Rows -->

<!-- Add User Modal -->
<div class="modal fade" id="addNewUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="usersFormCreate">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="error-message" class="alert alert-danger" style="display: none;">
                        <!-- Error message will be displayed here -->
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label">Username</label>
                            <input name="username" type="text" id="nameBasic" class="form-control"
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
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control"
                                placeholder="Confirm Password">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div style="width: 100%;">
                                <label for="selectRole" class="form-label">Role</label>
                                <select name="role" class="form-select form-control" id="selectRole"
                                    aria-label="Default select example" required>
                                    <option value="" selected disabled>Select Role</option>
                                    <option value="Doctor">Doctor</option>
                                    <option value="Dentist">Dentist</option>
                                    <option value="Nurse">Nurse</option>
                                    <!-- <option value="Midwife">Midwife</option> -->
                                    <option value="Staff">Staff</option>
                                </select>
                            </div>
                        </div>
                        <div class="col mb-3">
                            <div style="width: 100%;">
                                <label for="selectStatus" class="form-label">Status</label>
                                <select name="status" class="form-select form-control" id="selectStatus"
                                    aria-label="Default select example" required>
                                    <option value="" selected disabled>Select Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="tf-icons bx bxs-save me-1"></span>Save New User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--/ Add User Modal -->


<!-- Update User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="usersFormUpdate">
                @csrf
                <input type="hidden" name="id" id="user_id">

                <div class="modal-header">
                    <h5 class="modal-title">Update User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="error-message" class="alert alert-danger" style="display: none;">
                        <!-- Error message will be displayed here -->
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <label for="nameUpdate" class="form-label">Userame</label>
                            <input name="username" type="text" id="nameUpdate" class="form-control"
                                placeholder="Enter Name">
                        </div>
                        <div class="col mb-3">
                            <label for="emailUpdate" class="form-label">Email</label>
                            <input name="email" type="email" id="emailUpdate" class="form-control"
                                placeholder="xxxx@xxx.xx">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div style="width: 100%;">
                                <label for="selectRoleUpdate" class="form-label">Role</label>
                                <select name="role" class="form-select form-control" id="selectRoleUpdate"
                                    aria-label="Default select example" required>
                                    <option value="" selected disabled>Select Role</option>
                                    <option value="Doctor">Doctor</option>
                                    <option value="Dentist">Dentist</option>
                                    <option value="Nurse">Nurse</option>
                                    <option value="Staff">Staff</option>
                                    <option value="Patient">Patient</option>
                                </select>
                            </div>
                        </div>
                        <div class="col mb-3">
                            <div style="width: 100%;">
                                <label for="selectStatusUpdate" class="form-label">Status</label>
                                <select name="status" class="form-select form-control" id="selectStatusUpdate"
                                    aria-label="Default select example" required>
                                    <option value="" selected disabled>Select Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="tf-icons bx bxs-save me-1"></span>Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--/ Update User Modal -->
@endsection