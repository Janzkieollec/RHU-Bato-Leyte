@php
$user = Auth::user();
$width = $width ?? '50';
$withbg = $withbg ?? '#696cff';

// Fetch the first Admin user and get their logo
$admin = \App\Models\User::where('role', 'Admin')->first();
$municipalLogo = $admin && $admin->profile && $admin->profile->municipal_logo
? $admin->profile->municipal_logo
: 'assets/img/favicon/rhu-logo.ico'; // Default logo
@endphp

<img src="{{ asset($municipalLogo) }}" width="{{ $width }}" height="{{ $width }}" />