<div class="modal fade" id="create_airlines_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- use modal-lg for more space -->
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel">
                    <?= get_label('create_airlines', 'Create Airline') ?>
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form novalidate class="needs-validation" id="form_submit_event"
                action="{{ route('ypi.setting.airline.store') }}" method="POST">
                @csrf
                <input type="hidden" name="table" value="airlines_table">

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Airline Name & Carrier Code -->
                        <div class="col-md-6">
                            <label class="form-label"><?= get_label('name', 'Name') ?> <span class="asterisk">*</span></label>
                            <input required type="text" class="form-control" name="name"
                                placeholder="<?= get_label('please_enter_airline-name', 'Please enter Airline Name') ?>" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= get_label('carrier code', 'Carrier Code') ?> <span class="asterisk">*</span></label>
                            <input required type="text" class="form-control" name="carrier_code"
                                placeholder="<?= get_label('please_enter_carrier_code', 'Please enter Carrier Code') ?>" />
                        </div>

                        <!-- Country & IATA Code -->
                        <div class="col-md-6">
                            <label class="form-label"><?= get_label('country', 'Country') ?> <span class="asterisk">*</span></label>
                            <input required type="text" class="form-control" name="country"
                                placeholder="<?= get_label('please_enter_Country', 'Please enter Country') ?>" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= get_label('iata code', 'Iata Code') ?> <span class="asterisk">*</span></label>
                            <input required type="text" class="form-control" name="iata_code"
                                placeholder="<?= get_label('please_enter_iata_code', 'Please enter Iata Code') ?>" />
                        </div>

                        <!-- ICAO Code & Founded Year -->
                        <div class="col-md-6">
                            <label class="form-label"><?= get_label('icao code', 'Icao Code') ?> <span class="asterisk">*</span></label>
                            <input required type="text" class="form-control" name="icao_code"
                                placeholder="<?= get_label('please_enter_icao_code', 'Please enter Icao Code') ?>" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= get_label('founded_year', 'Founded Year') ?> <span class="asterisk">*</span></label>
                            <input required type="number" class="form-control" name="founded_year"
                                placeholder="<?= get_label('please_enter_year', 'Please enter Year') ?>" />
                        </div>

                        <!-- Website (Full width) -->
                        <div class="col-12">
                            <label class="form-label"><?= get_label('website', 'Website') ?> <span class="asterisk">*</span></label>
                            <input required type="url" class="form-control" name="website"
                                placeholder="<?= get_label('please_enter_website', 'Please enter Website') ?>" />
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


<div class="modal fade" id="edit_airlines_modal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- Make it wider -->
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel">Edit Airline</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate class="needs-validation" id="edit_form_submit_event" action="{{ route('ypi.setting.airline.update') }}" method="POST">
                @csrf
                <input type="hidden" id="edit_airlines_id" name="id" value="">
                <div class="modal-body">
                    <div class="row g-3"> <!-- Add gutters -->
                        <div class="col-md-6">
                            <label class="form-label"><?= get_label('name', 'Name') ?> <span class="asterisk">*</span></label>
                            <input type="text" id="edit_airlines_name" class="form-control" name="name" placeholder="<?= get_label('please_enter_name', 'Please enter Airline Name') ?>" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= get_label('carrier code', 'Carrier Code') ?> <span class="asterisk">*</span></label>
                            <input type="text" id="edit_airlines_carrier_code" class="form-control" name="carrier_code" placeholder="<?= get_label('please_enter_carrier_code', 'Please enter Carrier Code') ?>" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= get_label('country', 'Country') ?> <span class="asterisk">*</span></label>
                            <input type="text" id="edit_airlines_country" class="form-control" name="country" placeholder="<?= get_label('please_enter_country', 'Please enter Country') ?>" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= get_label('iata code', 'Iata Code') ?> <span class="asterisk">*</span></label>
                            <input type="text" id="edit_airlines_iata_code" class="form-control" name="iata_code" placeholder="<?= get_label('please_enter_iata_code', 'Please enter Iata Code') ?>" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= get_label('icao code', 'Icao Code') ?> <span class="asterisk">*</span></label>
                            <input type="text" id="edit_airlines_icao_code" class="form-control" name="icao_code" placeholder="<?= get_label('please_enter_icao_code', 'Please enter Icao Code') ?>" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= get_label('founded year', 'Founded Year') ?> <span class="asterisk">*</span></label>
                            <input type="number" id="edit_airlines_founded_year" class="form-control" name="founded_year" placeholder="<?= get_label('please_enter_year', 'Please enter Year') ?>" />
                        </div>
                        <div class="col-12">
                            <label class="form-label"><?= get_label('website', 'Website') ?> <span class="asterisk">*</span></label>
                            <input type="text" id="edit_airlines_website" class="form-control" name="website" placeholder="<?= get_label('please_enter_website', 'Please enter Website') ?>" />
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
