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
                    <h4 class="text-body mb-0" data-anchor="data-anchor">Add a Participant</h4>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="p-4 code-to-copy d-flex justify-content-center align-items-center">
                <div class="col-xl-9">
                    <form id="spinner-form" class="row g-3 mb-6 report-form needs-validation"
                        action="{{ route('ypi.customer.guardian.store') }}" method="POST" enctype="multipart/form-data"
                        novalidate>
                        @csrf
                        <div class="col-sm-6 col-md-8">
                            <div class="form-floating">
                                <input class="form-control" id="full_name" type="text" required
                                    placeholder="Participant full name" name="full_name" value="{{ old('full_name') }}" />
                                <label for="full_name">Participant full name</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="form-floating">
                                <select class="form-select" id="participant_type_id" name="participant_type_id" required>
                                    <option selected="selected" value="">Select participant type</option>
                                    @foreach ($participant_types as $type)
                                        <option value="{{ $type->id }}"
                                            {{ old('participant_type_id') == $type->id ? 'selected' : '' }}>
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
                                    value="{{ old('school_name') }}" />
                                <label for="school_name">Participant school name</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="form-floating">
                                <input class="form-control" id="qid" type="text" placeholder="Participant QID"
                                    name="qid" value="{{ old('qid') }}" required />
                                <label for="qid">Participant QID</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="form-floating">
                                <select class="form-select" id="gender_id" name="gender_id" required>
                                    <option selected="selected" value="">Select gender</option>
                                    @foreach ($genders as $gender)
                                        <option value="{{ $gender->id }}"
                                            {{ old('gender_id') == $gender->id ? 'selected' : '' }}>{{ $gender->title }}
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
                                            {{ old('nationality_id') == $nationality->id ? 'selected' : '' }}>
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
                                        value="{{ old('date_of_birth') }}"
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
                                    placeholder="Participant age" name="age" disabled value="{{ old('age') }}" />
                                <label for="participant_age">Participant age</label>
                            </div>
                        </div>
                        <div class=" gy-3">
                            <hr />
                        </div>
                        <div class="col mb-3">
                            <label class="form-label" for="qid_files">Upload Participant QID <span
                                    class='text-danger'></label>
                            <input class="form-control" id="qid_files" name="qid_files[]" type="file" multiple
                                required />
                            <small class="form-text text-muted">Max size: 2MB. Accepted formats: JPG, PNG,
                                GIF, PNG</small>
                        </div>
                        {{-- <div class="text-center mb-3">
                            <div class="mb-3 text-start">
                                <label class="form-label">Upload Participant QID <span class='text-danger'>(Extensions:
                                        jpg,jpeg,png,pdf Max 2MB)</span></label>
                                <input type="file" required name="qid_file" class="dropify" data-height="100"
                                    data-allowed-file-extensions="jpg jpeg png pdf" data-max-file-size="2M" />
                            </div>
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
                                            {{ old('pants_size_id') == $pant_size->id ? 'selected' : '' }}>
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
                                            {{ old('jersey_size_id') == $jersey_size->id ? 'selected' : '' }}>
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
                                            {{ old('jacket_size_id') == $jacket_size->id ? 'selected' : '' }}>
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
                                            {{ old('shoe_size_id') == $shoe_size->id ? 'selected' : '' }}>
                                            {{ $shoe_size->label }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="shoe_size_id">Shoe Size</label>
                            </div>
                        </div>

                        <div class="mb-0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="has_food_allergy"
                                    name="food_allergy" value="1">

                                <label class="form-check-label fw-semibold" for="has_food_allergy">
                                    Any food allergies?
                                </label>
                            </div>
                        </div>

                        <div class="mb-1 d-none" id="food_allergy_details_wrap">
                            <label class="form-label">
                                Please specify food allergies
                            </label>
                            <input type="text" class="form-control" name="food_allergy_details"
                                id="food_allergy_details" placeholder="e.g. nuts, dairy, shellfish">
                        </div>
                        <div class="mb-1">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="has_health_issues"
                                    name="health_issues" value="1">

                                <label class="form-check-label fw-semibold" for="has_health_issues">
                                    Any health issues?
                                </label>
                            </div>
                        </div>

                        <div class="mb-3 d-none" id="health_issues_details_wrap">
                            <label class="form-label">
                                Please specify health issues
                            </label>
                            <input type="text" class="form-control" id="health_issues_details"
                                name="health_issues_details" placeholder="e.g. nuts, dairy, shellfish">
                        </div>


                        <div class="col-12 gy-6">
                            <div class="row g-3 justify-content-end">
                                <div class="col-auto">
                                    <button class="btn btn-phoenix-primary px-5"
                                        onclick="window.location='{{ route('home') }}'" type="button">Cancel</button>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-primary px-5 px-sm-15" type="submit">Save Participant</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/pages/ypi/customer/create.js') }}"></script>

    {{-- @include('mds.admin.modals.booking_modals') --}}
@endsection

@push('script')
    <script>
        // showing the offcanvas for the task creation
        $(document).ready(function() {
            console.log('ready');
            $('.dropify').dropify();
        });

        const input = document.getElementById('photoUpload');
        const preview = document.getElementById('photoPreview');

        let selectedFiles = [];

        input.addEventListener('change', () => {
            const newFiles = Array.from(input.files);

            // ✅ Append new files, avoid duplicates (same name+size+lastModified)
            newFiles.forEach(f => {
                const exists = selectedFiles.some(x =>
                    x.name === f.name &&
                    x.size === f.size &&
                    x.lastModified === f.lastModified
                );
                if (!exists) selectedFiles.push(f);
            });

            syncInputFiles();
            renderPreviews();
        });

        function syncInputFiles() {
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            input.files = dt.files;
        }

        function renderPreviews() {
            preview.innerHTML = '';

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = e => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'preview-item';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'preview-img';

                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'photo-remove';
                    btn.innerHTML = '&times;';
                    btn.onclick = () => removeFile(index);

                    wrapper.appendChild(img);
                    wrapper.appendChild(btn);
                    preview.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });
        }

        function removeFile(index) {
            selectedFiles.splice(index, 1);
            syncInputFiles();
            renderPreviews();
        }
    </script>
@endpush
