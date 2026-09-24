@php
    $currentEvent = current_event();
@endphp

<div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis pt-6 border-y">
    <div data-participants-table="data-participants-table">
        <div class="row align-items-end justify-content-between pb-5 g-3">
            <div class="col-auto">
                <h3>Participants (Read Only)</h3>
                <p class="text-body-tertiary lh-sm mb-0">View participant information and uniform sizes</p>
            </div>
            <div class="col-12 col-md-auto">
                <div class="d-flex align-items-center">
                    <!-- Export Sizes Button -->
                    <a href="{{ route('ypi.uniform.participant.export.sizes') }}" class="btn btn-success">
                        <span class="fas fa-file-excel me-2"></span>Export Uniform Sizes
                    </a>
                </div>
            </div>
        </div>

        <!-- Toolbar showing the event selected in the header -->
        <div id="toolbar" class="mb-3">
            <h5 id="eventNameToolbar" class="mb-0 text-primary" style="display: {{ $currentEvent ? 'block' : 'none' }};">
                {{ $currentEvent?->name }}
            </h5>
        </div>
        <div class="mx-2 mb-2">
            <table id="participant_table" data-toggle="table" data-sortable="true" data-toolbar="#toolbar"
                data-classes="table table-hover fs-9 mb-0 border-top border-translucent"
                data-loading-template="loadingTemplate" data-url="{{ route('ypi.uniform.participant.list') }}"
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
                        <th data-field="pants_size">Pants Size</th>
                        <th data-field="jersey_size">Jersey Size</th>
                        <th data-field="jacket_size">Jacket Size</th>
                        <th data-field="shoe_size">Shoe Size</th>
                        <th data-field="food_allergies" data-visible="false">Food Allergies</th>
                        <th data-field="food_allergy_others" data-visible="false">Allergy Details</th>
                        <th data-field="health_issues" data-visible="false">Health Issues</th>
                        <th data-field="health_issues_details" data-visible="false">Health Issue Details</th>
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
</script>
