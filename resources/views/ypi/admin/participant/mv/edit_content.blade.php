<input type="hidden" id="edit_participant_table" name="table" value="participant_table" />
<input type="hidden" id="edit_participant_id" name="id" value="{{ $participant->id }}">
<div class="card">
    <div class="card-header d-flex align-items-center border-bottom">
        <div class="ms-3">
            <h5 class="mb-0 fs-sm">Edit Participant</h5>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <x-formy.form_select
                class="col-sm-12 col-md-12  mb-3"
                name="event_id"
                style=""
                itemIdForeach="id"
                selectedValue="{{ $participant->event_id }}"
                itemTitleForeach="name"
                floating='1'
                elementId="edit_event"
                label="Select Event"
                required="required"
                :forLoopCollection="$events"
                addDynamicButton='0'
                dynamicModal=null />
        </div>
        <div class="row mb-3">
            <x-formy.form_select
                class="col-sm-6 col-md-3  mb-3"
                name="participant_type_id"
                style=""
                itemIdForeach="id"
                selectedValue="{{ $participant->participant_type_id }}"
                itemTitleForeach="title"
                floating='1'
                elementId="edit_participant_type"
                label="Participant Type"
                required="required"
                :forLoopCollection="$participantTypes"
                addDynamicButton='0'
                dynamicModal=null />

            <x-formy.form_select
                class="col-sm-6 col-md-3  mb-3"
                floating='1'
                name="gender_id"
                style=""
                itemIdForeach="id"
                selectedValue="{{ $participant->gender_id }}"
                itemTitleForeach="title"
                elementId="edit_gender"
                label="Gender"
                required="required"
                :forLoopCollection="$genders"
                addDynamicButton='0'
                dynamicModal=null />

            <x-formy.form_input
                class="col-sm-12 col-md-6  mb-3"
                inputType="text"
                floating='1'
                inputValue="{{ $participant->full_name }}"
                name="full_name"
                elementId="edit_full_name"
                label="Participant Name"
                inputAttributes=""
                required="required"
                disabled='' />
        </div>

        <div class="row mb-3">
            <x-formy.form_input
                class="col-sm-6 col-md-6  mb-3"
                inputType="text"
                floating='1'
                inputValue="{{ $participant->qid }}"
                name="qid"
                elementId="edit_qid"
                label="QID"
                inputAttributes=""
                required="required"
                disabled='' />

            <x-formy.form_input
                class="col-sm-6 col-md-6  mb-3"
                inputType="text"
                floating='1'
                inputValue="{{ $participant->date_of_birth }}"
                name="date_of_birth"
                elementId="edit_date_of_birth"
                label="Date of Birth"
                inputAttributes=""
                required="required"
                disabled='' />
        </div>

        <div class="row mb-3">
            <x-formy.form_select
                class="col-sm-6 col-md-6  mb-3"
                name="nationality_id"
                style=""
                itemIdForeach="id"
                selectedValue="{{ $participant->nationality_id }}"
                itemTitleForeach="title"
                floating='1'
                elementId="edit_nationality"
                label="Nationality"
                required="required"
                :forLoopCollection="$nationalities"
                addDynamicButton='0'
                dynamicModal=null />

            <x-formy.form_input
                class="col-sm-6 col-md-6  mb-3"
                inputType="text"
                floating='1'
                inputValue="{{ $participant->school_name }}"
                name="school_name"
                elementId="edit_school_name"
                label="School Name"
                inputAttributes=""
                required="required"
                disabled='' />
        </div>

        @if(\App\Models\Ypi\Event::showsUniform($participant->event_id))
        <div class="row mb-3">
            <x-formy.form_select
                class="col-sm-6 col-md-3  mb-3"
                name="pants_size_id"
                style=""
                itemIdForeach="id"
                selectedValue="{{ $participant->pants_size_id }}"
                itemTitleForeach="label"
                floating='1'
                elementId="edit_pants_size"
                label="Pants Size"
                required="required"
                :forLoopCollection="$pantSizes"
                addDynamicButton='0'
                dynamicModal=null />

            <x-formy.form_select
                class="col-sm-6 col-md-3  mb-3"
                name="jersey_size_id"
                style=""
                itemIdForeach="id"
                selectedValue="{{ $participant->jersey_size_id }}"
                itemTitleForeach="label"
                floating='1'
                elementId="edit_jersey_size"
                label="Jersey Size"
                required="required"
                :forLoopCollection="$jerseySizes"
                addDynamicButton='0'
                dynamicModal=null />

            <x-formy.form_select
                class="col-sm-6 col-md-3  mb-3"
                name="jacket_size_id"
                style=""
                itemIdForeach="id"
                selectedValue="{{ $participant->jacket_size_id }}"
                itemTitleForeach="label"
                floating='1'
                elementId="edit_jacket_size"
                label="Jacket Size"
                required="required"
                :forLoopCollection="$jacketSizes"
                addDynamicButton='0'
                dynamicModal=null />

            <x-formy.form_select
                class="col-sm-6 col-md-3  mb-3"
                name="shoe_size_id"
                style=""
                itemIdForeach="id"
                selectedValue="{{ $participant->shoe_size_id }}"
                itemTitleForeach="label"
                floating='1'
                elementId="edit_shoe_size"
                label="Shoe Size"
                required="required"
                :forLoopCollection="$shoeSizes"
                addDynamicButton='0'
                dynamicModal=null />
        </div>
        @endif

        <div class="row mb-3">
            <div class="col-12">
                <button class="btn btn-primary" type="submit">Update Participant</button>
            </div>
        </div>
    </div>
</div>
