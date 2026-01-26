@extends('ypi.layout.admin_template')
@section('main')


<div class="content">
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#!">Page 1</a></li>
            <li class="breadcrumb-item"><a href="#!">Page 2</a></li>
            <li class="breadcrumb-item active">Default</li>
        </ol>
    </nav>
    <form class="mb-9 needs-validation" novalidate="">
        <div class="row justify-content-between align-items-end g-3 mb-5">
            <div class="col-12 col-sm-auto col-xl-8">
                <h2 class="mb-0">Create an Event</h2>
            </div>
            <div class="col-12 col-sm-auto col-xl-4">
                <div class="d-flex">
                    <button class="btn btn-phoenix-primary px-5 me-2">Save</button><a
                        class="btn btn-primary px-5 w-100 text-nowrap"
                        href="../../apps/events/event-detail.html">Publish event</a>
                </div>
            </div>
        </div>
        <div class="row g-5">
            <div class="col-xl-8">
                <div class="row gx-3 gy-4">
                    <h4 class="fs-7 mb-0">Event Details</h4>
                    <div class="col-sm-6 col-md-12">
                        <div class="input-group">
                            <div class="form-floating">
                                <input
                                    class="form-control"
                                    id="floatingEventInput"
                                    type="text"
                                    placeholder="Event title" />
                                <div class="invalid-feedback">
                                    Please select event.
                                </div>
                                <label for="floatingEventInput">Event title</label>
                            </div>
                            <!-- <span class="input-group-text" id="basic-addon2">@example.com</span> -->
                            <button type="button" class="btn btn-phoenix-primary px-3" data-bs-toggle="modal"
                                data-bs-target="#exampleModal">
                                <i class="fas fa-plus-circle text-success"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                        <div class="input-group">
                            <div class="form-floating">
                                <select class="form-select" id="floatingSelectTask" required>
                                    <option value="" selected="selected">Select event type</option>
                                    <option value="1">technical</option>
                                    <option value="2">external</option>
                                    <option value="3">organizational</option>
                                </select>
                                <label for="floatingSelectTask">type</label>
                            </div>
                            <button type="button" class="btn btn-phoenix-primary px-3" data-bs-toggle="modal"
                                data-bs-target="#exampleModal">
                                <i class="fas fa-plus-circle text-success"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="floatingSelectPrivacy">
                                <option selected="selected">Select topic</option>
                                <option value="1">Data select topic One</option>
                                <option value="2">Data select topic Two</option>
                                <option value="3">Data select topic Three</option>
                            </select>
                            <label for="floatingSelectPrivacy">topic</label>
                        </div>
                    </div>
                    <div class="col-12 mt-4">
                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                id="inlineRadio1"
                                type="radio"
                                name="inlineRadioOptions"
                                value="option1"
                                checked="checked" />
                            <label class="form-check-label" for="inlineRadio1">Online</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                id="inlineRadio2"
                                type="radio"
                                name="inlineRadioOptions"
                                value="option2" />
                            <label class="form-check-label" for="inlineRadio2">Offline</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                id="inlineRadio3"
                                type="radio"
                                name="inlineRadioOptions"
                                value="option3" />
                            <label class="form-check-label" for="inlineRadio3">Both</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-12 mt-md-0 mt-lg-1">
                        <div class="form-floating">
                            <input
                                class="form-control"
                                id="floatingVenueInput"
                                type="text"
                                placeholder="Venue" />
                            <label for="floatingVenueInput">Venue</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="form-floating">
                            <select class="form-select" id="floatingSelectCountry">
                                <option selected="selected">Select Country</option>
                                <option value="1">Country One</option>
                                <option value="2">Country Two</option>
                                <option value="3">Country Three</option>
                            </select>
                            <label for="floatingSelectCountry">Country</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="form-floating">
                            <select class="form-select" id="floatingSelectState">
                                <option selected="selected">Select State</option>
                                <option value="1">One</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                            </select>
                            <label for="floatingSelectState"> State</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="form-floating">
                            <select class="form-select" id="floatingSelectCity">
                                <option selected="selected">Select city</option>
                                <option value="1">Data Privacy One</option>
                                <option value="2">Data Privacy Two</option>
                                <option value="3">Data Privacy Three</option>
                            </select>
                            <label for="floatingSelectCity">City</label>
                        </div>
                    </div>
                    <h4 class="fs-7 mt-7">Schedule</h4>
                    <div class="col-sm-6">
                        <div class="flatpickr-input-container">
                            <div class="form-floating">
                                <input
                                    class="form-control datetimepicker"
                                    id="startDatepicker"
                                    type="text"
                                    placeholder="start date"
                                    data-options='{"disableMobile":true}' /><span
                                    class="uil uil-calendar-alt flatpickr-icon text-body-tertiary"></span>
                                <label class="ps-6" for="startDatepicker">Start date</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-floating">
                            <input
                                class="form-control datetimepicker"
                                id="timepickerEvent"
                                type="text"
                                placeholder="H:i"
                                data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"H:i","disableMobile":true}' />
                            <label for="timepickerEvent">Start Time</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="flatpickr-input-container">
                            <div class="form-floating">
                                <input
                                    class="form-control datetimepicker"
                                    id="endDatepicker"
                                    type="text"
                                    placeholder="end date"
                                    data-options='{"disableMobile":true}' /><span
                                    class="uil uil-calendar-alt flatpickr-icon text-body-tertiary"></span>
                                <label class="ps-6" for="endDatepicker">End date</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-floating">
                            <input
                                class="form-control datetimepicker"
                                id="timepickerEvent2"
                                type="text"
                                placeholder="H:i"
                                data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"H:i","disableMobile":true}' />
                            <label for="timepickerEvent2">End Time</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-floating">
                            <select class="form-select" id="floatingSelectTimeZone">
                                <option selected="selected">Select time zone</option>
                                <option value="1">Data Privacy One</option>
                                <option value="2">Data Privacy Two</option>
                                <option value="3">Data Privacy Three</option>
                            </select>
                            <label for="floatingSelectTimeZone">Time zone</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="flatpickr-input-container">
                            <div class="form-floating">
                                <input
                                    class="form-control datetimepicker"
                                    id="datepicker"
                                    type="text"
                                    placeholder="registration deadline"
                                    data-options='{"disableMobile":true}' />
                                <label for="datepicker">Registration deadline</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 gy-6">
                        <div class="form-floating">
                            <textarea
                                class="form-control"
                                id="floatingProjectDescription"
                                placeholder="Leave a comment here"
                                style="height: 128px"></textarea>
                            <label for="floatingProjectDescription">Description</label>
                        </div>
                    </div>
                    <div class="col-sm-6 gy-6">
                        <div class="form-floating">
                            <input
                                class="form-control"
                                id="floatingOrganizersInput"
                                type="text"
                                placeholder="ORGANIZERS" />
                            <label for="floatingOrganizersInput">ORGANIZERS</label>
                        </div>
                    </div>
                    <div class="col-sm-6 gy-6">
                        <div class="form-floating">
                            <input
                                class="form-control"
                                id="floatingSponsorsInput"
                                type="text"
                                placeholder="SPONSORS" />
                            <label for="floatingSponsorsInput">SPONSORS</label>
                        </div>
                    </div>
                    <div class="col-12 gy-6">
                        <div
                            class="dropzone dropzone-multiple p-0 mb-5"
                            id="my-awesome-dropzone"
                            data-dropzone="data-dropzone">
                            <div class="fallback">
                                <input name="file" type="file" multiple="multiple" />
                            </div>
                            <div class="dz-preview d-flex flex-wrap">
                                <div
                                    class="border border-translucent bg-body-emphasis rounded-3 d-flex flex-center position-relative me-2 mb-2"
                                    style="height: 80px; width: 80px">
                                    <img
                                        class="dz-image"
                                        src="../../assets/img/products/23.png"
                                        alt="..."
                                        data-dz-thumbnail="data-dz-thumbnail" /><a
                                        class="dz-remove text-body-quaternary"
                                        href="#!"
                                        data-dz-remove="data-dz-remove"><span data-feather="x"></span></a>
                                </div>
                            </div>
                            <div
                                class="dz-message text-body-tertiary text-center"
                                data-dz-message="data-dz-message">
                                Drag your photo here
                                <span class="text-body-secondary">or </span>
                                <button class="btn btn-link p-0" type="button">
                                    Browse from device</button><br /><img
                                    class="mt-3 me-2"
                                    src="../../assets/img/icons/image-icon.png"
                                    width="40"
                                    alt="" />
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div style="--phoenix-choices-inner-min-height: 128px">
                            <select
                                class="form-select mb-3"
                                data-choices="data-choices"
                                multiple="multiple"
                                data-options='{"removeItemButton":true,"placeholder":true}'>
                                <option value="">Add tags</option>
                                <option value="MUSIC">Music</option>
                                <option value="CONCEERT">CONCEERT</option>
                                <option value="GREATEST SHOW ON EARTH">
                                    GREATEST SHOW ON EARTH
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="border-bottom border-translucent pb-6 mb-6">
                    <h4 class="fs-7 mb-4">Privacy</h4>
                    <div class="form-check mb-2">
                        <input
                            class="form-check-input"
                            id="exampleRadios1"
                            type="radio"
                            name="exampleRadios"
                            value="option1"
                            checked="checked" />
                        <label class="form-check-label fw-normal" for="exampleRadios1">On Phoenix, our distribution partners, and search
                            engines,<br />anybody can find the event
                        </label>
                    </div>
                    <div class="form-check mb-0">
                        <input
                            class="form-check-input"
                            id="exampleRadios2"
                            type="radio"
                            name="exampleRadios"
                            value="option2" />
                        <label class="form-check-label fw-normal" for="exampleRadios2">Only the individuals you select have access to it.</label>
                    </div>
                </div>
                <div class="border-bottom border-translucent mb-6">
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            id="flexChecked"
                            type="checkbox"
                            checked="checked" />
                        <label
                            class="form-check-label fw-bold fs-8 mb-5"
                            for="flexChecked">Show the number of available tickets</label>
                    </div>
                </div>
                <h4 class="fs-7 mb-4">Ticket pricing</h4>
                <div class="form-check form-check-inline">
                    <input
                        class="form-check-input"
                        id="freeTicket"
                        type="radio"
                        name="ticketPricing"
                        value="freeTicket"
                        checked="checked" />
                    <label class="form-check-label" for="freeTicket">Free</label>
                </div>
                <div class="form-check form-check-inline">
                    <input
                        class="form-check-input"
                        id="paidTicket"
                        type="radio"
                        name="ticketPricing"
                        value="paidTicket" />
                    <label class="form-check-label" for="paidTicket">Paid</label>
                </div>
                <div
                    class="row g-3 mb-7 mt-1 border-bottom border-translucent pb-5">
                    <div class="col-6 col col-xl-7">
                        <h4 class="fs-9">Option</h4>
                    </div>
                    <div class="col-6 col-xl-5">
                        <h4 class="fs-9">Price</h4>
                    </div>
                    <div class="col-6 col-xl-7">
                        <input
                            class="form-control"
                            type="text"
                            placeholder="Top Gallery" />
                    </div>
                    <div class="col-6 col-xl-5">
                        <input
                            class="form-control"
                            type="number"
                            placeholder="$0.0" />
                    </div>
                    <div class="col-6 col-xl-7">
                        <input class="form-control" type="text" placeholder="VIP" />
                    </div>
                    <div class="col-6 col-xl-5">
                        <input
                            class="form-control"
                            type="number"
                            placeholder="$0.0" />
                    </div>
                    <div class="col-6 col-xl-7">
                        <input
                            class="form-control"
                            type="text"
                            placeholder="Front row" />
                    </div>
                    <div class="col-6 col-xl-5">
                        <input
                            class="form-control"
                            type="number"
                            placeholder="$0.0" />
                    </div>
                    <div class="col-12">
                        <button class="btn btn-link p-0" type="button">
                            <span class="fa-solid fa-plus me-2"></span>Add an option
                        </button>
                    </div>
                </div>
                <h4 class="fs-7 mb-3">Custom fields</h4>
                <div class="row g-3 mb-3">
                    <div class="col-md-6 col-lg-12 col-xl-6">
                        <label class="form-label" for="inputName">Name</label>
                        <input
                            class="form-control"
                            id="inputName"
                            type="text"
                            placeholder="Item name (e.g. Special T-shirt)" />
                    </div>
                    <div class="col-md-6 col-lg-12 col-xl-6">
                        <label class="form-label" for="inputState1">Type</label>
                        <select class="form-select" id="inputState1">
                            <option value="">Choose...</option>
                            <option value="">Type One</option>
                            <option value="">Type Two</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="filedOptions">Field options</label>
                        <textarea
                            class="form-control"
                            id="filedOptions"
                            placeholder="Description"
                            rows="6"></textarea>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6 col-lg-12 col-xl-6">
                        <label class="form-label" for="inputName2">Name</label>
                        <input
                            class="form-control"
                            id="inputName2"
                            type="text"
                            placeholder="Item name (e.g. Special T-shirt)" />
                    </div>
                    <div class="col-md-6 col-lg-12 col-xl-6">
                        <label class="form-label" for="inputState2">Type</label>
                        <select class="form-select" id="inputState2">
                            <option value="">Choose...</option>
                            <option value="">Type Three</option>
                            <option value="">Type Four</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="filedOptions2">Field options</label>
                        <textarea
                            class="form-control"
                            id="filedOptions2"
                            placeholder="Description"
                            rows="6"></textarea>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-link p-0" type="button">
                            <span class="fa-solid fa-plus me-2"></span>Add an item
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <footer class="footer position-absolute">
        <div class="row g-0 justify-content-between align-items-center h-100">
            <div class="col-12 col-sm-auto text-center">
                <p class="mb-0 mt-2 mt-sm-0 text-body">
                    Thank you for creating with Phoenix<span
                        class="d-none d-sm-inline-block"></span><span class="d-none d-sm-inline-block mx-1">|</span><br class="d-sm-none" />2025 &copy;<a
                        class="mx-1"
                        href="https://themewagon.com">Themewagon</a>
                </p>
            </div>
            <div class="col-12 col-sm-auto text-center">
                <p class="mb-0 text-body-tertiary text-opacity-85">v1.22.0</p>
            </div>
        </div>
    </footer>
</div>

@endsection

@push('script')


@endpush