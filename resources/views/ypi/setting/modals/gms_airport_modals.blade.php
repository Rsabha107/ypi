<div class="modal fade" id="create_airports_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- use modal-lg for more space -->
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel">
                    <?= get_label('create_airports', 'Create Airport') ?>
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form novalidate class="needs-validation" id="form_submit_event"
                action="{{ route('ypi.setting.airport.store') }}" method="POST">
                @csrf
                <input type="hidden" name="table" value="airports_table">

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"><?= get_label('name', 'Name') ?> <span class="asterisk">*</span></label>
                            <input required type="text" class="form-control" name="airport_name"
                                placeholder="<?= get_label('please_enter_airports', 'Please enter Airport Name') ?>" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= get_label('code', 'Code') ?> <span class="asterisk">*</span></label>
                            <input required type="text" class="form-control" name="airport_code"
                                placeholder="<?= get_label('please_enter_airport_code', 'Please enter Airport Code') ?>" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= get_label('city', 'City') ?> <span class="asterisk">*</span></label>
                            <input required type="text" class="form-control" name="city"
                                placeholder="<?= get_label('please_enter_city', 'Please enter City Name') ?>" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= get_label('country', 'Country') ?> <span class="asterisk">*</span></label>
                            <input required type="text" class="form-control" name="country"
                                placeholder="<?= get_label('please_enter_country', 'Please enter Country Name') ?>" />
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


<div class="modal fade" id="edit_airports_modal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- Make it wider -->
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel">Edit Flight</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate class="needs-validation" id="edit_form_submit_event" action="{{ route('ypi.setting.airport.update') }}" method="POST">
                @csrf
                <input type="hidden" id="edit_airports_id" name="id" value="">
                <div class="modal-body">
                    <div class="row g-3"> <!-- Add gutters -->
                        <div class="col-md-6">
                            <label class="form-label"><?= get_label('name', 'Name') ?> <span class="asterisk">*</span></label>
                            <input type="text" id="edit_airports_title" class="form-control" name="title" placeholder="<?= get_label('please_enter_name', 'Please enter Flight Name') ?>" />
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
