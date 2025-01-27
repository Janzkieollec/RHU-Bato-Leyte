@php
$user = Auth::user();

// Fetch the first Admin user and get their logo
$admin = \App\Models\User::where('role', 'Admin')->first();
$municipalLogo = $admin && $admin->profile && $admin->profile->municipal_logo
? $admin->profile->municipal_logo
: 'assets/img/favicon/rhu-logo.ico'; // Default logo
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link href="{{ asset('assets/vendor/css/style.css') }}" rel="stylesheet">
    <!-- Add the favicon link here -->
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset($municipalLogo) }}" />


    <title>Rural Health Unit Information System</title>
</head>

<body>
    <header>
        <nav>
            <div class="nav__logo">
                <img src="{{ asset($user->profile->municipal_logo ?? 'assets/img/favicon/login-rhu.png') }}"
                    alt="RHU Logo" class="logo-img" />
                <span>RHU Bato Leyte</span>
            </div>

            <ul class="nav__links" id="nav-links">
                <li class="link"><a href="#home" class="home">Home</a></li>
                <li class="link"><a href="#announcements" class="announcements">Announcements</a></li>
                <li class="link"><a href="#map" class="maps">Maps</a></li>
                <li class="link sign-in"><a href="/login" class="signin">Sign In</a></li> <!-- Sign In Button -->
            </ul>
            <div class="nav__menu__btn" id="menu-btn">
                <span><i class="ri-menu-line"></i></span>
            </div>

        </nav>
    </header>

    <section class="send section" id="announcements">
        <div class="send__container bd-container">
            <h2 class="section-title send__title">
                Announcements
            </h2>

            @if($announcements->isEmpty())
            <p class="announcement">No announcements available at this time.</p>
            @else
            <div class="announcement-slider">
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        @foreach($announcements as $announcement)
                        <div class="swiper-slide">
                            <h3 class="announcement-title">{{ $announcement->title }}</h3>
                            <p class="announcement-date"><b>Date</b>: {{ $announcement->date }}</p>
                            @if(!empty($announcement->location))
                            <p class="announcement-location"><b>Location</b>: {{ $announcement->location }}</p>
                            @endif
                            <p class="announcement-content">{{ $announcement->content }}</p>
                        </div>
                        @endforeach
                    </div>

                    <!-- Add Pagination -->
                    <div class="swiper-pagination"></div>

                    <!-- Add Navigation -->
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            </div>
            @endif
        </div>
    </section>


    <div class="section__container maps_container" id="map">
        <h1>Map of Rural Health Unit in Bato, Leyte</h1>
        <div id="mapContainer" style="height: 400px;"></div>
    </div>

    <footer class="footer">
        <div class="footer__bar">
            Copyright © 2024 Rural Health Unit. All rights reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="{{ asset('assets/vendor/js/main.js') }}"></script>

    <script>
    // Initialize the map
    const map = L.map('mapContainer', {
        center: [10.3286, 124.7889], // Coordinates for Bato, Leyte
        zoom: 14,
        zoomControl: false, // Hide the zoom control
        dragging: false, // Disable dragging
        scrollWheelZoom: false, // Disable zoom on scroll
        doubleClickZoom: false, // Disable zoom on double-click
        boxZoom: false, // Disable box zoom
        touchZoom: false // Disable touch zoom on mobile devices
    });

    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add a marker for the RHU
    const marker = L.marker([10.326768865112209, 124.79121227901517]).addTo(map);
    marker.bindPopup('<b>Rural Health Unit</b><br>Bato, Leyte').openPopup();
    </script>
</body>

</html>