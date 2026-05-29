<nav class="navbar navbar-vertical navbar-expand-lg" data-navbar-appearance="darker">
    <script>
        var navbarStyle = window.config.config.phoenixNavbarStyle;
        if (navbarStyle && navbarStyle !== 'transparent') {
            document.querySelector('body').classList.add(`navbar-${navbarStyle}`);
        }
    </script>
    <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
        <div class="navbar-vertical-content">
            <ul class="navbar-nav flex-column" id="navbarVerticalNav">
                
                <!-- Dashboard / Home -->
                <li class="nav-item">
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1" href="{{ route('ypi.catering.participant') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="home"></span></span>
                                <span class="nav-link-text">Home</span>
                            </div>
                        </a>
                    </div>
                </li>

                <!-- Participants Section -->
                <li class="nav-item">
                    <div class="nav-item-wrapper">
                        <a class="nav-link dropdown-indicator label-1" href="#nv-participants" role="button"
                            data-bs-toggle="collapse" aria-expanded="true" aria-controls="nv-participants">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper">
                                    <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                </div>
                                <span class="nav-link-icon"><span data-feather="users"></span></span>
                                <span class="nav-link-text">Participants</span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent show" data-bs-parent="#navbarVerticalCollapse"
                                id="nv-participants">
                                <li class="collapsed-nav-item-title d-none">Participants</li>
                                
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('ypi.catering.participant') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">View All Participants</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

                <!-- Dietary Information Section -->
                <li class="nav-item">
                    <div class="nav-item-wrapper">
                        <a class="nav-link dropdown-indicator label-1" href="#nv-dietary" role="button"
                            data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-dietary">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper">
                                    <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                </div>
                                <span class="nav-link-icon"><span data-feather="clipboard"></span></span>
                                <span class="nav-link-text">Dietary Info</span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse"
                                id="nv-dietary">
                                <li class="collapsed-nav-item-title d-none">Dietary Info</li>
                                
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('ypi.catering.participant') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Food Allergies</span>
                                            <span class="badge badge-phoenix badge-phoenix-warning ms-auto">View</span>
                                        </div>
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('ypi.catering.participant') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Health Issues</span>
                                            <span class="badge badge-phoenix badge-phoenix-warning ms-auto">View</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

                <!-- Profile Section -->
                <li class="nav-item">
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1" href="{{ route('admin.users.profile') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="user"></span></span>
                                <span class="nav-link-text">My Profile</span>
                            </div>
                        </a>
                    </div>
                </li>

                <!-- Logout -->
                <li class="nav-item">
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="log-out"></span></span>
                                <span class="nav-link-text">Logout</span>
                            </div>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>

            </ul>
        </div>
    </div>
</nav>

<!-- Navbar Toggle Button for Mobile -->
<div class="navbar-vertical-footer">
    <button class="btn navbar-vertical-toggle border-0 fw-semibold w-100 white-space-nowrap d-flex align-items-center">
        <span class="uil uil-left-arrow-to-left fs-8"></span>
        <span class="uil uil-arrow-from-right fs-8"></span>
        <span class="navbar-vertical-footer-text ms-2">Collapsed View</span>
    </button>
</div>
