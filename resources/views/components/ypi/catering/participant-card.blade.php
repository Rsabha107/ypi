@php
    $events = \App\Models\Ypi\Event::where('name', 'not like', '%Admin%')
        ->where('active_flag', 1)
        ->get();
@endphp

<div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis pt-6 border-y">
    <div data-participants-table="data-participants-table">
        <div class="row align-items-end justify-content-between pb-5 g-3">
            <div class="col-auto">
                <h3>Participants (Read Only)</h3>
                <p class="text-body-tertiary lh-sm mb-0">View participant information and dietary requirements</p>
            </div>
            <div class="col-12 col-md-auto">
                <div class="d-flex align-items-center">
                    <!-- Event Filter Button -->
                    <button class="btn btn-primary me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas" aria-controls="filterOffcanvas">
                        <span class="fas fa-filter me-2"></span>Filter by Event
                        <span id="filterIndicator" class="badge bg-danger ms-2" style="display: {{ session('participant_filter_event_id') ? 'inline' : 'none' }};">●</span>
                    </button>
                    <!-- Export Dietary Info Button -->
                    <a href="{{ route('ypi.catering.participant.export.dietary') }}" class="btn btn-success">
                        <span class="fas fa-file-excel me-2"></span>Export Dietary Info
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Offcanvas -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="filterOffcanvasLabel">Filter Participants</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form id="filterForm">
                    <div class="mb-3">
                        <label for="filter_event_id" class="form-label">Event</label>
                        <select class="form-select" id="filter_event_id" name="event_id">
                            <option value="">All Events</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ session('participant_filter_event_id') == $event->id ? 'selected' : '' }}>
                                    {{ $event->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">Apply Filter</button>
                        <button type="button" id="clearFilterBtn" class="btn btn-outline-secondary flex-fill">Clear</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Toolbar showing selected event -->
        <div id="toolbar" class="mb-3">
            <h5 id="eventNameToolbar" class="mb-0 text-primary" style="display: {{ session('participant_filter_event_id') ? 'block' : 'none' }};">
                {{ session('participant_filter_event_id') ? optional(\App\Models\Ypi\Event::find(session('participant_filter_event_id')))->name : '' }}
            </h5>
        </div>
        <div class="mx-2 mb-2">
            <table id="participant_table" data-toggle="table" data-sortable="true" data-toolbar="#toolbar"
                data-classes="table table-hover fs-9 mb-0 border-top border-translucent"
                data-loading-template="loadingTemplate" data-url="{{ route('ypi.catering.participant.list') }}"
                data-icons-prefix="bx" data-icons="icons"
                data-show-refresh="true" data-show-toggle="true"
                data-total-field="total" data-trim-on-search="false" data-data-field="rows"
                data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-searchable="true"
                data-strict-search="true" data-side-pagination="server"
                data-pagination="true" data-filter-control="true" data-filter-control-visible="true"
                data-show-search-clear-button="true" data-sort-name="id" data-sort-order="desc"
                data-mobile-responsive="true" data-buttons-class="secondary" data-query-params="participantQueryParams">

                <thead>
                    <tr>
                        <th data-field="participant_status">Participant Status</th>
                        <th data-sortable="true" data-field="created_at" data-visible="true">Created At</th>
                        <th data-field="assigned_venue_id">Assigned Venue</th>
                        <th data-field="assigned_match_id">Assigned Match</th>
                        <th data-field="participant_type">Participant Type</th>
                        <th data-field="full_name">Participant Name</th>
                        <th data-field="event_id">Event</th>
                        <th data-field="date_of_birth" data-visible="false">DOB</th>
                        <th data-field="gender" data-visible="false">Gender</th>
                        <th data-field="pants_size" data-visible="false">Pants Size</th>
                        <th data-field="jersey_size" data-visible="false">Jersey Size</th>
                        <th data-field="jacket_size" data-visible="false">Jacket Size</th>
                        <th data-field="shoe_size" data-visible="false">Shoe Size</th>
                        <th data-field="food_allergies">Food Allergies</th>
                        <th data-field="food_allergy_others">Allergy Details</th>
                        <th data-field="health_issues">Health Issues</th>
                        <th data-field="health_issues_details">Health Issue Details</th>
                        <th data-field="qid" data-visible="false">QID</th>
                        <th data-field="nationality" data-visible="false">Nationality</th>
                        <th data-field="updated_at" data-visible="false">Updated At</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    ("use strict");

    function participantQueryParams(p) {
        return {
            page: p.offset / p.limit + 1,
            limit: p.limit,
            sort: p.sort,
            order: p.order,
            offset: p.offset,
            search: p.search,
            filter: p.filter ? p.filter : ''
        };
    }

    window.icons = {
        refresh: "bx-refresh",
        toggleOn: "bx-toggle-right",
        toggleOff: "bx-toggle-left",
        fullscreen: "bx-fullscreen",
        columns: "bx-list-ul",
        export_data: "bx-list-ul",
        clearSearch: "bx-x-circle",
    };

    $('#participant_table').on('post-header.bs.table', function() {
        $('#participant_table').bootstrapTable('initFilterControls');
    });

    function loadingTemplate(message) {
        return '<i class="bx bx-loader-circle bx-spin bx-flip-vertical"></i>';
    }

    // Filter form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        const eventId = $('#filter_event_id').val();
        
        $.ajax({
            url: '{{ route("ypi.catering.participant.setFilter") }}',
            method: 'POST',
            data: {
                event_id: eventId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Show/hide indicator
                if (eventId) {
                    $('#filterIndicator').show();
                    const eventName = $('#filter_event_id option:selected').text();
                    $('#eventNameToolbar').text(eventName).show();
                } else {
                    $('#filterIndicator').hide();
                    $('#eventNameToolbar').hide();
                }
                
                // Refresh table
                $('#participant_table').bootstrapTable('refresh');
                
                // Close offcanvas
                const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('filterOffcanvas'));
                if (offcanvas) {
                    offcanvas.hide();
                }
            },
            error: function(xhr) {
                console.error('Filter error:', xhr);
            }
        });
    });

    // Clear filter
    $('#clearFilterBtn').on('click', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("ypi.catering.participant.clearFilter") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#filter_event_id').val('');
                $('#filterIndicator').hide();
                $('#eventNameToolbar').hide();
                $('#participant_table').bootstrapTable('refresh');
                
                const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('filterOffcanvas'));
                if (offcanvas) {
                    offcanvas.hide();
                }
            },
            error: function(xhr) {
                console.error('Clear filter error:', xhr);
            }
        });
    });
</script>
