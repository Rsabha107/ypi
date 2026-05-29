{{-- @extends('layouts.app') --}}
@extends('ypi.customer.layout.template')

@section('main')
<div class="pb-5">
    <div class="row g-4">
        <div class="col-12 col-xxl-12">
            <div class="mb-8">
                <h4 class="mb-2 text-white">Participant Management (Read Only)</h4>
                <h6 class="text-white fw-normal">View participant information and dietary requirements</h6>
            </div>

            <div id="participantTableContainer">
                <x-ypi.catering.participant-card />
            </div>
        </div>
    </div>
</div>

<!-- QID Image Modal -->
<div class="modal fade" id="qidImageModal" tabindex="-1" aria-labelledby="qidImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qidImageModalLabel">QID Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="qidImagePreview" src="" alt="QID Image" class="img-fluid" />
            </div>
        </div>
    </div>
</div>

<!-- Participant Details Modal -->
<div class="modal fade" id="participantDetailsModal" tabindex="-1" aria-labelledby="participantDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="participantDetailsModalLabel">Participant Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <!-- Personal Information -->
                    <div class="col-12">
                        <h6 class="text-primary border-bottom pb-2">Personal Information</h6>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Full Name:</label>
                        <p id="modal_full_name" class="mb-0">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">QID:</label>
                        <p id="modal_qid" class="mb-0">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Date of Birth:</label>
                        <p id="modal_date_of_birth" class="mb-0">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Gender:</label>
                        <p id="modal_gender" class="mb-0">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nationality:</label>
                        <p id="modal_nationality" class="mb-0">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">School:</label>
                        <p id="modal_school_name" class="mb-0">-</p>
                    </div>

                    <!-- Event & Status -->
                    <div class="col-12 mt-4">
                        <h6 class="text-primary border-bottom pb-2">Event Information</h6>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Event:</label>
                        <p id="modal_event" class="mb-0">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Participant Type:</label>
                        <p id="modal_participant_type" class="mb-0">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status:</label>
                        <p id="modal_status" class="mb-0">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Assigned Venue:</label>
                        <p id="modal_assigned_venue" class="mb-0">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Assigned Match:</label>
                        <p id="modal_assigned_match" class="mb-0">-</p>
                    </div>

                    <!-- Sizes -->
                    <div class="col-12 mt-4">
                        <h6 class="text-primary border-bottom pb-2">Sizes</h6>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Pants:</label>
                        <p id="modal_pants_size" class="mb-0">-</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Jersey:</label>
                        <p id="modal_jersey_size" class="mb-0">-</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Jacket:</label>
                        <p id="modal_jacket_size" class="mb-0">-</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Shoe:</label>
                        <p id="modal_shoe_size" class="mb-0">-</p>
                    </div>

                    <!-- Medical Information -->
                    <div class="col-12 mt-4">
                        <h6 class="text-primary border-bottom pb-2">Medical Information</h6>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Food Allergy:</label>
                        <p id="modal_food_allergy" class="mb-0">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Allergy Type:</label>
                        <p id="modal_food_allergy_type" class="mb-0">-</p>
                    </div>
                    <div class="col-12" id="food_allergy_others_section" style="display: none;">
                        <label class="form-label fw-bold">Other Allergies:</label>
                        <p id="modal_food_allergy_others" class="mb-0">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Health Issues:</label>
                        <p id="modal_health_issues" class="mb-0">-</p>
                    </div>
                    <div class="col-12" id="health_issues_details_section" style="display: none;">
                        <label class="form-label fw-bold">Health Issues Details:</label>
                        <p id="modal_health_issues_details" class="mb-0">-</p>
                    </div>

                    <!-- Guardian Information -->
                    <div class="col-12 mt-4">
                        <h6 class="text-primary border-bottom pb-2">Guardian Information</h6>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Guardian Name:</label>
                        <p id="modal_guardian_name" class="mb-0">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Guardian Email:</label>
                        <p id="modal_guardian_email" class="mb-0">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Guardian Phone:</label>
                        <p id="modal_guardian_phone" class="mb-0">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/pages/ypi/catering/participant.js') }}?v={{ time() }}"></script>
@endsection
