<div class="modal fade" id="create_flight_statuses_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel"><?= get_label('create_flight_statuses', 'Create Flight Status') ?></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate="" class="modal-content form-submit-event needs-validation" id="form_submit_event" action="{{ route('gms.setting.flight_status.store') }}" method="POST">
                @csrf
                <input type="hidden" name="table" value="flight_statuses_table">
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('status name', 'Status Name') ?> <span class="asterisk">*</span></label>
                            <input required type="text" id="nameBasic" class="form-control" name="status_name" placeholder="<?= get_label('please_enter_status_name', 'Please enter Status Name') ?>" />
                        </div>
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('description', 'Description') ?> <span class="asterisk">*</span></label>
                            <input required type="text" id="nameBasic" class="form-control" name="description" placeholder="<?= get_label('please_enter_description', 'Please enter Description') ?>" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary" id="submit_btn"><?= get_label('save','Save') ?></label></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_flight_statuses_modal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel">Edit Status Name</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate="" class="modal-content form-submit-event needs-validation" id="edit_form_submit_event"  action="{{ route('gms.setting.flight_status.update') }}" method="POST">
                @csrf
                <input type="hidden" id="edit_flight_statuses_id" name="id" value="">
                <input type="hidden" id="edit_flight_statuses_table" name="table">
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('status name', 'Status Name') ?> <span class="asterisk">*</span></label>
                            <input type="text" id="edit_flight_statuses_name" class="form-control" name="status_name" placeholder="<?= get_label('please_enter_status_name', 'Please enter status name') ?>" />
                        </div>
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('description', 'Description') ?> <span class="asterisk">*</span></label>
                            <input type="text" id="edit_flight_statuses_description" class="form-control" name="description" placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary" id="submit_btn"><?= get_label('save','Save') ?></label></button>
                </div>
            </form>
        </div>
    </div>
</div>