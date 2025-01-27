@php
$user = Auth::user();

// Fetch the first Admin user and get their logo
$admin = \App\Models\User::where('role', 'Admin')->first();
$municipalLogo = $admin && $admin->profile && $admin->profile->municipal_logo
? $admin->profile->municipal_logo
: 'assets/img/favicon/rhu-logo.ico'; // Default logo
@endphp

@extends('layouts/blankLayout')

@section('title', 'Login')

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-auth.css')}}">
@endsection

@section('content')

<div class="authentication-wrapper authentication-cover">
    <div class="authentication-inner row m-0">
        <div class="container">
            <div class="authentication-wrapper authentication-basic container-p-y">
                <div class="authentication-inner">
                    <div class="card">
                        <div class="card-body">
                            <div class="app-brand justify-content-center">
                                <a href="{{ url('/') }}" class="app-brand-link gap-2">
                                    <span class="app-brand-text demo text-body fw-bold">
                                        <img height="100" src="{{ asset( $municipalLogo ) }}" alt="">
                                    </span>
                                </a>
                            </div>
                            <h2 class="mb-2 text-center">RHU Patient Records Management System</h2>

                            <div id="loginmsg"></div>
                            <div id="error-alert" class="alert alert-danger text-center"
                                style="display:none; padding: 1px;">
                                <p id="error" style="color:red;"></p>
                                <p id="error-message" style="display:none; color:red;"></p>
                                <p id="error-time" style="display:none; color:red;"></p>
                            </div>

                            <form id="loginForm" class="mb-3">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" class="form-control" id="email" name="email"
                                        placeholder="Enter your email" autofocus>
                                </div>
                                <div class="mb-3 form-password-toggle">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label" for="password">Password</label>
                                    </div>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="password" class="form-control" name="password"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                            aria-describedby="password" />
                                        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-dark d-grid w-100" type="submit" id="btnLogin">Login</button>
                                </div>

                                <div id="text" class="justify-content-center divider d-flex align-items-center my-2">
                                    <div class="flex-grow-1">
                                        <hr class="my-0">
                                    </div>
                                    <p class="text-center fw-bold mx-3 mb-0 text-muted">OR</p>
                                    <div class="flex-grow-1">
                                        <hr class="my-0">
                                    </div>
                                </div>

                                <div class="row justify-content-center mb-3">
                                    <!-- Google Sign-In Button (via API) -->
                                    <div class="col-md-12 text-center" id="g_id_onload"
                                        data-client_id="{{ env('GOOGLE_CLIENT_ID') }}" data-callback="onSignIn"></div>

                                    <!-- Fallback Button if API fails -->
                                    <div class="col-md-12 text-center">
                                        <label class="text text-center">Please sign-in to your account.</label>
                                        <button class="g_id_signin btn btn-light form-control"
                                            data-type="standard"></button>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <a href="/" class="form-text">
                                        <span class="bx bxs-chevron-left"></span>Back
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection