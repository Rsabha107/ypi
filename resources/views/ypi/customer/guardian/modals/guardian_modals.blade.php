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
