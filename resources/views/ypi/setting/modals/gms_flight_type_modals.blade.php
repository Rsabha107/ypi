<div class="modal fade" id="create_flight_types_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- use modal-lg for more space -->
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel">
                    <?= get_label('create_flight_types', 'Create flight Type') ?>
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form novalidate class="needs-validation" id="form_submit_event"
                action="{{ route('ypi.setting.flight_type.store') }}" method="POST">
                @csrf
                <input type="hidden" name="table" value="flight_types_table">

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"><?= get_label('name', 'Name') ?> <span class="asterisk">*</span></label>
                            <input required type="text" class="form-control" name="title"
                                placeholder="<?= get_label('please_enter_flight_types', 'Please enter Flight Type Name') ?>" />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" class="btn btn-primary"><?= get_label('save','Save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="edit_flight_types_modal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- Make it wider -->
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel">Edit Flight</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate class="needs-validation" id="edit_form_submit_event" action="{{ route('ypi.setting.flight_type.update') }}" method="POST">
                @csrf
                <input type="hidden" id="edit_flight_types_id" name="id" value="">
                <div class="modal-body">
                    <div class="row g-3"> <!-- Add gutters -->
                        <div class="col-md-6">
                            <label class="form-label"><?= get_label('name', 'Name') ?> <span class="asterisk">*</span></label>
                            <input type="text" id="edit_flight_types_title" class="form-control" name="title" placeholder="<?= get_label('please_enter_name', 'Please enter Flight Name') ?>" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?= get_label('close', 'Close') ?></button>
                    <button type="submit" class="btn btn-primary"><?= get_label('save','Save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
