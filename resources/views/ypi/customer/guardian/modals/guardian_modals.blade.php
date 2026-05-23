<div class="offcanvas offcanvas-end offcanvas-global-modal custom-offcanvas in65" id="offcanvas-edit-participant-modal"
    tabindex="-1" aria-labelledby="offcanvasWithBackdropLabel" data-bs-backdrop="static">
    <a class="close-task-detail in" id="close-task-detail" style="display: block;" data-bs-dismiss="offcanvas">
        <span>
            <svg class="svg-inline--fa fa-times fa-w-11" aria-hidden="true" focusable="false" data-prefix="fa"
                data-icon="times" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512"
                data-fa-i2svg="">
                <path fill="currentColor"
                    d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z">
                </path>
            </svg><!-- <i class="fa fa-times"></i> Font Awesome fontawesome.com -->
        </span>
    </a>
    <x-ypi.customer.participant-drawer-edit id="" formAction="{{ route('ypi.customer.guardian.update') }}"
        formId="edit_participant_slot_form" :events="$events" :participantTypes="$participant_types" :genders="$genders" 
        :nationalities="$nationalities" :pantSizes="$pant_sizes" :jerseySizes="$jersey_sizes" :jacketSizes="$jacket_sizes"
        :shoeSizes="$shoe_sizes" />


</div>

<div class="offcanvas offcanvas-end offcanvas-global-modal custom-offcanvas in65" id="offcanvas-add-participant-modal"
    tabindex="-1" aria-labelledby="offcanvasWithBackdropLabel" data-bs-backdrop="static">
    <a class="close-task-detail in" id="close-task-detail" style="display: block;" data-bs-dismiss="offcanvas">
        <span>
            <svg class="svg-inline--fa fa-times fa-w-11" aria-hidden="true" focusable="false" data-prefix="fa"
                data-icon="times" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512"
                data-fa-i2svg="">
                <path fill="currentColor"
                    d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z">
                </path>
            </svg><!-- <i class="fa fa-times"></i> Font Awesome fontawesome.com -->
        </span>
    </a>
    <x-ypi.customer.participant-drawer id="" formAction="{{ route('ypi.customer.guardian.store') }}"
        formId="add_participant_slot_form" :events="$events" :participantTypes="$participant_types" :genders="$genders" 
        :nationalities="$nationalities" :pantSizes="$pant_sizes" :jerseySizes="$jersey_sizes"
        :jacketSizes="$jacket_sizes" :shoeSizes="$shoe_sizes" />


</div>

<div class="modal fade" id="qidImageModal" tabindex="-1" aria-labelledby="qidImageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qidImageModalLabel">QID Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="qidImagePreview" src="" alt="QID Document" class="img-fluid" style="max-height: 70vh;">
            </div>
        </div>
    </div>
</div>

