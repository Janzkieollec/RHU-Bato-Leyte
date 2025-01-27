@php
$containerNav = $containerNav ?? 'container-fluid';
$navbarDetached = $navbarDetached ?? '';

@endphp

<!-- Navbar -->
@if (isset($navbarDetached) && $navbarDetached == 'navbar-detached')
<nav class="layout-navbar {{ $containerNav }} navbar navbar-expand-xl {{ $navbarDetached }} align-items-center bg-navbar-theme"
    id="layout-navbar">
    @endif
    @if (isset($navbarDetached) && $navbarDetached == '')
    <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
        <div class="{{ $containerNav }}">
            @endif

            <!--  Brand demo (display only for navbar-full and hide on below xl) -->
            @if (isset($navbarFull))
            <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
                <a href="{{ url('/') }}" class="app-brand-link gap-2">
                    <span class="app-brand-logo demo">@include('_partials.macros', ['width' => 25, 'withbg' =>
                        'var(--bs-primary)'])</span>
                    <span class="app-brand-text demo menu-text fw-bold">{{ config('variables.templateName') }}</span>
                </a>
            </div>
            @endif

            <!-- ! Not required for layout-without-menu -->
            @if (!isset($navbarHideToggle))
            <div
                class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
                <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                    <i class="bx bx-menu bx-sm"></i>
                </a>
            </div>
            @endif

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

                <div class="navbar-nav align-items-center">
                    <div class="nav-item d-flex align-items-center">
                        Rural Health Unit Information System
                    </div>
                </div>

                <ul class="navbar-nav flex-row align-items-center ms-auto">
                    @if(Auth::user()->role === 'Nurse' || Auth::user()->role === 'Staff')
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" id="notificationsDropdown" data-bs-toggle="dropdown"
                            onclick="hideNotificationCount()">
                            <i class="bx bx-bell"></i>
                            <span class="badge bg-danger" id="notification-count"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">

                            @php
                            $users = \App\Models\User::join('patient_limits', 'users.id', '=', 'patient_limits.user_id')
                            ->whereIn('users.role', ['Doctor', 'Dentist'])
                            ->select('users.username', 'users.role', 'patient_limits.max_patients',
                            'patient_limits.created_at')
                            ->orderBy('patient_limits.id', 'desc')
                            ->whereDate('patient_limits.created_at', \Carbon\Carbon::today())
                            ->take(10)
                            ->get();
                            @endphp

                            @if($users->isEmpty())
                            <li class="dropdown-item text-center">
                                <small id="noti" class="text-muted">No notifications</small>
                            </li>
                            @else
                            @foreach ($users as $user)
                            <li class="dropdown-item">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="bx bx-info-circle"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="d-block fw-medium">
                                            Limit Set
                                            <em
                                                class="float-end">{{ \Carbon\Carbon::parse($user->created_at)->diffForHumans() }}</em>
                                        </span>
                                        <small class="text-muted">
                                            The {{ $user->role }} {{ $user->username }} has set the maximum number of
                                            patients to {{ $user->max_patients }}
                                        </small>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                            @endif
                            <li>
                                <div class="dropdown-divider"></div>
                            </li>
                            <li class="dropdown-header">Notifications</li>
                            <!-- Notifications will be dynamically added here -->
                        </ul>
                    </li>
                    @endif



                    <!-- User -->
                    <li class="nav-item navbar-dropdown dropdown-user dropdown">
                        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                            data-bs-toggle="dropdown">
                            <div class="avatar avatar-online">
                                <img src="{{ asset(Auth::user()->profile->profile_picture ?? 'assets/img/avatars/avatar.png') }}"
                                    alt class="w-px-40 h-auto rounded-circle">
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="javascript:void(0);">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar avatar-online">
                                                <img src="{{ asset(Auth::user()->profile->profile_picture ?? 'assets/img/avatars/avatar.png') }}"
                                                    alt class="w-px-40 h-auto rounded-circle">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="fw-medium d-block mb-1">
                                                @auth
                                                {{ Auth::user()->username }}
                                                @endauth
                                            </span>
                                            <small class="badge rounded-pill bg-label-success me-1">
                                                @auth
                                                {{ ucfirst(Auth::user()->role) }}
                                                <!-- Display role (Admin, Doctor, etc.) -->
                                                @endauth
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <div class="dropdown-divider"></div>
                            </li>
                            <li>
                                <a href="/profile" type="button" class="dropdown-item">
                                    <i class="fa-solid fa-id-badge me-2"></i>
                                    <span class="align-middle">Profile</span>
                                </a>
                            </li>
                            <li>
                                <a href="/logs" type="button" class="dropdown-item">
                                    <i class="fa-solid fa-clock-rotate-left me-2"></i>
                                    <span class="align-middle">Logs</span>
                                </a>
                            </li>
                            <li>
                                <form id="logout">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bx bx-power-off me-2"></i>
                                        <span class="align-middle">Log Out</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    <!-- /User -->

                </ul>
            </div>


            @if (!isset($navbarDetached))
        </div>
        @endif
    </nav>
    <!-- / Navbar -->

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
    function hideNotificationCount() {
        let countElement = document.getElementById('notification-count');

        if (countElement.innerText !== '0') {
            countElement.innerText = ''; // Hide the notification count
        }
    }

    Echo.channel('nurse-notifications')
        .listen('.max-patients-set', (event) => {
            let notificationCount = parseInt($('#notification-count').text()) || 0;
            $('#notification-count').text(notificationCount + 1);

            // Capture the timestamp when the notification is received
            let currentTime = new Date();

            // Function to format time as "x min ago"
            function timeAgo(timestamp) {
                let now = new Date();
                let secondsAgo = Math.floor((now - timestamp) / 1000);

                if (secondsAgo < 60) {
                    return `${secondsAgo} sec ago`;
                } else if (secondsAgo < 3600) {
                    let minutesAgo = Math.floor(secondsAgo / 60);
                    return `${minutesAgo} min ago`;
                } else if (secondsAgo < 86400) {
                    let hoursAgo = Math.floor(secondsAgo / 3600);
                    return `${hoursAgo} hour${hoursAgo > 1 ? 's' : ''} ago`;
                } else {
                    let daysAgo = Math.floor(secondsAgo / 86400);
                    return `${daysAgo} day${daysAgo > 1 ? 's' : ''} ago`;
                }
            }

            let newNotification = `
            <li>
                <a class="dropdown-item">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <i class="bx bx-info-circle"></i>
                        </div>
                        <div class="flex-grow-1">
                            <span class="d-block fw-medium">Limit Set
                                <em style="float: right;" class="time-ago" data-timestamp="${currentTime}">${timeAgo(currentTime)}</em>
                            </span>
                            <small class="text-muted">${event.message}</small>
                        </div>
                    </div>
                </a>
            </li>
        `;

            // Hide the "No notifications" message if visible
            $('#noti').hide();

            // Prepend the notification to the dropdown menu
            $('#notificationsDropdown').siblings('.dropdown-menu').prepend(newNotification);

            // Periodically update all "time-ago" elements
            setInterval(() => {
                $('.time-ago').each(function() {
                    let timestamp = new Date($(this).data('timestamp'));
                    $(this).text(timeAgo(timestamp));
                });
            }, 60000); // Update every 60 seconds

            $('#patient-limit').fadeOut(300, function() {
                $(this).text(
                    `${event.patientLimit.current_patients}/${event.patientLimit.max_patients} Patients`
                ).fadeIn(300);
            });

        });
    </script>