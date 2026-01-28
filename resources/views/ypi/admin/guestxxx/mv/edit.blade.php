<script src="{{ asset('fnx/assets/js/phoenix.js') }}"></script>
<script>
    // showing the offcanvas for the task creation
    $(document).ready(function() {
        console.log('ready');
        $('.dropify').dropify();

    });
</script>

<input type="hidden" id="edit_guest_table" name="table" value="guest_table" />
<input type="hidden" id="edit_guest_id" name="id" value="{{$guest->id}}">
<div class="card">
    <div class="card-header d-flex align-items-center border-bottom">
        <div class="ms-3">
            <h5 class="mb-0 fs-sm">Edit Guest</h5>
        </div>
    </div>
    <div class="card-body">

        <div class="text-center mb-3">
            <div class="mb-3 text-start">
                <input type="file" name="file_name" class="dropify"
                    data-height="200"
                    data-default-file="{{ !empty($guest->photo) ? url('storage/upload/profile_images/' . $guest->photo) : url('upload/default.png') }}" />
            </div>
        </div>
        <div class="row mb-3">
            <x-formy.form_select
                class="col-sm-6 col-md-4  mb-3"
                name="guest_type_id"
                style=""
                itemIdForeach="id"
                selectedValue="{{ $guest->guest_type_id }}"
                itemTitleForeach="title"
                floating='1'
                elementId="add_guest_type"
                label="Guest Type"
                required="required"
                :forLoopCollection="$guestTypes"
                addDynamicButton='0'
                dynamicModal=null />

            <x-formy.form_select
                class="col-sm-6 col-md-4  mb-3"
                floating='1'
                name="prefix_id"
                style=""
                itemIdForeach="id"
                selectedValue="{{ $guest->prefix_id }}"
                itemTitleForeach="title"
                elementId="add_prefix"
                label="Prefix"
                required="required"
                :forLoopCollection="$prefixes"
                addDynamicButton='0'
                dynamicModal=null />

            <x-formy.form_input
                class="col-sm-6 col-md-4  mb-3"
                inputType="text"
                floating='1'
                inputValue="{{ $guest->qid_passport }}"
                name="qid_passport"
                elementId="qid_passport"
                label="QID/Passport"
                inputAttributes=""
                required="required"
                disabled='' />

        </div>
        <div class="row mb-3">
            <x-formy.form_input
                class="col-sm-6 col-md-4  mb-3"
                inputType="text"
                floating='1'
                inputValue="{{ $guest->first_name }}"
                name="first_name"
                elementId="add_first_name"
                label="First Name"
                inputAttributes=""
                required="required"
                disabled="" />
            <x-formy.form_input
                class="col-sm-6 col-md-4  mb-3"
                inputType="text"
                floating='1'
                inputValue="{{ $guest->middle_name }}"
                name="middle_name"
                elementId="add_middle_name"
                label="middle Name"
                inputAttributes=""
                required=""
                disabled="" />
            <x-formy.form_input
                class="col-sm-6 col-md-4  mb-3"
                inputType="text"
                floating='1'
                inputValue="{{ $guest->last_name }}"
                name="last_name"
                elementId="add_last_name"
                label="Last Name"
                inputAttributes=""
                required="required"
                disabled="" />

        </div>
        <div class="row mb-3">
            <x-formy.form_input
                class="col-sm-6 col-md-4  mb-3"
                inputType="text"
                floating='1'
                inputValue="{{ $guest->popular_name }}"
                name="popular_name"
                elementId="add_popular_name"
                label="Popular Name"
                inputAttributes=""
                required=""
                disabled="" />
            <x-formy.form_input
                class="col-sm-6 col-md-4  mb-3"
                inputType="email"
                floating='1'
                inputValue="{{ $guest->email }}"
                name="email"
                elementId="add_email"
                label="Email"
                inputAttributes=""
                required="required"
                disabled="" />
            <x-formy.form_input
                class="col-sm-6 col-md-4  mb-3"
                inputType="phone"
                floating='1'
                inputValue="{{ $guest->mobile_number }}"
                name="mobile_number"
                elementId="add_mobile_number"
                label="Mobile"
                inputAttributes=""
                required="required"
                disabled="" />
        </div>
        <div class="row mb-3">
            <x-formy.form_select
                class="col-sm-6 col-md-3  mb-3"
                name="client_group_id"
                elementId="add_client_group"
                floating='1'
                style=""
                itemIdForeach="id"
                selectedValue="{{ $guest->client_group_id }}"
                itemTitleForeach="title"
                label="Client Group"
                required="required"
                :forLoopCollection="$clientGroups"
                addDynamicButton='0'
                dynamicModal=null />

            <x-formy.form_select
                class="col-sm-6 col-md-3  mb-3"
                floating='1'
                name="hosted_by_id"
                style=""
                itemIdForeach="id"
                selectedValue="{{ $guest->hosted_by_id }}"
                itemTitleForeach="title"
                elementId="add_hosted_by"
                label="Hosted By"
                required="required"
                :forLoopCollection="$hostedBy"
                addDynamicButton='0'
                dynamicModal=null />

            <x-formy.form_select
                class="col-sm-6 col-md-3  mb-3"
                name="nationality_id"
                style=""
                itemIdForeach="id"
                selectedValue="{{ $guest->nationality_id }}"
                itemTitleForeach="title"
                elementId="add_nationality"
                floating='1'
                label="Nationality"
                required=""
                :forLoopCollection="$nationalities"
                addDynamicButton='0'
                dynamicModal=null />

            <x-formy.form_select
                class="col-sm-6 col-md-3  mb-3"
                name="designation_id"
                elementId="add_designation"
                floating='1'
                style=""
                itemIdForeach="id"
                selectedValue="{{ $guest->designation_id }}"
                itemTitleForeach="name"
                label="Designation"
                required=""
                :forLoopCollection="$designations"
                addDynamicButton='0'
                dynamicModal=null />
        </div>
        <div class="row mb-3">
            <x-formy.form_input
                class="col-sm-6 col-md-4  mb-3"
                inputType="text"
                floating='1'
                inputValue="{{ $guest->flight_preference }}"
                name="flight_preference"
                elementId="add_flight_preference"
                label="Flight Preference"
                inputAttributes=""
                required=""
                disabled="" />
            <x-formy.form_input
                class="col-sm-6 col-md-4  mb-3"
                inputType="text"
                floating='1'
                inputValue="{{ $guest->accomodation_preference }}"
                name="accomodation_preference"
                elementId="add_accomodation_preference"
                label="Accomodation Preference"
                inputAttributes=""
                required=""
                disabled="" />
            <x-formy.form_input
                class="col-sm-6 col-md-4  mb-3"
                inputType="text"
                floating='1'
                inputValue="{{ $guest->transportation_preference }}"
                name="transportation_preference"
                elementId="add_transportation_preference"
                label="Transportation Preference"
                inputAttributes=""
                required=""
                disabled="" />
        </div>
        <div class="col-12 gy-3">
            <div class="row g-3 justify-content-end">
                <a href="javascript:void(0)" class="col-auto">
                    <button type="button" class="btn btn-phoenix-danger px-5"
                        data-bs-toggle="tooltip" data-bs-placement="right"
                        data-bs-dismiss="offcanvas">
                        Cancel
                    </button>
                </a>
                <div class="col-auto">
                    <button class="btn btn-primary px-5 px-sm-15" id="submit_btn">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>