<style>
    .detail-field { padding: 8px 12px; }
    .detail-field .detail-label {
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #8a8f98;
        margin-bottom: 2px;
    }
    .detail-field .detail-value {
        font-size: 0.875rem;
        font-weight: 500;
        color: #1a1e2d;
        margin: 0;
    }
    .detail-section-header {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #6c757d;
        padding: 6px 12px 4px;
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 2px;
    }
    .detail-grid { display: grid; gap: 0; }
    .detail-grid-2 { grid-template-columns: 1fr 1fr; }
    .detail-grid-4 { grid-template-columns: 1fr 1fr 1fr 1fr; }
    .detail-grid-3 { grid-template-columns: 1fr 1fr 1fr; }
    .detail-field:not(:last-child) { border-right: 1px solid #f0f1f3; }
    .detail-row { border-bottom: 1px solid #f0f1f3; }
    #participantDetailsModal .modal-body { padding: 0; }
    #participantDetailsModal .modal-header { padding: 12px 16px 10px; }
    #participantDetailsModal .modal-footer { padding: 8px 16px; }
    .status-banner {
        text-align: center;
        padding: 10px 16px;
        border-bottom: 1px solid #e9ecef;
        background: #f8f9fa;
    }
    @media (max-width: 575px) {
        .detail-grid-2, .detail-grid-4, .detail-grid-3 { grid-template-columns: 1fr 1fr; }
        .detail-field:nth-child(even) { border-right: none; }
    }
</style>

<div class="modal fade" id="participantDetailsModal" tabindex="-1" aria-labelledby="participantDetailsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content overflow-hidden">
            <div class="modal-header border-bottom">
                <h6 class="modal-title fw-semibold" id="participantDetailsModalLabel">Participant Details</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                {{-- Status --}}
                <div class="status-banner">
                    <span class="badge badge-phoenix fs-8 px-3 py-2" id="detail-status">Status</span>
                </div>

                {{-- Row 1: Name + QID --}}
                <div class="detail-row detail-grid detail-grid-2">
                    <div class="detail-field">
                        <div class="detail-label">Participant Name</div>
                        <p id="detail-full-name" class="detail-value">-</p>
                    </div>
                    <div class="detail-field" style="border-right:none">
                        <div class="detail-label">QID</div>
                        <p id="detail-qid" class="detail-value">-</p>
                    </div>
                </div>

                {{-- Row 2: DOB + Gender + Nationality + School --}}
                <div class="detail-row detail-grid detail-grid-4">
                    <div class="detail-field">
                        <div class="detail-label">Date of Birth</div>
                        <p id="detail-dob" class="detail-value">-</p>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Gender</div>
                        <p id="detail-gender" class="detail-value">-</p>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Nationality</div>
                        <p id="detail-nationality" class="detail-value">-</p>
                    </div>
                    <div class="detail-field" style="border-right:none">
                        <div class="detail-label">School Name</div>
                        <p id="detail-school" class="detail-value">-</p>
                    </div>
                </div>

                {{-- Row 3: Event + Type + Venue + Match --}}
                <div class="detail-row detail-grid detail-grid-4">
                    <div class="detail-field">
                        <div class="detail-label">Event</div>
                        <p id="detail-event" class="detail-value">-</p>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Participant Type</div>
                        <p id="detail-type" class="detail-value">-</p>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Assigned Venue</div>
                        <p id="detail-venue" class="detail-value">-</p>
                    </div>
                    <div class="detail-field" style="border-right:none">
                        <div class="detail-label">Assigned Match</div>
                        <p id="detail-match" class="detail-value">-</p>
                    </div>
                </div>

                {{-- Sizes section --}}
                <div class="detail-section-header">Sizes</div>
                <div class="detail-row detail-grid detail-grid-4">
                    <div class="detail-field">
                        <div class="detail-label">Pants Size</div>
                        <p id="detail-pants" class="detail-value">-</p>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Jersey Size</div>
                        <p id="detail-jersey" class="detail-value">-</p>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Jacket Size</div>
                        <p id="detail-jacket" class="detail-value">-</p>
                    </div>
                    <div class="detail-field" style="border-right:none">
                        <div class="detail-label">Shoe Size</div>
                        <p id="detail-shoe" class="detail-value">-</p>
                    </div>
                </div>

                {{-- Medical section --}}
                <div class="detail-section-header">Medical Information</div>
                <div class="detail-row detail-grid detail-grid-2">
                    <div class="detail-field">
                        <div class="detail-label">Food Allergy</div>
                        <p id="detail-food-allergy" class="detail-value">-</p>
                        <p id="detail-food-allergy-type" class="mb-0 text-muted" style="font-size:0.78rem">-</p>
                    </div>
                    <div class="detail-field" style="border-right:none">
                        <div class="detail-label">Health Issues</div>
                        <p id="detail-health-issues" class="detail-value">-</p>
                        <p id="detail-health-issues-details" class="mb-0 text-muted" style="font-size:0.78rem">-</p>
                    </div>
                </div>

                {{-- Guardian section --}}
                <div class="detail-section-header">Guardian Information</div>
                <div class="detail-grid detail-grid-3">
                    <div class="detail-field">
                        <div class="detail-label">Guardian Name</div>
                        <p id="detail-guardian-name" class="detail-value">-</p>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Guardian Email</div>
                        <p id="detail-guardian-email" class="detail-value">-</p>
                    </div>
                    <div class="detail-field" style="border-right:none">
                        <div class="detail-label">Guardian Phone</div>
                        <p id="detail-guardian-phone" class="detail-value">-</p>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top">
                <button type="button" class="btn btn-sm btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
