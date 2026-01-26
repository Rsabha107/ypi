<!-- meetings -->

<div class="card mt-4">
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            {{$slot}}
            <input type="hidden" id="data_type" value="booking">
            <div class="mx-2 mb-2">
                <table id="flight_table"
                    data-toggle="table"
                    data-classes="table table-hover  fs-9 mb-0 border-top border-translucent"
                    data-loading-template="loadingTemplate"
                    data-url="{{ route('gms.admin.flight.list')}}"
                    data-icons-prefix="bx"
                    data-icons="icons"
                    data-show-export="true"
                    data-export-types="['csv', 'txt', 'doc', 'excel', 'xlsx', 'pdf']"
                    data-show-columns-toggle-all="true"
                    data-show-refresh="true"
                    data-show-toggle="true"
                    data-total-field="total"
                    data-trim-on-search="false"
                    data-data-field="rows"
                    data-page-list="[5, 10, 20, 50, 100, 200]"
                    data-search="true"
                    data-searchable="true"
                    data-strict-search="true"
                    data-side-pagination="server"
                    data-show-columns="true"
                    data-pagination="true"
                    data-sort-name="id"
                    data-sort-order="desc"
                    data-mobile-responsive="true"
                    data-buttons-class="secondary"
                    data-query-params="flightQueryParams">

                    <thead>
                        <tr>
                            <!-- <th data-checkbox="true"></th> -->
                            <!-- <th data-sortable="true" data-field="id" class="align-middle white-space-wrap fw-bold fs-9"><?= get_label('id', 'ID') ?></th> -->

                            <!-- <th data-sortable="false" data-field="guestData" >Reference Guest</th> -->
                            <th data-sortable="true" data-field="ref_number"><?= get_label('ref_number', 'Guest Ref#') ?></th>
                            <th data-sortable="false" data-field="flight_number">Flight#</th>
                            <th data-sortable="false" data-field="airline">Airline</th>
                            <th data-sortable="false" data-field="cabin">Cabin</th>
                            <th data-sortable="false" data-field="flight_type">Type</th>
                            <th data-sortable="false" data-field="departure_airport">Departure Airport</th>
                            <th data-sortable="false" data-field="arrival_airport">Arrival Airport</th>
                            <th data-sortable="false" data-field="departure_time">Departure Time</th>
                            <th data-sortable="false" data-field="arrival_time">Arrival Time</th>
                            <th data-sortable="false" data-field="duration">Duration</th>
                            <th data-sortable="false" data-field="status">Status</th>
                            <th data-sortable="true" data-field="created_at" data-visible="false"><?= get_label('created_at', 'Created at') ?></th>
                            <th data-sortable="true" data-field="updated_at" data-visible="false"><?= get_label('updated_at', 'Updated at') ?></th>
                            <th data-field="action" class="text-end"><?= get_label('actions', 'Actions') ?></th>
                            <!-- <th data-formatter="actionsFormatter" class="text-end"><?= get_label('actions', 'Actions') ?></th> -->
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    ("use strict");

    function flightQueryParams(p) {
        return {
            guest_id: $("#guest_id").val(), // 👈 add this
            mds_schedule_event_filter: $("#mds_schedule_event_filter").val(),
            mds_schedule_venue_filter: $("#mds_schedule_venue_filter").val(),
            mds_schedule_rsp_filter: $("#mds_schedule_rsp_filter").val(),
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

    $("#mds_schedule_event_filter,#mds_schedule_venue_filter,#mds_schedule_rsp_filter").on("change", function(e) {
        e.preventDefault();
        console.log("tasks.js on change");
        $("#bookings_table").bootstrapTable("refresh");
    });
</script>