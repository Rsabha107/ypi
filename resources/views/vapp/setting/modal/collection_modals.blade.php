<div class="modal fade" id="create_collection_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel text-white"><?= get_label('add_collection', 'Add Collection') ?></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate="" class="modal-content form-submit-event needs-validation" id="form_submit_event" action="{{route('vapp.setting.collection.store')}}" method="POST">
                @csrf
                <input type="hidden" name="table" value="collections_table">
                <div class="modal-body">
                    <x-formy.form_select class="col-sm-12 col-md-12 mb-3" floating="0" selectedValue=""
                        name="event_id" elementId="add_event_id" label="Event" required=""
                        :forLoopCollection="$events" itemIdForeach="id" itemTitleForeach="name" style=""
                        addDynamicButton="0" />
                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('collection_location', 'Collection Location') ?> <span class="asterisk">*</span></label>
                        <input required type="text" id="nameBasic" class="form-control" name="collection_location" placeholder="<?= get_label('please_enter_collection_location', 'Please enter collection location') ?>" />
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('collection_time', 'Collection Time') ?> <span class="asterisk">*</span></label>
                        <input required type="text" id="nameBasic" class="form-control" name="collection_time" placeholder="<?= get_label('please_enter_collection_time', 'Please enter collection time') ?>" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary" id="submit_btn"><?= get_label('save', 'Save') ?></label></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_collection_modal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel">Edit</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate="" class="modal-content form-submit-event needs-validation" id="edit_form_submit_event" action="{{route('vapp.setting.collection.update')}}" method="POST">
                @csrf
                <input type="hidden" id="edit_collection_id" name="id" value="">
                <input type="hidden" id="edit_collection_table" name="table">
                <div class="modal-body">
                    <x-formy.form_select class="col-sm-12 col-md-12" floating="0" selectedValue=""
                        name="event_id" elementId="edit_event_id" label="Event" required=""
                        :forLoopCollection="$events" itemIdForeach="id" itemTitleForeach="name" style=""
                        addDynamicButton="0" />
                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('collection_location', 'Collection Location') ?> <span class="asterisk">*</span></label>
                        <input type="text" id="edit_collection_location" class="form-control" name="collection_location" placeholder="<?= get_label('please_enter_collection_location', 'Please enter collection location') ?>" />
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('collection_time', 'Collection Time') ?> <span class="asterisk">*</span></label>
                        <input required type="text" id="edit_collection_time" class="form-control" name="collection_time" placeholder="<?= get_label('please_enter_collection_time', 'Please enter collection time') ?>" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary" id="submit_btn"><?= get_label('save', 'Save') ?></label></button>
                </div>
            </form>
        </div>
    </div>
</div>