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
                                <li class="nav-item"><a class="nav-link" href="{{ route('vapp.admin.dashboard') }}">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">Dashboard</span>
                                        </div>
                                    </a>
                                    <!-- more inner pages-->
                                </li>
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
                            <ul class="nav collapse parent {{ Request::is('vapp/admin/booking')||Request::is('vapp/admin/booking/create') ? 'show' : '' }}" data-bs-parent="#navbarVerticalCollapse" id="nv-VAPP">
                                <li class="collapsed-nav-item-title d-none">VAPP
                                </li>
                                <li class="nav-item"><a class="nav-link {{ Request::is('vapp/admin/booking') ? 'active' : '' }}" href="{{route('vapp.admin.booking')}}">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">List of Requests</span>
                                        </div>
                                    </a>
                                    <!-- more inner pages-->
                                </li>
                                <li class="nav-item"><a class="nav-link {{ Request::is('vapp/admin/booking/create') ? 'active' : '' }}" href="{{route('vapp.admin.booking.create')}}">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">Make a Request</span>
                                        </div>
                                    </a>
                                    <!-- more inner pages-->
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
                @if (Auth::user()->can('setup.menu'))
                <li class="nav-item">
                    <!-- label-->
                    <p class="navbar-vertical-label">Settings
                    </p>
                    <hr class="navbar-vertical-line" />
                    <!-- parent pages-->
                    @can('setup.admin.menu')
                    <div class="nav-item-wrapper"><a class="nav-link label-1 {{ Request::is('vapp/setting/vapp_size') ? 'active' : '' }}" href="{{route('vapp.setting.vapp_size')}}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="compass"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">VAPP Sizes</span></span>
                            </div>
                        </a>
                    </div>
                    <div class="nav-item-wrapper"><a class="nav-link label-1 {{ Request::is('vapp/setting/event') ? 'active' : '' }}" href="{{route('vapp.setting.event')}}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="compass"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Event</span></span>
                            </div>
                        </a>
                    </div>
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1 {{ Request::is('vapp/setting/vehicle_type') ? 'active' : '' }}" href="{{route('vapp.setting.vehicle_type')}}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="compass"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Vehicle Type</span></span>
                            </div>
                        </a>
                    </div>
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1 {{ Request::is('vapp/setting/parking/master') ? 'active' : '' }}" href="{{route('vapp.setting.parking.master')}}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="compass"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Parking Master</span></span>
                            </div>
                        </a>
                    </div>
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1 {{ Request::is('vapp/setting/parking') ? 'active' : '' }}" href="{{route('vapp.setting.parking')}}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="compass"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Parking Capacity</span></span>
                            </div>
                        </a>
                    </div>
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1 {{ Request::is('vapp/setting/parking/variation') ? 'active' : '' }}" href="{{route('vapp.setting.parking.variation')}}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="compass"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">VAPP Variations</span></span>
                            </div>
                        </a>
                    </div>
                    <div class="nav-item-wrapper"><a class="nav-link label-1 {{ Request::is('vapp/setting/match') ? 'active' : '' }}" href="{{route('vapp.setting.match')}}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="compass"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Match</span></span>
                            </div>
                        </a>
                    </div>
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1 {{ Request::is('vapp/setting/inventory') ? 'active' : '' }}" href="{{route('vapp.setting.inventory')}}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="compass"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">VAPP Inventory</span></span>
                            </div>
                        </a>
                    </div>
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1 {{ Request::is('vapp/setting/funcareas') ? 'active' : '' }}" href="{{route('vapp.setting.funcareas')}}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="compass"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Functional Area</span></span>
                            </div>
                        </a>
                    </div>
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1 {{ Request::is('vapp/setting/venue') ? 'active' : '' }}" href="{{route('vapp.setting.venue')}}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="compass"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Venue</span></span>
                            </div>
                        </a>
                    </div>
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1 {{ Request::is('vapp/setting/collection') ? 'active' : '' }}" href="{{route('vapp.setting.collection')}}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="compass"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Collection</span></span>
                            </div>
                        </a>
                    </div>
                    @endcan
                </li>
                @endif
                @if (Auth::user()->hasRole('SecurityRole'))
                <li class="nav-item">
                    <!-- label-->
                    <p class="navbar-vertical-label">Roles and Permissions
                    </p>
                    <hr class="navbar-vertical-line" />
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nv-list" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-list">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper"><span class="fas fa-caret-right dropdown-indicator-icon"></span></div><span class="nav-link-icon"><span data-feather="file-text"></span></span><span class="nav-link-text">List</span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-list">
                                <li class="collapsed-nav-item-title d-none">List
                                </li>
                                <li class="nav-item"><a class="nav-link {{ Request::is('sec/groups/list') ? 'active' : '' }}" href="{{route('sec.groups.list')}}">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">Groups</span>
                                        </div>
                                    </a>
                                    <!-- more inner pages-->
                                </li>
                                <li class="nav-item"><a class="nav-link {{ Request::is('sec/permissions/list') ? 'active' : '' }}" href="{{route('sec.perm.list')}}">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">Permissions</span>
                                        </div>
                                    </a>
                                    <!-- more inner pages-->
                                </li>
                                <li class="nav-item"><a class="nav-link {{ Request::is('sec/roles/list') ? 'active' : '' }}" href="{{route('sec.roles.list')}}">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">Roles</span>
                                        </div>
                                    </a>
                                    <!-- more inner pages-->
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::is('log-viewer') ? 'active' : '' }}"
                                        href="{{ route('log-viewer.index') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Application Log</span>
                                        </div>
                                    </a>
                                    <!-- more inner pages-->
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1 {{ Request::is('sec/rolesetup/list') ? 'active' : '' }}" href="{{route('sec.rolesetup.list')}}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="server"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Roles in Permission</span></span>
                            </div>
                        </a>
                    </div>
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1 {{ Request::is('sec/audit') ? 'active' : '' }}" href="{{route('sec.audit')}}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="server"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Audit</span></span>
                            </div>
                        </a>
                    </div>

                </li>
                @endif
                @if (Auth::user()->can('manage.admin.users.menu'))
                <li class="nav-item">
                    <!-- label-->
                    <p class="navbar-vertical-label">User Management
                    </p>
                    <hr class="navbar-vertical-line" />
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1 {{ Request::is('sec/adminuser/list')}}" href="{{route('sec.adminuser.list')}}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="life-buoy"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">List Users</span></span>
                            </div>
                        </a>
                    </div>
                    <div class="nav-item-wrapper"><a class="nav-link label-1 {{ Request::is('sec/adminuser/add')}}" href="{{route('sec.adminuser.add')}}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="life-buoy"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Add User</span></span>
                            </div>
                        </a>
                    </div>
                    <div class="nav-item-wrapper"><a class="nav-link label-1 {{ Request::is('/auth/ms-signup')}}" href="{{route('auth.ms.signup')}}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="life-buoy"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Grant Access</span></span>
                            </div>
                        </a>
                    </div>
                    <div class="nav-item-wrapper"><a class="nav-link label-1 {{ Request::is('vapp/admin/users/invite-user')}}" href="{{route('vapp.admin.users.invite.form')}}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="life-buoy"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Invite Users</span></span>
                            </div>
                        </a>
                    </div>
                </li>
                @endif
                <li class="nav-item">
                    <!-- label-->
                    <p class="navbar-vertical-label">General</p>
                    <hr class="navbar-vertical-line" />
                    <div class="nav-item-wrapper">
                        <a class="nav-link dropdown-indicator label-1" href="#nv-customization" role="button"
                            data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-customization">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper">
                                    <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                </div>
                                <span class="nav-link-icon"><span data-feather="settings"></span></span><span
                                    class="nav-link-text">Settings</span><span
                                    class="fa-solid fa-circle text-info ms-1 new-page-indicator" style="font-size: 6px"></span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-customization">
                                <li class="collapsed-nav-item-title d-none">
                                    Customization
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('vapp.setting.application') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Application Settings</span>
                                        </div>
                                    </a>
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