@php

    $current_event_id = session()->get('EVENT_ID');
    $event = App\Models\Vapp\Event::find($current_event_id);

    appLog('Current Event:', ['event' => $event]);

    $user = Auth::user();
    $profileData = App\Models\User::find($user->id);

@endphp

<nav class="navbar navbar-top fixed-top navbar-expand mds_header_background" id="navbarDefault">
    <div class="collapse navbar-collapse justify-content-between">
        <div class="navbar-logo">

            <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse"
                aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span
                        class="toggle-line"></span></span></button>
            <a class="navbar-brand me-1 me-sm-3" href="{{ route('vapp.operator') }}">
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center">
                        {{-- <img src="{{ asset('assets/img/icons/mds.jpg') }}" alt="{{ __('mds.page_title') }}"
                            width="27" /> --}}
                        <img src="{{ asset(config('settings.website_logo')) }}"
                            alt="{{ config('settings.site_title') }}" width="150" />
                        <h3 class="logo-text ms-2 d-none d-sm-block text-white">{{ config('settings.website_name') }}
                        </h3>
                        <div class="theme-control-toggle px-2 d-none d-sm-block">
                            <h6 class="mt-2 d-sm-block d-none text-primary text-white">({{ $event?->name }})</h6>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @php
            $user_events = auth()->user()->events;
            $user_events = $user_events->where('active_flag', 1)->sortBy('name');
            // $user_events = App\Models\Vapp\Event::where('active_flag', 1)->orderBy('name')->get();
        @endphp

        <ul class="navbar-nav navbar-nav-icons flex-row">
            <li class="nav-item">
                <div class="theme-control-toggle px-2">
                    <h6 class="mt-2 text-white">{{ $profileData->name }}</h6>
                </div>
            </li>

            <li class="nav-item dropdown"><a class="nav-link lh-1 pe-0" id="navbarDropdownUser" href="#!"
                    role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true"
                    aria-expanded="false">
                    <div class="avatar avatar-l ">
                        <img class="rounded-circle "
                            src="{{ !empty($profileData->photo) ? url('storage/upload/profile_images/' . $profileData->photo) : url('storage/upload/default.png') }}"
                            alt="" />

                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border"
                    aria-labelledby="navbarDropdownUser">
                    <div class="card position-relative border-0">
                        <div class="card-body p-0">
                            <div class="text-center pt-4 pb-3">
                            </div>
                        </div>
                        <div class="overflow-auto scrollbar">
                            <ul class="nav d-flex flex-column mb-2 pb-1">
                                @if ($user->hasRole('Operator'))
                                    <li class="nav-item">
                                        <a class="nav-link px-3 d-block" href="{{ route('vapp.operator') }}"> <span
                                                class="me-2 text-body align-bottom"
                                                data-feather="user"></span><span>Operators's View</span>
                                        </a>
                                    </li>
                                @endif

                        </div>
                        <div class="card-footer p-2">

                            <div class="px-3">

                                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="nav-link text-danger" type="submit">
                                        <span class="me-2 text-danger" data-feather="log-out">
                                        </span>Sign out
                                    </button>
                                </form>

                            </div>

                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</nav>
