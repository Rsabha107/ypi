<div class="offcanvas-body">
    <div class="row">
        <div class="col-sm-12">
            <form class="row g-3 needs-validation form-submit-event" id="{{ $formId }}" novalidate=""
                action="{{ $formAction }}" method="POST">
                @csrf
                <input type="hidden" id="add_table" name="table" value="guest_table" />
                <div class="card">
                    <div class="card-header d-flex align-items-center border-bottom">
                        <div class="ms-3">
                            <h5 class="mb-0 fs-sm">Add A Guest</h5>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="text-center mb-3">
                            <div class="mb-3 text-start">
                                <input type="file" name="file_name" class="dropify"
                                    data-height="200"
                                    data-default-file="{{ !empty($user->photo) ? url('storage/upload/profile_images/' . $user->photo) : url('upload/default.png') }}" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <x-formy.form_select
                                class="col-sm-6 col-md-4  mb-3"
                                name="guest_type_id"
                                style=""
                                itemIdForeach="id"
                                selectedValue="title"
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
                                selectedValue="title"
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
                                inputValue=""
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
                                inputValue=""
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
                                inputValue=""
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
                                inputValue=""
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
                                inputValue=""
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
                                inputValue=""
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
                                inputValue=""
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
                                selectedValue=""
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
                                selectedValue=""
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
                                selectedValue=""
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
                                selectedValue=""
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
                                inputValue=""
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
                                inputValue=""
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
                                inputValue=""
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
            </form>
        </div>
    </div>
</div>