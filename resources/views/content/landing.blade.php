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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--========== BOX ICONS ==========-->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

    <!--========== CSS ==========-->
    <link href="{{ asset('assets/vendor/css/style1.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <!-- Include Swiper.js -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <link rel="icon" type="image/x-icon" href="{{ asset($municipalLogo) }}" />

    <title>Rural Health Unit Information System</title>
</head>

<body>
    <!--========== SCROLL TOP ==========-->
    <a href="#" class="scrolltop" id="scroll-top">
        <i class='bx bx-up-arrow-alt scrolltop__icon'></i>
    </a>

    <!--========== HEADER ==========-->
    <header class="l-header" id="header">
        <nav class="nav bd-container">
            <div class="nav__logo">
                <img src="{{ asset($user->profile->municipal_logo ?? 'assets/img/favicon/login-rhu.png') }}"
                    alt="RHU Logo" class="logo-img" />
                <span>&nbsp RHU Bato Leyte</span>
            </div>

            <div class="nav__menu" id="nav-menu">
                <ul class="nav__list">
                    <li class="nav__item"><a href="#home" class="nav__link active-link">Home</a></li>
                    <li class="nav__item"><a href="#announcements" class="nav__link">Announcements</a></li>
                    <li class="nav__item"><a href="#aboutus" class="nav__link">About Us</a></li>
                    <li class="nav__item"><a href="#maps" class="nav__link">Maps</a></li>
                    <li class="nav__item"><a href="/login" class="nav__link">Sign In</a></li>


                    <li><i class='<!--bx bx-moon--> change-theme' id="theme-button"></i></li>
                </ul>
            </div>


            <div class="nav__toggle" id="nav-toggle">
                <i class='bx bx-grid-alt'></i>
            </div>
        </nav>
    </header>

    <main class="l-main">
        <!--========== HOME ==========-->
        <section class="home" id="home">
            <div class="home__container bd-container bd-grid">
                <div class="home__img">
                    <img src="{{ asset($user->profile->landing_page_picture ?? 'assets/img/favicon/banner.png') }}"
                        alt="Rural Health" class="header__image" />
                </div>

                <div class="home__data">
                    <h1 class="home__title">Improving Healthcare Access in Bato, Leyte</h1>
                    <p class="home__description">RHU Bato Leyte is committed to providing accessible and quality
                        healthcare services to the community.</p>

                </div>
            </div>
        </section>

        <!--========== Announcement ==========-->
        <section class="send section" id="announcements">
            <div class="announcements__container bd-container">
                <h2 class="section-title send__title">
                    Announcements
                </h2>
                @if($announcements->isEmpty())
                <p class="announcement">No announcements available at this time.</p>
                @else
                <!-- Swiper Container -->
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        @foreach($announcements as $announcement)
                        <div class="swiper-slide">
                            <div class="announcement-slide-content">
                                <h3 class="announcement-title">{{ $announcement->title }}</h3>
                                <p class="announcement-date"><b>Date</b>: {{ $announcement->date }}</p>
                                @if(!empty($announcement->location))
                                <p class="announcement-location"><b>Location</b>: {{ $announcement->location }}</p>
                                @endif
                                <br>
                                <p class="announcement-content">{{ $announcement->content }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <!-- Add navigation buttons -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                @endif
            </div>
        </section>

        <!--========== About Us ==========-->
        <section class="aboutus section bd-container" id="aboutus">
            <h2 class="section-title">About Us</h2>

            <div class="container">
                <h3 class="aboutus__subtitle">Who We Are</h3>
                <p class="aboutus__text">
                    The Rural Health Unit of Bato, Leyte is dedicated to improving the quality of life in our community
                    by providing accessible healthcare services, health education, and preventive care. Our dedicated
                    team of healthcare professionals works tirelessly to ensure that every resident receives the care
                    and attention they deserve.
                </p>
            </div>

            <div class="aboutus__container">
                <div class="aboutus__content">
                    <img src="{{ asset('assets/img/favicon/book.png')}}" alt="" class="aboutus__img">
                    <h3 class="aboutus__title">Mission</h3>
                    <span class="aboutus__category">
                        To provide personalized and exceptional care for our patients in a compassionate, knowledgeable,
                        highly skilled and professional manner.
                    </span>
                </div>

                <div class="aboutus__content">
                    <img src="assets/img/favicon/earth.png" alt="" class="aboutus__img">
                    <h3 class="aboutus__title">Vision</h3>
                    <span class="aboutus__category">
                        Continuous provison of exceptional care to our patients with twenty-four hour a day coverage by
                        dedicated obstetrical RHU staff who are certified in Basic Emergency Obstrical and Neonatal Care
                        (BEmONC).
                    </span>
                </div>
            </div>
        </section>

        <!--========== Core Values ==========-->
        <section class="core-values section bd-container" id="core-values">
            <h2 class="section-title">Our Values</h2>

            <div class="core-values__container bd-grid">
                <!-- Core Value 1 -->
                <div class="core-values__content">
                    <h3 class="core-values__title">1</h3>
                    <p class="core-values__description">
                        We begin each new family experience in a personalized and loving environment
                        including all members of the family unit as appropriate. We endeavor to give the
                        couple the delivery experience they desire.
                    </p>
                </div>

                <!-- Core Value 2 -->
                <div class="core-values__content">
                    <h3 class="core-values__title">2</h3>
                    <p class="core-values__description">
                        We provide high quality services to all, regardless of race, color, creed, ethnic
                        origin, sexual orientation, gender, economic resources or disease process.
                    </p>
                </div>

                <!-- Core Value 3 -->
                <div class="core-values__content">
                    <h3 class="core-values__title">3</h3>
                    <p class="core-values__description">
                        We are competent and caring employees who treat all persons in a professional
                        manner and embrace the mission of the facility.
                    </p>
                </div>

                <!-- Core Value 4 -->
                <div class="core-values__content">
                    <h3 class="core-values__title">4</h3>
                    <p class="core-values__description">
                        We use the most updated technology possible, but personal loving, touching
                        nursing care is paramount and we do not allow technology to overshadow that
                        care.
                    </p>
                </div>

                <!-- Core Value 5 -->
                <div class="core-values__content">
                    <h3 class="core-values__title">5</h3>
                    <p class="core-values__description">
                        We are here to provide information, education and support in a competent and
                        dignified manner to help this new family unit begin as smoothly as possible.
                    </p>
                </div>

                <!-- Core Value 6 -->
                <div class="core-values__content">
                    <h3 class="core-values__title">6</h3>
                    <p class="core-values__description">
                        We value a working environment that enhances and encourages the growth of
                        each nurse as a provider, manager, teacher and advocate and where all work
                        together as a cohesive team.
                    </p>
                </div>
            </div>
        </section>



        <!--========== Maps ==========-->
        <section class="maps section bd-container" id="maps">
            <h2 class="section-title">Map of Rural Health Unit in <br> Bato, Leyte </h2>

            <div class="mapContainer" id="mapContainer" style="height: 400px;"></div>
        </section>
    </main>

    <!--========== FOOTER ==========-->
    <footer class="footer section">
        <p class="footer__copy">&#169; Copyright © 2024 Rural Health Unit. All rights reserved.</p>
    </footer>

    <!--========== SCROLL REVEAL ==========-->
    <script src="https://unpkg.com/scrollreveal"></script>

    <!--========== MAIN JS ==========-->
    <script src="{{ asset('assets/vendor/js/main1.js') }}"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

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

<script>
// Initialize the Swiper slider with only one announcement per slide
const swiper = new Swiper('.swiper-container', {
    loop: true, // Enable looping for continuous sliding
    slidesPerView: 1, // Show one slide at a time
    spaceBetween: 10, // Space between slides
    autoplay: {
        delay: 5000, // Slide every 5 seconds
    },
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
});
</script>