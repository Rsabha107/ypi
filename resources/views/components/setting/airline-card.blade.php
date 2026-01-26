<!-- meetings -->

<div class="card mt-4">
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            {{$slot}}
            <input type="hidden" id="data_type" value="airline">
            <div class="mx-2 mb-2">
                <table id="airline_table"
                    data-toggle="table"
                    data-classes="table table-hover  fs-9 mb-0 border-top border-translucent"
                    data-loading-template="loadingTemplate"
                    data-url="{{ route('gms.setting.airline.list')}}"
                    data-icons-prefix="bx"
                    data-icons="icons"
                    data-show-export="true"
                    data-show-columns-toggle-all="true"
                    data-show-refresh="true"
                    data-show-toggle="true"
                    data-total-field="total"
                    data-trim-on-search="false"
                    data-data-field="rows"
                    data-page-list="[5, 10, 20, 50, 100, 200]"
                    data-search="true"
                    data-side-pagination="server"
                    data-show-columns="true"
                    data-pagination="true"
                    data-sort-name="id"
                    data-sort-order="asc"
                    data-mobile-responsive="true"
                    data-buttons-class="secondary"
                    data-query-params="queryParams">
                    <thead>
                        <tr>
                            <!-- <th data-checkbox="true"></th> -->
                            <!-- <th data-sortable="true" data-field="id" class="align-middle white-space-wrap fw-bold fs-9"><?= get_label('id', 'ID') ?></th> -->
                            <th data-sortable="true" data-field="name"><?= get_label('name', 'Name') ?></th>
                            <th data-sortable="true" data-field="carrier_code"><?= get_label('Carrier Code', 'Carrier Code') ?></th>
                            <th data-sortable="true" data-field="country"><?= get_label('Country', 'Country') ?></th>
                            <th data-sortable="true" data-field="iata_code"><?= get_label('Iata Code', 'Iata Code') ?></th>
                            <th data-sortable="true" data-field="icao_code"><?= get_label('Icao Code', 'Icao Code') ?></th>
                            <!-- <th data-sortable="true" data-field="founded_year" ><?= get_label('Year', 'Year') ?></th>
                            <th data-sortable="true" data-field="website" ><?= get_label('Website', 'Website') ?></th> -->
                            <th data-formatter="actionsFormatter" class="text-end"><?= get_label('actions', 'Actions') ?></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>