<!-- meetings -->

<div class="card mt-4 mb-5">
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            {{$slot}}
            <input type="hidden" id="data_type" value="funcareas">
            <div class="mx-2 mb-2">
                <table  id="funcareas_table"
                        data-toggle="table"
                        data-classes="table table-hover  fs-9 mb-0 border-top border-translucent"
                        data-loading-template="loadingTemplate"
                        data-url="{{ route('vapp.setting.funcareas.list')}}"
                        data-icons-prefix="bx"
                        data-icons="icons"
                        data-show-columns-toggle-all="true"
                        data-show-toggle="true"
                        data-show-refresh="true"
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
                        <th data-sortable="true" data-field="title"><?= get_label('title', 'Title') ?></th>
                        <th data-sortable="true" data-field="fa_code"><?= get_label('fa_code', 'Code') ?></th>
                        <th data-sortable="true" data-field="focal_point_name"><?= get_label('name', 'Name') ?></th>
                        <th data-sortable="true" data-field="focal_point_email"><?= get_label('email', 'Email') ?></th>
                        <th data-sortable="true" data-field="created_at" data-visible="false"><?= get_label('created_at', 'Created at') ?></th>
                        <th data-sortable="true" data-field="updated_at" data-visible="false"><?= get_label('updated_at', 'Updated at') ?></th>
                        <th data-formatter="actionsFormatter" class="text-end"><?= get_label('actions', 'Actions') ?></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
