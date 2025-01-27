@extends('layouts/blankLayout')

@section('title', 'Unauthorized | Rural Health Unit Information Management System')

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-misc.css')}}">
@endsection

@section('content')
<!-- Error -->
<div class="container-xxl container-p-y">
    <div class="misc-wrapper">
        <h2 class="mb-2 mx-2">Unauthorized Access</h2>
        <p class="mb-4 mx-2">Oops! 😖 You don't have permission to access this page.</p>

        @php
        // Check the user's role and set the redirection URL accordingly
        $redirectUrl = '';
        if (Auth::check()) {
        $role = Auth::user()->role; // Assuming 'role' is the field where the user's role is stored

        if ($role === 'Admin') {
        $redirectUrl = url('/dashboard'); // Replace with the actual Admin dashboard route
        } elseif ($role === 'Doctor') {
        $redirectUrl = url('/doctor-dashboard'); // Replace with the actual Doctor dashboard route
        } elseif ($role === 'Dentist') {
        $redirectUrl = url('/dentist-dashboard'); // Default dashboard or home page for other roles
        } elseif ($role === 'Nurse') {
        $redirectUrl = url('/nurse-dashboard'); // Default dashboard or home page for other roles
        } elseif ($role === 'Staff') {
        $redirectUrl = url('/staff-dashboard'); // Default dashboard or home page for other roles
        } elseif ($role === 'Patient') {
        $redirectUrl = url('/patient-dashboard'); // Default dashboard or home page for other roles
        } else {
        $redirectUrl = url('/login'); // Redirect to login page if user is not authenticated
        }
        }
        @endphp

        <a href="{{ $redirectUrl }}" class="btn btn-primary">Back to Home</a>

        <div class="mt-3">
            <img src="{{asset('assets/img/illustrations/page-misc-error-light.png')}}" alt="page-misc-error-light"
                width="500" class="img-fluid">
        </div>
    </div>
</div>
<!-- /Error -->
@endsection