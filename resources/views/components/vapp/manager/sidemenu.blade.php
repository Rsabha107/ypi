<nav class="navbar navbar-vertical navbar-expand-lg" data-navbar-appearance="darker">
    <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
        <!-- scrollbar removed-->
        <div class="navbar-vertical-content">
            <ul class="navbar-nav flex-column" id="navbarVerticalNav">
                <li class="nav-item">
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nv-home" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-home">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper"><span class="fas fa-caret-right dropdown-indicator-icon"></span></div><span class="nav-link-icon"><span data-feather="pie-chart"></span></span><span class="nav-link-text">Home</span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-home">
                                <li class="collapsed-nav-item-title d-none">Home
                                </li>
                                {{-- <li class="nav-item"><a class="nav-link" href="{{ route('vapp.manager.dashboard') }}">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">Dashboard</span>
                                        </div>
                                    </a>
                                    <!-- more inner pages-->
                                </li> --}}
                            </ul>
                        </div>
                    </div>
                </li>
                <li class="nav-item">
                    <!-- label-->
                    <p class="navbar-vertical-label">Apps
                    </p>
                    <hr class="navbar-vertical-line" />
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator  label-1" href="#nv-VAPP" role="button" data-bs-toggle="collapse" aria-expanded="true" aria-controls="nv-VAPP">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper"><span class="fas fa-caret-right dropdown-indicator-icon"></span></div><span class="nav-link-icon"><span data-feather="phone"></span></span><span class="nav-link-text">VAPP</span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent {{ Request::is('vapp/manager/booking')||Request::is('vapp/manager/booking/create') ? 'show' : '' }}" data-bs-parent="#navbarVerticalCollapse" id="nv-VAPP">
                                <li class="collapsed-nav-item-title d-none">VAPP
                                </li>
                                <li class="nav-item"><a class="nav-link {{ Request::is('vapp/manager/booking') ? 'active' : '' }}" href="{{route('vapp.manager.booking')}}">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">List of Requests</span>
                                        </div>
                                    </a>
                                    <!-- more inner pages-->
                                </li>
                                <li class="nav-item"><a class="nav-link {{ Request::is('vapp/manager/booking/create') ? 'active' : '' }}" href="{{route('vapp.manager.booking.create')}}">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">Make a Request</span>
                                        </div>
                                    </a>
                                    <!-- more inner pages-->
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="navbar-vertical-footer">
        <button class="btn navbar-vertical-toggle border-0 fw-semibold w-100 white-space-nowrap d-flex align-items-center"><span class="uil uil-left-arrow-to-left fs-8"></span><span class="uil uil-arrow-from-right fs-8"></span><span class="navbar-vertical-footer-text ms-2">Collapsed View</span></button>
    </div>
</nav>