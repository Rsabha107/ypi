@extends('gms.layout.admin_template')
@section('main')
    <!-- ***************************************************************************** */ -->
    <!-- <div class="pb-9"> -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ url('home') }}"><?= get_label('home', 'Home') ?></a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('gms.admin.guest') }}"><?= get_label('guests', 'Guest') ?></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $guest_data->getFullNameAttribute() }}
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-center">
        <div id="cover-spin" style="display:none;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    {{-- <div class="col-12">
    <div class="container col-8">
        <div class="theme-wizard flight-booking-wizard">
            <ul class="nav justify-content-between nav-wizard nav-wizard-success">
                @foreach ($project->tasks->where('milestone', '1')->sortBy('id') as $milestone_task)
                @if ($milestone_task->status?->title === 'Completed')
                @php
                $done_flag = 'done completed';
                $fa = 'fas fa-check';
                @endphp
                @else
                @php
                $done_flag = null;
                $fa = 'fas fa-user';
                @endphp
                @endif
                <li class="nav-item">
                    <a class="{{ $done_flag }} nav-link fw-semibold" data-toggle="tooltip"
                        title="{{ $milestone_task->name }}" href="javascript:void(0);" data-wizard-step="1">
                        <div class="d-inline-block text-center">
                            <span class="nav-item-circle-parent">
                                <span class="d-block nav-item-circle">
                                    <span class="{{ $fa }}"></span>
                                </span>
                            </span>
                            <span class="mt-1 fs-9">{{ Str::substr($milestone_task->name, 0, 7) }}</span>
                        </div>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div> --}}

    <div class="row justify-content-between align-items-end mb-4 g-3">

        <div class="row align-items-center justify-content-between g-3 mb-4">
            <div class="col-12 col-md-auto">
                <h2 class="mb-0">{{ $guest_data->getFullNameAttribute() }}</h2>
            </div>
            <div class="col-12 col-md-auto d-flex">
                <a href="javascript:void(0);" id="edit_project_offcanv" data-id="{{ $guest_data->id }}" data-table="page"
                    class="btn btn-phoenix-secondary px-3 px-sm-5 me-2">
                    <span class="fa-solid fa-edit me-sm-2"></span>
                    <span class="d-none d-sm-inline">Edit </span>
                </a>
                <button class="btn btn-phoenix-danger me-2"><span class="fa-solid fa-trash me-2"></span><span>Delete
                        Guest</span></button>
                <!-- <div>
                                    <button class="btn px-3 btn-phoenix-secondary" type="button" data-bs-toggle="dropdown"
                                        data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span
                                            class="fa-solid fa-ellipsis"></span></button>
                                    <ul class="dropdown-menu dropdown-menu-end p-0" style="z-index: 9999;">
                                        <li><a class="dropdown-item" href="#!">View profile</a></li>
                                        <li><a class="dropdown-item" href="#!">Report</a></li>
                                        <li><a class="dropdown-item" href="#!">Manage notifications</a></li>
                                        <li><a class="dropdown-item text-danger" href="#!">Delete Lead</a></li>
                                    </ul>
                                </div> -->
            </div>
        </div>

        <div class="row g-0 border-top">
            <div class="nav flex-sm-row border-bottom border-bottom-sm-0 fs-9 vertical-tab h-100 justify-content-between"
                role="tablist" aria-orientation="horizontal">
                <a class="nav-link border-end border-end-sm border-bottom-sm text-center text-sm-start cursor-pointer outline-none d-sm-flex align-items-sm-center active"
                    id="overviewTab" data-bs-toggle="tab" data-bs-target="#overviewTabContent" role="tab"
                    aria-controls="overviewTabContent" aria-selected="true"> <svg xmlns="http://www.w3.org/2000/svg"
                        width="16px" height="16px" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="fa-solid fa-chart-line me-2 tab-icon-color">

                    </svg><span class="d-none d-sm-inline">Overview</span>
                </a>
                <a class="nav-link border-end border-end-sm border-bottom-sm text-center text-sm-start cursor-pointer outline-none d-sm-flex align-items-sm-center"
                    id="memeberTab" data-bs-toggle="tab" data-bs-target="#memeberTabContent" role="tab"
                    aria-controls="memeberTabContent" aria-selected="false" tabindex="-1"> <svg
                        xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="feather feather-truck me-sm-2 fs-4 nav-icons">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg><span class="d-none d-sm-inline">Memebers</span>
                </a>
                <a class="nav-link border-end border-end-sm border-bottom-sm text-center text-sm-start cursor-pointer outline-none d-sm-flex align-items-sm-center"
                    id="taskTab" data-bs-toggle="tab" data-bs-target="#taskTabContent" role="tab"
                    aria-controls="taskTabContent" aria-selected="false" tabindex="-1"> <svg
                        xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="fa-solid fa-chart-line me-2 tab-icon-color">
                    </svg><span class="d-none d-sm-inline">Tasks</span>
                </a>
                <a class="nav-link border-end border-end-sm border-bottom-sm text-center text-sm-start cursor-pointer outline-none d-sm-flex align-items-sm-center"
                    id="kanbanTab" data-bs-toggle="tab" data-bs-target="#kanbanTabContent" role="tab"
                    aria-controls="kanbanTabContent" aria-selected="false" tabindex="-1"> <svg
                        xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="fa-solid fa-chart-line me-2 tab-icon-color">
                    </svg><span class="d-none d-sm-inline">Task Board</span>
                </a>
                <a class="nav-link border-end border-end-sm border-bottom-sm text-center text-sm-start cursor-pointer outline-none d-sm-flex align-items-sm-center"
                    id="ganttTab" data-bs-toggle="tab" data-bs-target="#ganttTabContent" role="tab"
                    aria-controls="ganttTabContent" aria-selected="false" tabindex="-1"> <svg
                        xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="fa-solid fa-chart-line me-2 tab-icon-color">
                    </svg><span class="d-none d-sm-inline">Gantt</span>
                </a>
                <a class="nav-link border-end border-end-sm border-bottom-sm text-center text-sm-start cursor-pointer outline-none d-sm-flex align-items-sm-center"
                    id="notesTab" data-bs-toggle="tab" data-bs-target="#notesTabContent" role="tab"
                    aria-controls="notesTabContent" aria-selected="false" tabindex="-1"> <svg
                        xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="fa-solid fa-clipboard me-2 tab-icon-color">
                    </svg><span class="d-none d-sm-inline">Notes</span>
                </a>
                <a class="nav-link border-end border-end-sm border-bottom-sm text-center text-sm-start cursor-pointer outline-none d-sm-flex align-items-sm-center"
                    id="filesTab" data-bs-toggle="tab" data-bs-target="#filesTabContent" role="tab"
                    aria-controls="filesTabContent" aria-selected="false" tabindex="-1"> <svg
                        xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="fa-solid fa-paperclip me-2 tab-icon-color">
                    </svg><span class="d-none d-sm-inline">Files ({{ $guest_data->count() }})</span>
                </a>
                {{-- <a class="nav-link text-center text-sm-start border-bottom-sm cursor-pointer outline-none d-sm-flex align-items-sm-center"
                id="advancedTab" data-bs-toggle="tab" data-bs-target="#advancedTabContent" role="tab"
                aria-controls="advancedTabContent" aria-selected="false" tabindex="-1"> <svg
                    xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="feather feather-lock me-sm-2 fs-4 nav-icons">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg><span class="d-none d-sm-inline">Advanced</span>
            </a> --}}
            </div>
            <div class="col-sm-12 mt-1">
                <div class="tab-content py-3 h-100">
                    <!--======================================
                                            OVERVIEW TAB AREA
                                        ======================================-->
                    <div class="tab-pane fade active show" id="overviewTabContent" role="tabpanel"
                        aria-labelledby="overviewTab">
                        <h4 class="mb-3 d-sm-none">Overview</h4>
                        <div class="pb-9">
                            <div class="row g-4 g-xl-6">
                                <div class="col-xl-5 col-xxl-3">
                                    <div class="sticky-leads-sidebar">
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <div class="row align-items-center g-3">
                                                    <div class="col-12 col-sm-auto flex-1">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <h4 class="fw-bolder mb-2 line-clamp-1 mb-1">Guest Details
                                                            </h4>
                                                            <button class="btn btn-link p-0">
                                                                <a href="javascript:void(0);" id="edit_guest_offcanv"
                                                                    data-action="update" data-source="list"
                                                                    data-type="edit" data-table="none"
                                                                    data-id="{{ $guest_data->id }}" data-redirect="list"
                                                                    data-workspace_id="{{ session()->get('workspace_id') }}"
                                                                    role="button" title="edit">
                                                                    <span
                                                                        class="fas fa-pen fs-8 ms-3 text-body-quaternary"></span>
                                                                </a>
                                                            </button>
                                                        </div>
                                                        {{-- <span class="badge badge-phoenix badge-phoenix-warning"
                                                            role="button" data-bs-toggle="tooltip"
                                                            data-bs-placement="left"
                                                            title="Workspace">{{ $guest_data->workspaces?->title }}<span
                                                        class="ms-1 fa-solid fas fa-network-wired"></span>
                                                    </span> --}}
                                                        {{-- <span
                                                        class="badge badge-phoenix badge-phoenix-{{ $guest_data->status->color }} mb-4">{{ $guest_data->status->title }}<span
                                                            class="ms-1 uil uil-stopwatch"></span></span> --}}

                                                        <div class="align-items-center mb-4">
                                                            <div>
                                                                <div class="d-flex justify-content-between">
                                                                    <p class="text-body fw-semibold">Guest Type :</p>
                                                                    <p class="text-body-emphasis fw-semibold">
                                                                        {{ $guest_data->guest_type?->title }}
                                                                    </p>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <p class="text-body fw-semibold">Prefix :</p>
                                                                    <p class="text-body-emphasis fw-semibold">
                                                                        {{ $guest_data->prefix?->title }}
                                                                    </p>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <p class="text-body fw-semibold">First Name :</p>
                                                                    <p class="text-body-emphasis fw-semibold">
                                                                        {{ $guest_data->first_name }}
                                                                    </p>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <p class="text-body fw-semibold">Middle Name :</p>
                                                                    <p class="text-body-emphasis fw-semibold">
                                                                        {{ $guest_data->middle_name }}
                                                                    </p>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <p class="text-body fw-semibold">Last Name :</p>
                                                                    <p class="text-body-emphasis fw-semibold">
                                                                        {{ $guest_data->last_name }}
                                                                    </p>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <p class="text-body fw-semibold">Email :</p>
                                                                    <p class="text-danger fw-semibold">
                                                                        {{ $guest_data->email }}
                                                                    </p>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <p class="text-body fw-semibold">Phone :</p>
                                                                    <p class="text-body-emphasis fw-semibold">
                                                                        {{ $guest_data->mobile_number }}
                                                                    </p>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <p class="text-body fw-semibold">QID/Passport :</p>
                                                                    <p class="text-body-emphasis fw-semibold">
                                                                        {{ $guest_data->qid_passport }}
                                                                    </p>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <p class="text-body fw-semibold">Popular Name :</p>
                                                                    <p class="text-body-emphasis fw-semibold">
                                                                        {{ $guest_data->popular_name }}
                                                                    </p>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <p class="text-body fw-semibold">Client Group :</p>
                                                                    <p class="text-body-emphasis fw-semibold">
                                                                        {{ $guest_data->client_group?->title }}
                                                                    </p>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <p class="text-body fw-semibold">Hosted By :</p>
                                                                    <p class="text-body-emphasis fw-semibold">
                                                                        {{ $guest_data->hosted_by?->title }}
                                                                    </p>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <p class="text-body fw-semibold">Deadline :</p>
                                                                    <p class="text-body-emphasis fw-semibold">
                                                                        {{-- {{ \Carbon\Carbon::parse($guest_data->end_date)->format('d-M-Y') }} --}}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-md-flex d-xl-block align-items-center justify-content-between mb-5">
                                                            <div class="d-flex align-items-center mb-3 mb-md-0 mb-xl-3">
                                                                <div class="avatar avatar-xl me-3"><img
                                                                        class="rounded-circle"
                                                                        src="{{ asset('fnx/assets/img/team/72x72/58.webp') }}"
                                                                        alt="" /></div>
                                                                <div>
                                                                    <h5>{{ $guest_data->client?->first_name . ' ' . $guest_data->client?->last_name }}
                                                                    </h5>
                                                                    <div class="dropdown"><a
                                                                            class="text-body-secondary dropdown-toggle text-decoration-none dropdown-caret-none"
                                                                            href="#!" data-bs-toggle="dropdown"
                                                                            aria-expanded="false">
                                                                            Client<span
                                                                                class="fa-solid fa-caret-down text-body-secondary fs-9 ms-2"></span></a>
                                                                        <div class="dropdown-menu shadow-sm"
                                                                            style="min-width:20rem">
                                                                            <div class="card position-relative border-0">
                                                                                <div class="card-body p-0">
                                                                                    <div class="mx-3">
                                                                                        <form id="switchClientOwner"
                                                                                            class="needs-validation"
                                                                                            novalidate=""
                                                                                            {{-- action="{{ route('projects.admin.project.update.client') }}" --}}
                                                                                            method="POST">
                                                                                            @csrf
                                                                                            <input type="hidden"
                                                                                                name="project_id"
                                                                                                value="{{ $guest_data->id }}">
                                                                                            <h4 class="mb-3 fw-bold">Switch
                                                                                                ownership</h4>
                                                                                            <h5 class="mb-3">Project
                                                                                                Client
                                                                                            </h5>
                                                                                            <select id="change_client"
                                                                                                class="form-select mb-3"
                                                                                                data-id="{{ $guest_data->id }}"
                                                                                                aria-label="Default select"
                                                                                                name="client_id">
                                                                                                <option
                                                                                                    selected="selected">
                                                                                                    Select</option>
                                                                                                {{-- @foreach ($clients as $key => $client)
                                                                                            <option
                                                                                                value="{{ $client->id }}">
                                                                                                {{ $client->first_name . ' ' . $client->last_name }}
                                                                                            </option>
                                                                                            @endforeach --}}
                                                                                            </select>
                                                                                            <div class="text-end">
                                                                                                <button
                                                                                                    class="btn btn-link text-danger"
                                                                                                    type="button">Cancel</button>
                                                                                                <button
                                                                                                    class="btn btn-sm btn-primary px-5"
                                                                                                    type="submit">Save</button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                {{-- @foreach ($guest_data->tags as $key => $item)
                                                            <span class="badge badge-tag me-2 mb-1 pull-up"
                                                                style="background-color:{{ $item->color }};">{{ $item->title }}</span>
                                                            @endforeach --}}
                                                                <!-- <span class="badge badge-phoenix badge-phoenix-success me-2">Success</span>
                                                                                                <span class="badge badge-phoenix badge-phoenix-danger me-2">Lost</span>
                                                                                                <span class="badge badge-phoenix badge-phoenix-secondary">Close</span> -->
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex justify-content-between text-body-tertiary fw-semibold">
                                                            <p class="mb-2"> Progress</p>
                                                            {{-- <p class="mb-2 text-1100">{{ $project_progress }}%</p> --}}
                                                        </div>
                                                        {{-- <div class="progress bg-success-100">
                                                        <div class="progress-bar rounded bg-{{ $guest_data->status->color }}"
                                                            role="progressbar" aria-label="Success example"
                                                            style="width: {{ $project_progress }}%"
                                                            aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div> --}}

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                                <h4 class="fw-bolder mb-2 line-clamp-1 mb-5">Assigned Members
                                                    @if (Auth::user()->can('project.edit'))
                                                        <div class="float-end avatar avatar-m  me-1">
                                                            <a class="dropdown-toggle dropdown-caret-none d-inline-block"
                                                                href="javascript:void(0);" id="add_project_member"
                                                                data-source="list" data-table="page"
                                                                data-id="{{ $guest_data->id }}" data-redirect="list"
                                                                role="button" title="add">
                                                                <div class="avatar avatar-m  rounded-circle pull-up">
                                                                    <div
                                                                        class="avatar-name rounded-circle me-2 text-warning">
                                                                        <span>+</span>
                                                                    </div>
                                                                    <!-- <img class="rounded-circle " src="../../assets/img/team/34.webp" alt=""> -->
                                                                </div>
                                                            </a>
                                                        </div>
                                                    @endif
                                                </h4>
                                                <div class="row g-3">
                                                    <div class="col-12">

                                                        {{-- @foreach ($guest_data->employees as $key => $emp)
                                                    @if ($emp->emp_files?->file_path)
                                                    <div class="d-flex align-items-center mb-3"><a
                                                            href="#!" role="button"
                                                            title="{{ $emp->full_name }}">
                                                            <div class="avatar avatar-xl me-3 pull-up"><img
                                                                    class="rounded-circle"
                                                                    src="{{ $emp->emp_files->file_path }}{{ $emp->emp_files->file_name }}"
                                                                    alt="" />
                                                            </div>
                                                        </a>
                                                        <div><a class="fs-8 fw-bold"
                                                                href="#!">{{ $emp->full_name }}</a>
                                                            <div class="d-flex align-items-center">
                                                                <p
                                                                    class="mb-0 text-body-highlight fw-semibold fs-9 me-2">
                                                                    {{ $emp->designation->name }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="d-flex align-items-center mb-3"><a
                                                            href="#!" role="button"
                                                            title="{{ $emp->full_name }}">
                                                            <div
                                                                class="avatar avatar-xl me-3 rounded-circle pull-up">
                                                                <div class="avatar-name rounded-circle me-2">
                                                                    <span>{{ generateInitials($emp->full_name) }}</span>
                                                                </div>
                                                            </div>
                                                        </a>
                                                        <div><a class="fs-8 fw-bold"
                                                                href="#!">{{ $emp->full_name }}</a>
                                                            <div class="d-flex align-items-center">
                                                                <p
                                                                    class="mb-0 text-body-highlight fw-semibold fs-9 me-2">
                                                                    {{ $emp->designation->name }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @endforeach --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-7 col-xxl-9">
                                    <div class="card mb-3">
                                        <!-- <img class="card-img-top" src="../../../assets/img//generic/66.jpg" alt="..." /> -->
                                        <div class="card-body">
                                            <h4 class="card-title">Brief</h4>
                                            <!-- <p class="card-text">Here is the example of the Multiple Container Sortable feature of the </p> -->
                                            <p class="card-text text-body-secondary mb-4">
                                                {{ strip_tags($guest_data->description) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-2 row-cols-xxl-2 g-3 mb-3">
                                        <div class="col">
                                            <div class="card h-100 ">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <h4 class="fw-bolder mb-2 line-clamp-1 mb-1">Project Information
                                                        </h4>
                                                        <button class="btn btn-link p-0">
                                                            <a href="javascript:void(0);" id="edit_project_offcanv"
                                                                data-action="update" data-source="list" data-type="edit"
                                                                data-table="none" data-id="{{ $guest_data->id }}"
                                                                data-redirect="list"
                                                                data-workspace_id="{{ session()->get('workspace_id') }}"
                                                                role="button" title="edit">
                                                                <span
                                                                    class="fas fa-pen fs-8 ms-3 text-body-quaternary"></span>
                                                            </a>
                                                        </button>
                                                    </div>

                                                    <div class="col-12 col-sm-auto flex-1">
                                                        <table class="lh-sm">
                                                            <tbody>
                                                                <tr>
                                                                    <td
                                                                        class="align-top py-1 text-body text-nowrap fw-bold">
                                                                        Functional Area : </td>
                                                                    <td
                                                                        class="text-body-tertiary text-opacity-85 fw-semibold ps-3">
                                                                        {{ $guest_data->functional_area?->name ? $guest_data->functional_area?->name : 'not specified' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td
                                                                        class="align-top py-1 text-body text-nowrap fw-bold">
                                                                        Project Type :</td>
                                                                    <td
                                                                        class="text-body-tertiary text-opacity-85 fw-semibold ps-3">
                                                                        {{ $guest_data->types?->name ? $guest_data->types?->name : 'not specified' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td
                                                                        class="align-top py-1 text-body text-nowrap fw-bold">
                                                                        Project Category :</td>
                                                                    <td class="text-warning fw-semibold ps-3">
                                                                        {{ $guest_data->categories?->name ? $guest_data->categories?->name : 'not specified' }}
                                                                    </td>
                                                                </tr>
                                                                <tr class="mb-3">
                                                                    <td
                                                                        class="align-top py-1 text-body text-nowrap fw-bold">
                                                                        Attendeese :</td>
                                                                    <td
                                                                        class="text-body-tertiary text-opacity-85 fw-semibold ps-3">
                                                                        {{ $guest_data->audiences?->name ? $guest_data->audiences?->name : 'not specified' }}
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="card h-100 ">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <h4 class="fw-bolder mb-2 line-clamp-1 mb-1">Task status
                                                            ({{ $guest_data->count() }} tasks)</h4>
                                                    </div>

                                                    <div class="col-12 col-sm-auto flex-1">
                                                        <div class="echart-pie-edge-align-chart-example"
                                                            style="min-height:200px;width:100%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-2 row-cols-xxl-2 g-3 mb-3">
                                        <div class="col">
                                            <div class="card h-100 ">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <h4 class="fw-bolder mb-2 line-clamp-1 mb-1">Project Information
                                                        </h4>
                                                        <button class="btn btn-link p-0">
                                                            <a href="javascript:void(0);" id="edit_project_offcanv"
                                                                data-action="update" data-source="list" data-type="edit"
                                                                data-table="none" data-id="{{ $guest_data->id }}"
                                                                data-redirect="list"
                                                                data-workspace_id="{{ session()->get('workspace_id') }}"
                                                                role="button" title="edit">
                                                                <span
                                                                    class="fas fa-pen fs-8 ms-3 text-body-quaternary"></span>
                                                            </a>
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="card h-100">
                                                <div class="card-body d-flex flex-column">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <h5 class="mb-2">Total Spent</h5>
                                                            <h6 class="text-body-tertiary">Budget:
                                                                {{ number_format($guest_data->budget_allocation) }}
                                                            </h6>
                                                        </div>
                                                        {{-- <h4>{{ number_format($total_budget_spent) }}</h4> --}}
                                                    </div>
                                                    {{-- <div class="d-flex justify-content-center pt-3 flex-1">
                                                    <div class="echarts-budget-utilization"
                                                        style="height:100%;width:100%;"
                                                        data-percent="{{ number_format($budget_percentage_used, 2) }}"> --}}
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                {{-- <div class="d-flex align-items-center mb-2">
                                                        <div class="bullet-item bg-primary me-2"></div>
                                                        <h6 class="text-body fw-semibold flex-1 mb-0">Budget
                                                            Uitlization</h6>
                                                        <h6 class="text-body fw-semibold mb-0">
                                                            {{ number_format($budget_percentage_used, 2) }}%
                                                        </h6>
                                                    </div> --}}
                                                <!-- <div class="d-flex align-items-center">
                                                                                    <div class="bullet-item bg-primary-subtle me-2"></div>
                                                                                    <h6 class="text-body fw-semibold flex-1 mb-0">Non-paying customer</h6>
                                                                                    <h6 class="text-body fw-semibold mb-0">70%</h6>
                                                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!--======================================
                                            END OVERVIEW TAB AREA
                                        ======================================-->

            <!--======================================
                                            TASKS AREA
                                        ======================================-->
            <div class="tab-pane fade h-100" id="taskTabContent" role="tabpanel" aria-labelledby="taskTab">
                <h5 class="mb-3 text-body-highlight">Tasks</h5>
                <div class="mb-4 mt-4">
                    <div class="d-flex flex-wrap gap-3">
                        <!-- <div class="search-box">
                                                                <form class="position-relative">
                                                                    <input class="form-control search-input search" type="search" placeholder="Search products" aria-label="Search" />
                                                                    <span class="fas fa-search search-box-icon"></span>
                                
                                                                </form>
                                                            </div> -->
                        <div>
                            <select class="form-select select-appearance" id="tasks_department_filter"
                                aria-label="Default select example">
                                <option value="" selected>
                                    <?= get_label('select_department', 'Select department') ?></option>
                                {{-- @foreach ($departments as $key)
                                    <option value="{{ $key->id }}">{{ $key->name }}</option>
                                    @endforeach --}}
                            </select>
                        </div>
                        <div>
                            <select class="form-select select-appearance" id="tasks_employee_filter"
                                aria-label="Default select example">
                                <option value="" selected>
                                    <?= get_label('select_employee', 'Select employee') ?>
                                </option>
                                {{-- @foreach ($employees as $key)
                                    <option value="{{ $key->id }}">{{ $key->first_name }}
                                        {{ $key->last_name }}
                                    </option>
                                    @endforeach --}}
                            </select>
                        </div>
                        <div>
                            <select class="form-select select-appearance" id="tasks_status_filter"
                                aria-label="Default select example">
                                <option value="" selected><?= get_label('select_status', 'Select status') ?>
                                </option>
                                {{-- @foreach ($statuses as $key)
                                    <option value="{{ $key->id }}">{{ $key->title }}</option>
                                    @endforeach --}}
                            </select>
                        </div>
                        <!-- <div >
                                                                <select class="form-select select-appearance" id="active_archived_filter" aria-label="Default select example">
                                                                    <option value=""><?= get_label('active_archived', 'Select Active or Archived') ?></option>
                                                                    <option value="N" $selected>Active</option>
                                                                    <option value="Y" >Archived</option>
                                                                </select>
                                                            </div> -->

                        <div class="ms-xxl-auto">
                            <!-- <button class="btn btn-link text-body me-4 px-0"><span class="fa-solid fa-file-export fs-9 me-2"></span>Export</button> -->
                            <!-- <button class="btn btn-primary" id="addBtn"><span class="fas fa-plus me-2"></span>Add product</button> -->
                            @if (Auth::user()->can('task.create'))
                                <a href="javascript:void(0)" data-table="task_table" id="offcanvas-add-task"
                                    data-id="0">
                                    <button type="button" class="btn btn-primary px-5" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-original-title=" <?= get_label('add_new_task', 'Add new task') ?>">
                                        <i class="fa-solid fa-plus me-2"></i>Add new task
                                    </button>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mb-0">
                    {{-- <x-projects.admin-tasks-card :projectId="$guest_data->id" /> --}}
                </div>
            </div>
            <!--======================================
                                            END TASKS AREA
                                        ======================================-->

            <!--======================================
                                            GANTT AREA
                                        ======================================-->
            <div class="tab-pane fade h-100" id="ganttTabContent" role="tabpanel" aria-labelledby="ganttTab">
                <div>
                    {{-- <x-admin-project-gantt-card :project="$guest_data" /> --}}
                </div>
            </div>
            <!--======================================
                                            End GANTT AREA
                                        ======================================-->

            <!--======================================
                                            Kanban AREA
                                        ======================================-->
            <div class="tab-pane fade h-100" id="kanbanTabContent" role="tabpanel" aria-labelledby="kanbanTab">
                {{-- <h5 class="mb-3 text-body-highlight">kanbans</h5> --}}
                <div>
                    {{-- <x-admin-project-kanban-card :project="$guest_data" :taskStatuses="$statuses" :tasks="$tasks" /> --}}
                </div>
            </div>
            <!--======================================
                                        End Kanban AREA
                                    ======================================-->

            <!--======================================
                                            MEMEBER AREA
                                        ======================================-->
            <div class="tab-pane fade h-100" id="memeberTabContent" role="tabpanel" aria-labelledby="memeberTab">
                <div class="d-flex flex-column h-100">
                    {{-- <h5 class="mb-3 text-body-highlight">Memebers</h5> --}}
                    <div class="d-xl-flex justify-content-between mb-3">
                        <a href="javascript:void(0);">
                            <button type="button" id="add_project_member" data-id="{{ $guest_data->id }}"
                                class="btn btn-primary px-5" data-table="project_members_table" data-bs-toggle="tooltip"
                                data-bs-placement="right"
                                data-bs-original-title=" <?= get_label('add_new_project', 'Add project member') ?>">
                                <i class="fa-solid fa-plus me-2"></i>Add project member
                            </button>
                        </a>
                    </div>
                    <div class="mb-0">
                        {{-- <x-admin-project-members-card :users="$users" :projectId="$guest_data->id" :statuses="$statuses"
                            :departments="$departments" source="list" showpage="list"
                            showpageid="list_{{ $guest_data->id }}" /> --}}
                    </div>
                </div>
            </div>
            <!--======================================
                                            End Member Memebers
                                        ======================================-->

            <!--======================================
                                            Notes AREA
                                        ======================================-->
            <div class="tab-pane fade" id="notesTabContent" role="tabpanel" aria-labelledby="notesTab">
                <h5 class="mb-3 text-body-highlight">Notes</h5>
                <div class="card mb-5">
                    <div class="mb-2">
                        <div class="row align-items-center justify-content-between g-3 mt-2 ms-2">
                            <div class="col-12 col-md-auto d-flex">
                                @if (Auth::user()->can('project.note.create'))
                                    <a href="#!" class="btn btn-phoenix-primary px-3 px-sm-5 me-2"
                                        data-bs-toggle="modal" data-bs-target="#addEventNoteModal" aria-haspopup="true"
                                        aria-expanded="false" data-bs-reference="parent"><span
                                            class="fas fa-plus me-sm-2"></span><span class="d-none d-sm-inline">Add a
                                            new note</span></a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- <textarea class="form-control mb-3" id="notes" rows="4"> </textarea> -->
                    <div class="row gy-4">
                        <div class="col-12 col-xl-auto flex-1">

                            <!--======================================
                                            End Advanced AREA
                                        ======================================-->
                        </div>
                    </div>
                </div>

                <div class="col-xl-12 col-xxl-12">

                    <!-- this is the Add Attachement Modal for projects -->
                    <div class="modal fade" id="addProjectAttachementModal" tabindex="-1" data-bs-backdrop="static"
                        aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-top">
                            <div class="modal-content bg-100">
                                <div class="modal-header bg-modal-header">
                                    <h3 class=" text-white mb-0" id="staticBackdropLabel">Upload File</h3>
                                    <button class="btn p-1" type="button" data-bs-dismiss="modal"
                                        aria-label="Close"><span class="fas fa-times fs--1 text-danger"></span></button>
                                </div>
                                <form id="projectFileUploadForm" class="needs-validation form-submit-event"
                                    novalidate="" action="{{ route('file.store') }}" method="POST"
                                    enctype='multipart/form-data'>
                                    @csrf

                                    <input type="hidden" id="model_id" name="model_id" value="{{ $guest_data->id }}">
                                    <input type="hidden" id="employee_id" name="employee_id" value="0">
                                    <input type="hidden" name="model_name" value="project">
                                    <input type="hidden" name="table" value="project_file_table">

                                    <div class="modal-body">
                                        <div class="modal-body px-0">
                                            <div class="row g-4">
                                                <div class="col-lg-12">
                                                    <div class="mb-4">
                                                        <label class="text-1000 fw-bold mb-2">File</label>
                                                        <input class="form-control" type="file" name="file_name"
                                                            id="fileupld" required />
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="progress">
                                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                                                role="progressbar" aria-valuenow="0" aria-valuemin="0"
                                                                aria-valuemax="100" style="width: 0%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-outline-danger" type="button"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button class="btn btn-primary" type="submit" id="submit_btn">Upload</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="progressModal" tabindex="-1" data-bs-backdrop="static"
                        aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-top">
                            <div class="modal-content bg-100">
                                <div class="modal-header bg-modal-header">
                                    <h3 class=" text-white mb-0" id="staticBackdropLabel">Change Propgress %</h3>
                                    <button class="btn p-1" type="button" data-bs-dismiss="modal"
                                        aria-label="Close"><span class="fas fa-times fs--1 text-danger"></span></button>
                                </div>
                                <form class="needs-validation" novalidate="" action="" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="modal-body px-0">
                                            <div class="row g-4">
                                                <div class="col-lg-12">
                                                    <input type="hidden" id="editId" name="id">
                                                    <input type="hidden" id="editEventId" name="event_id">
                                                    <div class="mb-4">
                                                        <label class="text-1000 fw-bold mb-2">Name</label>
                                                        <input class="form-control" type="number" max="100"
                                                            min="0" name="prorgress_number"
                                                            id="editPoregessNumber" required />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-outline-danger" type="button"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button class="btn btn-primary" type="submit">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- this is the Add Event Notes Modal -->
                    <div class="modal fade" id="addEventNoteModal" tabindex="-1" data-bs-backdrop="static"
                        aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content bg-100">
                                <div class="modal-header bg-modal-header">
                                    <h3 class=" text-white mb-0" id="staticBackdropLabel">Add note</h3>
                                    <button class="btn p-1" type="button" data-bs-dismiss="modal"
                                        aria-label="Close"><span class="fas fa-times fs--1 text-danger"></span></button>
                                </div>
                                <form class="needs-validation" novalidate="" action="" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="modal-body px-0">
                                            <div class="row g-4">
                                                <div class="col-lg-12">
                                                    <input type="hidden" id="addId" name="event_id"
                                                        value="{{ $guest_data->id }}">
                                                    <div class="mb-4">
                                                        <label class="text-1000 fw-bold mb-2">Note</label>
                                                        <textarea class="form-control mb-3" id="notes" name="note_text" rows="4"> </textarea>

                                                    </div>
                                                    <!-- <div class="mb-4">
                                                                                        <label class="text-1000 fw-bold mb-2">Status</label>
                                                                                        <select class="form-select" name="active_flag" id="activeFlag" required>
                                                                                            <option value="" >Select</option>
                                                                                            <option value="1" selected>Active</option>
                                                                                            <option value="2">Inactive</option>
                                                                                        </select>
                                                                                    </div> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-outline-danger" type="button"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button class="btn btn-primary" type="submit">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal to show the overview of the task and notes and attachment with tasks and  -->
                    {{-- @include('projects.admin.modals.project_modal') --}}
                    {{-- @include('projects.admin.modals.task_modal') --}}

                </div>
            </div>
        @endsection

        @push('script')
            {{-- @include('projects.admin.partials.charts-js') --}}

            <script src="{{ asset('assets/jquery/dist/jquery.form.min.js') }}"></script>
            <script src="{{ asset('assets/js/pages/project/admin/projects.js') }}"></script>
            <script src="{{ asset('assets/js/pages/project/admin/tasks.js') }}"></script>

            <script>
                $("#taskTab").click(function(e) {
                    e.preventDefault();
                    console.log('taskTab')
                    $("#task_table").bootstrapTable("refresh");
                });

                $("body").on("click", "#delete_project_file", function(e) {
                    alert("inside delete_project_file from d.blade.php");
                    var id = $(this).data("id");
                    var tableID = $(this).data("table");
                    e.preventDefault();
                    // alert("id: "+id);
                    // alert("tableID: "+tableID);
                    var link = $(this).attr("href");
                    Swal.fire({
                        title: "Are you sure?",
                        text: "Delete This Data?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, delete it!",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "/global/files/delete/" + id,
                                type: "DELETE",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                    'content'), // Replace with your method of getting the CSRF token
                                },
                                dataType: "json",
                                success: function(result) {
                                    if (!result["error"]) {
                                        toastr.success(result["message"]);
                                        // divToRemove.remove();
                                        // $("#fileCount").html("File ("+result["count"]+")");
                                        // console.log('before table refrest for #'+tableID);
                                        $("#" + tableID).bootstrapTable("refresh");
                                        // Swal.fire(
                                        //     'Deleted!',
                                        //     'Your file has been deleted.',
                                        //     'success'
                                        //   )
                                    }
                                },
                                error: function(xhr, ajaxOptions, thrownError) {
                                    console.log(xhr.status);
                                    console.log(thrownError);
                                },
                            });
                        }
                    });
                });
            </script>

            <script type="text/javascript">
                $(document).ready(function() {

                    // var id = $('#addId').val();
                    $('#fileUploadForm').ajaxForm({
                        beforeSend: function() {
                            var percentage = '0';

                        },
                        uploadProgress: function(event, position, total, percentComplete) {
                            var percentage = percentComplete;
                            $('.progress .progress-bar').css("width", percentage + '%', function() {
                                return $(this).attr("aria-valuenow", percentage) + "%";
                            })
                        },
                        complete: function(xhr) {
                            console.log('File has uploaded: ' + "");
                            window.location.href = "";
                        }
                    });

                    $('#taskFileUploadForm').ajaxForm({
                        beforeSend: function() {
                            var percentage = '0';
                            console.log('File has uploaded: ' + "");

                        },
                        uploadProgress: function(event, position, total, percentComplete) {
                            var percentage = percentComplete;
                            $('.progress .progress-bar').css("width", percentage + '%', function() {
                                return $(this).attr("aria-valuenow", percentage) + "%";
                            })
                        },
                        complete: function(xhr) {
                            console.log('File has uploaded: ' + "");
                            window.location.href = "";
                        }
                    });

                    $('#dataList').DataTable({
                        "lengthChange": false,
                        "paging": false,
                        // "order": [
                        //     [0, "asc"]
                        // ],
                        // dom: 'Bfrtip',
                        // buttons: [
                        //     'copyHtml5',
                        //     'excelHtml5',
                        //     'csvHtml5',
                        //     'pdf',
                        //     // 'colvis'
                        // ]
                        // buttons: [{
                        //     extend: 'collection',
                        //     text: 'Export',
                        //     buttons: [{
                        //             extend: 'copyHtml5',
                        //             exportOptions: {
                        //                 columns: [0, ':visible']
                        //             }
                        //         },
                        //         {
                        //             extend: 'excelHtml5',
                        //             exportOptions: {
                        //                 columns: ':visible'
                        //             }
                        //         },
                        //         {
                        //             extend: 'csvHtml5',
                        //             exportOptions: {
                        //                 columns: ':visible'
                        //             }
                        //         },
                        //         {
                        //             extend: 'pdfHtml5',
                        //             exportOptions: {
                        //                 columns: [0, 1, 2, 5]
                        //             }
                        //         },
                        //         'colvis'
                        //     ],
                        // }]
                    });

                });
            </script>
            {{-- @include('tracki.partials.event-js') --}}
        @endpush
