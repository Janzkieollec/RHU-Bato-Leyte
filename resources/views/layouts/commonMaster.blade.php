@php
$user = Auth::user();

// Fetch the first Admin user and get their logo
$admin = \App\Models\User::where('role', 'Admin')->first();
$municipalLogo = $admin && $admin->profile && $admin->profile->municipal_logo
? $admin->profile->municipal_logo
: 'assets/img/favicon/rhu-logo.ico'; // Default logo
@endphp

<!DOCTYPE html>

<html class="light-style layout-menu-fixed" data-theme="theme-default" data-assets-path="{{ asset('/assets') . '/' }}"
    data-base-url="{{url('/')}}" data-framework="laravel" data-template="vertical-menu-laravel-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title') | Rural Health Unit Information System </title>
    <meta name="description"
        content="{{ config('variables.templateDescription') ? config('variables.templateDescription') : '' }}" />
    <meta name="keywords"
        content="{{ config('variables.templateKeyword') ? config('variables.templateKeyword') : '' }}">
    <!-- laravel CRUD token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Canonical SEO -->
    <link rel="canonical" href="{{ config('variables.productPage') ? config('variables.productPage') : '' }}">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset($municipalLogo) }}" />
    <!-- Font Awesome Icons -->

    <!-- Font Awesome 6 -->
    <link href="{{ asset('assets/vendor/fonts/fontawesome/css/fontawesome.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/fonts/fontawesome/css/brands.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/fonts/fontawesome/css/solid.css') }}" rel="stylesheet">

    <!-- Include Styles -->
    @include('layouts/sections/styles')

    <!-- Include Scripts for customizer, helper, analytics, config -->
    @include('layouts/sections/scriptsIncludes')
</head>

<body>


    <!-- Layout Content -->
    @yield('layoutContent')
    <!--/ Layout Content -->


    <!-- Include Scripts -->
    @include('layouts/sections/scripts')
</body>

<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets/js/login-register.js') }}"></script>
<script src="https://accounts.google.com/gsi/client" async defer></script>

<script src="{{ asset('assets/js/google-sso.js') }}"></script>

</html>