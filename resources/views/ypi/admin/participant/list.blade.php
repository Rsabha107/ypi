@extends('ypi.layout.admin_template')
@section('main')
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <div class="d-flex justify-content-between m-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}"><?= get_label('home', 'Home') ?></a>
                    </li>
                    <li class="breadcrumb-item active">
                        <?= get_label('participants', 'Participants') ?>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            {{-- <x-button_insert_js title='Add participant' selectionId="offcanvas-add-participant" dataId="{{ session()->get('EVENT_ID') }}"
                table="participant_table" /> --}}
            <button class="btn px-3 btn-phoenix-secondary position-relative" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#bookingFilterOffcanvas" aria-haspopup="true" aria-expanded="false"
                data-bs-reference="parent" id="filterButton">
                <svg class="svg-inline--fa fa-filter text-primary" data-fa-transform="down-3"
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
                </svg>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" id="filterIndicator" style="display: {{ $selectedEvent ? 'block' : 'none' }};">
                    <span class="visually-hidden">Filter active</span>
                </span>
            </button>
            <button class="btn px-3 btn-phoenix-secondary bg-body-emphasis bg-body-hover action-btn" type="button"
                data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false"
                data-bs-reference="parent"><svg class="svg-inline--fa fa-ellipsis" data-fa-transform="shrink-2"
                    aria-hidden="true" focusable="false" data-prefix="fas" data-icon="ellipsis" role="img"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""
                    style="transform-origin: 0.4375em 0.5em;">
                    <g transform="translate(224 256)">
                        <g transform="translate(0, 0)  scale(0.875, 0.875)  rotate(0 0 0)">
                            <path fill="currentColor"
                                d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"
                                transform="translate(-224 -256)"></path>
                        </g>
                    </g>
                </svg><!-- <span class="fas fa-ellipsis-h" data-fa-transform="shrink-2"></span> Font Awesome fontawesome.com -->
            </button>
            <ul class="dropdown-menu dropdown-menu-end" style="">
                <li>
                    <form method="POST" action="{{ route('ypi.admin.report.export') }}" id="filter_booking_export_form">
                        @csrf
                        <input type="hidden" id="export_client_group_filter" name="export_client_group_filter"
                            value="">
                        <input type="hidden" id="export_booking_status_filter" name="export_booking_status_filter"
                            value="">
                        <input type="hidden" id="export_event_filter" name="export_event_filter" value="">
                        <input type="hidden" id="export_venue_filter" name="export_venue_filter" value="">
                        <input type="hidden" id="export_rsp_filter" name="export_rsp_filter" value="">
                        <input type="hidden" id="export_date_range_filter" name="export_date_range_filter" value="">
                        {{-- <button type="submit">export</button> --}}
                        <button type="submit" class="btn btn-link p-2 m-2 align-baseline">
                            Export Filtered Results
                        </button>
                        {{-- <a class="dropdown-item ms-2 text-success me-2"
                            href="{{ route('ypi.admin.participant.test.email') }}">
                            <span class="fa-solid fa-download text-success me-2"></span>Dynamic Email Test
                        </a> --}}
                    </form>
                </li>
            </ul>
        </div>
    </div>
    <x-ypi.admin.participant-card />

    <!-- Filter Offcanvas -->
    <div class="offcanvas offcanvas-end" id="bookingFilterOffcanvas" tabindex="-1" aria-labelledby="bookingFilterOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="bookingFilterOffcanvasLabel">Filter Participants</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="mb-3">
                <label for="filter_event_id" class="form-label">Event</label>
                <select class="form-select" id="filter_event_id" name="event_id">
                    <option value="">All Events</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <button type="button" class="btn btn-primary w-100" id="applyFilter">Apply Filter</button>
            </div>
            <div class="mb-3">
                <button type="button" class="btn btn-secondary w-100" id="clearFilter">Clear Filter</button>
            </div>
        </div>
    </div>

    @include('ypi.admin.participant.modals.participant_modals')

    <script src="{{ asset('assets/js/pages/ypi/admin/participant.js') }}"></script>
    <script src="{{ asset('assets/js/pages/ypi/participant_upload_cert.js') }}"></script>
@endsection

@push('script')
    <script>
        console.log('Selected event from server:', @json($selectedEvent));
        console.log('Filter indicator element:', $('#filterIndicator'));
        console.log('Event name toolbar element:', $('#eventNameToolbar'));
        
        // Function to update filter indicator and event name
        function updateFilterUI(eventId, eventName) {
            console.log('updateFilterUI called with:', eventId, eventName);
            if (eventId && eventName) {
                $('#filterIndicator').css('display', 'block');
                $('#eventNameToolbar').text(eventName).css('display', 'block');
            } else {
                $('#filterIndicator').css('display', 'none');
                $('#eventNameToolbar').css('display', 'none');
            }
        }

        // showing the offcanvas for the task creation
        $(document).ready(function() {
            console.log('ready');
            $('.dropify').dropify();

            // Set initial filter value from session and update UI immediately
            @if($selectedEvent)
                console.log('Setting filter for event:', {{ $selectedEvent->id }}, {!! json_encode($selectedEvent->name) !!});
                $('#filter_event_id').val({{ $selectedEvent->id }});
                updateFilterUI({{ $selectedEvent->id }}, {!! json_encode($selectedEvent->name) !!});
            @else
                console.log('No filter set');
                updateFilterUI(null, null);
            @endif

            // Handle filter apply
            $('#applyFilter').on('click', function() {
                var eventId = $('#filter_event_id').val();
                var eventName = $('#filter_event_id option:selected').text();
                
                // Store in session via AJAX
                $.ajax({
                    url: '{{ route('ypi.admin.participant.setFilter') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        event_id: eventId
                    },
                    success: function(response) {
                        // Update UI without reload
                        updateFilterUI(eventId, eventName);
                        
                        // Refresh table
                        $('#participant_table').bootstrapTable('refresh');
                        
                        // Close offcanvas
                        var offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('bookingFilterOffcanvas'));
                        if (offcanvas) {
                            offcanvas.hide();
                        }
                    }
                });
            });

            // Handle filter clear
            $('#clearFilter').on('click', function() {
                $('#filter_event_id').val('');
                
                // Clear session via AJAX
                $.ajax({
                    url: '{{ route('ypi.admin.participant.clearFilter') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Update UI without reload
                        updateFilterUI(null, null);
                        
                        // Refresh table
                        $('#participant_table').bootstrapTable('refresh');
                        
                        // Close offcanvas
                        var offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('bookingFilterOffcanvas'));
                        if (offcanvas) {
                            offcanvas.hide();
                        }
                    }
                });
            });
        });
    </script>
@endpush
