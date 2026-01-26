@extends('ypi.layout.admin_template')
@section('main')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('ypi.admin.guest') }}">Guest</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $guestData->getFullNameAttribute() }}
            </li>
        </ol>
    </nav>
    <div class="row align-items-center justify-content-between g-3 mb-4">
        <div class="col-12 col-md-auto">
            <h2 class="mb-0 text-white">{{ $guestData->getFullNameAttribute() }}</h2>
        </div>
        <div class="col-12 col-md-auto d-flex">
            {{-- <a href="javascript:void(0);" id="edit_project_offcanv" data-id="{{ $guestData->id }}" data-table="page"
                class="btn btn-phoenix-secondary px-3 px-sm-5 me-2">
                <span class="fa-solid fa-edit me-sm-2"></span>
                <span class="d-none d-sm-inline">Edit </span>
            </a>
            <button class="btn btn-phoenix-danger me-2"><span class="fa-solid fa-trash me-2"></span><span>Delete
                    Guest</span></button> --}}
            <div class="me-2">
            
                {{-- <x-formy.button_insert_js title='Add Flight' selectionId="offcanvas-add-flight" dataId="0"
                    table="flight_table" icon="fas fa-plus me-2" /> --}}
                <button class="btn px-3 btn-phoenix-secondary" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#bookingFilterOffcanvas" aria-haspopup="true" aria-expanded="false"
                    data-bs-reference="parent"><svg class="svg-inline--fa fa-filter text-primary" data-fa-transform="down-3"
                        aria-hidden="true" focusable="false" data-prefix="fas" data-icon="filter" role="img"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""
                        style="transform-origin: 0.5em 0.6875em;">
                        <g transform="translate(256 256)">
                            <g transform="translate(0, 96)  scale(1, 1)  rotate(0 0 0)">
                                <path fill="currentColor"
                                    d="M3.9 54.9C10.5 40.9 24.5 32 40 32H472c15.5 0 29.5 8.9 36.1 22.9s4.6 30.5-5.2 42.5L320 320.9V448c0 12.1-6.8 23.2-17.7 28.6s-23.8 4.3-33.5-3l-64-48c-8.1-6-12.8-15.5-12.8-25.6V320.9L9 97.3C-.7 85.4-2.8 68.8 3.9 54.9z"
                                    transform="translate(-256 -256)"></path>
                            </g>
                        </g>
                    </svg><!-- <span class="fa-solid fa-filter text-primary" data-fa-transform="down-3"></span> Font Awesome fontawesome.com -->
                </button>
            </div>
            <div class="me-2">
                <button class="btn px-3 btn-phoenix-secondary" type="button" data-bs-toggle="dropdown"
                    data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent">
                    <span class="fa-solid fa-ellipsis"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end p-0" style="z-index: 9999;">
                    <li><a class="dropdown-item" href="#!">View profile</a></li>
                    <li><a class="dropdown-item" href="#!">Report</a></li>
                    <li><a class="dropdown-item" href="#!">Manage notifications</a></li>
                    <li><a class="dropdown-item text-danger" href="#!">Delete Lead</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row pb-9 gx-4">
        <div class="col-xl-10 pe-lg-2 flex-1">
            <ul class="nav nav-underline optionChainTableHeader gap-0 flex-nowrap scrollbar mb-4" id="stockDetailsTab"
                role="tablist">
                <li class="nav-item"> <a class="nav-link pt-0 text-nowrap active ps-0 pe-3" id="tab-flight"
                        href="#flight-tab" data-bs-toggle="tab" role="tab" aria-controls="flight-tab"
                        aria-selected="true">Flights</a></li>
                <li class="nav-item"> <a class="nav-link pt-0 text-nowrap px-3 " id="tab-accomm" href="#accomm-tab"
                        data-bs-toggle="tab" role="tab" aria-controls="accomm-tab"
                        aria-selected="false">Accomodation</a></li>
                <li class="nav-item"> <a class="nav-link pt-0 text-nowrap px-3 " id="tab-finStates" href="#finStates-tab"
                        data-bs-toggle="tab" role="tab" aria-controls="finStates-tab"
                        aria-selected="false">Transportation</a></li>
                <li class="nav-item"> <a class="nav-link pt-0 text-nowrap px-3 " id="tab-forecast" href="#forecast-tab"
                        data-bs-toggle="tab" role="tab" aria-controls="forecast-tab" aria-selected="false">Visa</a>
                </li>
                <li class="nav-item"> <a class="nav-link pt-0 text-nowrap px-3 " id="tab-news" href="#news-tab"
                        data-bs-toggle="tab" role="tab" aria-controls="news-tab" aria-selected="false">Dependents</a>
                </li>
                <li class="nav-item"> <a class="nav-link pt-0 text-nowrap px-3 " id="tab-events" href="#events-tab"
                        data-bs-toggle="tab" role="tab" aria-controls="events-tab"
                        aria-selected="false">Documents</a>
                </li>
                <li class="nav-item"> <a class="nav-link pt-0 text-nowrap px-3 " id="tab-comProfile"
                        href="#comProfile-tab" data-bs-toggle="tab" role="tab" aria-controls="comProfile-tab"
                        aria-selected="false">Profile</a></li>
                <li class="nav-item flex-1 d-none d-md-inline d-xl-none d-xxl-inline"> <a
                        class="nav-link pt-0 text-nowrap px-3 disabled h-100" id="tab-empty1" href="#empty1-tab"
                        data-bs-toggle="tab" role="tab" aria-selected="false"></a></li>
            </ul>
            <div class="tab-content" id="stockDetailsTabContent">
                <div class="tab-pane fade show active" id="flight-tab" role="tabpanel" aria-labelledby="tab-flight">
                    <div class="d-flex justify-content-between m-2">
                        <!-- <div class="row flex-between-center g-3 mb-4"> -->
                        <div class="col-auto">
                            {{-- <h4>Guest Flights </h4> --}}
                            <!-- <p class="text-body-tertiary mb-0">Updated inventory according to the sales report.</p> -->
                        </div>
                        {{-- <div>
                            <x-formy.button_insert_js title='Add Flight' selectionId="offcanvas-add-flight"
                                dataId="0" table="flight_table" icon="fas fa-plus me-2" />
                            <button class="btn px-3 btn-phoenix-secondary" type="button" data-bs-toggle="offcanvas"
                                data-bs-target="#bookingFilterOffcanvas" aria-haspopup="true" aria-expanded="false"
                                data-bs-reference="parent"><svg class="svg-inline--fa fa-filter text-primary"
                                    data-fa-transform="down-3" aria-hidden="true" focusable="false" data-prefix="fas"
                                    data-icon="filter" role="img" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 512 512" data-fa-i2svg="" style="transform-origin: 0.5em 0.6875em;">
                                    <g transform="translate(256 256)">
                                        <g transform="translate(0, 96)  scale(1, 1)  rotate(0 0 0)">
                                            <path fill="currentColor"
                                                d="M3.9 54.9C10.5 40.9 24.5 32 40 32H472c15.5 0 29.5 8.9 36.1 22.9s4.6 30.5-5.2 42.5L320 320.9V448c0 12.1-6.8 23.2-17.7 28.6s-23.8 4.3-33.5-3l-64-48c-8.1-6-12.8-15.5-12.8-25.6V320.9L9 97.3C-.7 85.4-2.8 68.8 3.9 54.9z"
                                                transform="translate(-256 -256)"></path>
                                        </g>
                                    </g>
                                </svg><!-- <span class="fa-solid fa-filter text-primary" data-fa-transform="down-3"></span> Font Awesome fontawesome.com -->
                            </button>
                        </div> --}}

                    </div>
                    <div class="mb-0">
                        @if (\App\Models\Gms\GuestFlight::where('guest_id', $guestData->id)->exists())
                            <x-ypi.admin.flight-card :projectId="$guestData->id" />
                        @endif

                    </div>
                </div>

            </div>
            <div
                class="row gap-3 g-0 flex-between-center mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-secondary py-3 border-y mt-4 position-sticky bottom-0 z-2 d-xl-none stock-details-footer">
                <div class="col-auto">
                    <div class="d-flex align-items-center gap-2">
                        <h3 class="mb-0 text-body">$226.51</h3>
                        <div class="badge badge-phoenix badge-phoenix-success">+0.62 (0.27%)</div>
                    </div>
                </div>
                <div class="col-12 col-sm-auto">
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-primary flex-1" id="offcanvasStockDetails" data-bs-toggle="offcanvas"
                            data-bs-target="#stockDetailsSidebar" aria-controls="stockDetailsSidebar">Buy Share</button>
                        <button class="btn btn-phoenix-secondary"> <span class="fas fa-clock"> </span></button>
                        <button class="btn btn-phoenix-secondary"> <span class="fas fa-eye"> </span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('ypi.admin.guest.modals.flight_modals')
    @include('ypi.admin.guest.modals.accommodation_modals')
    <script src="{{ asset('assets/js/pages/gms/flight.js') }}"></script>
    <script src="{{ asset('assets/js/pages/gms/accomm.js') }}"></script>
@endsection

@push('script')
    <script src="{{ asset('fnx/assets/js/pages/stock-details.js') }}"></script>

    <script>
        // showing the offcanvas for the task creation
        $(document).ready(function() {
            console.log('ready');
            $('.dropify').dropify();

        });
    </script>
@endpush
