@extends('vapp.operator.layout.template')
@section('main')
    {{-- <link rel="stylesheet" href="{{ asset('assets/css/vapp_box.css') }}"> --}}
    {{-- <div class="container-fluid px-2"> --}}
    <div class="row"></div>
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
    <div class="d-flex justify-content-between m-2">
        <div class="col-sm-8 col-md-8">
            <select class="js-example-basic-single" id="rfc_request_filter" data-with="100%"
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
        <div>
            <x-formy.button url="javascript:void(0);" title="Generate" icon="fa-solid fa-plus" btnId="print_receipt_btn"
                class="btn btn-subtle-primary px-3 px-sm-5 me-2" disabled="1" />

            <x-formy.button_green url="javascript:void(0);" title="Mark as Collected" icon="fa-solid fa-plus" btnId="mark_as_collected_btn"
                class="btn btn-success px-3 px-sm-5 me-2" disabled="1" />
        </div>
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

        <x-vapp.operator.booking-card />

    </div>

    {{-- JS for this page --}}

    <script>
        ("use strict");

        function bookingQueryParams(p) {
            return {
                rfc_request_filter: $("#rfc_request_filter").val(),
                event_filter: $("#event_filter").val(),
                venue_filter: $("#venue_filter").val(),
                vapp_size_filter: $("#vapp_size_filter").val(),
                fa_filter: $("#fa_filter").val(),
                parking_filter: $("#parking_filter").val(),
                variation_filter: $("#variation_filter").val(),
                date_range_filter: $("#date_range_filter").val(),
                status_filter: $("#status_filter").val(),
                page: p.offset / p.limit + 1,
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                search: p.search,
            };
        }

        window.icons = {
            refresh: "bx-refresh",
            toggleOn: "bx-toggle-right",
            toggleOff: "bx-toggle-left",
            fullscreen: "bx-fullscreen",
            columns: "bx-list-ul",
            export_data: "bx-list-ul",
        };

        function loadingTemplate(message) {
            return '<i class="bx bx-loader-circle bx-spin bx-flip-vertical" ></i>';
        }

        // $("#rfc_request_filter").on("change", function(e) {
        //     // e.preventDefault();
        //     console.log("tasks.js on change");
        //     $("#bookings_table").bootstrapTable("refresh");
        // });

        //     let selectedRows = $('#bookings_table').bootstrapTable('getSelections')
        // console.log('Currently selected rows:', selectedRows)
    </script>

    {{-- </div> --}}
    {{-- </div> --}}
@endsection
@push('script')
    <script src="{{ asset('assets/js/pages/vapp/operator/find.js') }}"></script>
@endpush
