@extends('ypi.customer.layout.template')
@section('main')
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->

    {{-- <div class="content"> --}}
    {{-- <div class="my-4 col-md-10 px-4" style="margin:0 auto;" data-component-card="data-component-card">
        <div class=" px-8" style="margin:0 auto;" data-component-card="data-component-card">
            <div class="d-flex justify-content-between m-2">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-style1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}"><?= get_label('home', 'Home') ?></a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ route('wdr.report.create') }}">
                                    <?= get_label('booking', 'Booking') ?></a>
                            </li>
                            <li class="breadcrumb-item active">
                                <?= get_label('save', 'Save') ?>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

    </div> --}}
    {{-- <div class="container"> --}}



    <div class="card shadow-none border my-4 col-md-8" style="margin:0 auto;" data-component-card="data-component-card">
        <div class="card-header p-4 border-bottom bg-body">
            <div class="row g-3 justify-content-between align-items-center">
                <div class="col-12 col-md">
                    <h4 class="text-body mb-0" data-anchor="data-anchor">Edit Participant</h4>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="p-4 code-to-copy d-flex justify-content-center align-items-center">
                <div class="col-xl-9">
                    <form id="spinner-form" class="row g-3 mb-6 report-form needs-validation"
                        action="{{ route('ypi.customer.guardian.update') }}" method="POST" enctype="multipart/form-data"
                        novalidate>
                        @csrf
                        <input type="hidden" name="participant_id" value="{{ $participant->id }}" />
                        <div class="col-sm-6 col-md-8">
                            <div class="form-floating">
                                <input class="form-control" id="full_name" type="text" required
                                    placeholder="Participant full name" name="full_name"
                                    value="{{ $participant->full_name }}" />
                                <label for="full_name">Participant full name</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="form-floating">
                                <select class="form-select" id="participant_type_id" name="participant_type_id" required>
                                    <option selected="selected" value="">Select participant type</option>
                                    @foreach ($participant_types as $type)
                                        <option value="{{ $type->id }}"
                                            {{ $participant->participant_type_id == $type->id ? 'selected' : '' }}>
                                            {{ $type->title }}</option>
                                    @endforeach
                                </select>
                                <label for="participant_type_id">Participant type</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-12">
                            <div class="form-floating">
                                <input class="form-control" id="school_name" type="text" required
                                    placeholder="Participant school name" name="school_name"
                                    value="{{ $participant->school_name }}" />
                                <label for="school_name">Participant school name</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="form-floating">
                                <input class="form-control" id="qid" type="text" placeholder="Participant QID"
                                    name="qid" value="{{ $participant->qid }}" required />
                                <label for="qid">Participant QID</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="form-floating">
                                <select class="form-select" id="gender_id" name="gender_id" required>
                                    <option selected="selected" value="">Select gender</option>
                                    @foreach ($genders as $gender)
                                        <option value="{{ $gender->id }}"
                                            {{ $participant->gender_id == $gender->id ? 'selected' : '' }}>
                                            {{ $gender->title }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="gender_id">Gender</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="form-floating">
                                <select class="form-select" id="nationality_id" name="nationality_id" required>
                                    <option selected="selected" value="">Select nationality</option>
                                    @foreach ($nationalities as $nationality)
                                        <option value="{{ $nationality->id }}"
                                            {{ $participant->nationality_id == $nationality->id ? 'selected' : '' }}>
                                            {{ $nationality->title }}</option>
                                    @endforeach
                                </select>
                                <label for="nationality_id">Nationality</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <div class="flatpickr-input-container">
                                <div class="form-floating">
                                    <input class="form-control datetimepicker" id="date_of_birth" type="text" required
                                        placeholder="participant date of birth" name="date_of_birth"
                                        value="{{ $participant->date_of_birth_dmy }}"
                                        data-options='{"disableMobile":true,"dateFormat":"d/m/Y"}' />
                                    <label class="ps-6" for="date_of_birth">Participant Date of
                                        Birth</label><span
                                        class="uil uil-calendar-alt flatpickr-icon text-body-tertiary"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <div class="form-floating">
                                <input class="form-control" id="participant_age" type="text" required
                                    placeholder="Participant age" name="age" value="{{ $age }}" disabled />
                                <label for="participant_age">Participant age</label>
                            </div>
                        </div>
                        <div class=" gy-3">
                            <hr />
                        </div>
                        {{-- @php
                            $doc = route('participant.docs.view', $participant->qidDocument?->id);
                            Log::info('inside edit blade doc: ' . $doc);
                        @endphp --}}
                        {{-- <div class="text-center mb-3">
                            <div class="mb-3 text-start">
                                <label class="form-label">Upload Participant QID <span class='text-danger'>(Extensions: jpg,jpeg,png,pdf Max 2MB)</span></label>
                                <input type="file" required name="qid_file" class="dropify" data-height="100"
                                    data-allowed-file-extensions="jpg jpeg png pdf" data-max-file-size="2M" 
                                    data-default-file="{{ route('participant.docs.view', $participant->qidDocument->id) }}" />
                            </div>
                        </div> --}}

                        {{-- <label class="form-label fw-semibold">
    Participant QID
    <span class="text-danger">(jpg, jpeg, png, pdf • max 2MB)</span>
</label> --}}

                        {{-- <div class="border rounded p-3 bg-white">

    @if ($participant->qidDocument)
        @php
            $doc = $participant->qidDocument;
        @endphp

        <div class="mb-2">
            <div class="small text-muted mb-1">Current file:</div>

            @if (Str::startsWith($doc->mime, 'image/'))
                <img
                    src="{{ route('participant.docs.view', $doc->id) }}"
                    style="max-height:140px;border:1px solid #ddd;padding:4px;background:#fff;"
                >
            @else
                <a href="{{ route('participant.docs.view', $doc->id) }}"
                   target="_blank"
                   class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-file-pdf me-1"></i> View current document
                </a>
            @endif
        </div>
    @else
        <div class="text-muted small mb-2">
            No QID document uploaded yet.
        </div>
    @endif

    <input
        type="file"
        name="qid_file"
        class="form-control mt-2"
        accept="image/*,application/pdf"
    >

    @error('qid_file')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div> --}}
                        <label class="form-label fw-semibold text-uppercase small mb-2">
                            Participant QID
                            <span class="text-danger">(jpg, jpeg, png, pdf • max 2MB)</span>
                        </label>

                        <div class="border rounded-3 p-3 bg-body">
                            <div class="d-flex align-items-start gap-3 flex-wrap">
                                <div class="qid-dropzone">
                                    <div
                                        class="qid-preview border rounded-3 bg-white d-flex align-items-center justify-content-center">
                                        @if ($participant->qidDocument)
                                            @php $doc = $participant->qidDocument; @endphp

                                            @if (Str::startsWith($doc->mime, 'image/'))
                                                <img src="{{ route('participant.docs.view', $doc->id) }}" alt="QID"
                                                    class="qid-preview-img">
                                            @else
                                                <div class="text-center p-3">
                                                    <i class="fa-solid fa-file-pdf fa-2x text-danger"></i>
                                                    <div class="small mt-2">PDF Document</div>
                                                    <a class="btn btn-sm btn-outline-primary mt-2" target="_blank"
                                                        href="{{ route('participant.docs.view', $doc->id) }}">
                                                        View
                                                    </a>
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-center p-3">
                                                <i class="fa-regular fa-image fa-2x text-body-tertiary"></i>
                                                <div class="small text-body-tertiary mt-2">No file uploaded</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    onclick="document.getElementById('qid_file').click()">
                                    Replace QID File
                                </button>
                                <input type="file" name="qid_file" id="qid_file" class="d-none"
                                    accept="image/*,application/pdf">
                            </div>
                        </div>

                        {{-- <div class="form-group">
                            <label for="photoUpload">Photo Uploads (Max 10 MB)</label>
                            <div class="photo-upload-area" id="photoUploadArea">
                                <input type="file" id="photoUpload" name="qid_file" accept="image/*" />
                                <div class="upload-placeholder">
                                    <span class="material-icons">cloud_upload</span>
                                    <p>Click to upload photos or drag and drop</p>
                                    <span>Support for multiple photos</span>
                                </div>
                            </div>
                            <div id="photoPreview" class="photo-preview"></div>
                        </div> --}}

                        <div class=" gy-3">
                            <hr />
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-floating">
                                <select class="form-select" id="pants_size_id" name="pants_size_id" required>
                                    <option selected="selected" value="">Select pants size</option>
                                    @foreach ($pant_sizes as $pant_size)
                                        <option value="{{ $pant_size->id }}"
                                            {{ $participant->pants_size_id == $pant_size->id ? 'selected' : '' }}>
                                            {{ $pant_size->label }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="pants_size_id">Pants Size</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-floating">
                                <select class="form-select" id="jersey_size_id" name="jersey_size_id" required>
                                    <option selected="selected" value="">Select jersey size</option>
                                    @foreach ($jersey_sizes as $jersey_size)
                                        <option value="{{ $jersey_size->id }}"
                                            {{ $participant->jersey_size_id == $jersey_size->id ? 'selected' : '' }}>
                                            {{ $jersey_size->label }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="jersey_size_id">Jersey Size</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-floating">
                                <select class="form-select" id="jacket_size_id" name="jacket_size_id" required>
                                    <option selected="selected" value="">Select jacket size</option>
                                    @foreach ($jacket_sizes as $jacket_size)
                                        <option value="{{ $jacket_size->id }}"
                                            {{ $participant->jacket_size_id == $jacket_size->id ? 'selected' : '' }}>
                                            {{ $jacket_size->label }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="jacket_size_id">Jacket Size</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-floating">
                                <select class="form-select" id="shoe_size_id" name="shoe_size_id" required>
                                    <option selected="selected" value="">Select shoe size</option>
                                    @foreach ($shoe_sizes as $shoe_size)
                                        <option value="{{ $shoe_size->id }}"
                                            {{ $participant->shoe_size_id == $shoe_size->id ? 'selected' : '' }}>
                                            {{ $shoe_size->label }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="shoe_size_id">Shoe Size</label>
                            </div>
                        </div>

                        @php
                            $hasAllergy = (int) old('has_food_allergy', $participant->food_allergy) === 1;
                            $hasHealthIssues = (int) old('has_health_issues', $participant->health_issues) === 1;

                            Log::info('participant->has_food_allergy: ' . $participant->ood_allergy);
                            Log::info('participant->has_health_issues: ' . $participant->health_issues);
                            Log::info('hasAllergy: ' . ($hasAllergy ? 'true' : 'false'));
                            Log::info('hasHealthIssues: ' . ($hasHealthIssues ? 'true' : 'false'));
                        @endphp

                        <div class="mb-0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="has_food_allergy"
                                    name="food_allergy" value="1" {{ $hasAllergy ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="has_food_allergy">
                                    Any food allergies?
                                </label>
                            </div>
                        </div>

                        <div class="mb-1 {{ $hasAllergy ? '' : 'd-none' }}" id="food_allergy_details_wrap">
                            <label class="form-label">
                                Please specify food allergies
                            </label>
                            <input type="text" class="form-control" name="food_allergy_details"
                                id="food_allergy_details" placeholder="e.g. nuts, dairy, shellfish" value="{{ $participant->food_allergy_details }}">
                        </div>
                        <div class="mb-1">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="has_health_issues"
                                    name="health_issues" value="1" {{ $hasHealthIssues ? 'checked' : '' }}>

                                <label class="form-check-label fw-semibold" for="has_health_issues">
                                    Any health issues?
                                </label>
                            </div>
                        </div>

                        <div class="mb-3 {{ $hasHealthIssues ? '' : 'd-none' }}" id="health_issues_details_wrap">
                            <label class="form-label">
                                Please specify health issues
                            </label>
                            <input type="text" class="form-control" id="health_issues_details" value="{{ $participant->health_issues_details }}"
                                name="health_issues_details" placeholder="e.g. nuts, dairy, shellfish">
                        </div>


                        <div class="col-12 gy-6">
                            <div class="row g-3 justify-content-end">
                                <div class="col-auto">
                                    <button class="btn btn-phoenix-primary px-5"
                                        onclick="window.location='{{ route('home') }}'" type="button">Cancel</button>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-primary px-5 px-sm-15" type="submit">Update
                                        Participant</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/pages/ypi/customer/create.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/pages/ypi/customer/photo_upload.js') }}"></script> --}}
    {{-- <link href="{{ asset('assets/css/photo_upload.css') }}" rel="stylesheet"> --}}

    {{-- @include('mds.admin.modals.booking_modals') --}}
@endsection

@push('script')
    <script>
        // showing the offcanvas for the task creation
        $(document).ready(function() {
            console.log('ready');
            $('.dropify').dropify();

            //             var drEvent = $('.dropify').dropify();
            // drEvent = drEvent.data('dropify');
            // console.log('drEvent', drEvent);
            // // If you changed the attribute via JS, you must reset it like this:
            // drEvent.resetPreview();
            // drEvent.clearElement();
            // drEvent.settings.defaultFile = "{{ route('participant.docs.view', 15) }}";
            // drEvent.destroy();
            // drEvent.init();
        });
    </script>
@endpush
