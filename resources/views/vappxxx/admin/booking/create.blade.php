@extends('vapp.admin.layout.admin_template')
@section('main')
    <link rel="stylesheet" href="{{ asset('assets/css/vapp_box.css') }}">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>

    <div class="container-fluid px-2">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center my-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}">{{ get_label('home', 'Home') }}</a>
                    </li>
                    <li class="breadcrumb-item ">
                        <a href="{{ route('vapp.manager') }}"><?= get_label('manager', 'Manager View') ?></a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ get_label('create_request', 'Create Request') }}
                    </li>
                </ol>
            </nav>
        </div>

        {{-- <div class="card shadow-sm p-3"> --}}
        <form class="row g-3  px-3 needs-validation" action="{{ route('vapp.admin.request.store') }}" id="form-1"
            novalidate method="POST">
            @csrf
            <input type="hidden" id="add_schedule_period_id" name="schedule_period_id" value="">
            <input type="hidden" id="add_booking_date" name="booking_date">
            <input type="hidden" id="add_variation_id" name="variation_id">

            <div class="row py-2">
                <div class="col-sm-6 col-md-8">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <li>{{ session('error') }}</li>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="card shadow-none border col-md-12" style="margin:0 auto;"
                        data-component-card="data-component-card">
                        <div class="card-header p-4 border-bottom bg-body">
                            <div class="row g-3 justify-content-between align-items-center">
                                <div class="col-12 col-md">
                                    <h4 class="text-body mb-0">Creat a new Request</h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="row mb-3">
                                <x-formy.form_select class="col-sm-6 col-md-12" floating="0" selectedValue=""
                                    name="var_functional_area_id" elementId="add_var_functional_area_id"
                                    label="Functional Area" required="" :forLoopCollection="$userFa" itemIdForeach="id"
                                    itemTitleForeach="title" style="" addDynamicButton="0" />

                            </div>
                            <div class="row mb-3">
                                {{-- <x-formy.form_select class="col-sm-6 col-md-6" floating="0" selectedValue=""
                                                name="parking_id" elementId="add_var_parking_id" label="VAPP Code"
                                                required="required" :forLoopCollection="$varParkingCode" itemIdForeach="id"
                                                itemTitleForeach="parking_code" style="" addDynamicButton="0" /> --}}
                                <div class="col-sm-6 col-md-6">
                                    <label class="form-label" for="add_var_parking_id">VAPP Code</label>
                                    <select class="form-select" id="add_var_parking_id" name="parking_id" data-with="100%"
                                        data-placeholder="Select VAPP Size...">
                                        <option value="">Select VAPP Code</option>
                                        <!-- Options loaded dynamically -->
                                    </select>
                                </div>

                                <x-formy.form_select class="col-sm-6 col-md-6" floating="0" selectedValue=""
                                    name="match_category_id" elementId="add_var_category_id" label="Category" required=""
                                    :forLoopCollection="$matchCategories" itemIdForeach="id" itemTitleForeach="title" style=""
                                    addDynamicButton="0" />
                            </div>
                            <div class="row mb-3">

                                <div class="col-sm-6 col-md-12">
                                    <label class="form-label" for="add_var_match_id">Match</label>
                                    <select class="form-select" id="add_var_match_id" name="match_id" data-with="100%"
                                        data-placeholder="Select VAPP Size...">
                                        <option value="">Select Match</option>
                                        <!-- Options loaded dynamically -->
                                    </select>
                                </div>
                            </div>
                            {{-- </div> --}}
                            <div class="row mb-3">
                                <div class="col-sm-6 col-md-12">
                                    <label class="form-label" for="add_var_venue_id">Venue</label>
                                    <select class="form-select" id="add_var_venue_id" name="venue_id" data-with="100%"
                                        data-placeholder="Select VAPP Size...">
                                        <option value="">Select Venue</option>
                                        <!-- Options loaded dynamically -->
                                    </select>
                                </div>
                                {{-- </div> --}}
                                {{-- <div class="row mb-3"> --}}
                                {{-- <div class="col-sm-6 col-md-6">
                                                <label class="form-label" for="add_var_match_id">Functional Area</label>
                                                <select class="form-select" id="add_var_functional_area_id"
                                                    name="var_functional_area_id" data-with="100%"
                                                    data-placeholder="Select Functional Area Size...">
                                                    <option value="">Select FA</option>
                                                    <!-- Options loaded dynamically -->
                                                </select>
                                            </div> --}}
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-6 col-md-6">
                                    <label class="form-label" for="add_var_vapp_size_id">VAPP Size</label>
                                    <select class="form-select" id="add_var_vapp_size_id" name="vapp_size_id"
                                        data-with="100%" data-placeholder="Select VAPP Size...">
                                        <option value="">Select Vap Size</option>
                                        <!-- Options loaded dynamically -->
                                    </select>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label class="form-label" for="add_requested_vapps"># VAPPS
                                    </label>
                                    <input class="form-control" id="add_requested_vapps" name="requested_vapps"
                                        type="number" placeholder="Quantity of Vapps requested" requered />
                                </div>

                                {{-- <x-formy.form_input class="col-sm-6 col-md-6" floating="1"
                                                inputAttributes="" inputValue="" name="requested_vapps"
                                                elementId="add_requested_vapps" inputType="number" label="#VAPPS"
                                                required="" disabled="0" /> --}}
                            </div>
                            <div class="row">
                                <div class="col-sm-6 col-md-12">
                                    {{-- <div class="card shadow-none border col-md-12" style="margin:0 auto;" --}}
                                    {{-- data-component-card="data-component-card"> --}}

                                    <x-formy.form_textarea class="col-sm-12 col-md-12" floating="1" inputValue=""
                                        name="justification" elementId="add_justification" label="Justification"
                                        required="" disabled="0" />
                                    {{-- </div> --}}
                                </div>
                            </div>
                        </div>
                        <div class="card-footer p-3">
                            <div class="row g-3 justify-content-end">
                                <div class="col-auto">
                                    <button class="btn btn-primary" type="submit">Save Request</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <!-- Overlay Spinner -->
                    <div id="page-spinner-overlay" class="position-absolute top-50 start-50 translate-middle d-none">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div> --}}
                    <!-- Spinner overlay -->
                    <div id="cardSpinner"
                        class="d-none position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex justify-content-center align-items-center"
                        style="z-index: 10;">
                        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 mb-3 mt-3">
                    <div class="card shadow-none border-0 h-100 mx-auto"
                        style="background-color: transparent !important;">
                        <div class="main-card text-center p-3">
                            <div class="day-label mb-2" id="sub-card-match-day">DAYX</div>
                            <div class="logo mb-2 booking-request-card-title" id="sub-card-event">{{ $event->name }}
                            </div>
                            <div class="sub-card lma mb-1" id="sub-card-venue">VENUE</div>
                            <div class="sub-card p14" id="sub-card-parking-code">P</div>
                        </div>
                    </div>
                </div>
                {{-- <div class="col-sm-6 col-md-4">
                        <div class="card shadow-none border-none col-md-12" style="margin:0 auto;">
                            <div class="main-card">
                                <div class="day-label">DAY1</div>
                                <div class="logo">2024 FIFA<br>Intercontinental Cup</div>
                                <div class="sub-card lma">LMA</div>
                                <div class="sub-card p14">P14</div>
                            </div>
                        </div>

                    </div> --}}
            </div>
            {{-- <div class="row">
                    <div class="col-sm-6 col-md-12">
                        <div class="card shadow-none border col-md-12" style="margin:0 auto;"
                            data-component-card="data-component-card">

                            <x-formy.form_textarea class="col-sm-12 col-md-12 p-3" floating="1" inputValue=""
                                name="comments" elementId="add_comments" label="Comments" required=""
                                disabled="0" />
                        </div>
                    </div>
                </div> --}}
            <!-- <div class="invisible">.</div> -->
            {{-- <div class="col-12 d-flex justify-content-end mt-6">
                <button class="btn btn-primary" type="submit">Save booking</button>
            </div> --}}
            <!-- <button class="btn btn-primary" type="submit">Submit</button> -->
        </form>
    </div>
    {{-- </div> --}}
    <script src="{{ asset('assets/js/pages/vapp/booking.js') }}"></script>
    <script src="{{ asset('assets/js/pages/vapp/booking_request.js') }}"></script>
@endsection
@push('script')
@endpush
