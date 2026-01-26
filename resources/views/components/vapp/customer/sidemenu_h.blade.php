      @php
      // $session_set = false;
      // if (session()->has('EVENT_ID')) {
      // $current_event_id = session()->get('EVENT_ID');
      // $event = App\Models\Mds\MdsEvent::findOrFail($current_event_id);
      // $set_ws_message = $event->name;
      // $badge_color = 'success';
      // $session_set = true;
      // }

      $current_event_id = session()->get('EVENT_ID');
      $event = App\Models\Vapp\Event::find($current_event_id);

      $id = Auth::user()->id;
      $profileData = App\Models\User::find($id);

      @endphp

      <style>
          .mds_header_background {
              background-image: url('/assets/img/background/wo_logo/P3-P4-P5-P6_1920x1080_pxl.png');
              /* path to your background image */
              background-size: cover;
              background-repeat: no-repeat;
              background-position: center;
          }
      </style>

      <nav class="navbar navbar-top fixed-top navbar-expand-lg mds_header_background" id="navbarTop">
          <div class="navbar-logo">

              <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button"
                  data-bs-toggle="collapse" data-bs-target="#navbarTopCollapse" aria-controls="navbarTopCollapse"
                  aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span
                          class="toggle-line"></span></span></button>
              <a class="navbar-brand me-1 me-sm-3" href="{{ route('vapp.customer') }}">
                  <div class="d-flex align-items-center">
                      <img src="{{ asset(config('settings.website_logo')) }}" alt="{{ __('mds.page_title') }}"
                          width="150" />
                      {{-- @if ($session_set) --}}
                      <span class="d-none d-sm-inline logo-text ms-2 text-white">{{ config('settings.website_name') }}</span>
                      <span class="d-inline d-sm-none logo-text ms-2 text-white">{{ config('settings.site_title') }}</span>
                      <div class="theme-control-toggle fa-icon-wait px-2 d-none d-sm-block">
                          <h6 class="mt-2 d-sm-block d-none text-white">({{ $event->name }})</h6>
                      </div>
                  </div>
              </a>
          </div>
          <div class="collapse navbar-collapse navbar-top-collapse order-1 order-lg-0 justify-content-end flex-row"
              id="navbarTopCollapse">
              <ul class="navbar-nav navbar-nav-top" data-dropdown-on-hover="data-dropdown-on-hover">
                  <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1 text-white" href="#!" role="button"
                          data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true"
                          aria-expanded="false"><span class="uil fs-8 me-2 uil-cube"></span>Apps</a>
                      <ul class="dropdown-menu navbar-dropdown-caret">
                          <li>
                              <a class="dropdown-item" href="{{ route('vapp.customer.booking') }}">
                                  <div class="dropdown-item-wrapper"><span class="me-2 fa-solid fa-list "></span>List of
                                      Bookings
                                  </div>
                              </a>
                          </li>
                          @hasrole('Manager')
                          <li>
                              <a class="dropdown-item" href="{{ route('vapp.manager.booking') }}">
                                  <div class="dropdown-item-wrapper"><span
                                          class="me-2 fa-solid fa-people-roof"></span>Functional Area List
                                  </div>
                              </a>
                          </li>
                          @endhasrole
                          <li>
                              <a class="dropdown-item" href="{{ route('vapp.customer.booking.create') }}">
                                  <div class="dropdown-item-wrapper"><span class="me-2 fa-solid fa-book"></span>Make a
                                      Booking
                                  </div>
                              </a>
                          </li>
                      </ul>
                  </li>
              </ul>
          </div>

          @php
          $user_events = auth()->user()->events;
          $user_events = $user_events->where('active_flag', 1)->sortBy('name');
          @endphp

          <ul class="navbar-nav navbar-nav-icons flex-row">
              <li class="nav-item">
                  <div class="theme-control-toggle fa-icon-wait px-2">
                      {{-- <h6 class="mt-2">{{ $profileData->name }}</h6> --}}
                      <span class="d-none d-sm-inline ms-2 text-white">{{ $profileData->name }}</span>
                      {{-- <span class="d-inline d-sm-none logo-text ms-2"></span> --}}
                  </div>
              </li>

              {{-- // Start of Event switch block --}}
              <li class="nav-item dropdown">
                  <a class="nav-link" href="#" style="min-width: 2.25rem" role="button" data-bs-toggle="dropdown"
                      aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside"><span class="d-block"
                          style="height:20px;width:20px;"><span data-feather="calendar" class="text-white"
                              style="height:20px;width:20px;"></span></span></a>

                  <div class="dropdown-menu dropdown-menu-end notification-dropdown-menu py-0 shadow border navbar-dropdown-caret"
                      id="navbarDropdownNotfication" aria-labelledby="navbarDropdownNotfication">
                      <div class="card position-relative border-0">
                          <div class="card-header p-2">
                              <div class="d-flex justify-content-between">
                                  <h5 class="text-body-emphasis mb-0">Events</h5>
                                  {{-- <button class="btn btn-secondary p-0 fs-9 fw-normal" type="button">Switch to another event</button> --}}
                              </div>
                          </div>
                          <div class="card-body p-0">
                              <div class="scrollbar-overlay" style="height: 27rem;">
                                  @foreach ($user_events as $event)
                                  @if (session()->get('EVENT_ID') == $event->id)
                                  @php
                                  $avatar_status = 'status-online';
                                  $pulse = '<span class="fa-solid fa-circle text-success ms-1 pulse" style="font-size: 10px"></span>';
                                  $read = 'read';
                                  $text_color = 'text-success';
                                  @endphp
                                  @else
                                  @php
                                  $avatar_status = '';
                                  $pulse = '';
                                  $text_color = 'text-body-emphasis';
                                  $read = 'unread';
                                  @endphp
                                  @endif
                                  <a href="{{ route('vapp.customer.booking.switch', $event->id) }}"
                                      class="text-decoration-none text-body-emphasis">
                                      <div
                                          class="px-2 px-sm-3 py-3 notification-card position-relative {{ $read }} border-bottom">
                                          <div
                                              class="d-flex align-items-center justify-content-between position-relative">
                                              <div class="d-flex">
                                                  <div class="avatar avatar-m {{ $avatar_status }} me-3">
                                                      <img class="rounded-circle"
                                                          src="{{ route('vapp.setting.event.file', $event->event_logo ? $event->event_logo : 'default.png') }}"
                                                          alt="" />
                                                  </div>
                                                  <div class="flex-1 me-sm-3">
                                                      <h4 class="fs-9 {{ $text_color }}">{{ $event->name }} {!! $pulse !!}</h4>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </a>
                                  @endforeach
                              </div>
                          </div>
                      </div>
                  </div>
              </li>

              <li class="nav-item dropdown"><a class="nav-link lh-1 pe-0" id="navbarDropdownUser" href="#!"
                      role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true"
                      aria-expanded="false">
                      <div class="avatar avatar-l ">
                          <img class="rounded-circle "
                              src="{{ !empty($profileData->photo) ? url('storage/upload/profile_images/' . $profileData->photo) : url('storage/upload/avatar-placeholder.webp') }}"
                              alt="" />
                      </div>
                  </a>
                  <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border"
                      aria-labelledby="navbarDropdownUser">
                      <div class="card position-relative border-0">
                          <div class="card-body p-0">
                              <div class="text-center pt-4 pb-3">
                              </div>
                              <div class="overflow-auto scrollbar" style="height: 10rem;">
                                  <ul class="nav d-flex flex-column mb-2 pb-1">
                                      <li class="nav-item"><a class="nav-link px-3 d-block"
                                              href="{{ route('vapp.customer.users.profile') }}"> <span
                                                  class="me-2 text-body align-bottom"
                                                  data-feather="user"></span><span>Profile</span></a></li>
                                      <li class="nav-item"><a class="nav-link px-3 d-block"
                                              href="{{ route('vapp.customer.dashboard') }}"><span
                                                  class="me-2 text-body align-bottom"
                                                  data-feather="pie-chart"></span>Dashboard</a></li>
                                      <li class="nav-item"><a class="nav-link px-3 d-block" href="#!"> <span
                                                  class="me-2 text-body align-bottom"
                                                  data-feather="settings"></span>Settings
                                              &amp; Privacy </a></li>
                                      <li class="nav-item"><a class="nav-link px-3 d-block" href="#!"> <span
                                                  class="me-2 text-body align-bottom"
                                                  data-feather="help-circle"></span>Help
                                              Center</a></li>
                                      <li class="nav-item"><a class="nav-link px-3 d-block" href="#!"> <span
                                                  class="me-2 text-body align-bottom"
                                                  data-feather="globe"></span>Language</a></li>
                                  </ul>
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
      </nav>