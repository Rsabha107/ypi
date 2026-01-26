@extends('vapp.customer.layout.template')
@section('main')
    <link rel="stylesheet" href="{{ asset('assets/css/vapp_box.css') }}">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>

    {{-- <div class="container-fluid px-2"> --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center my-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">{{ get_label('home', 'Home') }}</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('vapp.admin.booking') }}">{{ get_label('request', 'Requests') }}</a>
                </li>
                <li class="breadcrumb-item active">
                    {{ get_label('collection', 'Collection') }}
                </li>
            </ol>
        </nav>
    </div>

    {{-- <div class="card shadow-sm p-3"> --}}

    <div class="col-sm-12 col-md-12">
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    <li>{{ session('error') }}</li>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <form class="row g-3 px-3 needs-validation" id="rfc_request_form" novalidate method="POST">
            @csrf
            <input type="hidden" id="add_schedule_period_id" name="schedule_period_id" value="">
            <input type="hidden" id="add_booking_date" name="booking_date">
            <input type="hidden" id="add_variation_id" name="variation_id">
            <div class="card shadow-none border col-sm-12 col-md-8 mb-3" style="margin:0 auto;"
                data-component-card="data-component-card">
                <div class="card-body p-3">
                    <div class="row mb-3">
                        <div class="col-sm-12 col-md-12">
                            <label class="form-label" for="find_fa_requests">Functional Area</label>
                            <select class="js-example-basic-single" id="find_fa_requests" data-with="100%"
                                data-placeholder="Select Functional Area...">
                                <option value="">Select ...</option>
                                {{-- Options loaded dynamically --}}
                                @foreach ($rfcRequests as $rfc)
                                    <option value="{{ $rfc->id }}">
                                        {{ $rfc->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3 justify-content-center align-items-center">
                            <div class="col-auto">
                                <button class="btn btn-primary" type="submit">Find Requests</button>
                            </div>
                        </div>
                    </div>
                    <div id="cardSpinner"
                        class="d-none position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex justify-content-center align-items-center"
                        style="z-index: 10;">
                        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="card shadow-none border col-md-12 find-rfc-requests d-none" style="margin:0 auto;"
            data-component-card="data-component-card" id="rfc_requests_card">
            <div class="card-body p-3">
                <div class="row mb-3">

                    <div id="rfc_requests_content" class="col-sm-12 col-md-12"></div>

                </div>
                <div id="cardSpinner"
                    class="d-none position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex justify-content-center align-items-center"
                    style="z-index: 10;">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- </div> --}}
    {{-- </div> --}}
@endsection
@push('script')
    <script src="{{ asset('assets/js/pages/vapp/operator/find.js') }}"></script>
@endpush
