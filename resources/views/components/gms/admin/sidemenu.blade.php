<nav class="navbar navbar-vertical navbar-expand-lg" data-navbar-appearance="darker">
    <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
        <!-- scrollbar removed-->
        <div class="navbar-vertical-content">
            <ul class="navbar-nav flex-column" id="navbarVerticalNav">
                <li class="nav-item">
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nv-home"
                            role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-home">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper"><span
                                        class="fas fa-caret-right dropdown-indicator-icon"></span></div><span
                                    class="nav-link-icon"><span data-feather="pie-chart"></span></span><span
                                    class="nav-link-text">Home</span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-home">
                                <li class="collapsed-nav-item-title d-none">Home
                                </li>
                                <li class="nav-item"><a class="nav-link" href="#">
                                        <div class="d-flex align-items-center"><span
                                                class="nav-link-text">Dashboard</span>
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
                    <p class="navbar-vertical-label">Apps</p>
                    <hr class="navbar-vertical-line" />

                    <div class="nav-item-wrapper">
                        <a class="nav-link dropdown-indicator label-1" href="#nv-guest" role="button"
                            data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-guest">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper">
                                    <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                </div>
                                <span class="nav-link-icon"><span data-feather="users"></span></span>
                                <span class="nav-link-text">GMS</span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent {{ Request::is('gms/admin/guest*') || Request::is('gms/setting/*') ? 'show' : '' }}"
                                data-bs-parent="#navbarVerticalCollapse" id="nv-guest">
                                <li class="collapsed-nav-item-title d-none">Guest</li>

                                <!-- Guest main pages -->
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::is('gms/admin/guest') ? 'active' : '' }}"
                                        href="{{ route('gms.admin.guest') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Guest Registry</span>
                                        </div>
                                    </a>
                                </li>
                                <!-- Flight main pages -->
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::is('gms/admin/flight') ? 'active' : '' }}"
                                        href="{{ route('gms.admin.flight') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Flights</span>
                                        </div>
                                    </a>
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
                        <div class="nav-item-wrapper">
                            <a class="nav-link dropdown-indicator label-1" href="#nv-guest-setting" role="button"
                                data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-guest-setting">
                                <div class="d-flex align-items-center">
                                    <div class="dropdown-indicator-icon-wrapper">
                                        <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                    </div>
                                    <span class="nav-link-icon"><span data-feather="users"></span></span>
                                    <span class="nav-link-text">Guest Setting</span>
                                </div>
                            </a>
                            <div class="parent-wrapper label-1">
                                <ul class="nav collapse parent {{ Request::is('gms/admin/flight*') || Request::is('gms/setting/*') ? 'show' : '' }}"
                                    data-bs-parent="#navbarVerticalCollapse" id="nv-guest-setting">
                                    <li class="collapsed-nav-item-title d-none">Guest Setting</li>
                                    <!-- Flight-related settings -->
                                    @if (Auth::user()->can('setup.menu'))
                                        <!-- parent pages-->
                                        @can('setup.admin.menu')
                                            <li class="nav-item">
                                                <a class="nav-link {{ Request::is('gms/setting/guest_type') ? 'active' : '' }}"
                                                    href="{{ route('gms.setting.guest_type') }}">
                                                    <div class="d-flex align-items-center">
                                                        <span class="nav-link-text">Guest Type</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ Request::is('gms/setting/client_group') ? 'active' : '' }}"
                                                    href="{{ route('gms.setting.client_group') }}">
                                                    <div class="d-flex align-items-center">
                                                        <span class="nav-link-text">Client Group</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ Request::is('gms/setting/designation') ? 'active' : '' }}"
                                                    href="{{ route('gms.setting.designation') }}">
                                                    <div class="d-flex align-items-center">
                                                        <span class="nav-link-text">Designation</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ Request::is('gms/setting/nationality') ? 'active' : '' }}"
                                                    href="{{ route('gms.setting.nationality') }}">
                                                    <div class="d-flex align-items-center">
                                                        <span class="nav-link-text">Nationality</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ Request::is('gms/setting/hosted_by') ? 'active' : '' }}"
                                                    href="{{ route('gms.setting.hosted_by') }}">
                                                    <div class="d-flex align-items-center">
                                                        <span class="nav-link-text">Hosted By</span>
                                                    </div>
                                                </a>
                                            </li>
                                        @endcan
                                    @endif
                                </ul>
                            </div>
                        </div>
                        <div class="nav-item-wrapper">
                            <a class="nav-link dropdown-indicator label-1" href="#nv-flight-setting" role="button"
                                data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-flight-setting">
                                <div class="d-flex align-items-center">
                                    <div class="dropdown-indicator-icon-wrapper">
                                        <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                    </div>
                                    <span class="nav-link-icon"><i class="fas fa-plane"></i></span>
                                    <span class="nav-link-text">Flight Setting</span>
                                </div>
                            </a>
                            <div class="parent-wrapper label-1">
                                <ul class="nav collapse parent {{ Request::is('gms/admin/flight*') || Request::is('gms/setting/*') ? 'show' : '' }}"
                                    data-bs-parent="#navbarVerticalCollapse" id="nv-flight-setting">
                                    <li class="collapsed-nav-item-title d-none">Flight Setting</li>
                                    <!-- Flight-related settings -->
                                    @if (Auth::user()->can('setup.menu'))
                                        @can('setup.admin.menu')
                                            <li class="nav-item">
                                                <a class="nav-link {{ Request::is('gms/setting/flight_status') ? 'active' : '' }}"
                                                    href="{{ route('gms.setting.flight_status') }}">
                                                    <div class="d-flex align-items-center">
                                                        <span class="nav-link-text">Flight Status</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ Request::is('gms/setting/airline') ? 'active' : '' }}"
                                                    href="{{ route('gms.setting.airline') }}">
                                                    <div class="d-flex align-items-center">
                                                        <span class="nav-link-text">Airlines</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ Request::is('gms/setting/cabin_type') ? 'active' : '' }}"
                                                    href="{{ route('gms.setting.cabin_type') }}">
                                                    <div class="d-flex align-items-center">
                                                        <span class="nav-link-text">Cabin Type</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ Request::is('gms/setting/flight_type') ? 'active' : '' }}"
                                                    href="{{ route('gms.setting.flight_type') }}">
                                                    <div class="d-flex align-items-center">
                                                        <span class="nav-link-text">Flight Type</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ Request::is('gms/setting/airport') ? 'active' : '' }}"
                                                    href="{{ route('gms.setting.airport') }}">
                                                    <div class="d-flex align-items-center">
                                                        <span class="nav-link-text">Airport</span>
                                                    </div>
                                                </a>
                                            </li>
                                        @endcan
                                    @endif
                                </ul>
                            </div>
                        </div>
                        <div class="nav-item-wrapper">
                            <a class="nav-link dropdown-indicator label-1" href="#nv-event-setting" role="button"
                                data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-event-setting">
                                <div class="d-flex align-items-center">
                                    <div class="dropdown-indicator-icon-wrapper">
                                        <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                    </div>
                                    <span class="nav-link-icon"><span data-feather="users"></span></span>
                                    <span class="nav-link-text">Event Setting</span>
                                </div>
                            </a>
                            <div class="parent-wrapper label-1">
                                <ul class="nav collapse parent {{ Request::is('gms/admin/flight*') || Request::is('gms/setting/*') ? 'show' : '' }}"
                                    data-bs-parent="#navbarVerticalCollapse" id="nv-event-setting">
                                    <li class="collapsed-nav-item-title d-none">Event Setting</li>
                                    <!-- Flight-related settings -->
                                    @if (Auth::user()->can('setup.menu'))
                                        <!-- parent pages-->
                                        @can('setup.admin.menu')
                                            <li class="nav-item">
                                                <a class="nav-link {{ Request::is('gms/setting/event') ? 'active' : '' }}"
                                                    href="{{ route('gms.setting.event') }}">
                                                    <div class="d-flex align-items-center">
                                                        <span class="nav-link-text">Event</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ Request::is('gms/setting/venue') ? 'active' : '' }}"
                                                    href="{{ route('gms.setting.venue') }}">
                                                    <div class="d-flex align-items-center">
                                                        <span class="nav-link-text">Venue</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ Request::is('gms/setting/funcareas') ? 'active' : '' }}"
                                                    href="{{ route('gms.setting.funcareas') }}">
                                                    <div class="d-flex align-items-center">
                                                        <span class="nav-link-text">Funcational Area</span>
                                                    </div>
                                                </a>
                                            </li>
                                        @endcan
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('vapp.setting.application') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-text">Application Settings</span>
                            </div>
                        </a>
                    </li>
                @endif
                @if (Auth::user()->hasRole('SecurityRole'))
                    <li class="nav-item">
                        <!-- label-->
                        <p class="navbar-vertical-label">Roles and Permissions
                        </p>
                        <hr class="navbar-vertical-line" />
                        <!-- parent pages-->
                        <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nv-list"
                                role="button" data-bs-toggle="collapse" aria-expanded="false"
                                aria-controls="nv-list">
                                <div class="d-flex align-items-center">
                                    <div class="dropdown-indicator-icon-wrapper"><span
                                            class="fas fa-caret-right dropdown-indicator-icon"></span></div><span
                                        class="nav-link-icon"><span data-feather="file-text"></span></span><span
                                        class="nav-link-text">List</span>
                                </div>
                            </a>
                            <div class="parent-wrapper label-1">
                                <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse"
                                    id="nv-list">
                                    <li class="collapsed-nav-item-title d-none">List
                                    </li>
                                    <li class="nav-item"><a
                                            class="nav-link {{ Request::is('sec/groups/list') ? 'active' : '' }}"
                                            href="{{ route('sec.groups.list') }}">
                                            <div class="d-flex align-items-center"><span
                                                    class="nav-link-text">Groups</span>
                                            </div>
                                        </a>
                                        <!-- more inner pages-->
                                    </li>
                                    <li class="nav-item"><a
                                            class="nav-link {{ Request::is('sec/permissions/list') ? 'active' : '' }}"
                                            href="{{ route('sec.perm.list') }}">
                                            <div class="d-flex align-items-center"><span
                                                    class="nav-link-text">Permissions</span>
                                            </div>
                                        </a>
                                        <!-- more inner pages-->
                                    </li>
                                    <li class="nav-item"><a
                                            class="nav-link {{ Request::is('sec/roles/list') ? 'active' : '' }}"
                                            href="{{ route('sec.roles.list') }}">
                                            <div class="d-flex align-items-center"><span
                                                    class="nav-link-text">Roles</span>
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
                        <div class="nav-item-wrapper"><a
                                class="nav-link label-1 {{ Request::is('sec/rolesetup/list') ? 'active' : '' }}"
                                href="{{ route('sec.rolesetup.list') }}" role="button" data-bs-toggle=""
                                aria-expanded="false">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                            data-feather="server"></span></span><span
                                        class="nav-link-text-wrapper"><span class="nav-link-text">Roles in
                                            Permission</span></span>
                                </div>
                            </a>
                        </div>
                        <!-- parent pages-->
                        <div class="nav-item-wrapper"><a
                                class="nav-link label-1 {{ Request::is('sec/audit') ? 'active' : '' }}"
                                href="{{ route('sec.audit') }}" role="button" data-bs-toggle=""
                                aria-expanded="false">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                            data-feather="server"></span></span><span
                                        class="nav-link-text-wrapper"><span class="nav-link-text">Audit</span></span>
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
                        <div class="nav-item-wrapper"><a
                                class="nav-link label-1 {{ Request::is('sec/adminuser/list') }}"
                                href="{{ route('sec.adminuser.list') }}" role="button" data-bs-toggle=""
                                aria-expanded="false">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                            data-feather="life-buoy"></span></span><span
                                        class="nav-link-text-wrapper"><span class="nav-link-text">List
                                            Users</span></span>
                                </div>
                            </a>
                        </div>
                        <div class="nav-item-wrapper"><a
                                class="nav-link label-1 {{ Request::is('sec/adminuser/add') }}"
                                href="{{ route('sec.adminuser.add') }}" role="button" data-bs-toggle=""
                                aria-expanded="false">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                            data-feather="life-buoy"></span></span><span
                                        class="nav-link-text-wrapper"><span class="nav-link-text">Add
                                            User</span></span>
                                </div>
                            </a>
                        </div>
                        <div class="nav-item-wrapper"><a
                                class="nav-link label-1 {{ Request::is('/auth/ms-signup') }}"
                                href="{{ route('auth.ms.signup') }}" role="button" data-bs-toggle=""
                                aria-expanded="false">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                            data-feather="life-buoy"></span></span><span
                                        class="nav-link-text-wrapper"><span class="nav-link-text">Grant
                                            Access</span></span>
                                </div>
                            </a>
                        </div>
                        <div class="nav-item-wrapper"><a
                                class="nav-link label-1 {{ Request::is('vapp/admin/users/invite-user') }}"
                                href="{{ route('admin.users.invite.form') }}" role="button" data-bs-toggle=""
                                aria-expanded="false">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                            data-feather="life-buoy"></span></span><span
                                        class="nav-link-text-wrapper"><span class="nav-link-text">Invite
                                            Users</span></span>
                                </div>
                            </a>
                        </div>
                    </li>
                @endif
            </ul>
        </div>
    </div>
    <div class="navbar-vertical-footer">
        <button
            class="btn navbar-vertical-toggle border-0 fw-semibold w-100 white-space-nowrap d-flex align-items-center"><span
                class="uil uil-left-arrow-to-left fs-8"></span><span
                class="uil uil-arrow-from-right fs-8"></span><span class="navbar-vertical-footer-text ms-2">Collapsed
                View</span></button>
    </div>
</nav>
