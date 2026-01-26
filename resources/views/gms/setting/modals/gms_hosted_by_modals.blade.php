<div class="modal fade" id="create_hosted_bys_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel"><?= get_label('create_hosted_bys', 'Create Hosted By') ?></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate class="needs-validation" id="form_submit_event" action="{{ route('gms.setting.hosted_by.store') }}" method="POST">
                @csrf
                <input type="hidden" name="table" value="hosted_bys_table">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                            <input required type="text" class="form-control" name="title" placeholder="Enter title" />
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

<!-- Edit Modal -->
<div class="modal fade" id="edit_hosted_bys_modal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel">Edit Hosted By</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate class="needs-validation" id="edit_form_submit_event" action="{{ route('gms.setting.hosted_by.update') }}" method="POST">
                @csrf
                <input type="hidden" id="edit_hosted_bys_id" name="id">
                <input type="hidden" id="edit_hosted_bys_table" name="table">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                            <input type="text" id="edit_hosted_bys_title" class="form-control" name="title" placeholder="Enter title" />
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
