<div class="modal fade" id="create_sizes_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                Create sizes
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate="" class="modal-content form-submit-event needs-validation" id="form_submit_event"
                action="{{ route('ypi.setting.sizes.store') }}" method="POST">
                @csrf
                <input type="hidden" name="table" value="sizes_table">
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <select name="type" class="form-select" required>
                                <option value="">Select size type</option>
                                <option value="pant">Pant Size</option>
                                <option value="jersey">Jersey Size</option>
                                <option value="shoe">Shoe Size</option>
                                <option value="jacket">Jacket Size</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label">Code <span class="asterisk">*</span></label>
                            <input required type="text" id="nameBasic" class="form-control" name="code"
                                placeholder="<?= get_label('please_enter_code', 'Please enter code') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label">Label<span class="asterisk">*</span></label>
                            <input required type="text" id="nameBasic" class="form-control" name="label"
                                placeholder="<?= get_label('please_enter_label', 'Please enter label') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label">Sort Order <span class="asterisk">*</span></label>
                            <input required type="text" id="nameBasic" class="form-control" name="sort_order"
                                placeholder="<?= get_label('please_enter_sort_order', 'Please enter sort order') ?>" />
                        </div>
                    </div>
                    @include('ypi.setting.partials.event_scope_select')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('save', 'Save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_sizes_modal" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                Edit sizes
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate="" class="modal-content form-submit-event needs-validation" id="edit_form_submit_event"
                action="{{ route('ypi.setting.sizes.update') }}" method="POST">
                @csrf
                <input type="hidden" id="edit_sizes_id" name="id" value="">
                <input type="hidden" id="edit_sizes_table" name="table">
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <select name="type" id="edit_size_type" class="form-select" required>
                                <option value="">Select size type</option>
                                <option value="pant">Pant Size</option>
                                <option value="jersey">Jersey Size</option>
                                <option value="shoe">Shoe Size</option>
                                <option value="jacket">Jacket Size</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="edit_code" class="form-label">Code <span class="asterisk">*</span></label>
                            <input required type="text" id="edit_code" class="form-control" name="code"
                                placeholder="<?= get_label('please_enter_code', 'Please enter code') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="edit_label" class="form-label">Label<span class="asterisk">*</span></label>
                            <input required type="text" id="edit_label" class="form-control" name="label"
                                placeholder="<?= get_label('please_enter_label', 'Please enter label') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="edit_sort_order" class="form-label">Sort Order <span
                                    class="asterisk">*</span></label>
                            <input required type="text" id="edit_sort_order" class="form-control" name="sort_order"
                                placeholder="<?= get_label('please_enter_sort_order', 'Please enter sort order') ?>" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('save', 'Save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
